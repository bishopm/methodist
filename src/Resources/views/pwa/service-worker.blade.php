const staticCacheName = "pwa-v" + new Date().getTime();
const filesToCache = [
    "/",                // homepage
    "/offline",         // offline fallback page (Laravel route)
    "/css/app.css",
    "/js/app.js",
    "/icons/icon-192x192.png",
    "/icons/icon-512x512.png"
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
