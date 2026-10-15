<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function myDashboard(Request $request)
    {
        // Admin alihkan ke dashboard admin
        if ($request->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Dashboard siswa pindah ke Vue (frontend) — route ini jadi jembatan untuk link lama
        return redirect(frontendAuthUrl('/dashboard-siswa'));
    }

    // Data dashboard + profil siswa (JSON untuk view Vue di frontend)
    public function myDashboardData(Request $request)
    {
        if ($request->user()->role === 'admin') {
            abort(403);
        }

        $pendaftaran = Pendaftaran::where('user_id', $request->user()->id)->first();

        // Aturan bisnis yang sama dengan view lama: edit hanya bila status "baru" dan <3 hari
        $canEdit = (bool) $pendaftaran
            && $pendaftaran->status === 'baru'
            && now()->lt($pendaftaran->created_at->copy()->addDays(3));

        return response()->json([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'username' => $request->user()->username,
                'email' => $request->user()->email,
                'avatar' => $request->user()->avatar,
            ],
            'pendaftaran' => $pendaftaran,
            'can_edit' => $canEdit,
            'deadline' => $canEdit ? $pendaftaran->created_at->copy()->addDays(3)->toIso8601String() : null,
        ]);
    }

    public function myDashboardSnapshot(Request $request)
    {
        if ($request->user()->role === 'admin') {
            abort(403);
        }

        $pendaftaran = Pendaftaran::where('user_id', $request->user()->id)->first();

        return response()->json([
            'status' => $pendaftaran?->status ?? null,
            'status_updated_at' => $pendaftaran?->status_updated_at?->toIso8601String(),
            'nama_lengkap' => $pendaftaran?->nama_lengkap ?? null,
            'jurusan_pilihan' => $pendaftaran?->jurusan_pilihan ?? null,
        ]);
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
                    ->withErrors(['email' => 'Silakan masuk/daftar terlebih dahulu ya sob untuk mengirim pendaftaran. Tenang Data kamu sudah disimpan sementara dan akan terisi kembali setelah login.']);
            }

            return redirect()->route('login')
                ->withErrors(['email' => 'Silakan masuk/daftar ya terlebih dahulu ya sob untuk mengirim pendaftaran :D.']);
        }

        if (Pendaftaran::where('user_id', $request->user()->id)->exists()) {
            $request->session()->forget('pending_pendaftaran');

            return redirect()->route('dashboard.siswa')
                ->with('error', 'Kamu udah mengirim pendaftaran. Pantau status kamu di dashboard ya.');
        }

        $request->session()->put('pending_pendaftaran', $this->sanitizeDraft($request));

        $data = $request->validate($this->rules());
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'baru';
        $data = $this->handleFileUploads($request, $data);

        // Race condition guard: cek + create atomic (unique 1 pendaftaran/user).
        $created = \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $exists = Pendaftaran::where('user_id', $data['user_id'])->lockForUpdate()->exists();

            if ($exists) {
                return null;
            }

            return Pendaftaran::create($data);
        });

        if ($created === null) {
            $request->session()->forget('pending_pendaftaran');

            return redirect()->route('dashboard.siswa')
                ->with('error', 'Kamu udah mengirim pendaftaran. Pantau status kamu di dashboard ya.');
        }

        return redirect()->route('dashboard.siswa')->with('success', 'Yey! Pendaftaran berhasil dikirim. Pantau status kamu di dashboard ya.');
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
            'foto_3x4' => 'nullable|image|mimes:jpg,jpeg,png|mimetypes:image/jpeg,image/png|max:2048',
            'kk_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|mimetypes:image/jpeg,image/png,application/pdf|max:2048',
            'ijazah_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|mimetypes:image/jpeg,image/png,application/pdf|max:2048',
            'sktm_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|mimetypes:image/jpeg,image/png,application/pdf|max:2048',
            'nama_orang_tua' => 'nullable|string|max:255',
            'no_hp_orang_tua' => 'nullable|string|max:20',
            'status' => 'sometimes|in:baru,diproses,diterima,ditolak'
        ];
    }

    public function update(Request $request, Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id !== $request->user()->id) {
            abort(403);
        }

        $deadline = $pendaftaran->created_at->copy()->addDays(3);

        if ($pendaftaran->status !== 'baru' || !now()->lt($deadline)) {
            $message = 'Batas waktu edit formulir kamu udah abis nih. Hubungi admin jika ingin mengubah data ya.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $data = $request->validate($this->rules());

        // Preserve existing file paths for fields not uploaded
        $fileFields = ['foto_3x4', 'kk_file', 'ijazah_file', 'sktm_file'];
        foreach ($fileFields as $field) {
            if (!$request->hasFile($field)) {
                $data[$field] = $pendaftaran->$field;
            }
        }

        $data = $this->handleFileUploads($request, $data);
        unset($data['status']);

        $pendaftaran->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Data pendaftaran berhasil diperbarui.',
                'pendaftaran' => $pendaftaran->fresh(),
            ]);
        }

        return redirect()->route('dashboard.siswa')->with('success', 'Data pendaftaran berhasil diperbarui.');
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
