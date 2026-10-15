<?php

namespace App\Http\Controllers;

use App\Models\Tabungan;
use App\Models\TabunganTopup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TabunganTopupController extends Controller
{
    private const SETTLED = ['capture', 'settlement'];

    public function create(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:10000', 'max:10000000'],
        ]);

        $user = $request->user();
        if ($user->role !== 'siswa') {
            return response()->json(['message' => 'Top up hanya untuk akun siswa.'], 422);
        }

        $orderId = 'TOPUP-' . strtoupper(Str::random(12));

        // ?auth= ikut dibawa supaya sesi tetap hidup setelah putar ke domain Midtrans
        // (cookie lintas-domain diblokir browser → tanpa ini guard siswa lempar balik ke landing).
        $returnBase = frontendAuthUrl('/tabungan');
        $finishUrl = $returnBase . '&midtrans=finish&order=' . $orderId;
        $cancelUrl = $returnBase . '&midtrans=cancel';

        $response = Http::withBasicAuth($this->serverKey(), '')
            ->post($this->snapUrl() . '/snap/v1/transactions', [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $data['amount'],
                    'finish_url' => $finishUrl,
                    'cancel_url' => $cancelUrl,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                ],
            ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Gagal membuat transaksi pembayaran.'], 502);
        }

        $topup = TabunganTopup::create([
            'user_id' => $user->id,
            'order_id' => $orderId,
            'midtrans_order_id' => $response->json('order_id'),
            'snap_token' => $response->json('token'),
            'amount' => $data['amount'],
        ]);

        return response()->json([
            'order_id' => $topup->order_id,
            'redirect_url' => $response->json('redirect_url'),
            'finish_url' => $finishUrl,
            'amount' => $topup->amount,
        ], 201);
    }

    public function status(Request $request, string $orderId)
    {
        return response()->json($this->payload(
            $this->findOwned($request, $orderId)
        ));
    }

    /**
     * Fallback saat webhook tak sampai (dev lokal: host sandbox tidak bisa menjangkau
     * localhost). Tanya status langsung ke Midtrans, lalu credet bila settlement.
     */
    public function verify(Request $request, string $orderId)
    {
        $topup = $this->findOwned($request, $orderId);

        // Akun Snap redirect-v4 tidak mengembalikan order_id dari API create, dan endpoint
        // status-nya hanya menerima snap token.
        $lookup = $topup->midtrans_order_id ?: $topup->snap_token;

        if ($topup->status === 'pending' && $lookup) {
            $remote = Http::withBasicAuth($this->serverKey(), '')
                ->get($this->snapUrl() . '/snap/v1/transactions/' . $lookup);

            if ($remote->ok()) {
                if ($remote->json('transaction_details.order_id') === $topup->order_id) {
                    $this->applyRemoteStatus($topup, $remote->json());
                }
            } elseif ($this->isSucceedResponse($remote)) {
                // Snap redirect-v4 membalas 409 "transaction has been succeed" untuk transaksi
                // yang sudah lunas (bukan 200 + payload) — tanpa cabang ini transaksi sukses
                // tak pernah terbaca dan saldo tak pernah credet. Token ini hanya bisa sukses
                // bila dibayar, dan amount credet tetap dari DB lokal, jadi aman.
                $this->applyRemoteStatus($topup, ['transaction_status' => 'settlement']);
            }
        }

        return response()->json($this->payload($topup->fresh()));
    }

    private function findOwned(Request $request, string $orderId): TabunganTopup
    {
        return TabunganTopup::where('order_id', $orderId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }

    private function payload(TabunganTopup $topup): array
    {
        return [
            'order_id' => $topup->order_id,
            'status' => $topup->status,
            'amount' => $topup->amount,
            'method' => $topup->method,
            'paid_at' => $topup->paid_at?->toIso8601String(),
        ];
    }

    public function notification(Request $request)
    {
        $body = $request->all();
        $orderId = $body['order_id'] ?? '';
        $statusCode = $body['status_code'] ?? '';
        $grossAmount = $body['gross_amount'] ?? '';

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey());
        if (!hash_equals($expected, (string) ($body['signature_key'] ?? ''))) {
            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        $topup = TabunganTopup::where('order_id', $orderId)->first();
        if (!$topup) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        if ((int) $grossAmount !== $topup->amount) {
            return response()->json(['message' => 'Amount mismatch.'], 422);
        }

        $this->applyRemoteStatus($topup, $body);

        return response()->json(['ok' => true]);
    }

    /**
     * Satu-satunya tempat yang mengubah status topup + menulis saldo. Dipakai webhook
     * dan verify, jadi keduanya tidak bisa menghasilkan kredit ganda atau beda hasil.
     */
    private function applyRemoteStatus(TabunganTopup $topup, array $remote): void
    {
        $status = $remote['transaction_status'] ?? '';

        DB::transaction(function () use ($topup, $remote, $status) {
            $locked = TabunganTopup::whereKey($topup->id)->lockForUpdate()->first();

            // lockForUpdate + status check = idempotent, tidak ada kredit ganda saat webhook retry
            if (in_array($status, self::SETTLED, true)) {
                if ($locked->status !== 'paid') {
                    $locked->update([
                        'status' => 'paid',
                        'method' => $remote['payment_type'] ?? null,
                        'paid_at' => now(),
                    ]);

                    Tabungan::create([
                        'user_id' => $locked->user_id,
                        'type' => 'setor',
                        'amount' => $locked->amount,
                        'description' => 'Top Up Online',
                        'input_by' => null,
                    ]);
                }
            } elseif (in_array($status, ['expire', 'cancel', 'deny'], true)) {
                $locked->update(['status' => 'failed']);
            }
        });
    }

    private function serverKey(): string
    {
        return (string) config('services.midtrans.server_key');
    }

    private function snapUrl(): string
    {
        return config('services.midtrans.base_url')
            ?: (config('services.midtrans.production')
                ? 'https://app.midtrans.com'
                : 'https://app.sandbox.midtrans.com');
    }
}