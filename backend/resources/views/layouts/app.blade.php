<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EquiTask AI — @yield('title', 'Platform Assessment Adaptif')</title>

    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        html {
            overflow-y: scroll;
        }
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-soft: #edf4ff;
            --secondary: #0f766e;
            --secondary-soft: #e8f7f4;
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --danger: #ef4444;
            --amber: #f59e0b;
            --radius: 16px;
            --radius-sm: 12px;
            --shadow: 0 10px 24px -14px rgba(37, 99, 235, 0.24), 0 4px 12px -8px rgba(15, 23, 42, 0.12);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Plus Jakarta Sans", sans-serif;
            background-color: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
        }

        .material-symbols-outlined {
            font-variation-settings: "FILL" 0, "wght" 500, "GRAD" 0, "opsz" 24;
            line-height: 1;
            vertical-align: middle;
        }

        /* ===== Layout Responsif Utama ===== */
        .app-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            max-width: 1024px;
            margin: 0 auto;
            background-color: var(--bg);
            position: relative;
            box-shadow: 0 0 40px rgba(0,0,0,0.03);
        }

        .main-content {
            flex: 1;
            padding-bottom: 90px; /* Ruang untuk bottom nav */
        }

        /* ===== Global UI Components ===== */
        .topbar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 24px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 40;
            border-bottom: 1px solid var(--border);
        }
        .topbar h1 {
            font-size: 18px;
            font-weight: 700;
            flex: 1;
            margin: 0;
        }

        .card {
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            padding: 20px;
            border: 1px solid var(--border);
            margin-bottom: 14px;
        }

        /* ===== Bottom Navigation (Fixed di bawah layer) ===== */
        .bottomnav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            max-width: 1024px;
            width: 100%;
            display: flex;
            justify-content: space-around;
            background: rgba(255, 255, 255, 0.95);
            border-top: 1px solid var(--border);
            padding: 10px 6px calc(10px + env(safe-area-inset-bottom, 0px));
            z-index: 50;
            backdrop-filter: blur(14px);
        }
        .bottomnav .navitem {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            background: none;
            text-decoration: none;
            transition: color 0.2s;
        }
        .bottomnav .navitem:hover,
        .bottomnav .navitem.active {
            color: var(--primary);
        }
        .bottomnav .navitem .material-symbols-outlined {
            font-size: 24px;
        }

        /* Media Query untuk Desktop/Tablet */
        @media (min-width: 768px) {
            .bottomnav {
                border-radius: 20px 20px 0 0;
                border-left: 1px solid var(--border);
                border-right: 1px solid var(--border);
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <div class="app-wrapper">
        <!-- Area Konten Dinamis -->
        <main class="main-content">
            @yield('content')
        </main>

        <!-- Area Navigasi Bawah Dinamis -->
        @yield('bottom_nav')
    </div>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
