<?php

namespace App\Http\Controllers;

use App\Models\KoperasiOrder;
use App\Models\KoperasiProduct;
use App\Models\Tabungan;
use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class KoperasiController extends Controller
{
    private const SETTLED = ['capture', 'settlement'];

    // Jam operasional koperasi (WIB). Di luar ini pesanan ditolak.
    private const OPEN_HOUR = 7;
    private const CLOSE_HOUR = 15;

    /** Katalog produk + status buka/tutup + saldo siswa (katalog boleh dilihat publik). */
    public function index(Request $request)
    {
        $products = KoperasiProduct::where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(fn (KoperasiProduct $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->description,
                'category' => $p->category,
                'price' => $p->price,
                'stock' => $p->stock,
                'image' => $p->image,
                'emoji' => $p->emoji,
            ]);

        $user = $request->user();

        return response()->json([
            'products' => $products,
            'categories' => $products->pluck('category')->unique()->values(),
            'open' => self::isOpen(),
            'hours' => self::hoursLabel(),
            'saldo' => $user ? self::saldoFor($user->id) : 0,
        ]);
    }

    /**
     * Buat pesanan. Harga & nama SELALU diambil dari DB — kiriman client hanya
     * product_id + qty, jadi harga tidak bisa dimanipulasi.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'siswa') {
            return response()->json(['message' => 'Hanya siswa yang bisa berbelanja.'], 422);
        }

        if (!self::isOpen()) {
            return response()->json(['message' => 'Koperasi sedang tutup. Jam operasional ' . self::hoursLabel() . '.'], 422);
        }

        $data = $request->validate([
            'items' => 'required|array|min:1|max:50',
            'items.*.product_id' => 'required|integer',
            'items.*.qty' => 'required|integer|min:1|max:99',
        ]);

        $products = KoperasiProduct::whereIn('id', collect($data['items'])->pluck('product_id'))
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $items = [];
        $total = 0;
        foreach ($data['items'] as $line) {
            $product = $products->get($line['product_id']);
            if (!$product) {
                return response()->json(['message' => 'Produk tidak ditemukan atau sudah tidak dijual.'], 422);
            }
            if ($product->stock < $line['qty']) {
                return response()->json(['message' => "Stok {$product->name} tidak mencukupi (tersisa {$product->stock})."], 422);
            }
            $total += $product->price * $line['qty'];
            $items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'qty' => $line['qty'],
            ];
        }

        $order = KoperasiOrder::create([
            'user_id' => $user->id,
            'kode' => KoperasiOrder::generateKode(),
            'items' => $items,
            'total' => $total,
            'pickup_code' => KoperasiOrder::generatePickupCode(),
            'metode' => null,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Pesanan dibuat. Pilih metode pembayaran.',
            'order' => $this->orderPayload($order),
        ], 201);
    }

    /**
     * Bayar pesanan dari saldo tabungan. Kunci baris user dulu (anti double-spend),
     * lalu tandai dibayar + kurangi stok + tulis ledger tarik — semua satu transaksi.
     */
    public function payWithTabungan(Request $request, string $kode)
    {
        $user = $request->user();

        return DB::transaction(function () use ($user, $kode) {
            User::whereKey($user->id)->lockForUpdate()->first();

            $order = KoperasiOrder::where('kode', $kode)->where('user_id', $user->id)->first();
            if (!$order) {
                return response()->json(['message' => 'Pesanan tidak ditemukan.'], 404);
            }
            if ($order->status !== 'pending') {
                return response()->json(['message' => 'Pesanan ini sudah dibayar atau dibatalkan.'], 422);
            }

            $saldo = self::saldoFor($user->id);
            if ($order->total > $saldo) {
                return response()->json(['message' => 'Saldo tabungan tidak mencukupi. Silakan top up dulu.'], 422);
            }

            $err = $this->markPaid($order, 'tabungan');
            if ($err) {
                return response()->json(['message' => $err], 422);
            }

            Tabungan::create([
                'user_id' => $user->id,
                'type' => 'tarik',
                'amount' => $order->total,
                'description' => 'Pembayaran Koperasi ' . $order->kode,
                'input_by' => $user->id,
            ]);

            return response()->json([
                'message' => 'Pembayaran dari tabungan berhasil.',
                'saldo' => $saldo - $order->total,
                'order' => $this->orderPayload($order->fresh()),
            ], 201);
        });
    }

    /** Buat transaksi Midtrans Snap untuk pesanan, kembalikan token popup. */
    public function createMidtrans(Request $request, string $kode)
    {
        $user = $request->user();
        $order = KoperasiOrder::where('kode', $kode)->where('user_id', $user->id)->firstOrFail();

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Pesanan ini sudah dibayar atau dibatalkan.'], 422);
        }

        // kode dipakai sebagai order_id Midtrans (sudah unik), jadi webhook bisa
        // menemukan order langsung tanpa kolom tambahan.
        $returnBase = frontendAuthUrl('/koperasi');
        $response = Http::withBasicAuth($this->serverKey(), '')
            ->post($this->snapUrl() . '/snap/v1/transactions', [
                'transaction_details' => [
                    'order_id' => $order->kode,
                    'gross_amount' => $order->total,
                    'finish_url' => $returnBase . '&midtrans=finish&order=' . $order->kode,
                    'cancel_url' => $returnBase . '&midtrans=cancel',
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                ],
            ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Gagal membuat transaksi pembayaran.'], 502);
        }

        $order->update([
            'metode' => 'midtrans',
            'snap_token' => $response->json('token'),
            'midtrans_order_id' => $order->kode,
        ]);

        return response()->json([
            'order' => $this->orderPayload($order->fresh()),
            'snap_token' => $order->snap_token,
            'client_key' => $this->clientKey($order->snap_token),
            'snap_script_url' => $this->snapUrl() . '/snap/snap.js',
            'redirect_url' => $response->json('redirect_url'),
        ], 201);
    }

    public function status(Request $request, string $kode)
    {
        $order = KoperasiOrder::where('kode', $kode)->where('user_id', $request->user()->id)->firstOrFail();

        return response()->json(['order' => $this->orderPayload($order)]);
    }

    /** Fallback webhook: tanya langsung ke Midtrans, lalu tandai dibayar bila lunas. */
    public function verify(Request $request, string $kode)
    {
        $order = KoperasiOrder::where('kode', $kode)->where('user_id', $request->user()->id)->firstOrFail();

        if ($order->status === 'pending' && $order->snap_token) {
            $this->queryAndApply($order);
        }

        return response()->json(['order' => $this->orderPayload($order->fresh())]);
    }

    public function myOrders(Request $request)
    {
        $orders = KoperasiOrder::where('user_id', $request->user()->id)->latest()->limit(50)->get();

        return response()->json(['orders' => $orders->map(fn (KoperasiOrder $o) => $this->orderPayload($o))]);
    }

    public function midtransNotification(Request $request)
    {
        $body = $request->all();
        $kode = $body['order_id'] ?? '';
        $statusCode = $body['status_code'] ?? '';
        $grossAmount = $body['gross_amount'] ?? '';

        $expected = hash('sha512', $kode . $statusCode . $grossAmount . $this->serverKey());
        if (!hash_equals($expected, (string) ($body['signature_key'] ?? ''))) {
            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        $order = KoperasiOrder::where('kode', $kode)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        if ((int) $grossAmount !== $order->total) {
            return response()->json(['message' => 'Amount mismatch.'], 422);
        }

        $this->applyRemoteStatus($order, $body);

        return response()->json(['ok' => true]);
    }

    /**
     * Tandai order dibayar + kurangi stok, dalam satu transaksi (dipanggil pemanggil
     * yang sudah memastikan status pending). Return pesan error, atau null bila sukses.
     */
    private function markPaid(KoperasiOrder $order, string $metode): ?string
    {
        foreach ($order->items as $line) {
            $product = KoperasiProduct::whereKey($line['product_id'])->lockForUpdate()->first();
            if (!$product || $product->stock < $line['qty']) {
                return "Stok {$line['name']} sudah habis. Silakan ulangi pesanan.";
            }
            $product->decrement('stock', $line['qty']);
        }

        $order->update([
            'status' => 'dibayar',
            'metode' => $metode,
            'paid_at' => now(),
        ]);

        return null;
    }

    /**
     * Satu-satunya tempat status Midtrans diterapkan (dipakai webhook & verify) →
     * lockForUpdate + guard status pending = idempotent, stok tidak berkurang dua kali.
     */
    private function applyRemoteStatus(KoperasiOrder $order, array $remote): void
    {
        $status = $remote['transaction_status'] ?? '';

        DB::transaction(function () use ($order, $status) {
            $locked = KoperasiOrder::whereKey($order->id)->lockForUpdate()->first();

            if (in_array($status, self::SETTLED, true)) {
                if ($locked->status === 'pending') {
                    $this->markPaid($locked, 'midtrans');
                }
            } elseif (in_array($status, ['expire', 'cancel', 'deny'], true)) {
                if ($locked->status === 'pending') {
                    $locked->update(['status' => 'expired']);
                }
            }
        });
    }

    private function queryAndApply(KoperasiOrder $order): void
    {
        $lookup = $order->midtrans_order_id ?: $order->snap_token;
        if (!$lookup) {
            return;
        }

        $remote = Http::withBasicAuth($this->serverKey(), '')
            ->get($this->snapUrl() . '/snap/v1/transactions/' . $lookup);

        if ($remote->ok()) {
            if ($remote->json('transaction_details.order_id') === $order->kode) {
                $this->applyRemoteStatus($order, $remote->json());
            }
        } elseif ($this->isSucceedResponse($remote)) {
            $this->applyRemoteStatus($order, ['transaction_status' => 'settlement']);
        }
    }

    private function orderPayload(KoperasiOrder $order): array
    {
        return [
            'kode' => $order->kode,
            'items' => $order->items,
            'total' => $order->total,
            'pickup_code' => $order->pickup_code,
            'metode' => $order->metode,
            'status' => $order->status,
            'paid_at' => $order->paid_at?->toIso8601String(),
            'picked_up_at' => $order->picked_up_at?->toIso8601String(),
            'created_at' => $order->created_at?->toIso8601String(),
        ];
    }

    private function clientKey(?string $snapToken): ?string
    {
        if (!$snapToken) {
            return null;
        }

        try {
            $res = Http::withBasicAuth($this->serverKey(), '')
                ->get($this->snapUrl() . '/snap/v1/transactions/' . $snapToken);

            return $res->ok() ? $res->json('merchant.client_key') : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function isSucceedResponse(Response $remote): bool
    {
        return $remote->status() === 409
            && str_contains(implode(' ', (array) $remote->json('error_messages', [])), 'transaction has been succeed');
    }

    // ponytail: saldo via 1 aggregate query, sama dengan TabunganController::saldoFor
    private static function saldoFor(int $userId): int
    {
        return (int) Tabungan::where('user_id', $userId)
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'setor' THEN amount ELSE -amount END), 0) as saldo")
            ->value('saldo');
    }

    private static function isOpen(): bool
    {
        $now = now('Asia/Jakarta');

        return $now->dayOfWeek !== 0
            && $now->hour >= self::OPEN_HOUR
            && $now->hour < self::CLOSE_HOUR;
    }

    private static function hoursLabel(): string
    {
        return 'Senin–Sabtu, 07.00–15.00 WIB';
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
