// EPAS-E Service Worker
const CACHE_NAME = 'epas-e-v3';
const OFFLINE_URL = '/offline.html';

// Static assets to cache immediately
const STATIC_ASSETS = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/offline.html',
    '/images/logo.png'
];

// Cache strategies
const CACHE_FIRST_PATTERNS = [
    /\/images\//,
    /\/fonts\//,
    /\.woff2?$/,
    /\.ttf$/,
    /\.otf$/,
    /\.png$/,
    /\.jpg$/,
    /\.jpeg$/,
    /\.gif$/,
    /\.svg$/,
    /\.ico$/
];

const NETWORK_FIRST_PATTERNS = [
    /\/api\//,
    /\/dashboard/,
    /\/courses/,
    /\/modules/,
    /\/grades/
];

// Install event - cache static assets
self.addEventListener('install', event => {
    console.log('[SW] Installing service worker...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('[SW] Caching static assets');
                return cache.addAll(STATIC_ASSETS.filter(url => {
                    // Only cache URLs that exist
                    return true;
                })).catch(err => {
                    console.warn('[SW] Some static assets failed to cache:', err);
                });
            })
            .then(() => self.skipWaiting())
    );
});

// Activate event - clean up old caches and take control immediately
self.addEventListener('activate', event => {
    console.log('[SW] Activating service worker...');
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(name => name !== CACHE_NAME)
                    .map(name => {
                        console.log('[SW] Deleting old cache:', name);
                        return caches.delete(name);
                    })
            );
        }).then(() => {
            // Take control of all pages immediately
            return self.clients.claim();
        })
    );
});

// Fetch event - handle requests
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Skip external requests
    if (url.origin !== location.origin) {
        return;
    }

    // Skip auth, admin, and private routes - don't cache these
    if (url.pathname.startsWith('/private/') ||
        url.pathname.startsWith('/admin/') ||
        url.pathname.startsWith('/login') ||
        url.pathname.startsWith('/register') ||
        url.pathname.startsWith('/logout') ||
        url.pathname.startsWith('/verify') ||
        url.pathname.startsWith('/email/') ||
        url.pathname.startsWith('/password')) {
        // Just fetch, don't cache auth routes at all
        return;
    }

    // Check if this is a cache-first pattern (static assets)
    if (CACHE_FIRST_PATTERNS.some(pattern => pattern.test(url.pathname))) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // Check if this is a network-first pattern (dynamic content)
    if (NETWORK_FIRST_PATTERNS.some(pattern => pattern.test(url.pathname))) {
        event.respondWith(networkFirst(request));
        return;
    }

    // Default: stale-while-revalidate for other content
    event.respondWith(staleWhileRevalidate(request));
});

// Cache-first strategy
async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) {
        return cached;
    }

    try {
        const response = await fetch(request);
        // Only cache successful, non-redirected responses
        if (response.ok && !response.redirected && response.type === 'basic') {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        console.error('[SW] Cache-first fetch failed:', error);
        return new Response('Offline', { status: 503 });
    }
}

// Network-first strategy
async function networkFirst(request) {
    try {
        const response = await fetch(request);
        // Only cache successful, non-redirected responses
        if (response.ok && !response.redirected && response.type === 'basic') {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        console.log('[SW] Network failed, trying cache:', request.url);
        const cached = await caches.match(request);
        if (cached) {
            return cached;
        }

        // Return offline page for navigation requests
        if (request.mode === 'navigate') {
            const offlinePage = await caches.match(OFFLINE_URL);
            if (offlinePage) {
                return offlinePage;
            }
        }

        return new Response('You are offline', { status: 503 });
    }
}

// Deduplicate concurrent requests
const pendingRequests = new Map();

// Stale-while-revalidate strategy
async function staleWhileRevalidate(request) {
    const cached = await caches.match(request);

    // Create a cache key for deduplication
    const cacheKey = request.url;

    // Check if there's already a pending request for this URL
    if (pendingRequests.has(cacheKey)) {
        console.log('[SW] Deduplicating request:', cacheKey);
        return cached || await pendingRequests.get(cacheKey) || new Response('Offline', { status: 503 });
    }

    // Create the network fetch promise
    const networkFetch = fetch(request)
        .then(response => {
            // Clone the response immediately before any potential consumption
            const responseClone = response.clone();

            // Only cache successful, non-redirected responses
            if (response.ok && !response.redirected && response.type === 'basic') {
                caches.open(CACHE_NAME).then(cache => {
                    // Use the clone for caching to avoid body-already-used errors
                    return cache.put(request, responseClone)
                        .catch(err => console.warn('[SW] Failed to cache response:', err));
                });
            }
            return response;
        })
        .catch(error => {
            console.warn('[SW] Network fetch failed:', error);
            return null;
        })
        .finally(() => {
            // Remove from pending requests when done
            pendingRequests.delete(cacheKey);
        });

    // Store the promise for deduplication
    pendingRequests.set(cacheKey, networkFetch);

    return cached || await networkFetch || new Response('Offline', { status: 503 });
}

// Handle messages from the main thread
self.addEventListener('message', event => {
    // Handle skip waiting to activate new service worker immediately
    if (event.data.type === 'SKIP_WAITING') {
        console.log('[SW] Received SKIP_WAITING message, activating...');
        self.skipWaiting();
        return;
    }

    if (event.data.type === 'CACHE_MODULE') {
        const { moduleId, urls } = event.data;
        cacheModule(moduleId, urls).then(() => {
            event.ports[0].postMessage({ success: true, moduleId });
        }).catch(error => {
            event.ports[0].postMessage({ success: false, error: error.message });
        });
    }

    if (event.data.type === 'CLEAR_MODULE_CACHE') {
        const { moduleId } = event.data;
        clearModuleCache(moduleId).then(() => {
            event.ports[0].postMessage({ success: true });
        });
    }

    if (event.data.type === 'GET_CACHED_MODULES') {
        getCachedModules().then(modules => {
            event.ports[0].postMessage({ modules });
        });
    }
});

// Cache a module for offline access
async function cacheModule(moduleId, urls) {
    const cache = await caches.open(`module-${moduleId}`);
    const failures = [];

    for (const url of urls) {
        try {
            const response = await fetch(url);
            if (response.ok) {
                await cache.put(url, response);
            }
        } catch (error) {
            console.warn(`[SW] Failed to cache ${url}:`, error);
            failures.push(url);
        }
    }

    // Store module metadata
    const metadata = {
        moduleId,
        cachedAt: new Date().toISOString(),
        urlCount: urls.length - failures.length,
        failures: failures.length
    };

    await cache.put(
        new Request(`/module-${moduleId}-metadata`),
        new Response(JSON.stringify(metadata))
    );

    return metadata;
}

// Clear a module's cache
async function clearModuleCache(moduleId) {
    await caches.delete(`module-${moduleId}`);
}

// Get list of cached modules
async function getCachedModules() {
    const cacheNames = await caches.keys();
    const modulesCaches = cacheNames.filter(name => name.startsWith('module-'));
    const modules = [];

    for (const cacheName of modulesCaches) {
        const cache = await caches.open(cacheName);
        const metadataResponse = await cache.match(new RegExp(`${cacheName}-metadata`));
        if (metadataResponse) {
            modules.push(await metadataResponse.json());
        }
    }

    return modules;
}
