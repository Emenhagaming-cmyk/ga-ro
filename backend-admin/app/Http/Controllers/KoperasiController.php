<?php

namespace App\Http\Controllers;

use App\Models\KoperasiOrder;
use App\Models\KoperasiProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KoperasiController extends Controller
{
    public function adminIndex(Request $request)
    {
        $orders = $this->filteredOrders($request)->latest()->paginate(20)->withQueryString();

        $stats = [
            'omzet' => (int) KoperasiOrder::whereIn('status', KoperasiOrder::PAID_STATUSES)->sum('total'),
            'pending' => KoperasiOrder::where('status', 'pending')->count(),
            'siap' => KoperasiOrder::where('status', 'dibayar')->count(),
            'diambil' => KoperasiOrder::where('status', 'diambil')->count(),
        ];

        return view('koperasi.index', [
            'orders' => $orders,
            'stats' => $stats,
            'status' => $request->query('status'),
            'q' => trim((string) $request->query('q')),
        ]);
    }

    /** Filter pesanan dipakai bareng oleh daftar & export CSV. */
    private function filteredOrders(Request $request)
    {
        $status = $request->query('status');
        $q = trim((string) $request->query('q'));

        return KoperasiOrder::with('user:id,name,username')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('kode', 'like', "%{$q}%")
                    ->orWhere('pickup_code', 'like', "%{$q}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%")
                        ->orWhere('username', 'like', "%{$q}%"));
            }));
    }

    /** Unduh pesanan (mengikuti filter aktif) sebagai CSV. */
    public function exportCsv(Request $request)
    {
        $orders = $this->filteredOrders($request)->latest()->get();

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Kode', 'Kode Ambil', 'Siswa', 'Username', 'Item', 'Total', 'Metode', 'Status', 'Dibuat', 'Dibayar', 'Diambil'], ',', '"', '\\');
            foreach ($orders as $o) {
                $items = collect($o->items)->map(fn ($i) => ($i['name'] ?? $i['title'] ?? '-') . ' x' . ($i['qty'] ?? 0))->implode('; ');
                fputcsv($out, [
                    $o->kode,
                    $o->pickup_code,
                    $o->user->name ?? '-',
                    $o->user->username ?? '-',
                    $items,
                    $o->total,
                    $o->metode ?? '-',
                    $o->status,
                    optional($o->created_at)->format('Y-m-d H:i'),
                    optional($o->paid_at)->format('Y-m-d H:i'),
                    optional($o->picked_up_at)->format('Y-m-d H:i'),
                ], ',', '"', '\\');
            }
            fclose($out);
        }, 'koperasi-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv']);
    }

    /** Batalkan pesanan yang belum dibayar (stok/belanja belum tersentuh). */
    public function cancelOrder(int $orderId)
    {
        $order = KoperasiOrder::findOrFail($orderId);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Hanya pesanan yang belum dibayar yang bisa dibatalkan.');
        }

        $order->update(['status' => 'batal']);

        return back()->with('success', "Pesanan {$order->kode} dibatalkan.");
    }

    public function adminShow(int $orderId)
    {
        $order = KoperasiOrder::with('user:id,name,username')->findOrFail($orderId);

        return view('koperasi.show', ['order' => $order]);
    }

    /** Tandai pesanan sudah diambil siswa di ruang TU. */
    public function markPickedUp(int $orderId)
    {
        $order = KoperasiOrder::findOrFail($orderId);

        if ($order->status !== 'dibayar') {
            return back()->with('error', 'Hanya pesanan yang sudah dibayar yang bisa ditandai diambil.');
        }

        $order->update(['status' => 'diambil', 'picked_up_at' => now()]);

        return back()->with('success', "Pesanan {$order->kode} ditandai sudah diambil.");
    }

    public function products()
    {
        $products = KoperasiProduct::orderBy('category')->orderBy('name')->paginate(30);

        return view('koperasi.products', compact('products'));
    }

    public function productCreate()
    {
        return view('koperasi.product-form', ['product' => new KoperasiProduct(['is_active' => true])]);
    }

    public function productStore(Request $request)
    {
        $data = $this->validateProduct($request);

        KoperasiProduct::create([
            'name' => $data['name'],
            'category' => $data['category'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'description' => $data['description'] ?? null,
            'emoji' => $data['emoji'] ?? null,
            'image' => $this->storeImage($request),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('koperasi.products')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function productEdit(KoperasiProduct $product)
    {
        return view('koperasi.product-form', compact('product'));
    }

    public function productUpdate(Request $request, KoperasiProduct $product)
    {
        $data = $this->validateProduct($request);

        if ($image = $this->storeImage($request)) {
            $this->deleteImage($product->image);
            $product->image = $image;
        }

        $product->fill([
            'name' => $data['name'],
            'category' => $data['category'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'description' => $data['description'] ?? null,
            'emoji' => $data['emoji'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ])->save();

        return redirect()->route('koperasi.products')->with('success', 'Produk berhasil diperbarui.');
    }

    public function productDestroy(KoperasiProduct $product)
    {
        $this->deleteImage($product->image);
        $product->delete();

        return redirect()->route('koperasi.products')->with('success', 'Produk berhasil dihapus.');
    }

    public function laporan()
    {
        $paid = KoperasiOrder::whereIn('status', KoperasiOrder::PAID_STATUSES)->get();

        $omzet = (int) $paid->sum('total');
        $count = $paid->count();

        $perProduct = [];
        foreach ($paid as $order) {
            foreach ($order->items as $line) {
                $id = $line['product_id'] ?? $line['id'] ?? $line['name'];
                $perProduct[$id] ??= ['name' => $line['name'] ?? $line['title'] ?? 'Produk', 'qty' => 0, 'omzet' => 0];
                $perProduct[$id]['qty'] += $line['qty'] ?? 0;
                $perProduct[$id]['omzet'] += ($line['price'] ?? 0) * ($line['qty'] ?? 0);
            }
        }
        usort($perProduct, fn ($a, $b) => $b['qty'] <=> $a['qty']);

        $perDay = $paid
            ->filter(fn ($o) => $o->paid_at)
            ->groupBy(fn ($o) => $o->paid_at->format('Y-m-d'))
            ->map(fn ($g) => (int) $g->sum('total'))
            ->sortKeysDesc();

        return view('koperasi.laporan', compact('omzet', 'count', 'perProduct', 'perDay'));
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string|max:2000',
            'emoji' => 'nullable|string|max:8',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);
    }

    private function storeImage(Request $request): ?string
    {
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            return $request->file('image')->store('produk', 'public');
        }

        return null;
    }

    private function deleteImage(?string $image): void
    {
        if ($image && !str_starts_with($image, '/') && !str_starts_with($image, 'http')) {
            Storage::disk('public')->delete($image);
        }
    }
}
