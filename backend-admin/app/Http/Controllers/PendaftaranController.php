<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\User;
use App\Services\RegistrationInsightService;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    // ponytail: in-memory cache — hit TiDB 1x per 10 detik, bukan 1x per polling browser
    private static ?array $statsCache = null;
    private static int $statsCacheTs = 0;

    private static function freshStats(): array
    {
        $now = time();
        if (self::$statsCache && $now - self::$statsCacheTs < 10) {
            return self::$statsCache;
        }

        // ponytail: 1 query GROUP BY, bukan 8 COUNT terpisah.
        // GROUP BY status saja — kalau jurusan ikut di-group, key `status` duplikat
        // dan pluck() saling menimpa (angka per status cuma 1 jurusan terakhir).
        $rows = Pendaftaran::selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->get()
            ->pluck('cnt', 'status');

        $jRows = Pendaftaran::selectRaw('jurusan_pilihan, COUNT(*) as cnt')
            ->groupBy('jurusan_pilihan')
            ->get()
            ->pluck('cnt', 'jurusan_pilihan');

        self::$statsCache = [
            'total' => $rows->sum(),
            'baru' => $rows->get('baru', 0),
            'diproses' => $rows->get('diproses', 0),
            'diterima' => $rows->get('diterima', 0),
            'ditolak' => $rows->get('ditolak', 0),
            'jurusan' => [
                'RPL' => $jRows->get('RPL', 0),
                'TKJ' => $jRows->get('TKJ', 0),
                'AKL' => $jRows->get('AKL', 0),
            ],
        ];
        self::$statsCacheTs = $now;
        return self::$statsCache;
    }

public function dashboard(RegistrationInsightService $insightService)
    {
        $stats = self::freshStats();

        $terbaru = Pendaftaran::latest()->take(5)->get();
        $akunSiswa = User::where('role', '!=', 'admin')->latest()->take(5)->get();
        $insight = $insightService->generateSummary($stats);

        return response()->view('pendaftaran.dashboard', compact('stats', 'insight', 'terbaru', 'akunSiswa'));
    }

    public function index(Request $request)
    {
        $duplicateNisn = Pendaftaran::whereNotNull('nisn')->where('nisn', '!=', '')
            ->selectRaw('nisn')->groupBy('nisn')->havingRaw('count(*) > 1')->pluck('nisn');
        $duplicateNik = Pendaftaran::whereNotNull('nik')->where('nik', '!=', '')
            ->selectRaw('nik')->groupBy('nik')->havingRaw('count(*) > 1')->pluck('nik');

        $pendaftarans = Pendaftaran::query()
            ->when($request->filled('q'), fn ($q) => $q->where(function ($w) use ($request) {
                $w->where('nama_lengkap', 'like', '%' . $request->q . '%')
                    ->orWhere('nisn', 'like', '%' . $request->q . '%')
                    ->orWhere('nik', 'like', '%' . $request->q . '%')
                    ->orWhere('asal_sekolah', 'like', '%' . $request->q . '%')
                    ->orWhere('no_hp', 'like', '%' . $request->q . '%');
            }))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('jurusan'), fn ($q) => $q->where('jurusan_pilihan', $request->jurusan))
            ->when($request->boolean('duplikat'), fn ($q) => $q->where(function ($w) use ($duplicateNisn, $duplicateNik) {
                $w->whereIn('nisn', $duplicateNisn)->orWhereIn('nik', $duplicateNik);
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pendaftaran.index', compact('pendaftarans', 'duplicateNisn', 'duplicateNik'));
    }

    public function laporan()
    {
        $start = now()->startOfWeek();
        $end = now()->endOfWeek();

        $stats = self::freshStats();

        $mingguIni = Pendaftaran::whereBetween('created_at', [$start, $end])->latest()->limit(100)->get();

        return view('pendaftaran.laporan', compact('stats', 'mingguIni', 'start', 'end'));
    }

    public function show(Pendaftaran $pendaftaran)
    {
        return view('pendaftaran.show', compact('pendaftaran'));
    }

    public function updateStatus(Request $request, Pendaftaran $pendaftaran)
    {
        $validated = $request->validate([
            'status' => 'required|in:baru,diproses,diterima,ditolak'
        ]);

        $pendaftaran->update([
            'status' => $validated['status'],
            'status_updated_at' => now()
        ]);

        // ponytail: user_id nullable (pendaftar lama bisa tanpa akun) — jangan deref buta,
        // kalau null panggil update() = 500.
        if ($user = $pendaftaran->user) {
            if ($validated['status'] === 'diterima') {
                $user->update(['role' => 'siswa']);
            } elseif (in_array($validated['status'], ['ditolak', 'baru'])) {
                $user->update(['role' => 'pendaftar']);
            }
        }

        return back()->with('success', 'Status berhasil diperbarui.');
    }

    public function destroy(Pendaftaran $pendaftaran)
    {
        $pendaftaran->delete();

        return redirect()->route('pendaftaran.index')
            ->with('success', 'Data pendaftaran berhasil dihapus');
    }

    public function snapshot(Request $request)
    {
        $request->user() || abort(401);
        $request->user()->role === 'admin' || abort(403);

        $latest = Pendaftaran::orderByDesc('id')->first();

        $stats = self::freshStats();

        $rows = Pendaftaran::latest()->limit(15)->get(['id', 'nama_lengkap', 'no_hp', 'asal_sekolah', 'jurusan_pilihan', 'status', 'created_at']);

        return response()->json([
            'latest_id' => $latest?->id,
            'latest_created_at' => $latest?->created_at?->toIso8601String(),
            'stats' => $stats,
            'rows' => $rows,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    public function exportCsv()
    {
        $fields = [
            'nama_lengkap' => 'Nama Lengkap',
            'nisn' => 'NISN',
            'nik' => 'NIK',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'jenis_kelamin' => 'Jenis Kelamin',
            'alamat' => 'Alamat',
            'rt_rw' => 'RT/RW',
            'kode_pos' => 'Kode Pos',
            'asal_sekolah' => 'Asal Sekolah',
            'gelombang' => 'Gelombang',
            'jurusan_pilihan' => 'Jurusan',
            'no_hp' => 'No HP',
            'email' => 'Email',
            'nama_ayah' => 'Nama Ayah',
            'nama_ibu' => 'Nama Ibu',
            'status' => 'Status',
            'created_at' => 'Tanggal Daftar',
        ];

        // ponytail: cursor() streaming — model dibangun satu per satu, bukan ALL rows di RAM.
        // select() hanya kolom yang dipakai (18 kolom, bukan ~45).
        $pendaftarans = Pendaftaran::select(array_keys($fields))->orderBy('id')->cursor();

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, array_merge(['No'], array_values($fields)), ';');

        $i = 0;
        foreach ($pendaftarans as $p) {
            $i++;
            $row = [$i];
            foreach (array_keys($fields) as $field) {
                $row[] = $field === 'created_at'
                    ? optional($p->created_at)->format('Y-m-d H:i')
                    : $p->{$field};
            }
            fputcsv($handle, $row, ';');
        }

        rewind($handle);
        $csv = "\xEF\xBB\xBF" . stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="pendaftar-' . now()->format('Ymd-His') . '.csv"',
        ]);
    }

    public function resetUserPassword(User $user)
    {
        abort_if($user->role === 'admin', 403);

        $plain = strtoupper(substr(uniqid(), -8));
        $user->update(['password' => bcrypt($plain)]);

        return redirect()->route('admin.dashboard')->with('reset_password', [
            'name' => $user->name,
            'password' => $plain,
        ]);
    }

}
