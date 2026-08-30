<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Admin - SPMB SMK Bahrul Ulum')</title>
    <link rel="icon" type="image/webp" href="{{ asset('logo.webp') }}">
    <link rel="preload" href="/fonts/Quicksand-Variable.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/logo.webp" as="image">
    <style>
        @font-face{font-family:'Quicksand';font-style:normal;font-weight:300 700;font-display:swap;src:url('/fonts/Quicksand-Variable.woff2') format('woff2')}
        *,body{font-family:'Quicksand',system-ui,sans-serif;margin:0;padding:0;box-sizing:border-box}
        body{background:radial-gradient(circle at top left,rgba(125,184,141,.14),transparent 28%),#f2f4f1;color:#1c2a23;line-height:1.6;min-height:100vh}
        .layout{display:flex;min-height:100vh}
        .sidebar{width:248px;flex-shrink:0;background:linear-gradient(180deg,#1f3d2e,#2a5238);color:#e8f0e6;display:flex;flex-direction:column;position:fixed;inset:0 auto 0 0;z-index:200}
        .sidebar-brand{display:flex;align-items:center;gap:12px;padding:22px 20px 18px;border-bottom:1px solid rgba(255,255,255,.12);text-decoration:none}
        .sidebar-brand img{height:40px;width:auto;border-radius:10px;background:rgba(255,255,255,.9);padding:3px}
        .sidebar-brand-text{font-size:16px;font-weight:800;color:#fff;letter-spacing:-.02em;line-height:1.25}
        .sidebar-brand-text small{display:block;font-size:11px;font-weight:500;color:rgba(232,240,230,.65);letter-spacing:.02em;margin-top:2px}
        .sidebar-nav{flex:1;padding:16px 12px;display:flex;flex-direction:column;gap:2px}
        .sidebar-label{font-size:11px;font-weight:700;color:rgba(232,240,230,.5);text-transform:uppercase;letter-spacing:.06em;padding:0 10px;margin-bottom:6px}
        .sidebar-link{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;color:rgba(232,240,230,.8);text-decoration:none;font-size:14px;font-weight:600;transition:all .15s}
        .sidebar-link:hover,.sidebar-link.active{background:rgba(255,255,255,.1);color:#fff}
        .sidebar-link.active{background:rgba(255,255,255,.14)}
        .sidebar-footer{padding:16px 12px;border-top:1px solid rgba(255,255,255,.12)}
        .sidebar-footer .btn{width:100%;justify-content:center}
        .main{flex:1;margin-left:248px;min-width:0;display:flex;flex-direction:column}
        .topbar{position:sticky;top:0;z-index:100;background:rgba(255,255,255,.96);border-bottom:1px solid rgba(223,228,221,.95);box-shadow:0 8px 30px rgba(28,42,35,.06);padding:0 32px;height:68px;display:flex;align-items:center;justify-content:space-between;gap:16px}
        .topbar-title{font-size:18px;font-weight:800;color:#1c2a23;letter-spacing:-.02em;display:flex;align-items:center;gap:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-width:0}
        .hamburger{display:none;background:#3a6450;color:#fff;border:none;border-radius:10px;width:40px;height:40px;cursor:pointer;align-items:center;justify-content:center}
        .main-content{max-width:1180px;width:100%;margin:0 auto;padding:36px 32px 60px}
        .sidebar-backdrop{display:none}
        .form-section{background:#fbfcfa;border:1px solid #dfe4dd;padding:36px 40px;border-radius:22px;box-shadow:0 12px 24px rgba(35,55,42,.06);position:relative;overflow:hidden}
        .form-section::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#2a5238 0%,#3a6450 58%,#7db88d 100%)}
        .form-title{font-size:24px;font-weight:800;margin-bottom:6px;color:#1c2a23;letter-spacing:-.03em}
        .form-subtitle{color:#647067;margin-bottom:28px;font-size:14px}
    </style>
    <link rel="preload" href="/css/admin.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="/css/admin.css"></noscript>
</head>
<body>
<div class="layout">
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar(false)"></div>

    <aside class="sidebar" id="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <img src="{{ asset('logo.webp') }}" alt="Logo SMK Bahrul Ulum" width="40" height="40" />
            <span class="sidebar-brand-text">SMK Bahrul Ulum<small>Panel Admin</small></span>
        </a>

        <nav class="sidebar-nav">
            @auth
            <span class="sidebar-label">Menu</span>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->is('admin') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg>
                Dashboard
            </a>
            <a href="{{ route('pendaftaran.index') }}" class="sidebar-link {{ request()->is('pendaftaran*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="9" y1="13" x2="15" y2="13"></line><line x1="9" y1="17" x2="15" y2="17"></line></svg>
                Data Pendaftar
                <span class="sidebar-new-badge" id="newBadge" style="display:none;">0</span>
            </a>
            <a href="{{ route('pendaftaran.laporan') }}" class="sidebar-link {{ request()->is('laporan') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line><line x1="8" y1="9" x2="10" y2="9"></line></svg>
                Laporan
            </a>
            <a href="{{ route('admin.spp.index') }}" class="sidebar-link {{ request()->is('admin/spp') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line><rect x="6" y="13" width="2" height="2"></rect></svg>
                Rekap SPP
            </a>
            <a href="{{ route('tabungan.index') }}" class="sidebar-link {{ request()->is('tabungan*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 5c-1.5 0-2.8 1.4-3 2-3.5-1.5-11-.3-11 5 0 1.2 1.2 3 3 3.5 1.5.5 3 1.5 3 3.5 0 2.5-2 4.5-4.5 4.5S3 18 3 15.5c0-2 1.5-3 3-3.5"></path><path d="M2 9v1c0 1.1.9 2 2 2h1"></path><path d="M16 11h.01"></path><path d="M19 11h.01"></path></svg>
                Kelola Tabungan
            </a>
            <a href="{{ route('koperasi.index') }}" class="sidebar-link {{ request()->is('koperasi*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                Kelola Koperasi
            </a>
            <a href="{{ route('berita.index') }}" class="sidebar-link {{ request()->is('berita*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6z"/></svg>
                Kelola Berita
            </a>
            @else
            <span style="font-size:13px;font-weight:700;color:rgba(232,240,230,0.7);padding:0 10px;line-height:1.7;">Akses terbatas untuk admin sekolah.</span>
            @endauth
        </nav>

        <div class="sidebar-footer">
            @auth
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-light">Logout</button>
            </form>
            @else
            <a href="{{ route('login') }}" class="btn btn-outline-light" style="text-decoration:none;">Masuk Panel</a>
            @endauth
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:12px;">
                <button class="hamburger" onclick="toggleSidebar(true)" aria-label="Buka menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
                <span class="topbar-title">@yield('page-title', 'Panel Admin SPMB')</span>
            </div>
            @auth
            <span class="hide-sm" style="font-size:12px;font-weight:700;color:#647067;flex-shrink:0;">{{ auth()->user()->username }}</span>
            @endauth
        </header>

        <div class="main-content">
            @yield('content')
        </div>
    </div>
</div>

<script>
    function toggleSidebar(open) {
        document.getElementById('sidebar').classList.toggle('open', open);
        document.getElementById('sidebarBackdrop').classList.toggle('open', open);
    }
</script>
</body>
</html>