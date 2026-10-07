<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EquiTask AI — Platform Evaluasi Diferensiasi Inklusif</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
    />
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
        min-height: 100vh;
        color: var(--text);
        background: linear-gradient(135deg, #f5f8ff 0%, #eef5ff 45%, #f8fbff 100%);
        display: flex;
        flex-direction: column;
      }
      .material-symbols-outlined {
        font-variation-settings: "FILL" 0, "wght" 500, "GRAD" 0, "opsz" 24;
        line-height: 1;
      }

      /* Wrapper Utama Responsive (Menyesuaikan HP, Tablet, & Desktop) */
      .web-container {
        width: 100%;
        max-width: 600px; /* Lebar optimal untuk keterbacaan antarmuka aplikasi */
        margin: 0 auto;
        min-height: 100vh;
        background: linear-gradient(180deg, #f9fcff 0%, #f4f8ff 100%);
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow-lg);
      }

      @media (min-width: 768px) {
        .web-container {
          max-width: 680px;
          margin: 24px auto;
          border-radius: 28px;
          overflow: hidden;
          min-height: calc(100vh - 48px);
        }
      }

      .app {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
      }

      /* Komponen UI */
      .topbar {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 18px 24px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.95) 0%, rgba(244, 248, 255, 0.9) 100%);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(223, 232, 247, 0.6);
      }
      .topbar h1 { font-size: 18px; font-weight: 700; }
      .iconbtn {
        width: 40px; height: 40px; border-radius: 14px;
        background: rgba(255, 255, 255, 0.8);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 10px 20px -12px rgba(15, 23, 42, 0.28);
        border: 1px solid rgba(255, 255, 255, 0.75);
        cursor: pointer; color: var(--primary); flex-shrink: 0;
      }
      .px { padding-left: 24px; padding-right: 24px; }
      .card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 251, 255, 0.96) 100%);
        border-radius: var(--radius);
        box-shadow: 0 16px 36px -20px rgba(15, 23, 42, 0.24);
        padding: 20px;
        border: 1px solid rgba(255, 255, 255, 0.7);
      }
      .btn {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        border: none; border-radius: 14px; font-family: inherit; font-weight: 800;
        font-size: 14.5px; padding: 14px 18px; cursor: pointer;
        transition: transform 0.15s ease;
      }
      .btn:active { transform: scale(0.97); }
      .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%);
        color: #fff; box-shadow: 0 12px 24px -10px rgba(37, 99, 235, 0.42);
      }
      .badge {
        font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 99px;
        display: inline-flex; align-items: center; gap: 4px;
      }
      .badge-green { background: var(--secondary-soft); color: #059669; }
      .badge-amber { background: #fffbeb; color: #b45309; }
      .badge-blue { background: var(--primary-soft); color: var(--primary); }
      .badge-slate { background: #f1f5f9; color: var(--muted); }
      .input {
        width: 100%; border: 1.5px solid var(--border); background: var(--card);
        border-radius: 14px; padding: 14px 16px; font-family: inherit;
        font-size: 14px; outline: none; margin-bottom: 14px;
      }
      .input:focus { border-color: var(--primary); }
      .muted { color: var(--muted); font-size: 12.5px; }
    </style>
  </head>
  <body>
    <!-- Kontainer Responsif (Tanpa Bingkai HP, Menyesuaikan Layar Browser) -->
    <div class="web-container">
      <div class="app">
        @yield('content')
      </div>
    </div>
  </body>
</html>
