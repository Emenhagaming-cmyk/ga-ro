<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Services\RegistrationInsightService;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
public function dashboard(RegistrationInsightService $insightService)
    {
        $stats = [
            'total' => Pendaftaran::count(),
            'baru' => Pendaftaran::where('status', 'baru')->count(),
            'diproses' => Pendaftaran::where('status', 'diproses')->count(),
            'diterima' => Pendaftaran::where('status', 'diterima')->count(),
            'ditolak' => Pendaftaran::where('status', 'ditolak')->count(),
            'jurusan' => [
                'RPL' => Pendaftaran::where('jurusan_pilihan', 'RPL')->count(),
                'TKJ' => Pendaftaran::where('jurusan_pilihan', 'TKJ')->count(),
                'AKL' => Pendaftaran::where('jurusan_pilihan', 'AKL')->count(),
            ],
        ];

        $terbaru = Pendaftaran::latest()->take(5)->get();
        $insight = $insightService->generateSummary($stats);
        $chart = $this->chartData();

        return view('pendaftaran.dashboard', compact('stats', 'insight', 'terbaru', 'chart'));
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

        $stats = [
            'baru' => Pendaftaran::where('status', 'baru')->count(),
            'diproses' => Pendaftaran::where('status', 'diproses')->count(),
            'diterima' => Pendaftaran::where('status', 'diterima')->count(),
            'ditolak' => Pendaftaran::where('status', 'ditolak')->count(),
            'jurusan' => [
                'RPL' => Pendaftaran::where('jurusan_pilihan', 'RPL')->count(),
                'TKJ' => Pendaftaran::where('jurusan_pilihan', 'TKJ')->count(),
                'AKL' => Pendaftaran::where('jurusan_pilihan', 'AKL')->count(),
            ],
        ];

        $mingguIni = Pendaftaran::whereBetween('created_at', [$start, $end])->latest()->get();

        return view('pendaftaran.laporan', compact('stats', 'mingguIni', 'start', 'end'));
    }

    private function chartData(): array
    {
        $daily = collect(range(29, 0))->map(function ($i) {
            $date = now()->subDays($i)->format('Y-m-d');
            return [
                'label' => now()->subDays($i)->format('d/m'),
                'count' => Pendaftaran::whereDate('created_at', $date)->count(),
            ];
        });

        return [
            'daily' => $daily->pluck('count')->values(),
            'daily_labels' => $daily->pluck('label')->values(),
            'jurusan' => [
                'RPL' => Pendaftaran::where('jurusan_pilihan', 'RPL')->count(),
                'TKJ' => Pendaftaran::where('jurusan_pilihan', 'TKJ')->count(),
                'AKL' => Pendaftaran::where('jurusan_pilihan', 'AKL')->count(),
            ],
            'status' => [
                'Baru' => Pendaftaran::where('status', 'baru')->count(),
                'Diproses' => Pendaftaran::where('status', 'diproses')->count(),
                'Diterima' => Pendaftaran::where('status', 'diterima')->count(),
                'Ditolak' => Pendaftaran::where('status', 'ditolak')->count(),
            ],
        ];
    }

    public function myDashboard(Request $request)
    {
        // Admin alihkan ke dashboard admin
        if ($request->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Dapatkan data pendaftaran siswa (bisa null)
        $pendaftaran = Pendaftaran::where('user_id', $request->user()->id)->first();

        // Selalu render view, view akan menampilkan empty‑state bila null
        return view('pendaftaran.dashboard-siswa', compact('pendaftaran'));
    }

    public function create()
    {
        $draft = session('pending_pendaftaran', []);

        return view('pendaftaran.create', compact('draft'));
    }

    public function store(Request $request)
    {
        if (!$request->user()) {
            $draft = $this->sanitizeDraft($request);

            $request->session()->put('pending_pendaftaran', $draft);

            if (!empty($draft)) {
                $key = (string) \Illuminate\Support\Str::uuid();

                \Illuminate\Support\Facades\DB::table('pendaftaran_drafts')->insert([
                    'key' => $key,
                    'payload' => json_encode($draft),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return redirect()->route('login')
                    ->withCookie(cookie('pending_draft', $key, 10080))
                    ->withErrors(['email' => 'Silakan masuk/daftar terlebih dahulu untuk mengirim pendaftaran. Data Anda sudah disimpan sementara dan akan terisi kembali setelah login.']);
            }

            return redirect()->route('login')
                ->withErrors(['email' => 'Silakan masuk/daftar terlebih dahulu untuk mengirim pendaftaran.']);
        }

        $request->session()->put('pending_pendaftaran', $this->sanitizeDraft($request));

        if (Pendaftaran::where('user_id', $request->user()->id)->exists()) {
            $request->session()->forget('pending_pendaftaran');

            return redirect()->route('dashboard.siswa')
                ->with('error', 'Anda sudah mengirim pendaftaran. Pantau status Anda di dashboard.');
        }

        $data = $request->validate($this->rules());
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'baru';
        $data = $this->handleFileUploads($request, $data);

        $request->session()->forget('pending_pendaftaran');

        Pendaftaran::create($data);

        return redirect()->route('dashboard.siswa')->with('success', 'Pendaftaran berhasil dikirim. Pantau status Anda di dashboard.');
    }

    private function sanitizeDraft(Request $request): array
    {
        return collect($request->except(['_token', '_method']))
            ->reject(fn ($v) => $v instanceof \Illuminate\Http\UploadedFile)
            ->all();
    }

    private function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string|max:255',
            'nama_panggilan' => 'nullable|string|max:100',
            'nisn' => 'required|string|max:20',
            'nik' => 'nullable|string|max:20',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'umur' => 'nullable|integer|min:4|max:25',
            'agama' => 'nullable|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'kewarnegaraan' => 'nullable|string|max:50',
            'kategori_pendaftar' => 'nullable|string|max:100',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string',
            'rt_rw' => 'nullable|string|max:30',
            'kode_pos' => 'nullable|string|max:10',
            'asal_sekolah' => 'required|string|max:255',
            'gelombang' => 'nullable|string|max:50',
            'tahun_lulus' => 'nullable|integer|min:2015|max:2035',
            'rata_rata_nilai' => 'nullable|string|max:20',
            'no_hp' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'jurusan_pilihan' => 'required|in:RPL,TKJ,AKL',
            'jumlah_saudara' => 'nullable|string|max:10',
            'anak_ke' => 'nullable|string|max:10',
            'status_keluarga' => 'nullable|string|max:50',
            'nama_ayah' => 'nullable|string|max:255',
            'pendidikan_ayah' => 'nullable|string|max:50',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'penghasilan_ayah' => 'nullable|string|max:50',
            'alamat_ayah' => 'nullable|string',
            'hp_ayah' => 'nullable|string|max:20',
            'nama_ibu' => 'nullable|string|max:255',
            'pendidikan_ibu' => 'nullable|string|max:50',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'penghasilan_ibu' => 'nullable|string|max:50',
            'alamat_ibu' => 'nullable|string',
            'hp_ibu' => 'nullable|string|max:20',
            'nama_wali' => 'nullable|string|max:255',
            'hubungan_wali' => 'nullable|string|max:100',
            'email_orang_tua' => 'nullable|email|max:255',
            'jenis_pembayaran' => 'nullable|in:Transfer,Tunai',
            'berkas_tambahan' => 'nullable|string',
            'foto_3x4' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'kk_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'ijazah_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'sktm_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'nama_orang_tua' => 'nullable|string|max:255',
            'no_hp_orang_tua' => 'nullable|string|max:20',
            'status' => 'sometimes|in:baru,diproses,diterima,ditolak'
        ];
    }

    public function show(Pendaftaran $pendaftaran)
    {
        return view('pendaftaran.show', compact('pendaftaran'));
    }

    public function update(Request $request, Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id !== $request->user()->id) {
            abort(403);
        }

        $deadline = $pendaftaran->created_at->copy()->addDays(3);

        if ($pendaftaran->status !== 'baru' || !now()->lt($deadline)) {
            return back()->with('error', 'Batas waktu edit formulir telah berakhir. Hubungi admin jika ingin mengubah data.');
        }

        $data = $request->validate($this->rules());
        $data = $this->handleFileUploads($request, $data);
        unset($data['status']);

        $pendaftaran->update($data);

        return redirect()->route('dashboard.siswa')->with('success', 'Data pendaftaran berhasil diperbarui.');
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

        if ($validated['status'] === 'diterima') {
            $pendaftaran->user->update(['role' => 'siswa']);
        } elseif (in_array($validated['status'], ['ditolak', 'baru'])) {
            $pendaftaran->user->update(['role' => 'pendaftar']);
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

        $stats = [
            'total' => Pendaftaran::count(),
            'baru' => Pendaftaran::where('status', 'baru')->count(),
            'diproses' => Pendaftaran::where('status', 'diproses')->count(),
            'diterima' => Pendaftaran::where('status', 'diterima')->count(),
            'ditolak' => Pendaftaran::where('status', 'ditolak')->count(),
            'jurusan' => [
                'RPL' => Pendaftaran::where('jurusan_pilihan', 'RPL')->count(),
                'TKJ' => Pendaftaran::where('jurusan_pilihan', 'TKJ')->count(),
                'AKL' => Pendaftaran::where('jurusan_pilihan', 'AKL')->count(),
            ],
        ];

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

        $pendaftarans = Pendaftaran::orderBy('id')->get();

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, array_merge(['No'], array_values($fields)), ';');

        foreach ($pendaftarans as $i => $p) {
            $row = [$i + 1];
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

    private function handleFileUploads(Request $request, array $data): array
    {
        foreach (['foto_3x4', 'kk_file', 'ijazah_file', 'sktm_file'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('pendaftaran', 'public');
            }
        }

        return array_filter($data, fn ($value) => $value !== null);
    }

    /**
     * Download surat keterangan diterima PDF (hanya untuk pendaftaran berstatus diterima).
     */
    public function downloadBukti()
    {
        $user = request()->user();
        $pendaftaran = Pendaftaran::where('user_id', $user->id)->first();

        if (!$pendaftaran || $pendaftaran->status !== 'diterima') {
            abort(403, 'Bukti hanya tersedia setelah pendaftaran diterima.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pendaftarans.bukti', [
            'pendaftaran' => $pendaftaran,
        ]);

        return $pdf->download('bukti_diterima_' . $pendaftaran->id . '.pdf');
    }

}
