<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Admin - SPMB SMK Bahrul Ulum')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* ===== Sidebar ===== */
        .sidebar {
            width: 248px;
            flex-shrink: 0;
            background: linear-gradient(180deg, #1f3d2e 0%, #2a5238 100%);
            color: #e8f0e6;
            display: flex;
            flex-direction: column;
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 200;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 22px 20px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            text-decoration: none;
        }

        .sidebar-brand img {
            height: 40px;
            width: auto;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.9);
            padding: 3px;
        }

        .sidebar-brand-text {
            font-size: 16px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            line-height: 1.25;
        }

        .sidebar-brand-text small {
            display: block;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #9fd0b1;
            margin-top: 2px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 18px 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .sidebar-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: rgba(232, 240, 230, 0.55);
            padding: 0 10px;
            margin-bottom: 4px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 12px;
            color: rgba(232, 240, 230, 0.85);
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .sidebar-link svg {
            flex-shrink: 0;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
        }

        .sidebar-link.active {
            background: #ffffff;
            color: #2a5238;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.18);
        }

        .sidebar-link .sidebar-new-badge {
            margin-left: auto;
            background: #e2564d;
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            border-radius: 999px;
            padding: 1px 8px;
            line-height: 1.6;
        }

        .sidebar-footer {
            padding: 16px 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }

        .sidebar-footer .btn {
            width: 100%;
            justify-content: center;
        }

        /* ===== Main ===== */
        .main {
            flex: 1;
            margin-left: 248px;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.96);
            border-bottom: 1px solid rgba(223, 228, 221, 0.95);
            box-shadow: 0 8px 30px rgba(28, 42, 35, 0.06);
            padding: 0 32px;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .topbar-title {
            font-size: 18px;
            font-weight: 800;
            color: #1c2a23;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            min-width: 0;
        }

        .hamburger {
            display: none;
            background: #3a6450;
            color: #fff;
            border: none;
            border-radius: 10px;
            width: 40px;
            height: 40px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }

        .main-content {
            max-width: 1180px;
            width: 100%;
            margin: 0 auto;
            padding: 36px 32px 60px;
        }

        .sidebar-backdrop {
            display: none;
        }

        /* ===== Tabel responsif ===== */
        .table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 12px;
        }

        .table-wrap table {
            min-width: 560px;
        }

        .table-wrap th,
        .table-wrap td {
            white-space: nowrap;
        }

        /* ===== Komponen umum (tetap) ===== */
        .form-section {
            background: #fbfcfa;
            border: 1px solid #dfe4dd;
            padding: 36px 40px;
            border-radius: 22px;
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
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

        .btn-outline-light {
            background: transparent;
            color: #e8f0e6;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.1);
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

        @media (max-width: 900px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-backdrop {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(28, 42, 35, 0.5);
                z-index: 150;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.3s ease;
            }

            .sidebar-backdrop.open {
                opacity: 1;
                pointer-events: auto;
            }

            .main {
                margin-left: 0;
            }

            .hamburger {
                display: inline-flex;
            }

            .topbar {
                padding: 0 16px;
                height: 60px;
            }

            .main-content {
                padding: 24px 16px 40px;
            }

            .form-section {
                padding: 24px 20px;
                border-radius: 18px;
            }

            .form-title {
                font-size: 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }

            .sidebar-link {
                min-height: 46px;
            }

            input, select, textarea {
                font-size: 16px;
            }

            .hide-sm {
                display: none !important;
            }

            .table-wrap table {
                min-width: 480px;
            }
        }
    </style>
</head>
<body>
<div class="layout">
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar(false)"></div>

    <aside class="sidebar" id="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <img src="{{ asset('logo.png') }}" alt="Logo SMK Bahrul Ulum" />
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

    @auth
    (function newBadgePoll() {
        const badge = document.getElementById('newBadge');
        if (!badge) return;
        const key = 'spmb_admin_latest_id';
        let baseline = parseInt(sessionStorage.getItem(key) || '0', 10);

        async function poll() {
            try {
                const res = await fetch('{{ route("pendaftaran.snapshot") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
                if (!res.ok) return;
                const data = await res.json();
                if (!data.latest_id) return;
                if (!baseline) {
                    baseline = data.latest_id;
                    sessionStorage.setItem(key, String(baseline));
                    return;
                }
                if (data.latest_id > baseline) {
                    badge.textContent = data.latest_id - baseline;
                    badge.style.display = 'inline-block';
                }
            } catch (e) {}
        }
        setInterval(poll, 20000);
        poll();
    })();
    @endauth
</script>
</body>
</html>