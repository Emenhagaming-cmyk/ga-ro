<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SPMB - SMK Bahrul Ulum')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- ponytail: @vite removed — no build dir in production, causes 404 --}}
    <style>
        * {
            font-family: 'Quicksand', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(125, 184, 141, 0.14), transparent 28%),
                #f2f4f1;
            color: #1c2a23;
            line-height: 1.6;
            min-height: 100vh;
        }

        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.96);
            padding: 0 7%;
            height: 74px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(223, 228, 221, 0.95);
            box-shadow: 0 8px 30px rgba(28, 42, 35, 0.08);
        }

        .navbar-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
            font-weight: 800;
            color: #1c2a23;
            text-decoration: none;
            letter-spacing: -0.02em;
        }

        .navbar-brand span {
            color: #3a6450;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links a {
            text-decoration: none;
            color: #5b6475;
            font-weight: 700;
            font-size: 14px;
            padding: 9px 14px;
            border-radius: 999px;
            transition: all 0.25s ease;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: #ffffff;
            background: linear-gradient(90deg, #2f5b45 0%, #3a6450 100%);
            box-shadow: 0 8px 18px rgba(58, 100, 80, 0.18);
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 32px 24px 60px;
        }

        .form-section {
            background: #fbfcfa;
            border: 1px solid #dfe4dd;
            padding: 28px 32px;
            border-radius: 20px;
            box-shadow: 0 12px 24px rgba(35, 55, 42, 0.06);
            position: relative;
            overflow: hidden;
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2a5238 0%, #3a6450 58%, #7db88d 100%);
        }

        .form-title {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 6px;
            color: #1c2a23;
            letter-spacing: -0.03em;
        }

        .form-subtitle {
            color: #647067;
            margin-bottom: 28px;
            font-size: 14px;
            line-height: 1.7;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 700;
            color: #3a6450;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #dfe4dd;
            border-radius: 12px;
            font-family: inherit;
            font-size: 13px;
            color: #1c2a23;
            background: #ffffff;
            transition: all 0.25s ease;
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23647067' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 36px;
        }

        input::placeholder, textarea::placeholder {
            color: #a3a8a4;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #3a6450;
            box-shadow: 0 0 0 3px rgba(58, 100, 80, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 70px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .section-divider {
            border: none;
            border-top: 1px solid #e8ece6;
            margin: 24px 0;
        }

        .btn {
            padding: 11px 26px;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            font-size: 14px;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .btn-primary {
            background: #3a6450;
            color: #fff;
            box-shadow: 0 10px 24px rgba(58, 100, 80, 0.17);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(58, 100, 80, 0.25);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.85);
            color: #1c2a23;
            border: 1px solid #dfe4dd;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(28, 42, 35, 0.06);
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 28px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid;
        }

        .alert-success {
            background: #e8f0e6;
            color: #2a5238;
            border-color: rgba(58, 100, 80, 0.2);
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border-color: rgba(153, 27, 27, 0.15);
        }

        .error-message {
            color: #dc2626;
            font-size: 11px;
            margin-top: 4px;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        th, td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #e8ece6;
            font-size: 13px;
        }

        th {
            background: #f0f4ee;
            font-weight: 700;
            color: #3a6450;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-size: 11px;
        }

        tr:hover {
            background: #f8faf6;
        }

        .action-btn {
            padding: 5px 10px;
            font-size: 11px;
            margin-right: 4px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-family: inherit;
            transition: all 0.25s ease;
        }

        .action-btn-edit {
            background: #e8f0e6;
            color: #3a6450;
            border: 1px solid rgba(58, 100, 80, 0.15);
        }

        .action-btn-edit:hover {
            background: #d4e8ce;
        }

        .action-btn-view {
            background: #eef5ff;
            color: #1d4ed8;
            border: 1px solid rgba(29, 78, 216, 0.15);
        }

        .action-btn-view:hover {
            background: #dbeafe;
        }

        .action-btn-delete {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid rgba(220, 38, 38, 0.15);
        }

        .action-btn-delete:hover {
            background: #fee2e2;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-top: 20px;
            list-style: none;
        }

        .pagination li a,
        .pagination li span {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            color: #647067;
            border: 1px solid #dfe4dd;
            transition: all 0.25s ease;
        }

        .pagination li.active span {
            background: #3a6450;
            color: #fff;
            border-color: #3a6450;
        }

        .pagination li a:hover {
            background: #e8f0e6;
            color: #3a6450;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #647067;
        }

        .empty-state p {
            font-size: 14px;
            margin-bottom: 16px;
        }

        /* ── Sidebar layout ── */
        .app-shell { display: flex; min-height: 100vh; }
        .app-sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: 250px; background: #1c2a23;
            display: flex; flex-direction: column;
            z-index: 200; padding: 0;
            transition: transform 0.25s ease;
        }
        .sb-brand {
            display: flex; align-items: center; gap: 12px;
            padding: 24px 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sb-brand img { height: 36px; width: auto; border-radius: 10px; background: #fff; padding: 4px; }
        .sb-brand span { font-size: 16px; font-weight: 800; color: #fff; letter-spacing: -0.02em; }
        .sb-nav { flex: 1; padding: 16px 12px; display: flex; flex-direction: column; gap: 4px; }
        .sb-nav a {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 16px; border-radius: 12px;
            color: rgba(255,255,255,0.6); font-size: 14px; font-weight: 600;
            text-decoration: none; transition: all 0.2s ease;
        }
        .sb-nav a:hover { color: #fff; background: rgba(255,255,255,0.08); }
        .sb-nav a.active { color: #fff; background: #3a6450; font-weight: 700; }
        .sb-nav a svg { width: 18px; height: 18px; flex-shrink: 0; }
        .sb-divider { height: 1px; background: rgba(255,255,255,0.08); margin: 8px 16px; }
        .sb-footer { padding: 12px; border-top: 1px solid rgba(255,255,255,0.08); }
        .sb-footer a {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 16px; border-radius: 12px;
            color: rgba(255,255,255,0.5); font-size: 14px; font-weight: 600;
            text-decoration: none; transition: all 0.2s ease;
        }
        .sb-footer a:hover { color: #fff; background: rgba(255,255,255,0.08); }
        .sb-footer a svg { width: 18px; height: 18px; flex-shrink: 0; }
        .app-main { flex: 1; margin-left: 250px; }
        .app-topbar {
            position: sticky; top: 0; z-index: 100;
            background: rgba(255,255,255,0.96);
            padding: 0 32px; height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid rgba(223,228,221,0.95);
            box-shadow: 0 4px 20px rgba(28,42,35,0.04);
        }
        .app-topbar-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: #1c2a23; }
        .app-topbar-brand img { height: 32px; }
        .app-topbar-brand span { font-size: 16px; font-weight: 800; color: #1c2a23; }
        .app-topbar-right { display: flex; align-items: center; gap: 14px; }
        .app-topbar-user {
            display: flex; align-items: center; gap: 10px;
            padding: 6px 14px 6px 8px; border-radius: 999px;
            background: #f0f4ee; font-size: 13px; font-weight: 700; color: #1c2a23;
            text-decoration: none; border: none;
        }
        a.app-topbar-user:hover { background: #e2eade; color: #1c2a23; }
        .app-topbar-avatar {
            position: relative;
            width: 32px; height: 32px; border-radius: 50%;
            background: #3a6450; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 800; overflow: hidden;
            flex-shrink: 0;
        }
        .app-topbar-avatar img { position: absolute; inset: 0; width: 100%; height: 100%; border-radius: 50%; object-fit: cover; display: block; }
        .sb-hamburger {
            display: none; width: 40px; height: 40px; border-radius: 10px;
            border: 1px solid #dfe4dd; background: #fff;
            align-items: center; justify-content: center; cursor: pointer;
            color: #3a6450; flex-shrink: 0;
        }
        .sb-overlay {
            display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4);
            z-index: 199;
        }
        @media (max-width: 768px) {
            .app-sidebar { transform: translateX(-100%); }
            .app-sidebar.open { transform: translateX(0); }
            .sb-overlay.open { display: block; }
            .app-main { margin-left: 0; }
            .sb-hamburger { display: flex; }
            .app-topbar { padding: 0 16px; }
            .app-topbar-brand span { display: none; }
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .navbar {
                flex-wrap: wrap;
                height: auto;
                padding: 10px 12px;
                row-gap: 6px;
            }

            .navbar > div:first-child {
                flex: 1;
                min-width: 0;
            }

            .navbar-brand {
                font-size: 15px;
                gap: 6px;
                min-width: 0;
            }

            .navbar-brand img {
                height: 32px;
                flex-shrink: 0;
            }

            .navbar-brand span {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                min-width: 0;
            }

            .nav-links {
                flex-shrink: 0;
            }

            .nav-links a {
                font-size: 13px;
                padding: 6px 10px;
            }

            .nav-links .btn {
                padding: 7px 14px;
                font-size: 13px;
            }

            .nav-links:first-of-type {
                order: 3;
                width: 100%;
                justify-content: center;
                gap: 6px;
                border-top: 1px solid #eef1ec;
                padding-top: 8px;
            }

            .container {
                padding: 24px 16px 40px;
            }

            .form-section {
                padding: 24px 20px;
                border-radius: 18px;
            }

            .form-title {
                font-size: 20px;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }

            input, select, textarea {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
@if (View::hasSection('sidebar'))
<div class="sb-overlay" id="sbOverlay" onclick="toggleSidebar()"></div>
<aside class="app-sidebar" id="appSidebar">
    <div class="sb-brand">
        <img src="{{ asset('logo.png') }}" alt="Logo" />
        <span>SMK Bahrul Ulum</span>
    </div>
    <nav class="sb-nav">
        @yield('sidebar')
    </nav>
    <div class="sb-footer">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-sb').submit();">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Keluar
        </a>
    </div>
</aside>
<form id="logout-form-sb" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
<div class="app-main">
    <div class="app-topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button class="sb-hamburger" onclick="toggleSidebar()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <a href="{{ frontendAuthUrl() }}" class="app-topbar-brand">
                <img src="{{ asset('logo.png') }}" alt="Logo SMK Bahrul Ulum" />
                <span>SMK Bahrul Ulum</span>
            </a>
        </div>
        <div class="app-topbar-right">
            @php
                $topbarRole = Auth::user()->role ?? '';
                $topbarInitial = strtoupper(mb_substr(Auth::user()->name ?? 'S', 0, 1));
            @endphp
            <a class="app-topbar-user" @if (in_array($topbarRole, ['siswa', 'pendaftar'], true)) href="{{ route('profil') }}" title="Lihat profil saya" @endif>
                <span class="app-topbar-avatar">
                    <span class="app-topbar-initial">{{ $topbarInitial }}</span>
                    @if (Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="" onerror="this.style.display='none'">
                    @endif
                </span>
                {{ Auth::user()->name ?? 'Siswa' }}
            </a>
        </div>
    </div>
    <div style="padding:28px 32px 48px;">
        @yield('content')
    </div>
</div>
<script>
function toggleSidebar() {
    document.getElementById('appSidebar').classList.toggle('open');
    document.getElementById('sbOverlay').classList.toggle('open');
}
</script>
@else
<nav class="navbar">
    <div style="display:flex;align-items:center;gap:12px;">
        @auth
        <a href="{{ frontendAuthUrl() }}" aria-label="Kembali ke beranda"
           style="width:36px;height:36px;border-radius:10px;border:1px solid #dfe4dd;background:#fff;display:flex;align-items:center;justify-content:center;text-decoration:none;color:#3a6450;transition:all 0.25s ease;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5"></path>
                <path d="M12 19l-7-7 7-7"></path>
            </svg>
        </a>
        @endauth
        <a href="{{ frontendAuthUrl() }}" class="navbar-brand">
            <img src="{{ asset('logo.png') }}" alt="Logo SMK Bahrul Ulum" style="height:38px;width:auto;vertical-align:middle;" />
            <span>SMK Bahrul Ulum</span>
        </a>
    </div>
</nav>
    @yield('content')
@endif
</body>
</html>
