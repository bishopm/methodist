<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $pageName ?? 'Connexion' }}</title>

  <!-- PWA manifest -->
  <link rel="manifest" href="{{ url('/manifest.json') }}" crossorigin="use-credentials" />
  <meta name="theme-color" content="#000000">

  <!-- Chrome/Android & iOS -->
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="application-name" content="Connexion">
  <link rel="icon" sizes="512x512" href="{{ asset('methodist/images/icons/android/android-launchericon-512-512.png') }}">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="Connexion">
  <link rel="apple-touch-icon" href="{{ asset('methodist/images/icons/ios/512.png') }}">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="{{ asset('methodist/images/icons/android/android-launchericon-512-512.png') }}">

  <!-- CSS -->
  <link rel="stylesheet" href="{{ asset('methodist/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('methodist/css/bootstrap-icons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('methodist/css/leaflet.css') }}">
  <script src="{{ asset('methodist/js/leaflet.js') }}"></script>

  <style>
    a { text-decoration: none; }

    /* Header */
    .pwa-header {
        width: 100vw;
        height: 56px;
        display: flex;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 1030;
        background-color: #f8f9fa;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 0 1rem;
    }
    .pwa-header .menu-btn { flex: 0 0 auto; background:none; border:none; margin-right:0.5rem; color:#000; }
    .pwa-header .navbar-title { flex:1 1 auto; text-align:center; font-weight:600; font-size:1.1rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; padding:0 0.5rem; }

    /* Bottom toolbar */
    .pwa-bottom-toolbar {
        position: fixed; bottom:0; width:100vw; height:56px; background-color:#f8f9fa;
        z-index:1030; border-top:1px solid #ddd; display:flex; justify-content:space-around; align-items:center;
    }
    .pwa-bottom-toolbar button,
    .pwa-bottom-toolbar a { flex:1; text-align:center; transition: transform 0.2s, color 0.2s; }
    .pwa-bottom-toolbar button:disabled { opacity:0.3; pointer-events:none; }
    .pwa-bottom-toolbar button:not(:disabled):hover,
    .pwa-bottom-toolbar a:hover { color:#0d6efd; transform:scale(1.1); }
    .pwa-bottom-toolbar button.enable-pulse { animation: pulse 0.3s ease; }
    @keyframes pulse { 0% { transform:scale(1); } 50% { transform:scale(1.2); } 100% { transform:scale(1); } }
  </style>
</head>

<body>

  <!-- Header -->
  <header class="pwa-header">
      <button class="menu-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu">
          <i class="bi bi-list fs-3"></i>
      </button>
      <span class="navbar-title">{{ $pageName ?? 'Connexion' }}</span>
  </header>

  <!-- Offcanvas menu -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu">
      <div class="offcanvas-header">
          <h5 class="offcanvas-title">Menu</h5>
          <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
      </div>
      <div class="offcanvas-body">
          <ul class="list-unstyled">
              <li><a href="/" class="d-block py-2" data-title="Home"><i class="bi bi-house me-2"></i> Home</a></li>
              <li><a href="/lectionary" class="d-block py-2" data-title="Lectionary"><i class="bi bi-book me-2"></i> Lectionary</a></li>
              <li><a href="/projects" class="d-block py-2" data-title="Projects"><i class="bi bi-lightbulb me-2"></i> Projects</a></li>
              <li><a href="/admin" class="d-block py-2" data-title="Login"><i class="bi bi-lock me-2"></i> Login</a></li>
          </ul>
      </div>
  </div>

  <!-- Main content wrapper -->
  <main class="pt-1 px-3" id="pwaMainContent" data-title="{{ $pageName ?? 'Connexion' }}">
      <div class="d-flex justify-content-center my-2">
          <button id="installPwaBtn" class="btn btn-primary btn-md d-none">
              <i class="bi bi-download me-2"></i> Install App
          </button>
      </div>
      <div id="pwaContentWrapper">
          {{ $slot }}
      </div>
  </main>

  <!-- Bottom toolbar -->
  <nav class="pwa-bottom-toolbar shadow-sm">
      <button class="btn btn-link text-dark" id="pwaBackBtn" disabled>
          <i class="bi bi-arrow-left fs-4"></i>
      </button>
      <button class="btn btn-link text-dark" id="pwaHomeBtn">
          <i class="bi bi-house fs-4"></i>
      </button>
      <button class="btn btn-link text-dark" id="pwaForwardBtn" disabled>
          <i class="bi bi-arrow-right fs-4"></i>
      </button>
  </nav>

  <!-- JS -->
  <script src="{{ asset('methodist/js/bootstrap.min.js') }}"></script>
  <script>
    // Service worker
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register("{{ url('/service-worker.js') }}", { scope: '/' })
        .then(reg => console.log('ServiceWorker registered:', reg.scope))
        .catch(err => console.log('ServiceWorker registration failed:', err));
    }

    // PWA install
    if (location.protocol === "https:" || location.hostname === "localhost" || location.hostname === "127.0.0.1") {
      let deferredPrompt;
      const installBtn = document.getElementById("installPwaBtn");

      window.addEventListener("beforeinstallprompt", (e) => {
          e.preventDefault();
          deferredPrompt = e;
          installBtn.classList.remove("d-none");
      });

      installBtn.addEventListener("click", async () => {
          if (deferredPrompt) {
              deferredPrompt.prompt();
              const { outcome } = await deferredPrompt.userChoice;
              console.log(`User response to install prompt: ${outcome}`);
              deferredPrompt = null;
              installBtn.classList.add("d-none");
          }
      });

      window.addEventListener("appinstalled", () => {
          console.log("PWA installed successfully");
          installBtn.classList.add("d-none");
      });
    }

    document.addEventListener("DOMContentLoaded", () => {
      const headerTitle = document.querySelector('.navbar-title');
      const contentWrapper = document.getElementById('pwaContentWrapper');
      const mainContent = document.getElementById('pwaMainContent');
      const backBtn = document.getElementById('pwaBackBtn');
      const forwardBtn = document.getElementById('pwaForwardBtn');

      function updateHeader(title) {
          headerTitle.textContent = title;
          mainContent.dataset.title = title;
      }

      function updateNavButtons() {
          const state = window.history.state;
          backBtn.disabled = !state || state.idx <= 0;
          forwardBtn.disabled = !state || state.idx >= window.history.length - 1;
      }

      async function loadPage(url, push = true) {
          try {
              const res = await fetch(url, { headers: { "X-PJAX": "true" }});
              if (!res.ok) throw new Error("Network response not ok");
              const html = await res.text();

              // Inject only the inner content
              contentWrapper.innerHTML = html;

              // Update page title
              const pageTitle = contentWrapper.dataset.title || document.title || 'Connexion';
              updateHeader(pageTitle);

              if (push) {
                  const idx = window.history.state?.idx ? window.history.state.idx + 1 : 1;
                  window.history.pushState({ title: pageTitle, idx: idx }, pageTitle, url);
              }

              updateNavButtons();

              // Close offcanvas
              const offcanvasMenu = document.getElementById('offcanvasMenu');
              const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasMenu);
              bsOffcanvas.hide();

          } catch (err) {
              console.error("AJAX load failed:", err);
              window.location.href = url; // fallback to full reload
          }
      }

      // Intercept all internal links
      document.addEventListener('click', (e) => {
          const link = e.target.closest('a');
          if (!link) return;
          const href = link.getAttribute('href');
          if (href && href.startsWith('/')) {
              e.preventDefault();
              loadPage(href);
          }
      });

      // Back/forward buttons
      backBtn.addEventListener('click', () => window.history.back());
      forwardBtn.addEventListener('click', () => window.history.forward());

      // Home button
      document.getElementById('pwaHomeBtn').addEventListener('click', () => loadPage('/', true));

      // Handle browser navigation
      window.addEventListener('popstate', () => loadPage(window.location.href, false));

      if (!window.history.state) {
          window.history.replaceState({ title: mainContent.dataset.title, idx: 0 }, '', window.location.href);
      }

      updateNavButtons();
    });
  </script>
</body>
</html>
