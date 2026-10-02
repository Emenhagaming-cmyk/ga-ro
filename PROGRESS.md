# PROGRESS.md — Log Pekerjaan SPMB SMK Bahrul Ulum

Update file ini setiap akhir sesi agar sesi berikutnya langsung lanjut tanpa perlu menjelaskan ulang.

---

---

## STATUS TERAKHIR (2026-10-02) — Fix: Tombol Mata Show Password Hilang di Production (Deploy `spmb-backend`)

**Laporan user:** "ini gw kan ada fitur mata buat show password di halaman login atau daftar, tapi di versi yang dideploy ga muncul jir".

**Diagnosis (bukan bug CSS — deployment yang tertinggal):**
- Fitur **ada dan lengkap** di lokal: `backend/resources/views/auth/login.blade.php` + `register.blade.php` (tombol `.pw-toggle` dengan dua SVG `.eye-open`/`.eye-off`), JS `togglePw()` + styling di `backend/resources/views/layouts/auth.blade.php:435`.
- HTML production **sebelum deploy** di-`grep`: `/login` dan `/register` → `pw-toggle=False`, `toggleKw/togglePw=False`, `eye-off=False`, padahal `type="password"` dan `.input-wrap` ada. Artinya Vercel masih menyajikan Blade versi lama.
- `git status backend` bersih ⇒ perubahannya **sudah di-commit**, cuma project Vercel `spmb-backend` belum pernah di-deploy ulang sejak fiturnya dibuat. Sesi ini hanya memicu deploy — **tidak ada perubahan kode**.

**Prasyarat sebelum deploy (sesuai alur AGENTS.md):**
- `php artisan view:cache` → `INFO Blade templates cached successfully.`
- `php artisan test` → **39 passed (144 assertions)**, 22.92s.
- Working tree `backend` bersih, jadi tidak ada perubahan lokal yang ikut ter-deploy tanpa sengaja.
- **Tidak menjalankan `migrate`** — perubahan ini murni view, dan production = TiDB (migrasi ke production hanya atas perintah eksplisit user).

**Deploy:**
- `vercel deploy --prod --yes` di `backend/` → `https://spmb-backend-bxsp4k54q-zakkys-projects-99c4bf23.vercel.app`, `Build Completed in /vercel/output [5s]`, `✓ Ready in 29s`.
- Alias production otomatis: → **`https://pendaftaranspmb.vercel.app`** (`spmb-backend` punya alias production yang menempel ke tiap deploy baru, sama seperti project `lomba`).

**Verifikasi production (Playwright, 2 route × 2 viewport = 4 kasus):**
- `/login` & `/register` HTTP **200**, markup `pw-toggle` / `togglePw` / `eye-off` **sekarang ada**.
- Tombol benar-benar terlihat: `26x26`, `visibility: visible`, `opacity > 0.05` (desktop & mobile).
- Fungsional: `input.type` `password` → **text** setelah klik → **password** lagi setelah klik kedua; `aria-pressed` `false`→`true`, `aria-label` "Tampilkan password"→"Sembunyikan password"; ikon `.eye-open` disembunyikan & `.eye-off` ditampilkan.
- **0 console error / 0 pageerror** di keempat kasus.

**Temuan samping (sudah dikerjakan di sesi ini, atas persetujuan user "oke"):** login **panel admin** (`https://paneladminsmkbu.vercel.app/login`) juga **belum punya** `.pw-toggle` — `backend-admin/resources/views/auth/login.blade.php` sudah punya fiturnya secara lokal, tapi project `spmb-admin` juga belum di-deploy ulang. Detail deploy + verifikasinya ada di entri di bawah.

---

## STATUS TERAKHIR (2026-10-02) — Recovery: `git pull` Belum Sengaja → Hero Rusak (build + live)

**Laporan user (panik):** "gw ga sengaja error ini di heronya plisss, balikin kayak semula bisa ga, ga sengaja git pull guwee".

**Rantai kejadian (berdasar `git reflog` + `vercel ls`, bukan asumsi):**
1. User ada di branch `syn`, commit `12e3bd4` "update berita preview and tabungan banner".
2. `checkout` ke `main` (masih di `9d01e43`), lalu `git pull origin main` → **Fast-forward** ke `db2c1ba` ("fix Laravel auth helper conflict", cuma menyentuh `backend/app/helpers.php`).
3. Merge branch `syn` ke `main` dijalankan dan **BERHENTI di tengah** ⇒ repo `mid-merge` (`MERGE_HEAD = 12e3bd4`). Conflict: `AGENTS.md`, `src/components/layout/Footer.vue`, dan **`src/components/sections/Hero.vue`**.
4. Hero.vue sempat "diresolve" (di-`git add`) dengan hasil yang **rusak**, tapi **tanpa conflict markers** ⇒ lolos dari deteksi marker dan baru ketahuan saat compile.

**Dua gejala yang berbeda (penting dibedakan — user mengira satu masalah):**
- **Lokal:** Vite balas **HTTP 500** untuk `src/views/HomeView.vue` ⇒ `TypeError: Failed to fetch dynamically imported module` ⇒ **seluruh halaman kosong**, termasuk hero. Penyebabnya **dua lapis**: (a) conflict markers di `Footer.vue`, (b) `Hero.vue` yang tag-nya tidak seimbang.
- **Live `smkbu-sby.vercel.app`:** **bukan** build sesi ini. `vercel ls` membuktikan ada **2 deploy production lain** (1 jam lalu & 32 menit lalu, username `zakkyilhamf-7419` = sesi CLI yang sama) yang **menimpa alias** hasil deploy sesi ini (`lomba-378j7oyq6`, 14 jam lalu). Hash aset di `index.html` live (`index-km3uEFSD.js`) **tidak sama** dengan build gw (`index-P8kwws1B.js`), dan DOM live **tidak punya** `.hero-copy`/`.hero-visual` sama sekali ⇒ live masih hero lama.
  - Kenapa bisa lama: perubahan hero sesi-sesi sebelumnya **belum pernah di-commit**, jadi `origin/main` (`db2c1ba`) masih memuat Hero.vue versi lama. Deployment yang menimpa itu dibangun dari kondisi itu.

**Yang Dampar (sebelum sentuh file apa pun):** `src/`, `public/hero-siswa.webp`, `AGENTS.md`, `PERF.md`, `PROGRESS.md` → `C:\Users\LENOVO\AppData\Local\Temp\opencode\ga-ro-backup-20261002` (63 file, 847 KB). Penting karena conflict resolution bisa menimpa file tanpa jejak.

**Resolution (pilihan user: "Pertahankan hero gambar (versi gw)" + "Beresin conflict, gabungin"):**
- `git checkout --ours -- AGENTS.md src/components/layout/Footer.vue` → **sisi HEAD/main** dipakai untuk dua file ini (versi `origin/main` jadi sumber kebenaran, tidak ada commit yang hilang). Konsekuensi yang perlu diketahui: tweak kosmetik `Footer.vue` (background putih, border dihapus) **tidak ikut** — kembali ke versi `origin/main`.
- `git commit` merge `99b955a` "Merge branch 'syn': navbar sub-page seragam + hero ilustrasi siswa". **Belum di-push.**
- `Hero.vue`: `script` (`heroVisual`) dan **seluruh CSS** (`.hero-copy`, `.hero-visual`, `heroIn`, `bg-word`, 3 breakpoint) utuh — yang rusak **hanya template**: blok `<div class="hero-visual"><img></div>` hilang dan 2 tag penutup hilang (`</div>` untuk `.hero-copy` dan untuk `.container`), indentasi `.buttons` juga meleset. Ditulis ulang pakai `edit`, bukan `git checkout` — supaya versi two-column (gambar + tombol ketengah) dipertahankan sesuai pilihan user, bukan di-rollback ke versi lama.

**Verifikasi (setelah fix):**
- `npm run build` → **sukses** (`✓ built in 22.15s`), `HomeView-DMytzTws.js` 26.38 kB (gz 8.64).
- Playwright lokal @1440: `.hero-copy` x=130 w=560, `.hero-visual` x=850 w=460, **tidak overlap**; `img.complete = true`, `naturalWidth×height = 740x740`, `src = /hero-siswa.webp`.
- Tombol Login: x=366 w=88 → center **410** = center kolom copy (130+280) ⇒ masih terpusat, bukan ke tengah layar.
- `scrollWidth == clientWidth` (1440), **0 console error / 0 pageerror**.

**Pelajaran (penting untuk sesi depan):**
- Merge/pull yang terhenti di tengah **tidak otomatis merusak yang sudah ter-deploy** — Vercel membaca file kerja, bukan git. Tapi **yang ter-deploy dari working tree bisa lebih tua/beda** kalau ada deploy lain yang menimpa alias. Selalu cek `vercel ls` + hash aset `index.html` live sebelum menyimpulkan "yg live beda".
- `git merge --abort`/marker scan **tidak cukup**: file yang sudah di-`git add` tapi salah resolve **tidak** punya marker. Satu-satunya deteksi yang jujur = **build**. `npm run build` harus jadi gate wajib setelah pull/merge, bukan hanya setelah edit biasa.
- Konflik `Hero.vue` sudah ter-*stage* sebelum sesi ini ⇒ `git diff HEAD` sempat menampilkan file "utuh" padahal templatenya tidak valid. Always verify with a real build.

---

## STATUS TERAKHIR (2026-10-02) — Deploy `spmb-admin`: Tombol Mata Login Admin Live

**Permintaan user:** "oke" (menyetujui deploy project admin panel yang ditemukan di sesi sebelumnya).

**Konteks:** sama seperti `spmb-backend`, masalahnya **deployment yang tertinggal**, bukan kode. `backend-admin/resources/views/auth/login.blade.php` sudah punya `.pw-toggle` + `togglePw()` (JS-nya di `layouts/app.blade.php`), tapi HTML production `/login` sebelum deploy tidak mengandung `pw-toggle`/`togglePw`. `git status backend-admin` **bersih** ⇒ tidak ada perubahan lokal yang ikut ter-deploy tanpa sengaja. **Tidak ada perubahan kode di sesi ini.**

**Prasyarat sebelum deploy:**
- `php artisan view:cache` → `INFO Blade templates cached successfully.` (satu-satunya cek Blade yang valid; error LSP di helper/controller = false positive).
- **Tidak menjalankan `migrate` sama sekali** — penting karena di `backend-admin` **dilarang** `migrate:fresh`/`migrate:rollback` (tabel `migrations` production sudah mencatat 5 migrasi yang tidak ada di folder ini). Tidak ada kode/DB yang berubah, jadi migrasi memang tak perlu.
- `.vercel/project.json` → `{"projectName":"spmb-admin", ...}` (sudah ter-link ⇒ tidak perlu `vercel link`, jadi tidak ada `.env.local` yang perlu dihapus setelahnya).
- Test suite `backend-admin` **tidak dijalankan**: sudah MERAH sejak awal (2 gagal) dan unrelated dengan sesi ini.

**Deploy:**
- `vercel deploy --prod --yes` di `backend-admin/` → `https://spmb-admin-dz8lxix8i-zakkys-projects-99c4bf23.vercel.app`, `Build Completed in /vercel/output [5s]`, `✓ Ready in 29s`.
- Alias production otomatis: → **`https://paneladminsmkbu.vercel.app`**.

**Verifikasi production (Playwright):**
- `/login` HTTP **200**, title "Masuk Admin - SPMB SMK Bahrul Ulum", `.pw-toggle` **terlihat** 25×25 di 1440px & 390px.
- Fungsional: `input.type` `password` → **text** → **password**; `aria-pressed` `false`→`true`, `aria-label` "Tampilkan password"→"Sembunyikan password", `.eye-off` muncul.
- `/` tanpa session → **200** dan redirect ke `/login` (bukan 500).
- **Smoke test end-to-end pakai kredensial dev:** login `admin` / `admin123` — attention: field-nya bernama **`username`**, bukan `email` (selector `input[name="email"]` timeout 30 dtk) → redirect ke `/admin` "Dashboard Admin", sidebar lengkap (Dashboard, Data Pendaftar, Rekap SPP, Tabungan, Koperasi, Berita, Logout).
- `/pendaftaran`, `/tabungan`, `/koperasi` → semuanya **200** ⇒ koneksi TiDB + `CACHE_STORE=database` tetap sehat setelah deploy.
- **0 console error / 0 pageerror** di semua pemeriksaan.

**Pelajaran untuk sesi depan:** dua laporan "fitur tidak muncul di production" berturut-turut ternyata murni **project Vercel yang tertinggal**, dan gejalanya khas — HTML production **tidak mengandung** markup-nya sama sekali (bukan elemen ada tapi tak terlihat / CSS rusak). Diagnosis cepat: `Invoke-WebRequest` ke route production lalu `-match` penanda unik fitur (`.pw-toggle`, `togglePw`) **sebelum** menyentuh kode.

---

## STATUS TERAKHIR (2026-10-02) — Topbar Sub-page Diseragamkan (Info SPMB + Karya Siswa)

**Permintaan user:** "coba navbar di halaman info spmb disama kan dengan navbar berita dan pengumuman" + "sama navbar karya siswa juga sama kan". Lalu: "sama sekalian kamu deploy ke vercel nanti".

**Masalah:** tiap sub-page punya topbar sendiri dengan gaya berbeda — `/berita` sudah "sticky + tombol kembali bulat + logo + nama halaman", tapi `/spmb-info` (tombol pill "Kembali ke Beranda" + `top-badge` kosong) dan `/produk-siswa` (tombol `<` polos tanpa logo) tidak sama. Wrapper-nya juga `padding-top: 80px`, jadi topbar kalau dibuat sticky akan menempel dengan halaman yang sudah bergeser.

**Perubahan:**
- **Komponen baru `src/components/layout/PageTopbar.vue`** — topbar yang lifted dari `NewsView` (pola yang dianggap benar). Props: `brand` (wajib), `bg` (default `#f2f4f1`). Isi: tombol kembali bulat 40px (SVG chevron, bukan teks `<`), `<img src="/logo.png">` + `width/height` (anti-CLS), nama halaman. `goBack()` = `router.back()` bila `history.length > 1`, else `router.push("/")`. Warna background dihitung dari hex → `rgba(...,0.92)` via computed + `v-bind()` di CSS, supaya bar transparan menyatu dengan background halaman yang berbeda-beda.
- **`src/views/NewsView.vue`** → pakai `<PageTopbar brand="Berita & Pengumuman" bg="#f2f4f1" />`. Blok `<header class="news-topbar">` + `function goBack()` + 6 blok CSS topbar + aturan `.topbar-brand` di media query **dihapus**. `useRouter`/`router` **tetap** (mas dipakai `openDetail` → `router.push`).
- **`src/views/SpmbInfoView.vue`** → pakai `<PageTopbar brand="Informasi Biaya SPMB" bg="#eef4ec" />`. `goBack()` + `useRouter` + `const router` + import `Sparkles` (cuma dipakai di blok `top-badge` yang **sudah dikomentari** sebelumnya) **dihapus**; CSS `.top-bar`/`.back-button`/`.back-icon` dihapus. `.spmb-info-page` `padding: 80px 7% 100px` → `0 7% 100px` (mobile `70px 4%` → `0 4%`) supaya topbar full-bleed;compensating spacing dipindah ke `.page-header` `margin: 28px 0` (mobile `20px`).
- **`src/views/ProdukSiswaView.vue`** → pakai `<PageTopbar brand="Karya Siswa" bg="#eef4ec" />`. `function goBack()` + CSS `.top-bar`/`.back-button`/`.back-icon` dihapus. `.produk-page` `padding: 80px 7%` → `0 7% 80px` (mobile `70px 5%` → `0 5% 60px`); `.page-header` dapat `margin: 32px 0 28px` (mobile `24px 0`).

**Verifikasi (Playwright lokal, dev server :5174, 3 halaman × 2 viewport):**
- `npm run build` → sukses (`✓ built in 24.35s`). Tidak ada warning baru.
- **Identik di 3 halaman:** `position: sticky` (setelah `scrollTo(0,400)` `top` = **0** di semua), tinggi bar **71px** @1440 / **67px** @390, `border-bottom: 1px solid rgb(227,232,227)`, `backdrop-filter: blur(8px)`, tombol kembali **40px** `border-radius: 50%`, logo **42px** loaded (`complete: true`).
- **Teks brand benar per halaman:** "Berita & Pengumuman" / "Informasi Biaya SPMB" / "Karya Siswa".
- **Tidak ada horizontal overflow:** `scrollWidth == clientWidth` (1440 & 390) di semua halaman.
- **Jarak konten aman dari sticky bar:** Info SPMB konten pertama y=**99** (71+28), Karya Siswa y=**103** (71+32); mobile 87 & 91.
- **0 console error / 0 pageerror** di 6 kombinasi.

**Catatan:**
- Tint background tiap halaman sengaja dibedakan lewat prop `bg` (`#f2f4f1` vs `#eef4ec`, selisih tak kasat mata) supaya bar transparannya menyatu dengan background halamannya sendiri.
- `/e-tracer` masih pakai `.top-bar` gaya lama — **tidak** ikut disentuh karena user hanya menyebut 2 halaman. Kandidat penyelarasan berikutnya kalau diminta.
- `src/views/NewsDetail.vue` tidak punya topbar sendiri.
- `src/components/layout/Footer.vue` punya perubahan **bukan dari sesi ini** (`.contact-btn` kehilangan border, menyisakan `border-radius` menggantung) — masih uncommitted dari sebelumnya, ikut ter-deploy.

**Deploy (project Vercel `lomba`, akun `zakkyilhamf-7419`):**
- Percobaan 1 → `Error: fetch failed` (known issue, sesuai AGENTS.md). Retry langsung sukses.
- `vercel deploy --prod --yes` → `https://lomba-378j7oyq6-zakkys-projects-99c4bf23.vercel.app`, build Vercel `✓ built in 3.67s`, `Build Completed in /vercel/output [6s]`, `✓ Ready in 39s`.
- Alias production **otomatis** terpasang: `lomba-378j7oyq6-…` → **`https://smkbu-sby.vercel.app`** (dikonfirmasi `vercel alias ls`). Tidak perlu `vercel alias set` manual.
- **Verifikasi production (Playwright ke `https://smkbu-sby.vercel.app`):** ketiga route HTTP **200**, `.page-topbar` `position: sticky` tinggi **71px**, brand benar ("Berita & Pengumuman" / "Informasi Biaya SPMB" / "Karya Siswa"), **0** elemen `.top-bar`/`.news-topbar` lama tersisa, **0 console error**.

**Belum di-commit** (sesuai aturan: commit hanya atas permintaan).

---

## STATUS TERAKHIR (2026-10-01) — Hero: Loop Melayang Dihapus, Fade In Saja + Tombol Login Ketengah

**Permintaan user (revisi sesi sebelumnya):** "hapus aja animasi atas bawahnya, biarin diem aja kecuali animasi fade in nya, sama tombol login nya taruh ditengah".

**Perubahan `src/components/sections/Hero.vue` (style saja):**
- **Loop `heroFloat` DIHAPUS** dari `.hero-visual img` ⇒ gambar benar-benar diam (user: "biarin diem aja"). Keyframe `heroFloat` juga dihapus.
- **Animasi `heroShadow` DIHAPUS** dari `.hero-visual::after` ⇒ bayangan jadi statis (perlu sinkron dengan float; tanpa float jadi tak ada gunanya). Keyframe `heroShadow` dihapus. Bayangan tetap ada sebagai penanda "berdiri di lantai" — tidak ikut bergerak.
- **`heroIn` disederhanakan jadi fade in murni**: `transform: translateX(48px) scale(0.97)` → `translateX(0) scale(1)` **dihapus**, sisanya `opacity: 0 → 1`. Durasi 0.85s + delay 0.25s + `both` tetap (efek stagger setelah teks). Praktisnya `animation: heroIn ...` sekarang 1 blok 2 baris.
- **`will-change: transform` DIHAPUS** dari `.hero-visual img` — tidak ada lagi transform yang dianimasikan, jadi hint GPU itu jadi pemborosan memory.
- **Tombol login di tengah**: `.buttons` dapat `justify-content: center` (tambah, bukan ganti `align-items: flex-start`) → tombol Login + grup sub-buttons rata tengah di dalam kolom teks kiri. Teks & judul tetap rata kiri (tidak diubah — user hanya minta tombolnya).

**Verifikasi (Playwright lokal, dev server :5174):**
- `npm run build` → sukses (`✓ built in 14.06s`).
- **Gambar diam (terbukti, bukan asumsi):** `getComputedStyle(img).animationName = none`, `transform = none`, sampling 10× tiap 400ms → **1 nilai transform unik** (sebelumnya 14 nilai + `translateY` sampai −13.86px).
- **Hanya fade in:** wrapper `heroIn` 0.85s delay 0.25s `both`, `opacity: 1`, `playState: finished` (sekali selesai, tidak loop).
- **Bayangan statis:** `::after` `animationName: none`, transform konstan `matrix(1,0,0,1,-119.594,0)`.
- **Tombol tengah:** center tombol **410px** = center kolom copy **410px**, selisih **0px**. Lebar tombol 88px di dalam kolom 560px.
- **0 console error / 0 pageerror.**

**Belum di-commit/deploy.**

---

## STATUS TERAKHIR (2026-10-01) — Hero Landing: Teks Kiri + Gambar 3D Kanan (Animasi Melayang)

**Permintaan user:** hero_section dirapikan — teks di kiri, ilustrasi 3D (siswa naik podium, `Downloads/transparent-image.png`) di kanan, gambar diberi animasi. Keputusan user (3 pertanyaan): animasi **masuk + melayang terus**, gambar **convert ke WebP**, di mobile **gambar disembunyikan**.

**Perubahan `src/components/sections/Hero.vue` (1 file, layout + style):**
- **Template:** `.container` dibelah dua — `<div class="hero-copy">` (h1, p, bg-word, buttons) + `<div class="hero-visual"><img></div>`. Path gambar via konstanta `const heroVisual = "/hero-siswa.webp"`.
- **Aset baru `public/hero-siswa.webp`** — convert dari PNG 740×740 **338 KB → 41.7 KB** (WebP quality 86, alpha terverifikasi: 4 sudut `(0,0,0,0)`, isi center opaque). Dibuat dengan Pillow lokal (Python 12.1.0 punya webp), **tanpa dependency baru** — sesuai guard PERF.md "tanpa dependency baru".
- **CSS `.container`:** `flex-direction: column` → `row` + `align-items:center; justify-content:space-between; gap:40px`. `align-items:center; text-align:center` DIHAPUS → teks rata kiri.
- **`.hero-copy`** baru: `position:relative; flex:1 1 0%; min-width:0; max-width:560px`.
- **`.hero-visual`** baru: `flex:0 1 460px; min-width:260px` — flex-basis dibuat variabel supaya rentang 900–1100px kompres mulus (tak kejepit).
- **`.bg-word` PENTING:** `top:52px→8px`, `left:-8px→-10px`. Elemen ini `position:absolute` — sebelumnya relatif ke `.container`, sekarang relative ke `.hero-copy`. Kalau tidak dipindah ke dalam `.hero-copy`, posisinya akan lompat saat container jadi flex-row.
- **`<img>`** dapat `width="740" height="740" decoding="async"` → reserved aspect ratio, **cegah CLS** (guard PERF.md). **Tanpa** `loading="lazy"` (hero = above fold / LCP).
- **Responsif:** breakpoint baru `@media (max-width:1100px)` (gap 24px, visual 380px) untuk preventsqueeze; `@media (max-width:900px)` → `.hero-visual { display:none }` (pilihan user) + `.hero-copy { max-width:none }`. Teks tetap rata kiri di semua ukuran.
- **Animasi 3 lapis (pisah elemen ⇒ transform tidak saling override):** `heroIn` 0.85s delay 0.25s `both` di wrapper (slide dari kanan + fade + scale 0.97→1) → `heroFloat` 4.6s `translateY ±14px` di `<img>` (delay 1.1s, pas entrance selesai) → `heroShadow` 4.6s sinkron di `.hero-visual::after` (ellipse radial-gradient blur di bawah podium, `scale 1→0.82` + `opacity 1→0.55` ikut saat gambar naik ⇒ kesan melayang, bukan nempel).
- `prefers-reduced-motion` **tidak perlu touched** — sudah ada global di `src/style.css:91-99` (duration 0.01ms + iteration 1 ⇒ animasi loop auto mati, entrance langsung capai state akhir).

**Verifikasi (Playwright lokal, dev server :5174 yang sudah jalan):**
- `npm run build` → **sukses** (`✓ built in 11.89s`); HomeView 27.99 kB (gz 9.62, naik tipis dari 27.76/9.51 karena markup img).
- `public/hero-siswa.webp` → `dist/hero-siswa.webp` 41.738 byte, referenced di HomeView chunk. Dev server: `HTTP 200, content-type: image/webp`.
- **Layout 3 breakpoint (DOM terukur, bukan asumsi):** 1440px → copy x=130 w=560, visual x=850 w=460 (**tidak overlap**); 1100px → copy w=542 + visual w=380 (kompres mulus); 390px → visual **DISEMBUNYIKAN**, copy w=354 full-width.
- **Animasi float terbukti jalan:** sampling `getComputedStyle(img).transform` tiap ~420ms selama 5.6 dtk → `translateY` 0 → −13.86px → 0, 14 nilai transform berbeda, `getAnimations().playState = "running"`.
- **0 console error / 0 pageerror** di semua 3 viewport.
- Skill `frontend-ui-engineering` dipakai (alur kerja AGENTS.md).

**Catatan:** `heroIn`/`heroFloat` bernama ber-hash (`-d09717b8`) di dev karena `<style scoped>` di-Vite, normal.

**Belum di-commit/deploy.**

---

## STATUS TERAKHIR (2026-09-30) — Audit & Rewrite `AGENTS.md` (Analisis Struktur Repo)

**Tugas:** audit seluruh file `.md` + struktur folder, lalu tulis ulang `AGENTS.md` supaya FUTURE sesi agent tidak salah command/gotcha.

**Diverifikasi langsung (bukan dari dokumen lama):**
- `backend/`: `php artisan test` → **39 passed / 144 assertions** (~25 dtk), sqlite `:memory:`. `php artisan view:cache` → OK.
- `backend-admin/`: `php artisan test` → **2 failed, 1 passed** sejak awal. Penyebab: `ExampleTest` (`/` redirect 302 → `/admin`, test harap 200) + `RegistrationInsightServiceTest` (service di admin sengaja fallback-only, test masih expect panggilan NINEROUTER). **Bukan regresi.**
- Root: `npm run build` → sukses, `✓ built in 27.58s`. **PowerShell memblokir `npm`** (ExecutionPolicy `npm.ps1`) → harus `& "C:\nvm4w\nodejs\npm.cmd" run build`. Ini hadn't terdokumentasi sebelumnya.
- `backend-admin` = **FORK**, bukan hasil build `backend`. 10 file beda isi + `routes/web.php`, dan 5 migrasi tidak ada di admin (`create_tabungans`, `create_lowongans`, `create_lamarans` ×3, `drop_plain_password`, `add_avatar`) ⇒ jangan `migrate:fresh` di admin.
- Draft pendaftaran ternyata disimpan di session **dan** tabel `pendaftaran_drafts` (cookie `pending_draft`) — dokumentasi lama hanya menyebut session.
- Root `.env.local` / `.env.production` = `VERCEL_OIDC_TOKEN` (gitignored, jangan di-commit).
- `.playwright-mcp/` (46 file) **ter-track di git** — noise, belum dibersihkan (perlu konfirmasi user).

**Perubahan:** `AGENTS.md` di-ringtas 149 → 109 baris. Yang dihapus: daftar 45 kolom `pendaftarans`, daftar file kunci controller/model yang sudah terlihat dari struktur folder, section "Command Penting" (duplikat), uraian skill panjang. Yang ditambahkan/diperbaiki: peta repo 3 deployable + 3 project Vercel; daftar file yang divergen `backend`↔`backend-admin`; blok command dengan `npm.cmd`; status baseline test `backend-admin` (MERAH sejak awal); 4 gotcha baru (`/api/` prefix, session auth + `?auth=` handoff, `Accept: application/json`, `CACHE_STORE`/`env.deploy` menua, upload `/tmp` tidak persisten, `public/` tak disajikan vercel-php); `NINEROUTER_*` untuk ringkasan AI admin; house rules.

**Belum di-commit/deploy.**

---

## STATUS TERAKHIR (2026-09-30) — Showcase Karya Siswa Dibuka untuk Umum (Feedback Juri)

**Feedback juri:** karya siswa tidak bisa dilihat sebelum login — minta showcase dibuka supaya orang lain bisa yakin dengan sekolah & melihat project.

**Akar masalah — 3 gerbang akses, semua frontend** (data karya hardcoded di Vue, tanpa backend):
1. `src/router/index.js` — route `/produk-siswa` pakai `meta: { requiresSiswa: true }` → tamu di-redirect ke `/`.
2. `src/components/sections/ProdukPreview.vue` — `goProduk()` cek role → toast "Khusus siswa, silakan login terlebih dahulu".
3. `src/components/layout/Navbar.vue` — link "Produk Siswa" dropdown Layanan + menu mobile pakai `@click="guardSiswa"` → `preventDefault` + toast.

**Perubahan (4 file):**
- `router/index.js` — hapus `meta: { requiresSiswa: true }` dari `/produk-siswa` (jadi publik seperti `/berita`, `/spmb-info`, `/e-learning`). `beforeEach` & guard `/koperasi`, `/tabungan`, `/spp` tetap utuh.
- `ProdukPreview.vue` — `goProduk()` jadi cukup `router.push("/produk-siswa")`; import `useAuthSession` & `useToast` yang tak terpakai dihapus.
- `Navbar.vue` — lepas `@click="guardSiswa"` dari link Karya Siswa (baris 30 & 149; mobile tetap `closeMenu()`); label "Produk Siswa" → **"Karya Siswa"**. `guardSiswa` tetap menjaga Koperasi/SPP/Career (6 referensi tersisa).
- `ProdukSiswaView.vue` — pill hardcode "18 Karya terpajang" → dinamis `{{ products.length }}` (sekarang 9); `page-label` → "Karya Siswa" (konsisten dengan h1 "Galeri Karya Siswa").

**Verifikasi:** `npm run build` sukses. Frontend tanpa test framework (scripts: dev/build/preview) → cek manual: tamu buka "Lihat Karya", navbar "Karya Siswa", atau `/produk-siswa` langsung → tampil tanpa redirect; tamu buka `/spp` tetap di-redirect. PROGRESS.md terupdate; belum di-commit/deploy.

---

## STATUS TERAKHIR (2026-09-30) — Hierarki Dashboard Siswa: Pengumuman Kelulusan Naik ke Posisi Paling Atas

**Permintaan user:** card pengumuman lulus/tidak di dashboard siswa ("hierarki ke siswa lebih kena") — semula tersembunyi di tengah halaman.

**Keputusan (konfirmasi user):** posisi **tepat setelah banner sambutan** (awalnya "paling atas sebelum banner", lalu user meminta **"dibawah banner aja deh"** → diubah), **semua status naik** (diterima, ditolak, diproses, edit berakhir), dan **gaya dibedakan** jadi banner berwarna + tombol Unduh Bukti ikut di dalamnya.

**Perubahan di `backend/resources/views/pendaftaran/dashboard-siswa.blade.php`:**
- `@php $badge` di-hoist dari dalam `.ds-stats` ke atas (dipakai announce + stats + badge sama).
- Blok pengumuman (`ds-card--status-message`) dipindah ke posisi **4** → Kembali → alert → **banner sambutan** → **pengumuman** → statistik → detail → aksi → edit — di-rename jadi `.ds-announce` + modifier `ds-announce--{status}`:
  - `diterima` hijau, `ditolak` merah, `diproses` biru, `baru` (lewat deadline) amber → tinted bg + border kiri 6px + ikon SVG + eyebrow uppercase + teks (wording lama tetap).
  - `role="status"` untuk aksesibilitas.
  - Wrapper tetap `@if (!$canEdit)` — cakupan 4 kondisi sama persis; status `baru` yang masih bisa edit memang tanpa pesan (tidak dibuat baru).
- Tombol **Unduh Bukti Diterima** kini di dalam card pengumuman (hanya `diterima`); `.ds-actions` dibungkus `@if ($canEdit)` (sisa isinya cuma tombol Edit — div kosong tak lagi muncul saat diproses/ditolak).
- CSS lama `.ds-card--status-message` & `.ds-status-text` dihapus; tambah blok `.ds-announce` + modifier + ikon.

**Test baru `tests/Feature/DashboardSiswaHierarchyTest.php` (5 kasus):** assert posisi string di HTML — `ds-announce` muncul **setelah** `ds-banner` (urutan: banner → pengumuman) untuk diterima/ditolak/diproses/baru-lewat-deadline; tombol Unduh ada saat diterima & hilang saat ditolak; status `baru` yang masih bisa edit **tidak** menampilkan pengumuman tapi tetap ada Edit + form.

**Verifikasi:** `php artisan view:cache` OK (error LSP = false positive parser Blade di dalam HTML/CSS). `php artisan test` → **39 passed (144 assertions)** (34 lama + 5 baru). Belum di-commit/deploy.

---

## STATUS TERAKHIR (2026-09-30) — Ikon Instagram Footer Diperbaiki (Kenapa Tampil Jelek)

**Masalah:** ikon Instagram di footer (`Footer.vue`) tampil jelek/bolong, padahal TikTok tampil normal.

**Akar masalah:** IG memakai `<i class="fa-brands fa-instagram">` (Font Awesome) yang butuh font **"Font Awesome 7 Brands"** — font itu **tidak pernah di-load** di seluruh proyek Vue (grep `fa-brands|font-awesome|@fortawesome` → cuma 1 kemunculan, di Footer itu sendiri). Tanpa font tersebut browser merender placeholder glyph. Sementara TikTok memakai **inline SVG** — itu sebabnya TikTok bagus, IG jelek.

**Perbaikan:**
- `src/components/layout/Footer.vue` — ganti `<i class="fa-brands fa-instagram">` dengan **inline SVG** path resmi Instagram (diambil dari `cdn.simpleicons.org` — garis besar Instagram Simple Icons, viewBox `0 0 24 24`, `fill="currentColor"`, ukuran 18×18) sehingga mengikuti pola TikTok dan otomatis mengikuti warna hover (hijau brand ⇄ putih).
- Hapus aturan CSS `.social-icon i { font-family: "Font Awesome 7 Brands" }` yang tak terpakai.

**Verifikasi:** `npm run build` sukses (dist/ ter-ignore git). Belum di-commit/deploy.

---

## STATUS TERAKHIR (2026-09-30) — Foto Profil Tidak Muncul di Navbar Landing Page (Vue)

**Laporan user:** foto profil tampil di dashboard siswa & halaman profil, tetapi **tidak tampil di navbar/topbar landing page** (Vue). Padahal sidebar/topbar backend (`layouts/app.blade.php`) sudah menampilkan fotonya.

**Analisis (explore menyeluruh, 3 akar masalah):**
1. Backend `/auth-status` (`AuthController::authStatus()`) **tidak mengirim `avatar`** — padahal frontend menyimpan seluruh response apa adanya di `session` ref + `sessionStorage` (`useAuthSession.js`).
2. Payload login `?auth=` dari `frontendAuthUrl()` (`helpers.php`) hanya membawa 5 field — avatar tidak ikut, padahal itu jalur yang dipakai saat cookie third-party diblokir.
3. `Navbar.vue` hanya merender **inisial** (`<span class="nav-avatar">{{ initial }}</span>`) — tidak pernah ada `<img>` sama sekali.

**Perubahan:**
- `backend/app/Http/Controllers/AuthController.php` — `authStatus()` menambahkan `'avatar' => $request->user()->avatar` pada response login, dan `'avatar' => null` pada response guest (bentuk konsisten).
- `backend/app/helpers.php` — `frontendAuthUrl()` menambahkan `'avatar' => $user->avatar` ke payload `?auth=` (logged-in) dan `null` (guest).
- `src/composable/useAuthSession.js` — `GUEST` mendapat `avatar: null`.
- `src/components/layout/Navbar.vue` — avatar **desktop** (`nav-avatar`) dan **mobile** (`mobile-avatar`) kini menampilkan `<img>` foto profil bila `session.avatar` ada (pola overlay sama seperti topbar backend: inisial tetap di bawah, gambar di atas; `@error` → gambar disembunyikan, inisial tetap tampil). Tambah state `avatarBroken` / `avatarBrokenMobile`, CSS `position: relative + img{position:absolute;inset:0;object-fit:cover}` + `overflow:hidden`.

**Verifikasi:**
- `backend`: 3 test baru di `ProfileAvatarTest` → `/auth-status` mengirim avatar (login + guest), dan payload `frontendAuthUrl()` menyertakan avatar. `php artisan test` → **34 passed (113 assertions)** (31 lama + 3 baru).
- `frontend`: `npm run build` sukses (dist/ regenerated, tapi ter-ignore git — tidak menodai repo).
- Cek visual: setelah login di landing page (`npm run dev` + backend `php artisan serve --port=8000`), navbar desktop & drawer mobile harus menampilkan foto profil; polling 30 detik / `?auth=` juga sudah membawa avatar.
- **Belum di-commit / di-deploy** (user handle git sendiri).

**Catatan scope:** widget profil di `src/views/ELearningView.vue` (panel berbentuk inisial di side panel) ikut memakai `session.name`/`email` tapi **belum** otomatis menampilkan foto — di luar keluhan navbar; kalau mau disamakan, tinggal bilang.

---

## STATUS TERAKHIR (2026-09-30) — Redesign Halaman Profil Siswa + Foto Profil (base64)

**Permintaan user:** redesign halaman profil siswa (`/profil`) mengikuti gambar referensi yang dikirim, dengan tab **Profil | Pendaftaran | Keluarga**, warna hijau brand, plus **siswa bisa upload foto profil** sendiri dan avatar di topbar ikut memakai foto tersebut.

**Keputusan user:** tab 3 tersebut; palet hijau; foto disimpan **base64 di kolom DB** (bukan file storage) karena `public/storage` tidak ada symlink, `backend/.env.deploy` memakai `PUBLIC_DISK_ROOT=/tmp`, dan `vercel.json` tidak merutekan `/storage/*` → file-based tidak persisten di production. Link topbar menuju `/profil`; middleware `/profil` **dilonggarkan** dari `role:siswa` → `role:siswa,pendaftar` (pendaftar yang belum dinyatakan diterima tetap boleh buka, konsisten dengan `dashboard-siswa` yang middleware-nya hanya `auth`).

**Perubahan (7 file):**
1. **BARU** `backend/database/migrations/2026_09_30_090000_add_avatar_to_users_table.php` — `$table->mediumText('avatar')->nullable()->after('password')` (mediumText, bukan text: base64 ≈ 4/3 ukuran file, `text` cuma 64KB). Dijalankan di MySQL lokal.
2. `backend/app/Models/User.php` — `'avatar'` masuk `$fillable`, dan juga masuk `$hidden` supaya data URI tidak ikut bocor saat `toArray()`/JSON.
3. `backend/app/Http/Controllers/AuthController.php`:
   - `showProfile()` → tambah variabel `$canEdit` (status `baru` **dan** belum lewat 3 hari, aturan bisnis yang sama dengan `PendaftaranController::update()`), juga pakai import `Pendaftaran` (sebelumnya FQCN).
   - **BARU** `updateAvatar()` — validasi `required|image|mimes:jpg,jpeg,png,webp|max:512` dengan pesan error Bahasa Indonesia.
   - **BARU** `destroyAvatar()` — kosongkan kolom.
   - **BARU** `avatarToDataUri()` (private) — resize pakai GD (sisi terpanjang → 256px, aspect ratio dijaga) → JPEG quality 82, latar putih dulu supaya PNG transparan tidak jadi hitam; bila hasil masih > 350KB, ukuran diturunkan bertahap 256→192→160→128. Fallback ke base64 file asli **hanya** bila ekstensi GD tidak tersedia. **File rusak / bukan gambar → `null`** (tidak pernah disimpan). `detectMimeFromBytes()` private helper.
4. `backend/routes/web.php` — `POST /profil/avatar` (`profil.avatar.update`) + `DELETE /profil/avatar` (`profil.avatar.destroy`), keduanya `role:siswa,pendaftar`; middleware `/profil` dilonggarkan.
5. `backend/resources/views/auth/profile.blade.php` — **rewrite total** mengikuti referensi: header bar (chevron kembali · judul "Profil Siswa" · ikon gear → dashboard), kartu profil (avatar + badge kamera sebagai `<label>` file picker + nama + meta NISN/Jurusan/Status pill + ikon pensil → `dashboard-siswa#edit-section` hanya saat `$canEdit`), tab bar 3 tab dengan `role="tablist"` + arrow-key navigation, 4 kartu statistik gradasi hijau (Jurusan · Nilai Rata-rata · Status · Gelombang, tiap kartu ada tombol "Lihat detail" → pindah tab), tabel "Informasi Pribadi", panel Pendaftaran (Data Sekolah & Seleksi · Status Pendaftaran · Berkas Upload — berkas **dibiarkan teks** "Terunggah/Belum diunggah", tidak ditautkan karena URL storage memang rusak), panel Keluarga (Data Keluarga + dua kartu Ayah/Ibu + Wali), empty state "Isi Formulir Pendaftaran" bila belum daftar, footer berisi tombol Logout + Kembali ke Dashboard. Format tanggal Bahasa Indonesia ditulis lokal di view (helper `formatPeriode` hanya untuk periode SPP "Y-m").
6. `backend/resources/views/layouts/app.blade.php` — `.app-topbar-user` jadi elemen `<a>` (link `/profil` hanya bila role `siswa`/`pendaftar`, selain itu tanpa `href` agar kasir/guru tidak kena error), avatar topbar menampilkan foto bila ada (fallback inisial via CSS overlay + `onerror` hide img), `mb_substr` untuk inisial, CSS hover untuk `a.app-topbar-user`.
7. `backend/tests/Feature/ProfileAvatarTest.php` — **BARU**, 12 test (lihat bagian Verifikasi).

**Penyesuaian setelah review user:** tombol **pengaturan (gear)** dihapus, dan tombol **back (chevron)** di bawah navbar juga dihapus karena navbar sudah punya tombol kembali di sebelah logo sekolah (branch navbar di `layouts/app` — tombol itu hanya muncul saat `@auth`, dan halaman profil selalu butuh auth). Header halaman kini hanya berisi judul terpusat **"Profil Siswa"**. Selector CSS `.pf-header` (grid 3 kolom) dan `.pf-icon-btn` ikut dihapus karena sudah tidak dipakai. `frontendAuthUrl()` tidak lagi dipanggil di view profil. Ikon **pensil (edit data)** dan **kamera (upload foto)** tetap ada karena bukan tombol navigasi.

**Dua bug yang ketahuan saat verifikasi (dan sudah diperbaiki):**
- Gambar **rusak** sempat lolos: versi pertama `avatarToDataUri()` jatuh ke fallback base64 mentah dan menyimpan sampah. Diperbaiki: fallback mentah hanya bila GD tidak ada; bila GD ada tapi `imagecreatefromstring()` gagal → `null` + pesan error.
- View memakai `$errors` yang **hanya di-share middleware web** → view crash saat dirender di luar HTTP (mis. testing/CLI). Diperbaiki: `$errorBag = $errors ?? new \Illuminate\Support\ViewErrorBag`.

**Verifikasi (lokal, tanpa deploy):**
- `php artisan migrate --force` → `users.avatar` ada (dicek via `Schema::hasColumn`).
- `php artisan route:list --path=profil` → 3 route terdaftar.
- `php artisan view:cache` + `view:clear` → blade compiles bersih.
- `php artisan test` → **31 passed (106 assertions)** (19 test lama tetap hijau + 12 test baru). Test baru mencakup: siswa & pendaftar boleh akses profil, role lain (guru) kena middleware, halaman profil + semua elemen utama, empty state tanpa pendaftaran, upload → base64 + resize terverifikasi (`getimagesizefromstring` menghasilkan 256×192 dari 1600×1200), foto tampil di halaman, file >512KB ditolak, file non-gambar ditolak, gambar rusak tidak disimpan, hapus foto, avatar tidak bocor di serialisasi, dan link profil di topbar hanya untuk siswa.
- Render nyata (script PHP yang boot Laravel, dihapus setelah dipakai): akun dengan data lengkap → 4 kartu statistik, 3 tab, 46 baris data, seluruh section Pendaftaran & Keluarga ada; akun tanpa pendaftaran → empty state + 0 ikon pensil + inisial fallback; pipeline resize 3000×2000 → 256×171 dengan data-URI 1883 byte.
- **Belum di-commit / di-deploy** (user ingin melakukan commit & push sendiri).

**Catatan production (VPS/Vercel):** migrasi `2026_09_30_090000_add_avatar_to_users_table` **wajib dijalankan** di DB production (TiDB / MySQL VPS) saat deploy berikutnya, kalau tidak kolom `users.avatar` tidak ada dan upload foto akan error. Tidak ada perubahan `.env`, `vercel.json`, atau frontend Vue.

**Catatan:** diagnostic LSP di file blade (`at-rule or selector expected`, `Property assignment expected`) tetap **false positive** — LSP memparse `<style>`/`<script>` inline sebagai CSS/JS murni sehingga `{{ }}` Blade dianggap selector tidak valid. Acuan validasi tetap `php artisan view:cache` + `php artisan test`.

---

## STATUS TERAKHIR (2026-09-30) — Alas Stiker Bulat untuk Doodle Banner Dashboard Siswa

**Permintaan user:** doodle di banner dashboard siswa `nyatu sama warna hijau` sehingga tidak kelihatan jelas → minta diberi "apa gitu" di belakangnya agar kontras.

**Analisis:** `images/doodle-studying.png` adalah line art **hitam** (buku pink) dengan background transparan, langsung menempel di banner gradasi hijau tua `#2f5b45 → #4a7a62` → kontrasnya rendah. Panel admin tidak punya doodle banner, jadi cakupannya hanya 1 file.

**Pilihan user:** gaya **badge bulat putih (stiker)**, dashboard siswa saja.

**Perubahan (1 file — `backend/resources/views/pendaftaran/dashboard-siswa.blade.php`):**
- HTML: `<img class="ds-banner-doodle">` dibungkus `<div class="ds-banner-doodle-wrap">` (sekarang img jadi anak dari wrapper, bukan flex child langsung).
- CSS: blok `.ds-banner-doodle` diganti jadi 2 rule baru — wrapperlingkaran 176×176px `border-radius:50%` dengan `background: radial-gradient(circle at 35% 30%, #ffffff 0%, #f1f7f0 72%)` + `box-shadow: 0 14px 30px rgba(11,30,20,.30), 0 0 0 8px rgba(255,255,255,.14)`; img di dalamnya `height:132px` + `filter: drop-shadow(...)` halus.
- Animasi `ds-float` **dipindah dari img ke wrapper** → stiker utuh yang mengambang; `@keyframes ds-float` tidak diubah.
- Media query `max-width:768px`: aturan lama `.ds-banner-doodle{height:120px;margin-top:20px;margin-left:0}` diganti jadi wrapper 140×140px + img 104px (proporsional saat banner berubah jadi kolom).

**Verifikasi (lokal, tanpa deploy):**
- `php artisan view:cache` + `view:clear` → blade ter-compile tanpa error.
- Render nyata dicek via `php artisan tinker` (view `pendaftaran.dashboard-siswa` dengan `Pendaftaran` pertama dari DB + `Auth::login`, read-only): `wrapper=3` (1 HTML + 2 CSS), `cssBadge=1`, `anim=1`, `img132=1`, `mobile=1`, `doodleImg=1`; markup ban terpotong sesuai harapan (wrapper membungkus img).
- `php artisan test` → **19 passed (55 assertions)**.
- Catatan: login HTTP via akun demo `siswa` **tidak bisa** dipakai untuk cek banner karena akun demo tidak punya data pendaftaran (halaman render empty-state, banner tidak ikut muncul) — makanya verifikasi dilakukan lewat render view langsung. Cek visual akhir tetap di sisi user (server dev port 8000 sudah jalan, cukup refresh).
- **Belum di-commit / di-deploy** (user ingin melakukan commit & push sendiri untuk belajar git).

**Catatan:** diagnostic LSP yang muncul di file blade ini (error "at-rule or selector expected" di baris `class="ds-stat-icon ds-stat-icon--{{ ... }}"`, dll.) adalah **false positive** — LSP memarse `<style>`/`<script>` inline sebagai CSS/JS murni sehingga `{{ }}` Blade dianggap selector tidak valid. Tidak related dengan perubahan ini; acuan validasi tetap `php artisan view:cache`.

---

## STATUS TERAKHIR (2026-09-30) — Tombol Mata (Lihat/Sembunyikan Password) di Halaman Login & Daftar

**Permintaan user:** tambah ikon mata di input password — halaman login (siswa + panel admin), lalu dilanjutkan ke form daftar.
Frontend Vue tidak diubah karena route `/login` & `/register` di Vue memang redirect ke backend.

**Perubahan (5 file view/CSS/JS — tanpa controller, route, atau DB):**
- `backend/resources/views/auth/login.blade.php` — `<div class="input-wrap">` password diberi modifier `pw-field`; tombol `<button type="button" class="pw-toggle" onclick="togglePw(this)">` setelah input, berisi 2 SVG inline (`.eye-open` tampil, `.eye-off` `display:none`) — path SVG sama dengan yang sudah dipakai di `backend-admin/.../pendaftaran/dashboard.blade.php`.
- `backend/resources/views/auth/register.blade.php` — tombol mata yang sama pada **dua** field: `password` dan `password_confirmation` (masing-masing wrap `.input-wrap pw-field` sendiri, jadi toggle satu tidak memengaruhi yang lain).
- `backend/resources/views/layouts/auth.blade.php` — CSS `.pw-field input{padding-right:44px}` + `.pw-toggle` (absolut `right:10px`, tengah vertikal, transparan, `color:#7d8a80`, hover `#3a6450`); **selector lama `.input-wrap svg` diubah jadi `.input-wrap > svg`** supaya ikon di dalam tombol tidak ikut rule `left:14px` milik ikon kunci; fungsi JS `togglePw(btn)` ditambahkan sebelum `</body>` (ditempatkan di layout agar dipakai bersama oleh login + register tanpa duplikasi).
- `backend-admin/resources/views/auth/login.blade.php` — input password dibungkus `.pw-wrap` + tombol mata; CSS page-scoped di dalam `<style>` (pola yang sama dipakai `berita/create.blade.php`, tidak menyentuh `admin.css`/layout bersama). Panel admin tidak punya halaman daftar.
- `backend-admin/resources/views/layouts/app.blade.php` — `togglePw(btn)` ditambahkan di blok `<script>` yang sudah ada, sebelah `toggleSidebar`.

**Perilaku toggle:** `input.type` `password`↔`text`, tukar visibilitas `.eye-open`/`.eye-off`, update `aria-pressed` + `aria-label` + `title`, fokus dikembalikan ke input. Tombol `type="button"` → tidak ikut submit form.

**Verifikasi (lokal, tanpa deploy):**
- `php artisan view:cache` + `view:clear` di `backend` **dan** `backend-admin` → semua blade ter-compile tanpa error.
- `php artisan serve` + cek HTML hasil render: `GET /register` → 200 dengan 2 tombol `.pw-toggle` / 2 `.pw-field` / 2 `.eye-open` / 2 `.eye-off`; `GET /login` → 200 dengan 1 tombol (tetap aman); port 8001 (panel admin `/login`) → 200 dengan tombol + CSS `.pw-wrap`/`.pw-toggle` + fungsi `togglePw`.
- `php artisan test` (backend) → **19 passed (55 assertions)**.
- Server dev dimatikan setelah verifikasi. **Belum di-deploy** (sesuai instruksi user; user ingin melakukan commit/push sendiri).
- Verifikasi visual klik ikon (tampilan mata) belum dilakukan — silakan cek langsung di browser.

**Catatan lanjutan:** `togglePw` di kedua layout sudah siap dipakai, jadi penambahan tombol mata di halaman `forgot-password`/`reset-password` cukup menyalin markup tombolnya (belum dikerjakan — belum diminta).

---

## STATUS TERAKHIR (2026-09-09) — Fix Reset Password Panel Admin

**Bug:** Tombol "Reset" di dashboard admin (`/admin`) tidak menghasilkan password yang bisa dipakai login siswa.

**Root cause (2):**
1. **Double-hash**: `User` model di `backend-admin/app/Models/User.php` punya cast `'password' => 'hashed'`. Controller `resetUserPassword()` memanggil `bcrypt($plain)` lalu cast `hashed` meng-hash ulang → password tersimpan 2x hash, sehingga `Hash::check($plain, ...)` selalu gagal → siswa tidak bisa login.
2. **Redirect salah**: `return back()` mengikuti HTTP Referer → jika admin klik Reset dari halaman selain dashboard, flash `reset_password` tampil di halaman lain (tidak terlihat).

**Fix (backend-admin):**
- `app/Models/User.php`: hapus cast `'password' => 'hashed'` → password disimpan sekali hash.
- `app/Http/Controllers/PendaftaranController.php` `resetUserPassword()`: ganti `return back()` → `redirect()->route('admin.dashboard')`; simpan `plain_password` via `forceFill()` (wrap try/catch, kolom mungkin sudah di-drop).
- Verifikasi: `php -l` bersih, `route:list` menampilkan `admin.resetPassword`, tinker `Hash::check($plain, bcrypt($plain))` → OK.
- **Data siswa tidak hilang**: reset hanya mengubah kolom `password` (+ `plain_password`); tidak menyentuh pendaftaran/status/relasi lain.
- **Deploy**: `vercel deploy --prod --yes` sukses → auto-alias ke `paneladminsmkbu.vercel.app`; `GET /login` → 200 (live).
- **TODO verifikasi manual**: login admin di production → klik Reset pada akun siswa → password baru tampil & bisa login.

### Sesi 2026-09-09 — Fix Eye Icon Password Admin + Deploy

**Masalah lanjutan:** Eye icon di dashboard admin menampilkan "(tidak ada)" karena kolom `plain_password` sudah di-drop dari shared DB.

**Fix:**
- `dashboard.blade.php`: `data-pw` sekarang menampilkan `substr($akun->password, 0, 8) + "…"` (8 karakter pertama hash sebagai indikator). Toggle JS disederhanakan (tidak perlu fallback).
- `PendaftaranController.php`: hapus `try/catch forceFill plain_password` — tidak diperlukan lagi.
- Deploy `spmb-admin-hma0yk9ck` → auto-alias `paneladminsmkbu.vercel.app` → 200 OK.

---

## STATUS TERAKHIR (2026-08-30 malam)

### Fix 500 Internal Server Error di Panel Admin Tabungan/Koperasi

**Root cause:** Model `User` di `backend-admin/app/Models/User.php` tidak punya relasi `tabungans()` dan `koperasiOrders()`. Controller `TabunganController::adminIndex()` memakai `withSum('tabungans', ...)` → memicu 500 di production.

**Fix:** Tambah `tabungans(): HasMany` dan `koperasiOrders(): HasMany` ke `User` model panel admin. Deploy `spmb-admin-4eeh2wab8` → `paneladminsmkbu.vercel.app` + `spmb-admin.vercel.app`.

---

## Fitur yang SUDAH LIVE di Production

### Backend (pendaftaranspmb.vercel.app)
- Form pendaftaran multi-step (5 langkah) + draft persistence
- Auth (login/register/logout) — session-based, bukan Sanctum
- Dashboard siswa (status pendaftaran + edit ≤3 hari)
- Dashboard admin (live polling 5s + export CSV + quick status)
- SPP (kasir input, guru rekap, ortu link signed URL)
- Tabungan siswa (setor/tarik, saldo aggregate)
- Career Center (lowongan + lamaran + CV upload)
- Berita API (`GET /berita`, `GET /berita/{slug}`)
- Chatbot BISA (Groq-powered, model `openai/gpt-oss-120b`)
- Security hardening (rate limiting, CSRF, security headers, upload validation, race condition fix)

### Panel Admin (paneladminsmkbu.vercel.app)
- Dashboard dengan stat cards + Chart.js + "Ringkasan Data"
- Data pendaftar (filter, search, duplikat detect, export CSV)
- Rekap SPP + link ortu
- Kelola Tabungan, Koperasi, Berita (CRUD)
- Laporan mingguan (print-ready)
- Badge live sidebar
- Reset password siswa + show/hide password
- DB indexes untuk performa query

### Frontend Vue 3 (smkbu-sby.vercel.app)
- 12+ halaman: Homepage, Berita, E-Learning, E-Tracer, Career Center, Koperasi, Produk Siswa, Chat, Login, Register, Dashboard Siswa, Dashboard Admin
- Landing page dengan 8 section (Hero, AboutSchool, SpmbBanner, BeritaPreview, CareerPreview, KoperasiPreview, ProdukPreview, TabunganBanner)
- Navbar glassmorphism (3 dropdown: Layanan, Informasi, Tentang)
- Lazy-load + code splitting (bundle awal ~25KB gzip)
- Font self-hosted (Quicksand woff2)

### Domain Production
| Service | Domain |
|---------|--------|
| Landing page (Vue) | `smkbu-sby.vercel.app` |
| Backend (Laravel) | `pendaftaranspmb.vercel.app` |
| Panel Admin | `paneladminsmkbu.vercel.app` |
| Legacy redirect | `bhapppp.vercel.app` → smkbu-sby (307) |

---

## Log Sesi Detail (13 Agustus 2026 ke atas)

### Sesi 2026-08-30 — Deploy Tabungan, Koperasi, Berita ke Panel Admin
- Fitur Kelola Tabungan, Koperasi, Berita di panel admin (sidebar + CRUD)
- Migrasi baru ke TiDB production (`tabungans`, `koperasi_orders`, `beritas`)
- Deploy Vercel + alias set

### Sesi 2026-08-30 — Fix 500 Panel Admin Tabungan/Koperasi
- Model `User` tambah relasi `tabungans()` + `koperasiOrders()`

### Sesi 2026-08-30 — UI Berita + Fitur Kelola Berita Admin
- Migration `beritas` table (id, title, slug, category, content, image_path, author, published_at, featured, read_time, is_published)
- `BeritaController.php`: CRUD + upload gambar + auto read_time
- Views: index (tabel + thumbnail), create (drag-drop upload), edit (pre-filled)
- Backend API: `GET /berita` (JSON list), `GET /berita/{slug}` (detail)
- Frontend `NewsView.vue` di-redesign (featured card split layout, grid cards, skeleton loading)

### Sesi 2026-08-30 — Fix 500 Fresh Deploy (Root Cause: PHP 8.3 vs Pdo\Mysql)
- Root cause: `config/database.php` pakai `use Pdo\Mysql;` (PHP 8.5) tapi Vercel pakai PHP 8.3
- Fix: ganti `Mysql::ATTR_SSL_CA` → `PDO::MYSQL_ATTR_SSL_CA`
- Deploy kedua backend berhasil

### Sesi 2026-08-30 — Performance Audit & Optimization (3 bagian)
- **Frontend**: CursorGlow → requestAnimationFrame, lazy-mount section via LazyMount.vue (IntersectionObserver, rootMargin 600px), CareerPreview pakai `/lowongan/count`, auth polling hanya saat login
- **Backend**: Endpoint `/lowongan/count`, Tabungan saldo via aggregate SQL, Spp `kasirIndex` filter di SQL
- **Panel Admin**: `chartData()` 30×COUNT → 1×GROUP BY, `laporan()` pakai `freshStats()` cached

### Sesi 2026-08-30 — Fitur SPP (bukti bayar satu arah)
- Migrations: `spp_bills` + `spp_payments` + role enum guru/kasir
- Models: `SppBill`, `SppPayment` + relasi di `User`
- `SppController`: index (siswa), store (kasir/admin), adminIndex, kasirIndex, rekapIndex, ortuIndex (signed URL)
- Views: kasir.blade, rekap.blade, ortu.blade
- Artisan command `spp:generate {--periode=} {--nominal=}`
- Test: 19 passed (55 assertions)

### Sesi 2026-08-30 — Security Hardening Komprehensif
- Hapus `plain_password` (migration drop)
- XSS chatbot fix (DOMPurify)
- Rate limiting (login 5/min, register 5/min, forgot 3/min)
- CSRF diaktifkan kembali + frontend `csrf.js` header `X-CSRF-TOKEN`
- Security headers middleware (X-Content-Type-Options, X-Frame-Options, HSTS, dll)
- IDOR TabunganController fix
- Upload hardening (mimetypes validation)
- Race condition pendaftaran (DB::transaction + lockForUpdate)

### Sesi 2026-08-29 — Tabungan Siswa
- Migration `tabungans` (user_id, type setor/tarik, amount, description)
- `TabunganController`: index (saldo aggregate), store (setor/tarik)
- Frontend `TabunganView.vue` + `TabunganBanner.vue` (landing)
- CSRF fix: tambah `tabungan*` ke exception list

### Sesi 2026-08-29 — Koperasi Keranjang & Checkout
- Restore sistem keranjang + checkout (QRIS/Transfer, countdown 1 jam)
- Redesign ulang (referensi Chanta store)
- Fix bug: crop kanan, tombol + kembali

### Sesi 2026-08-29 — Bento Restructure
- `feature.vue` (~750 baris) DIHAPUS → 5 komponen baru: SpmbBanner, BeritaPreview, CareerPreview, KoperasiPreview, ProdukPreview
- HomeView urutan baru: Hero → AboutSchool → SpmbBanner → BeritaPreview → CareerPreview → KoperasiPreview → ProdukPreview → TabunganBanner → Footer
- Hapus section News.vue (ganda)

### Sesi 2026-08-28 — ContactModal + Footer "Hubungi Kami"
- `ContactModal.vue`: glassmorphism modal, form Nama/Email/WhatsApp/Pesan (UX-only, no backend)

### Sesi 2026-08-26 — Chatbot BISA Fix
- Model Groq `llama-3.3-70b-versatile` deprecated → ganti `openai/gpt-oss-120b`
- Fix `import ... with { type:"json" }` (jangan readFileSync — Vercel ENOENT)
- Fix `typing` ref tidak didefinisikan di ChatView
- Kirim seluruh history messages (bukan hanya pesan terakhir)

### Sesi 2026-08-23 — Web Vitals Optimization (LCP/CLS/INP)
- Self-host Quicksand font (woff2)
- Hero h1 animation fix (LCP saving ~0.85s)
- Image optimization + CLS fix (width/height)
- Disable heavy effects on mobile
- Vite code splitting (manualChunks)

### Sesi 2026-08-22 — Career Center Full-Stack
- Migrations: `lowongans` + `lamarans` tables
- Models + Controllers + Seeder (25 lowongan)
- Frontend: CareerJobCard, CariLowonganView, ApplyModal, LamaranSayaView
- Fix: `/api/` prefix bentrok Vercel PHP → pindah tanpa prefix `/api/`

### Sesi 2026-08-22 — LCP Optimization Panel Admin
- Critical CSS split + font preload + Chart.js defer
- Stats cached 30s + Cache-Control header

### Sesi 2026-08-19 — Panel Admin Terpisah + UI Polish
- Backend admin dipisah ke `backend-admin/` (project Vercel `spmb-admin`)
- Route admin dihapus dari web utama (404)
- Login admin ditolak di web utama
- Redesign ke sidebar layout
- 5 fitur: Chart.js, pencarian+filter, duplikat detect, laporan mingguan, badge live

### Sesi 2026-08-19 — Login/Register Glassmorphism
- Full-page hijau + glass card + blob dekorasi + SVG icons

### Sesi 2026-08-18 — Deploy Vercel (Frontend + Backend)
- Frontend: project `lomba` → `smkbu-sby.vercel.app`
- Backend: project `spmb-backend` → `pendaftaranspmb.vercel.app`
- Fix SPA 404, button SPMB, env vars

### Sesi 2026-08-17 — E-Learning + E-Tracer
- `ELearningView.vue`: 6 materi + 3 kuis + filter kategori
- `ETracerView.vue`: form tracer alumni + statistik

### Sesi 2026-08-11 — Landing Page Vue 3
- 12 halaman: Homepage, Berita, Koperasi, Produk Siswa, Career Center, Chat, Login, Register, Dashboard Siswa, Dashboard Admin
- Navbar 3 dropdown, bento grid, AI chatbot

### Sesi 12 (Awal Agustus 2026) — Backend Laravel + Core Features
- Setup database MySQL `pendaftaran_db` + AdminSeeder
- Form multi-step 5 langkah + draft persistence
- Auth (login/register/logout) + reset password
- Dashboard admin (stats + export CSV + quick status)
- Dashboard siswa (status + edit ≤3 hari)
- 419 CSRF handler (`HandleTokenMismatch`)
- Role-based access (admin/siswa/pendaftar)
- Frontend landing page (Hero, AboutSchool, Features, News, Footer)

---

## TODO (Belum Dikerjakan)

- [ ] Dynamic School Statistics (admin-managed) — disetujui user
- [ ] Universal Search — disetujui user
- [ ] Info SPMB Center (syarat/biaya/beasiswa/timeline)
- [ ] Profil Sekolah (Visi Misi)
- [ ] Fasilitas Sekolah
- [ ] Hubungi Kami (backend — ContactModal sudah ada, UX-only)
- [ ] Galeri Kegiatan
- [ ] Foto untuk 5 berita lainnya (masih 404)

---

## Verifikasi Production (terkini)

| Test | Hasil |
|------|-------|
| Landing page 200 | ✅ |
| Backend /login, /register | ✅ |
| Form submit → dashboard siswa | ✅ |
| Admin login → dashboard + export | ✅ |
| Panel admin /admin | ✅ |
| SPP kasir/rekap/ortu | ✅ |
| Tabungan setor/tarik | ✅ |
| Lowongan/lamaran | ✅ |
| Chatbot POST /api/chat | ✅ |
| Security headers | ✅ |
| Rate limiting 429 | ✅ |

---

## Catatan Penting

- **DB**: MySQL `pendaftaran_db` (Laragon lokal + TiDB production)
- **Deploy pattern**: `vercel deploy --prod --yes` lalu `vercel alias set <url> <domain>`
- **Trap**: `vercel link` bikin `.env.local` → hapus; deploy kadang `fetch failed` → retry
- **Trap**: `/api/*` prefix bentrok Vercel PHP runtime → route backend tanpa prefix `/api/`
- **Trap**: `config/database.php` harus pakai `PDO::MYSQL_ATTR_SSL_CA` (bukan `Mysql::ATTR_SSL_CA`) untuk PHP 8.3
