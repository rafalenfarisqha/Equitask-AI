<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EquiTask AI — @yield('title', 'Platform Assessment Adaptif')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-soft: #edf4ff;
            --secondary: #0f766e;
            --secondary-soft: #e8f7f4;
            --bg: #f4f8ff;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --border: #dfe8f7;
            --danger: #ef4444;
            --amber: #f59e0b;
            --radius: 20px;
            --radius-sm: 14px;
            --shadow: 0 10px 24px -14px rgba(37, 99, 235, 0.24), 0 4px 12px -8px rgba(15, 23, 42, 0.12);
            --shadow-lg: 0 24px 48px -20px rgba(15, 23, 42, 0.28);
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
            /* Membatasi lebar maksimal di layar laptop/PC agar tidak terlalu melebar */
            max-width: 1024px;
            margin: 0 auto;
            background-color: var(--bg);
            position: relative;
            box-shadow: 0 0 40px rgba(0,0,0,0.03);
        }

        .main-content {
            flex: 1;
            /* Memberi ruang di bawah agar konten tidak tertutup bottom nav */
            padding-bottom: 80px;
        }

        /* ===== Global UI Components ===== */
        .topbar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            background: rgba(255, 255, 255, 0.85);
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
        }
        .iconbtn {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            background: var(--card);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            cursor: pointer;
            color: var(--primary);
            transition: all 0.2s;
        }
        .iconbtn:hover {
            background: var(--primary-soft);
        }

        .px { padding-left: 20px; padding-right: 20px; }

        .card {
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: 0 4px 12px -8px rgba(15, 23, 42, 0.12);
            padding: 18px;
            border: 1px solid var(--border);
            margin-bottom: 14px;
        }

        .row { display: flex; align-items: center; gap: 12px; }

        /* Grid dinamis: otomatis menjadi 1 kolom di HP, dan 2-4 kolom di Laptop */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 14px;
            margin-bottom: 14px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            border-radius: 14px;
            font-family: inherit;
            font-weight: 700;
            font-size: 14.5px;
            padding: 12px 20px;
            cursor: pointer;
            transition: transform 0.15s ease, opacity 0.15s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%);
            color: #fff;
            box-shadow: 0 8px 16px -8px rgba(37, 99, 235, 0.4);
        }
        .btn-secondary {
            background: var(--secondary-soft);
            color: var(--secondary);
            border: 1px solid #bfe7de;
        }
        .btn-outline {
            background: var(--card);
            color: var(--text);
            border: 1.5px solid var(--border);
        }
        .btn-block { width: 100%; }

        .input {
            width: 100%;
            border: 1.5px solid var(--border);
            background: var(--card);
            border-radius: 14px;
            padding: 14px 16px;
            font-family: inherit;
            font-size: 14px;
            outline: none;
            margin-bottom: 14px;
        }
        .input:focus { border-color: var(--primary); }
        .field-label {
            font-size: 13px;
            font-weight: 700;
            color: var(--muted);
            margin-bottom: 6px;
            display: block;
        }

        /* ===== Bottom Navigation (Fixed di bawah layer) ===== */
        .bottomnav {
            position: fixed;
            bottom: 0;
            /* Mengikuti batas max-width dari app-wrapper */
            max-width: 1024px;
            width: 100%;
            display: flex;
            background: rgba(255, 255, 255, 0.9);
            border-top: 1px solid var(--border);
            padding: 12px 6px calc(12px + env(safe-area-inset-bottom, 0px));
            z-index: 50;
            backdrop-filter: blur(14px);
        }
        .navitem {
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
            font-family: inherit;
            transition: color 0.2s;
        }
        .navitem:hover, .navitem.active { color: var(--primary); }
        .navitem .material-symbols-outlined { font-size: 24px; }

        .badge {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 99px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-green { background: var(--secondary-soft); color: #059669; }
        .badge-amber { background: #fffbeb; color: #b45309; }
        .badge-blue { background: var(--primary-soft); color: var(--primary); }
        .muted { color: var(--muted); font-size: 13px; }

        /* Media Query untuk Desktop/Tablet */
        @media (min-width: 768px) {
            .px {
                padding-left: 32px;
                padding-right: 32px;
            }
            .bottomnav {
                border-radius: 24px 24px 0 0;
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

    @stack('scripts')
</body>
</html>
