var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    '/',
];

// Cache on install
self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                cache.add('/').catch(error => {
                    console.error('Failed to cache root route:', error);
                });
                return cache.addAll(filesToCache);
            })
    )
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache
self.addEventListener('fetch', (event) => {
  event.respondWith(caches.open(staticCacheName).then((cache) => {
    return cache.match(event.request).then((cachedResponse) => {
        const fetchedResponse = fetch(event.request).then((networkResponse) => {
            cache.put(event.request, networkResponse.clone());
    
            return networkResponse;
        });
    
        return cachedResponse || fetchedResponse;
        });
    }));
});