# PLAN.md — Landing Page Kompetisi (8 Hari)

Dokumentasi rencana & kode yang sudah dikerjakan untuk kompetisi.

---

## Status Terakhir

| Komponen | Status | Keterangan |
|---|---|---|
| `public/data/news.json` | ✅ DONE | 6 berita mock (Pengumuman, Prestasi, Kerjasama, Kegiatan, Acara) |
| `src/components/sections/News.vue` | ✅ DONE | Komponen grid berita: tab kategori, load more, kartu news |
| `src/views/NewsView.vue` | ✅ DONE | Halaman `/berita` list + detail modal |
| `src/views/KoperasiView.vue` | ✅ DONE | Katalog produk statis (16 item) |
| `src/views/ProdukSiswaView.vue` | ✅ DONE | Galeri karya siswa (9 produk) |
| `src/router/index.js` | ✅ DONE | 3 routes baru: `/berita`, `/koperasi`, `/produk-siswa` |
| `src/views/HomeView.vue` | ✅ DONE | Sisipkan `<News />` di antara Feature & Footer |
| `src/components/sections/feature.vue` | ✅ DONE | Bento cards: Berita → `/berita`, Koperasi → `/koperasi`, Produk Siswa → `/produk-siswa` |
| `src/components/layout/Navbar.vue` | ✅ DONE | Tambah menu "Berita" (desktop + mobile) |
| `npm run build` | ✅ DONE | `dist/` = 424 KB (< 500KB) |
| `PLAN.md` | 🔄 IN PROGRESS | Dokumentasi ini |

---

## Rencana 8 Hari (Kompetisi)

### Day 1: News System ✅ DONE
- [x] `public/data/news.json` — 6 berita mock
- [x] `News.vue` — komponen bento grid
- [x] `NewsView.vue` — halaman `/berita` + detail modal

### Day 2: Koperasi & Produk Siswa ✅ DONE
- [x] `KoperasiView.vue` — katalog produk statis (16 item)
- [x] `ProdukSiswaView.vue` — galeri karya (9 produk)

### Day 3: Routes & Navigation ✅ DONE
- [x] `router/index.js` — 3 routes baru
- [x] `Navbar.vue` — tambah menu Berita
- [x] `feature.vue` — link bento cards

### Day 4: Landing Page Polish
- [ ] Hero — animasi entrance, SPMB stats badge
- [ ] AboutSchool — update stats (1280 siswa → real?)
- [ ] Feature — pastikan SPMB card bersinar

### Day 5: Mobile & Performance
- [ ] Mobile audit — fix breakpoints, touch targets 44px+
- [ ] Image optimization — WebP, lazy load
- [ ] Bundle analysis

### Day 6: Backend Integration
- [ ] News API — `/api/berita` endpoint (Laravel)
- [ ] Auth status sync — `useAuthSession` cross-origin
- [ ] Test SPMB flow

### Day 7: Polish & Edge Cases
- [ ] Error boundaries, loading skeletons
- [ ] Accessibility — ARIA labels, focus visible
- [ ] Cross-browser test

### Day 8: Final Build & Deploy
- [ ] Final build, verify `dist/` size
- [ ] Documentation
- [ ] Buffer for bugs

---

## File Structure (Hasil Kerja)

```
ga-ro/
├── public/
│   └── data/
│       └── news.json              ← NEW: 6 berita mock
├── src/
│   ├── components/
│   │   ├── layout/
│   │   │   ├── Navbar.vue         ← UPDATED: tambah menu Berita
│   │   │   └── Footer.vue
│   │   └── sections/
│   │       ├── feature.vue        ← UPDATED: link bento cards
│   │       ├── News.vue           ← NEW: komponen berita
│   │       ├── Hero.vue
│   │       └── AboutSchool.vue
│   ├── views/
│   │   ├── HomeView.vue           ← UPDATED: sisipkan News
│   │   ├── NewsView.vue           ← NEW: halaman berita
│   │   ├── KoperasiView.vue       ← NEW: katalog produk
│   │   ├── ProdukSiswaView.vue    ← NEW: galeri karya
│   │   └── CareerCenterView.vue
│   └── router/
│       └── index.js               ← UPDATED: 3 routes baru
└── dist/
    └── (424 KB)
```

---

## Routes (Lengkap)

| Path | Komponen | Keterangan |
|---|---|---|
| `/` | HomeView | Landing page |
| `/berita` | NewsView | Daftar berita + detail modal |
| `/koperasi` | KoperasiView | Katalog produk koperasi |
| `/produk-siswa` | ProdukSiswaView | Galeri karya siswa |
| `/career-center` | CareerCenterView | Lowongan PKL & kerja |
| `/chat` | ChatView | Chatbot |
| `/login` | LoginView | Login |
| `/register` | RegisterView | Register |
| `/dashboard-siswa` | DashboardSiswa | Dashboard siswa |
| `/dashboard-admin` | DashboardAdmin | Dashboard admin |

---

## Build Status

```
✓ built in 29.53s
dist/ = 424.0 KB
```

---

## Constraint

- **JANGAN** otak-atik `create.blade.php`, dashboard admin, dashboard siswa tanpa izin
- File `.env`, chatbot, `vite.config.js` bagian chat — jangan sentuh
