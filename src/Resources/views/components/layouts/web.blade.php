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

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>
<body class="container">
  <style>
    a {
      text-decoration: none;
    }
  </style>
  <main class="pt-3">
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
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
  </script>
</body>
</html>