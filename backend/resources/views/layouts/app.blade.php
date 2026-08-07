<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SPMB - SMK Bahrul Ulum')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
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

        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
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
            max-width: 820px;
            margin: 0 auto;
            padding: 40px 24px 60px;
        }

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

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .navbar {
                padding: 0 18px;
                height: 60px;
            }

            .nav-links a {
                font-size: 13px;
                padding: 6px 10px;
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
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div style="display:flex;align-items:center;gap:12px;">
        @auth
        <a href="http://localhost:5174/?no-intro=1" aria-label="Kembali ke beranda"
           style="width:36px;height:36px;border-radius:10px;border:1px solid #dfe4dd;background:#fff;display:flex;align-items:center;justify-content:center;text-decoration:none;color:#3a6450;transition:all 0.25s ease;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5"></path>
                <path d="M12 19l-7-7 7-7"></path>
            </svg>
        </a>
        @endauth
        <a href="http://localhost:5174/?no-intro=1" class="navbar-brand">
            <img src="{{ asset('logo.png') }}" alt="Logo SMK Bahrul Ulum" style="height:38px;width:auto;vertical-align:middle;" />
            <span>SMK Bahrul Ulum</span>
        </a>
    </div>
    <div class="nav-links">
        @auth
        {{-- Logged in: no Beranda/Formulir/Dashboard links, ganti tombol back di atas --}}
        @else
        <a href="http://localhost:5174/" target="_blank" rel="noopener noreferrer">Beranda</a>
        <a href="/pendaftaran/create">Formulir</a>
        @endauth
    </div>

    <div class="nav-links">
        @auth
        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="action-btn action-btn-edit" style="font-size:13px;">Logout</button>
        </form>
        @else
        <a href="{{ route('login') }}" class="btn btn-secondary" style="padding:8px 18px;">Masuk</a>
        <a href="{{ route('register') }}" class="btn btn-primary" style="padding:8px 18px;">Daftar</a>
        @endauth
    </div>
</nav>

    <div class="container">
        @yield('content')
    </div>
</body>
</html>
