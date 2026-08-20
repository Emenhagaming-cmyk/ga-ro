<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SPMB - SMK Bahrul Ulum')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Quicksand', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #214936 0%, #2f5b45 55%, #3a6450 100%);
            background-attachment: fixed;
            color: #1c2a23;
            line-height: 1.6;
            min-height: 100vh;
        }

        .auth-wrapper {
            position: relative;
            display: flex;
            min-height: 100vh;
            overflow: hidden;
            padding: 40px 24px;
        }

        .auth-illustration {
            position: absolute;
            inset: 0;
            overflow: hidden;
        }

        .auth-blob {
            position: absolute;
            border-radius: 42% 58% 63% 37% / 46% 38% 62% 54%;
            filter: blur(2px);
            opacity: 0.9;
        }

        .blob-1 {
            width: 380px;
            height: 380px;
            top: -80px;
            left: -80px;
            background: rgba(255, 255, 255, 0.10);
        }

        .blob-2 {
            width: 280px;
            height: 280px;
            bottom: -60px;
            right: -60px;
            background: rgba(125, 184, 141, 0.28);
        }

        .blob-3 {
            width: 180px;
            height: 180px;
            top: 60%;
            left: 8%;
            background: rgba(255, 255, 255, 0.10);
        }

        .blob-4 {
            width: 120px;
            height: 120px;
            top: 12%;
            right: 10%;
            background: rgba(125, 184, 141, 0.22);
        }

        .auth-form-area {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 28px;
            width: 100%;
        }

        .auth-illustration-content {
            position: static;
            color: #ffffff;
            max-width: 420px;
            text-align: center;
            padding: 0 16px;
        }

        .auth-illustration-content .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .auth-illustration-content .brand img {
            height: 44px;
            width: 44px;
            border-radius: 12px;
            background: #fff;
            padding: 6px;
        }

        .auth-illustration-content .brand span {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .auth-illustration-content h1 {
            font-size: 26px;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.03em;
            margin-bottom: 10px;
        }

        .auth-illustration-content p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.7;
        }

        .auth-card {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.28);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 36px 36px;
            box-shadow: 0 30px 70px rgba(13, 26, 20, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.42);
            position: relative;
            overflow: hidden;
        }

        .auth-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #7db88d 0%, #3a6450 58%, #2a5238 100%);
        }

        .form-title {
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 6px;
            color: #edf7f2;
            letter-spacing: -0.03em;
        }

        .form-subtitle {
            color: #0a0a0a;
            margin-bottom: 26px;
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
            color: #ffffff;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #7d8a80;
        }

        .input-wrap input {
            width: 100%;
            padding: 11px 14px 11px 44px;
            border: 1.5px solid #dfe4dd;
            border-radius: 12px;
            font-family: inherit;
            font-size: 13px;
            color: #020202;
            background: #ffffff;
            transition: all 0.25s ease;
        }

        .input-wrap input::placeholder {
            color: #000000;
        }

        .input-wrap input:focus {
            outline: none;
            border-color: #3a6450;
            box-shadow: 0 0 0 3px rgba(58, 100, 80, 0.1);
        }

        .btn {
            width: 100%;
            padding: 12px 26px;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            font-size: 15px;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.25s ease;
            display: block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(90deg, #2f5b45 0%, #3a6450 100%);
            color: #fff;
            box-shadow: 0 10px 24px rgba(58, 100, 80, 0.22);
            margin-top: 6px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(58, 100, 80, 0.3);
        }

        .auth-links {
            margin-top: 18px;
            text-align: center;
            font-size: 13px;
            color: #ffffff;
        }

        .auth-links a {
            color: #ffffff;
            font-weight: 700;
            text-decoration: none;
        }

        .auth-links a:hover {
            text-decoration: underline;
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

        @media (max-width: 768px) {
            .auth-illustration-content .brand {
                margin-bottom: 14px;
            }

            .auth-illustration-content .brand img {
                height: 38px;
                width: 38px;
            }

            .auth-illustration-content .brand span {
                font-size: 16px;
            }

            .auth-illustration-content h1 {
                font-size: 22px;
            }

            .auth-illustration-content p {
                font-size: 13px;
            }

            .blob-1 {
                width: 180px;
                height: 180px;
                top: -40px;
                left: -40px;
            }

            .blob-2 {
                width: 140px;
                height: 140px;
                bottom: -30px;
                right: -30px;
            }

            .blob-3 {
                width: 90px;
                height: 90px;
            }

            .blob-4 {
                width: 70px;
                height: 70px;
            }

            .auth-form-area {
                padding: 24px 16px 40px;
                gap: 20px;
            }

            .auth-card {
                padding: 28px 24px;
            }

            .input-wrap input {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-illustration">
        <div class="auth-blob blob-1"></div>
        <div class="auth-blob blob-2"></div>
        <div class="auth-blob blob-3"></div>
        <div class="auth-blob blob-4"></div>
    </div>
    <div class="auth-form-area">
        <div class="auth-illustration-content">
            <div class="brand">
                <img src="{{ asset('logo.png') }}" alt="Logo SMK Bahrul Ulum" />
                <span>SMK Bahrul Ulum</span>
            </div>
            <h1>Selamat Datang di SMK Bahrul Ulum</h1>
            <p>Silahkan Login untuk menikmati fitur yang tersedia di SMK Bahrul Ulum</p>
        </div>
        <div class="auth-card">
            @yield('content')
        </div>
    </div>
</div>
</body>
</html>