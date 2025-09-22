<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Connexion</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- PWA -->
  <link rel="manifest" href="{{ url('/manifest.json') }}" crossorigin="use-credentials" />
  <!-- Chrome for Android theme color -->
  <meta name="theme-color" content="#000000">
  
  <!-- Add to homescreen for Chrome on Android -->
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="application-name" content="Connexion">
  <link rel="icon" sizes="512x512" href="{{ asset('methodist/images/icons/android/android-launchericon-512-512.png') }}">
  
  <!-- Add to homescreen for Safari on iOS -->
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="Connexion">
  <link rel="apple-touch-icon" href="{{ asset('methodist/images/icons/ios/512.png') }}">
  <!-- Tile for Win8 -->
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="{{ asset('methodist/images/icons/android/android-launchericon-512-512.png') }}">

  <link rel="stylesheet" href="{{ asset('methodist/css/bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{ asset('methodist/css/bootstrap-icons.min.css')}}">
  <link rel="stylesheet" href="{{ asset('methodist/css/leaflet.css')}}">
  <script src="{{ asset('methodist/js/leaflet.js')}}"></script>
</head>
<body class="container">
  <style>
    a {
      text-decoration: none;
    }
  </style>
  <main class="pt-1">
    <div class="d-flex justify-content-center my-2">
        <button id="installPwaBtn" class="btn btn-primary btn-md d-none">
            <i class="bi bi-download me-2"></i> Install App
        </button>
    </div>
    {{$slot}}
  </main>
  <footer id="footer" class="footer dark-background">

    <div class="container footer-top">
      
    </div>

    <div class="container copyright text-center mt-4">
      
    </div>

  </footer>
  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="{{ asset('methodist/js/bootstrap.min.js')}}"></script>
  <script type="text/javascript">
      // Initialize the service worker
      if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register("{{ url('/service-worker.js') }}", {
            scope: '/'
        }).then(function (registration) {
            // Registration was successful
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, function (err) {
            // registration failed :(
            console.log('ServiceWorker registration failed: ', err);
        });
      }
    
    // PWA installation prompt
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
  </script>
</body>
</html>