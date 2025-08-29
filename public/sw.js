// Define a unique cache name. Bumping this version will trigger the 'activate' event
// and clean up old caches, ensuring users get the latest version of the app shell.
const CACHE_NAME = 'villastudio-v1.2';

// List all the core files and pages that make up the "app shell".
// These will be cached on installation for a reliable offline experience.
const urlsToCache = [
    // Core App Shell
    '/',
    '/public/css/style.css',
    '/public/js/app.js',
    '/public/manifest.json',
    '/public/images/logo.png',
    '/public/images/favicon.ico',
    '/public/images/icons/icon-192x192.png',
    '/public/images/icons/icon-512x512.png',
    'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Poppins:wght@600;700&display=swap',
    
    // Core Pages for Offline Access
    '/login',
    '/register',
    '/movies',
    '/about',
    '/contact',
    '/terms',
    '/privacy',
    '/faq',
    '/copyright',
    
    // Cache the "Coming Soon" pages
    '/series',
    '/community',
];

// --- SERVICE WORKER EVENT LISTENERS ---

// 1. Install Event: Triggered when the service worker is first registered.
self.addEventListener('install', event => {
    console.log('[ServiceWorker] Install');
    // We don't want to interrupt the main thread, so we wait until caching is done.
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('[ServiceWorker] Caching app shell');
                // Use {cache: 'reload'} to bypass the browser's HTTP cache, ensuring we get the latest files from the server.
                return cache.addAll(urlsToCache.map(url => new Request(url, {cache: 'reload'})));
            })
    );
});

// 2. Activate Event: Triggered after installation, when the new service worker takes control.
self.addEventListener('activate', event => {
    console.log('[ServiceWorker] Activate');
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                // Map over all cache names and delete any that are not in our whitelist.
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        console.log('[ServiceWorker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// 3. Fetch Event: Triggered for every network request made by the page.
self.addEventListener('fetch', event => {
    // We only want to cache GET requests. POST requests, etc., should always go to the network.
    if (event.request.method !== 'GET') {
        return;
    }

    // This is a "Cache then Network" strategy.
    // It's fast and provides a good offline experience for the app shell.
    event.respondWith(
        caches.match(event.request)
            .then(cachedResponse => {
                // If a cached response is found, return it immediately.
                if (cachedResponse) {
                    return cachedResponse;
                }

                // If not in cache, fetch the request from the network.
                return fetch(event.request).then(networkResponse => {
                    // We don't dynamically cache everything, only what's in our initial list.
                    // This prevents the cache from being filled with API data or movie poster images.
                    // A more advanced strategy could cache those things in a separate, dynamic cache.
                    return networkResponse;
                });
            })
    );
});