<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran - SMK Bahrul Ulum</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Quicksand', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #eef6ee;
            color: #2f3f2f;
            min-height: 100vh;
            padding-top: 110px;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            left: 50%;
            top: 24px;
            transform: translateX(-50%);
            width: min(1180px, 92%);
            height: 74px;
            padding: 0 24px;
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(18px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 28px;
            border: 1px solid rgba(223, 228, 221, 0.9);
            box-shadow: 0 16px 36px rgba(28, 42, 35, 0.08);
            transition: all 0.35s ease;
            z-index: 999;
        }

        .navbar.shrink {
            height: 64px;
            width: min(980px, 88%);
            border-radius: 999px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: #1c2a23;
        }

        .logo-img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .logo:hover .logo-img {
            transform: rotate(-5deg) scale(1.05);
        }

        .logo h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            color: #1c2a23;
        }

        .logo span {
            font-size: 13px;
            color: #3a6450;
            font-weight: 600;
        }

        .desktop-nav {
            display: flex;
            gap: 28px;
        }

        .desktop-nav a {
            text-decoration: none;
            color: #5b6475;
            font-weight: 700;
            font-size: 15px;
            transition: color 0.25s ease;
        }

        header,
        .navbar,
        .desktop-nav a,
        .ppdb,
        .logo h2,
        .logo span,
        .btn-masuk,
        label,
        .step-text h4,
        .step-text p,
        .sidebar-header h3,
        .sidebar-header p,
        .sidebar-header small,
        .form-group label,
        .form-group input,
        .form-group select,
        .form-group textarea,
        .form-actions button {
            font-family: 'Quicksand', sans-serif;
        }

        .desktop-nav a:hover,
        .desktop-nav a.active {
            color: #3a6450;
        }

        .ppdb {
            height: 48px;
            padding: 0 22px;
            border: none;
            border-radius: 16px;
            background: #3a6450;
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 10px 24px rgba(58, 100, 80, 0.2);
        }

        .ppdb:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 30px rgba(58, 100, 80, 0.25);
        }

        .nav-auth {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .btn-dashboard {
            display: inline-flex;
            align-items: center;
            height: 44px;
            padding: 0 18px;
            border-radius: 14px;
            background: #3a6450;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            box-shadow: 0 8px 20px rgba(58, 100, 80, 0.18);
            transition: all 0.25s ease;
        }

        .btn-dashboard:hover {
            background: #2a5238;
            transform: translateY(-1px);
        }

        .btn-logout {
            display: inline-flex;
            align-items: center;
            height: 44px;
            padding: 0 16px;
            border: 1px solid #dfe4dd;
            border-radius: 14px;
            background: #fff;
            color: #5b6475;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .btn-logout:hover {
            border-color: #c8d0c9;
            color: #b91c1c;
        }

        .menu {
            display: none;
            width: 42px;
            height: 42px;
            padding: 0;
            border: 1px solid #dfe4dd;
            border-radius: 12px;
            background: #fff;
            cursor: pointer;
        }

        .menu span {
            display: block;
            width: 19px;
            height: 2px;
            margin: 4px auto;
            border-radius: 999px;
            background: #1c2a23;
            transition: transform 0.3s ease, opacity 0.2s ease;
        }

        .menu.active span:nth-child(1) {
            transform: translateY(6px) rotate(45deg);
        }

        .menu.active span:nth-child(2) {
            opacity: 0;
        }

        .menu.active span:nth-child(3) {
            transform: translateY(-6px) rotate(-45deg);
        }

        .mobile-nav {
            display: none;
        }

        @media (max-width: 900px) {
            .desktop-nav,
            .ppdb {
                display: none;
            }

            .menu {
                display: block;
            }

            .mobile-nav {
                position: absolute;
                top: calc(100% + 12px);
                right: 0;
                left: 0;
                display: flex;
                flex-direction: column;
                gap: 4px;
                padding: 12px;
                border: 1px solid rgba(223, 228, 221, 0.95);
                border-radius: 20px;
                background: rgba(251, 252, 250, 0.98);
                box-shadow: 0 18px 38px rgba(35, 55, 42, 0.12);
                backdrop-filter: blur(18px);
            }

            .mobile-nav a {
                padding: 13px 15px;
                border-radius: 12px;
                color: #1c2a23;
                font-size: 15px;
                font-weight: 700;
                text-decoration: none;
                transition: background 0.2s ease, color 0.2s ease;
            }

            .mobile-nav a:hover {
                background: #e8f0e6;
                color: #3a6450;
            }

            .mobile-nav .mobile-ppdb {
                margin-top: 4px;
                background: #3a6450;
                color: #fff;
                text-align: center;
            }

            .mobile-nav .mobile-ppdb:hover {
                background: #2a5238;
                color: #fff;
            }

            .logo h2 {
                font-size: 18px;
            }

            .navbar {
                height: 66px;
                padding: 0 18px;
                width: 92%;
            }

            .nav-auth {
                gap: 6px;
            }

            .btn-dashboard,
            .btn-logout {
                height: 38px;
                padding: 0 12px;
                font-size: 13px;
            }
        }

        /* Main Container */
        .container {
            max-width: 1240px;
            margin: 2.25rem auto 3rem;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 1.75rem;
        }

        /* Sidebar */
        .sidebar {
            background: white;
            border: 1px solid #e8efe7;
            border-radius: 28px;
            padding: 1.75rem;
            box-shadow: 0 24px 60px rgba(34, 90, 54, 0.06);
            height: fit-content;
            position: sticky;
            top: 2rem;
        }

        .sidebar-header {
            margin-bottom: 1.5rem;
        }

        .sidebar-header h3 {
            font-size: 1.35rem;
            margin-bottom: 0.5rem;
            color: #1f3424;
        }

        .sidebar-header p {
            font-size: 0.85rem;
            color: #999;
            margin: 0;
        }

        .sidebar-header small {
            font-size: 0.75rem;
            color: #3a6450;
            display: block;
            margin-top: 0.5rem;
        }

        .steps-list {
            margin: 2rem 0;
        }

        .step {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            align-items: flex-start;
            opacity: 0.85;
            transition: all 0.25s ease;
            padding: 0.95rem 0.85rem;
            border-radius: 20px;
            border: 1px solid transparent;
        }

        .step.active {
            opacity: 1;
            border-color: #d6e6d6;
            background: #eef7ee;
        }

        .step.completed {
            opacity: 1;
        }

        .step-circle {
            min-width: 32px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #eef4ec;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.92rem;
            color: #4c6b52;
            flex-shrink: 0;
            border: 1px solid #d8e5d6;
        }

        .step.active .step-circle {
            background: #3a6450;
            color: white;
        }

        .step.completed .step-circle {
            background: #4caf50;
            color: white;
        }

        .step-text h4 {
            font-size: 0.9rem;
            margin: 0 0 0.25rem;
            color: #333;
        }

        .step-text p {
            font-size: 0.8rem;
            color: #999;
            margin: 0;
        }

        .help-box {
            position: relative;
            background: #fdfdfc;
            border: 1px solid rgba(58, 100, 80, 0.12);
            border-radius: 32px;
            padding: 1.75rem 1.5rem;
            margin-top: 2rem;
            display: block;
            min-height: 154px;
            box-shadow: 0 18px 36px rgba(33, 81, 44, 0.08);
            overflow: hidden;
        }

        .help-box-content {
            text-align: left;
            max-width: 100%;
        }

        .help-box-icon {
            position: absolute;
            top: 0.5rem;
            right: 1.2rem;
            width: 64px;
            height: 64px;
            display: grid;
            place-items: center;
            /* background: rgba(58, 100, 80, 0.1); */
            border-radius: 20px;
            /* border: 1px solid rgba(58, 100, 80, 0.16); */
            overflow: hidden;
        }

        .help-box-icon img {
            width: 25px;
            height: 25px;
            object-fit: contain;
            display: block;
        }

        .help-box h4 {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            color: #1b3425;
            font-weight: 700;
        }

        .help-box p {
            font-size: 0.95rem;
            color: #5f6f61;
            margin-bottom: 1.2rem;
            line-height: 1.6;
            max-width: 220px;
        }

        .help-box-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #2b5a36;
            color: #fff;
            text-decoration: none;
            padding: 0.95rem 1.4rem;
            border-radius: 16px;
            font-size: 0.95rem;
            font-weight: 700;
            transition: transform 0.25s ease, background 0.25s ease;
            box-shadow: 0 12px 24px rgba(43, 90, 54, 0.18);
        }

        .help-box-button:hover {
            background: #234b30;
            transform: translateY(-1px);
        }

        .help-box a:hover {
            background: #1d4f30;
            transform: translateY(-1px);
        }


        .main-content {
            background: white;
            border: 1px solid #e9f0e7;
            border-radius: 28px;
            padding: 2.4rem;
            box-shadow: 0 24px 65px rgba(34, 90, 54, 0.07);
        }

        .form-header {
            margin-bottom: 2rem;
        }

        .form-header h2 {
            font-size: 1.7rem;
            margin-bottom: 0.5rem;
            color: #1e3724;
        }

        .form-header p {
            font-size: 0.9rem;
            color: #666;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.95rem 1rem;
            border: 1px solid #d8dfd8;
            border-radius: 14px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.25s ease;
            background: #fbfcfb;
        }

        .form-group input::placeholder,
        .form-group select::placeholder,
        .form-group textarea::placeholder {
            color: #bbb;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3a6450;
            box-shadow: 0 0 0 3px rgba(58, 100, 80, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e8f0e7;
            align-items: center;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .btn-prev {
            background: #f0f0f0;
            color: #333;
            flex: 1;
            display: none;
        }

        .btn-prev:hover {
            background: #e0e0e0;
        }

        .btn-next {
            background: #225a36;
            color: white;
            flex: 2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            border-radius: 14px;
            min-height: 48px;
            padding: 0 1.5rem;
        }

        .btn-next:hover {
            background: #1f4f31;
        }

        .btn-next:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .input-error {
            border-color: #dc2626 !important;
            background: #fef2f2 !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12) !important;
        }

        .notif {
            position: fixed;
            top: 104px;
            left: 50%;
            transform: translate(-50%, -8px);
            z-index: 1200;
            padding: 14px 24px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 14px;
            color: #fff;
            background: #3a6450;
            box-shadow: 0 18px 44px rgba(28, 42, 35, 0.22);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease, transform 0.3s ease;
            max-width: min(90vw, 520px);
            text-align: center;
        }

        .notif.show {
            opacity: 1;
            transform: translate(-50%, 0);
        }

        .notif.error {
            background: #b91c1c;
        }

        .confirm-modal {
            position: fixed;
            inset: 0;
            z-index: 1500;
            background: rgba(28, 42, 35, 0.5);
            backdrop-filter: blur(5px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .confirm-modal.show {
            display: flex;
        }

        .confirm-box {
            width: min(420px, 92%);
            background: #fbfcfa;
            border: 1px solid #dfe4dd;
            border-radius: 22px;
            padding: 30px 28px;
            text-align: center;
            box-shadow: 0 24px 60px rgba(28, 42, 35, 0.25);
            animation: modalPop 0.25s ease;
        }

        @keyframes modalPop {
            from { transform: scale(0.92); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .confirm-icon {
            width: 58px;
            height: 58px;
            margin: 0 auto 16px;
            border-radius: 50%;
            background: #fef3c7;
            color: #b45309;
            display: grid;
            place-items: center;
        }

        .confirm-box h3 {
            margin: 0 0 10px;
            font-size: 19px;
            font-weight: 800;
            color: #1c2a23;
            letter-spacing: -0.02em;
        }

        .confirm-box p {
            margin: 0 0 24px;
            font-size: 14px;
            color: #647067;
            line-height: 1.7;
        }

        .confirm-actions {
            display: flex;
            gap: 10px;
        }

        .confirm-cancel {
            flex: 1;
            display: block;
            height: 46px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
        }

        .confirm-ok {
            flex: 1;
            display: flex;
            height: 46px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
        }


        /* Form Steps */
        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 900px) {
            .container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .navbar-nav {
                display: none;
            }
        }

        @media (max-width: 600px) {
            header {
                padding: 1rem;
                flex-direction: column;
            }

            .navbar-brand {
                margin-bottom: 1rem;
            }

            .main-content {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header/Navbar -->
    <header class="navbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ frontendAuthUrl() }}" aria-label="Kembali ke beranda"
               style="width:36px;height:36px;border-radius:10px;border:1px solid #dfe4dd;background:#fff;display:flex;align-items:center;justify-content:center;text-decoration:none;color:#3a6450;transition:all 0.25s ease;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5"></path>
                    <path d="M12 19l-7-7 7-7"></path>
                </svg>
            </a>
            <a href="{{ frontendAuthUrl() }}" class="logo">
                <img src="{{ asset('logo.png') }}" alt="Logo SMK Bahrul Ulum" class="logo-img" />
                <div>
                    <h2>SMK Bahrul Ulum</h2>
                    <span>Smart School</span>
                </div>
            </a>
        </div>

        <nav class="desktop-nav">
            <a href="{{ frontendAuthUrl() }}">Beranda</a>
            <a href="{{ frontendAuthUrl() }}#layanan">Layanan</a>
            <a href="{{ frontendAuthUrl() }}#tentang">Tentang</a>
            <a href="{{ frontendAuthUrl() }}#contact">Kontak</a>
        </nav>

        @auth
        <div class="nav-auth">
            <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
        @endauth
    </header>

    <div id="notif" class="notif"></div>

    <div id="confirmModal" class="confirm-modal" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
        <div class="confirm-box">
            <div class="confirm-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <h3 id="confirmTitle">Periksa Kembali Data Kamu Ya Sob</h3>
            <p>Apakah data yang Kamu isi sudah benar? Mohon periksa kembali sebelum mengirim ya :D.</p>
            <div class="confirm-actions">
                <button type="button" class="btn btn-prev confirm-cancel" id="confirmCancel">Cek Lagi :/</button>
                <button type="button" class="btn btn-next confirm-ok" id="confirmOk">Ya, Kirim</button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <small>SPMB 2025/2026</small>
                <h3>Pendaftaran</h3>
                <p>Lengkapi data diri kamu dengan benar ya sob untuk memulai proses pendaftaran SPMB SMK Bahrul Ulum.</p>
            </div>

            <div class="steps-list">
                <div class="step active" data-step="1">
                    <div class="step-circle">1</div>
                    <div class="step-text">
                        <h4>Data Diri</h4>
                        <p>Lengkapi identitas diri calon peserta</p>
                    </div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-circle">2</div>
                    <div class="step-text">
                        <h4>Data Sekolah</h4>
                        <p>Informasi sekolah asal dan nilai</p>
                    </div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-circle">3</div>
                    <div class="step-text">
                        <h4>Data Orang Tua</h4>
                        <p>Informasi orang tua / wali</p>
                    </div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-circle">4</div>
                    <div class="step-text">
                        <h4>Unggah Berkas</h4>
                        <p>Upload dokumen persyaratan</p>
                    </div>
                </div>
                <div class="step" data-step="5">
                    <div class="step-circle">5</div>
                    <div class="step-text">
                        <h4>Konfirmasi</h4>
                        <p>Periksa & kirim pendaftaran</p>
                    </div>
                </div>
            </div>

            <div class="help-box">
                <div class="help-box-content">
                    <h4>Butuh Bantuan?</h4>
                    <p>Jika mengalami kendala saat pendaftaran, hubungi tim kami segera.</p>
                    <a href="#" class="help-box-button">
                        <span>Hubungi Kami</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"></path>
                            <path d="M13 6l6 6-6 6"></path>
                        </svg>
                    </a>
                </div>
                <div class="help-box-icon" aria-hidden="true">
                    <img src="{{ asset('cs.png') }}" alt="Customer Support">
                </div>
            </div>
        </aside>

        <!-- Main Form -->
        <main class="main-content">
            <div class="form-header">
                <h2>Data Diri Calon Peserta</h2>
                <p>Pastikan semua informasi diisi dengan benar.</p>
            </div>

            @if ($errors->any())
            <div style="background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;border-radius:8px;padding:14px 18px;margin-bottom:20px;font-size:14px;line-height:1.7;">
                @foreach ($errors->all() as $error)
                     {{ $error }}<br>
                @endforeach
            </div>
            @endif

            @if (session('error'))
            <div style="background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;border-radius:8px;padding:14px 18px;margin-bottom:20px;font-size:14px;">
                ⚠️ {{ session('error') }}
            </div>
            @endif

            @if (session('success'))
            <div style="background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;border-radius:8px;padding:14px 18px;margin-bottom:20px;font-size:14px;">
                {{ session('success') }}
            </div>
            @endif

            <form id="registrationForm" method="POST" action="{{ route('pendaftaran.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Step 1 -->
                <div class="form-step active" data-step="1">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Panggilan</label>
                            <input type="text" name="nama_panggilan" placeholder="Contoh: Ahmad" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>NISN</label>
                            <input type="text" name="nisn" placeholder="Masukkan NISN" required>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" placeholder="dd/mm/yyyy" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" placeholder="Masukkan tempat lahir" required>
                        </div>
                        <div class="form-group">
                            <label>Umur</label>
                            <input type="number" name="umur" placeholder="Masukkan umur" min="4" max="25" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Agama</label>
                            <select name="agama" required>
                                <option value="Islam">Islam</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" required>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Kewarnegaraan</label>
                            <select name="kewarnegaraan" required>
                                <option value="Indonesia">Indonesia</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Kategori Pendaftar</label>
                            <select name="kategori_pendaftar" required>
                                <option value="SMP Bahrul Ulum">SMP Bahrul Ulum</option>
                                <option value="SMP Umum">Yatim Piatu</option>
                                <option value="SMP Yatim">Yatim</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Alamat Lengkap</label>
                            <textarea name="alamat" placeholder="Masukkan alamat lengkap" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>RT / RW</label>
                            <input type="text" name="rt_rw" placeholder="Contoh: RT.01 / RW.12" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Kode Pos</label>
                            <input type="text" name="kode_pos" placeholder="Contoh: 60295" required>
                        </div>
                        <div class="form-group">
                            <label>No. Telepon / WhatsApp</label>
                            <input type="tel" name="no_hp" placeholder="081234567890" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email (Opsional)</label>
                            <input type="email" name="email" placeholder="Masukkan email">
                        </div>
                        <div class="form-group"></div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="form-step" data-step="2">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Asal Sekolah</label>
                            <input type="text" name="asal_sekolah" placeholder="Tamatan dari" required>
                        </div>
                        <div class="form-group">
                            <label>Gelombang</label>
                            <select name="gelombang" required>
                                <option value="Grand Opening">Grand Opening</option>
                                <option value="Gelombang 1">Gelombang 1</option>
                                <option value="Gelombang 2">Gelombang 2</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Tahun Lulus</label>
                            <input type="number" name="tahun_lulus" placeholder="Contoh: 2025" min="2015" max="2035" required>
                        </div>
                        <div class="form-group">
                            <label>Rata-rata Nilai</label>
                            <input type="text" name="rata_rata_nilai" placeholder="Contoh: 85.50" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Jurusan Pilihan</label>
                            <select name="jurusan_pilihan" required>
                                <option value="RPL">Rekayasa Perangkat Lunak (RPL)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jumlah Saudara Kandung</label>
                            <select name="jumlah_saudara" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Anak Ke-</label>
                            <select name="anak_ke" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status Keluarga</label>
                            <select name="status_keluarga" required>
                                <option value="Anak Kandung">Anak Kandung</option>
                                <option value="Anak Angkat">Anak Angkat</option>
                                <option value="Yatim">Yatim</option>
                                <option value="Yatim Piatu">Yatim Piatu</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="form-step" data-step="3">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Ayah</label>
                            <input type="text" name="nama_ayah" placeholder="Nama ayah" required>
                        </div>
                        <div class="form-group">
                            <label>Pendidikan Ayah</label>
                            <select name="pendidikan_ayah" required>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA">SMA</option>
                                <option value="DIPLOMA">DIPLOMA</option>
                                <option value="SARJANA">SARJANA</option>
                                <option value="Lain-lain">Lain-lain</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Pekerjaan Ayah</label>
                            <select name="pekerjaan_ayah" required>
                                <option value="PNS">PNS</option>
                                <option value="ABRI">ABRI</option>
                                <option value="PKL">PKL</option>
                                <option value="Buruh">Buruh</option>
                                <option value="Dagang">Dagang</option>
                                <option value="Lain-lain">Lain-lain</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Penghasilan Ayah</label>
                            <select name="penghasilan_ayah" required>
                                <option value="< Rp. 1.000.000">&lt; Rp. 1.000.000</option>
                                <option value="1.000.000 - 2.000.000">Rp. 1.000.000 - 2.000.000</option>
                                <option value="> Rp. 2.000.000">&gt; Rp. 2.000.000</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Alamat Ayah</label>
                            <textarea name="alamat_ayah" placeholder="Alamat ayah" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>No. Telp./Hp Ayah</label>
                            <input type="tel" name="hp_ayah" placeholder="081234567890" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Ibu</label>
                            <input type="text" name="nama_ibu" placeholder="Nama ibu" required>
                        </div>
                        <div class="form-group">
                            <label>Pendidikan Ibu</label>
                            <select name="pendidikan_ibu" required>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA">SMA</option>
                                <option value="DIPLOMA">DIPLOMA</option>
                                <option value="SARJANA">SARJANA</option>
                                <option value="Lain-lain">Lain-lain</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Pekerjaan Ibu</label>
                            <select name="pekerjaan_ibu" required>
                                <option value="PNS">PNS</option>
                                <option value="ABRI">ABRI</option>
                                <option value="PKL">PKL</option>
                                <option value="Buruh">Buruh</option>
                                <option value="Dagang">Dagang</option>
                                <option value="Lain-lain">Lain-lain</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Penghasilan Ibu</label>
                            <select name="penghasilan_ibu" required>
                                <option value="< Rp. 1.000.000">&lt; kurang dari Rp. 1.000.000</option>
                                <option value="< Rp. 1.000.000">&lt; Rp. 1.000.000</option>
                                <option value="1.000.000 - 2.000.000">Rp. 1.000.000 - 2.000.000</option>
                                <option value="> Rp. 2.000.000">&gt; Rp. 2.000.000</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Alamat Ibu</label>
                            <textarea name="alamat_ibu" placeholder="Alamat ibu" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>No. Telp./Hp Ibu</label>
                            <input type="tel" name="hp_ibu" placeholder="081234567890" required>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="form-step" data-step="4">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Wali (opsional)</label>
                            <input type="text" name="nama_wali" placeholder="Nama wali jika ada">
                        </div>
                        <div class="form-group">
                            <label>Hubungan dengan Wali</label>
                            <input type="text" name="hubungan_wali" placeholder="Contoh: Paman / Nenek">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email Orang Tua/Wali</label>
                            <input type="email" name="email_orang_tua" placeholder="Contoh: admin@gmail.com" required>
                        </div>
                        <div class="form-group">
                            <label>Jenis Pembayaran</label>
                            <select name="jenis_pembayaran" required>
                                <option value="Transfer">Transfer</option>
                                <option value="Tunai">Tunai</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Berkas Tambahan</label>
                        <textarea name="berkas_tambahan" placeholder="Catatan dokumen tambahan (misal: KIP, PKH, SKTM)" rows="4"></textarea>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="form-step" data-step="5">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Unggah Foto 3x4</label>
                            <input type="file" name="foto_3x4">
                        </div>
                        <div class="form-group">
                            <label>Unggah Kartu Keluarga</label>
                            <input type="file" name="kk_file">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Unggah Ijazah/Raport</label>
                            <input type="file" name="ijazah_file">
                        </div>
                        <div class="form-group">
                            <label>Unggah Surat Keterangan</label>
                            <input type="file" name="sktm_file">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="agreedToTerms" required>
                            Saya setuju dengan syarat dan ketentuan pendaftaran
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-prev" id="prevBtn" onclick="changeStep(-1)">Sebelumnya</button>
                    <button type="button" class="btn btn-next" id="nextBtn" onclick="changeStep(1)">Selanjutnya</button>
                    <button type="submit" class="btn btn-next" id="submitBtn" style="display: none;">Kirim Pendaftaran</button>
                </div>
            </form>
        </main>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 5;

        function showNotif(message, type = 'success') {
            const notif = document.getElementById('notif');
            notif.textContent = message;
            notif.className = 'notif show ' + type;
            clearTimeout(showNotif._t);
            showNotif._t = setTimeout(() => notif.classList.remove('show'), 3000);
        }

        function validateStep(step) {
            const stepEl = document.querySelector(`.form-step[data-step="${step}"]`);
            if (!stepEl) return { valid: true, firstInvalid: null, count: 0 };
            const fields = stepEl.querySelectorAll('[required]');
            let count = 0;
            let firstInvalid = null;
            fields.forEach(f => {
                const ok = f.type === 'checkbox' ? f.checked : f.value.trim() !== '';
                f.classList.toggle('input-error', !ok);
                if (!ok) {
                    count++;
                    if (!firstInvalid) firstInvalid = f;
                }
            });
            return { valid: count === 0, firstInvalid, count };
        }

        function changeStep(direction) {
            if (direction > 0) {
                const r = validateStep(currentStep);
                if (!r.valid) {
                    showNotif(`Harap lengkapi ${r.count} kolom wajib yang ditandai merah sebelum lanjut ya.`, 'error');
                    r.firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }
            }
            const newStep = currentStep + direction;
            if (newStep >= 1 && newStep <= totalSteps) {
                currentStep = newStep;
                updateUI();
            }
        }

        document.querySelectorAll('#registrationForm [required]').forEach(f => {
            ['input', 'change'].forEach(ev => f.addEventListener(ev, () => f.classList.remove('input-error')));
        });

        const form = document.getElementById('registrationForm');
        form.addEventListener('submit', function (e) {
            let allValid = true;
            let firstInvalid = null;
            let invalidStep = null;

            for (let s = 1; s <= totalSteps; s++) {
                const r = validateStep(s);
                if (!r.valid) {
                    allValid = false;
                    if (!firstInvalid) {
                        firstInvalid = r.firstInvalid;
                        invalidStep = s;
                    }
                }
            }

            if (!allValid) {
                e.preventDefault();
                currentStep = invalidStep;
                updateUI();
                showNotif('Ada kolom wajib yang belum diisi nih. Lengkapi data yang ditandai merah itu ya.', 'error');
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            e.preventDefault();

            const confirmModal = document.getElementById('confirmModal');

            const closeConfirm = () => confirmModal.classList.remove('show');

            document.getElementById('confirmOk').onclick = () => {
                closeConfirm();
                const btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.textContent = 'Mengirim...';
                showNotif('Yey Pendaftaran berhasil dikirim!', 'success');
                setTimeout(() => form.submit(), 1200);
            };
            document.getElementById('confirmCancel').onclick = closeConfirm;
            confirmModal.onclick = (e) => { if (e.target === confirmModal) closeConfirm(); };
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeConfirm(); }, { once: true });

            confirmModal.classList.add('show');
        });

        function updateUI() {
            // Update form steps
            document.querySelectorAll('.form-step').forEach(el => {
                el.classList.remove('active');
            });
            document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.add('active');

            // Update sidebar steps
            document.querySelectorAll('.step').forEach((el, idx) => {
                el.classList.remove('active', 'completed');
                const circle = el.querySelector('.step-circle');
                if (idx + 1 === currentStep) {
                    el.classList.add('active');
                    circle.textContent = (idx + 1);
                } else if (idx + 1 < currentStep) {
                    el.classList.add('completed');
                    circle.innerHTML = '&#10003;';
                } else {
                    circle.textContent = (idx + 1);
                }
            });

            // Update buttons
            document.getElementById('prevBtn').style.display = currentStep > 1 ? 'block' : 'none';
            document.getElementById('nextBtn').style.display = currentStep < totalSteps ? 'block' : 'none';
            document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'block' : 'none';
        }

        updateUI();

        // Prefill form dari draft (data tersimpan sementara utk guest / saat validasi error)
        const draft = @json($draft ?? null);
        if (draft) {
            document.querySelectorAll('#registrationForm [name]').forEach(el => {
                const name = el.getAttribute('name');
                if (draft[name] !== undefined && el.type !== 'file' && el.type !== 'hidden') {
                    el.value = draft[name];
                    if (el.type === 'checkbox') {
                        el.checked = !!draft[name];
                    }
                }
            });
        }
    </script>
</body>
</html>
