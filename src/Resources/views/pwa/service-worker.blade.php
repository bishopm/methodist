const staticCacheName = "pwa-v" + new Date().getTime();
const filesToCache = [
    "/",                // homepage
    "/offline",         // offline fallback page (Laravel route)
    "{{ asset('methodist/css/bootstrap.min.css')}}",
    "{{ asset('methodist/js/bootstrap.min.js')}}",
    "{{ asset('methodist/images/icons/android/android-launchericon-192-192.png') }}",
    "{{ asset('methodist/images/icons/android/android-launchericon-512-512.png') }}",
    "{{ asset('methodist/images/icons/ios/512.png') }}"
];

// Install: pre-cache core files
self.addEventListener("install", event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName).then(cache => {
            return cache.addAll(filesToCache);
        })
    );
});

// Activate: clear old caches
self.addEventListener("activate", event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(name => name.startsWith("pwa-") && name !== staticCacheName)
                    .map(name => caches.delete(name))
            );
        })
    );
    self.clients.claim();
});

// Fetch: network-first for HTML (dynamic routes), cache-first for assets
self.addEventListener("fetch", event => {
    if (event.request.method !== "GET") return; // don’t cache POST etc.

    if (event.request.mode === "navigate") {
        // Handle page navigation (HTML requests like /district/circuit/society)
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    return caches.open(staticCacheName).then(cache => {
                        cache.put(event.request, response.clone());
                        return response;
                    });
                })
                .catch(() => {
                    // Offline fallback if no cache
                    return caches.match(event.request).then(cached => {
                        return cached || caches.match("/offline");
                    });
                })
        );
    } else {
        // Handle static assets (CSS, JS, images)
        event.respondWith(
            caches.match(event.request).then(cached => {
                return cached || fetch(event.request).then(networkResponse => {
                    return caches.open(staticCacheName).then(cache => {
                        cache.put(event.request, networkResponse.clone());
                        return networkResponse;
                    });
                });
            })
        );
    }
});
