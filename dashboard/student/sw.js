const CACHE_NAME = 'educational-panel-v2';
const urlsToCache = [
    '/dashboard/student/',
    '/assets/css/bootstrap.rtl.min.css',
    '/assets/js/bootstrap.min.js'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
            .catch(err => console.log('Failed to cache:', err))
    );
});

self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

self.addEventListener('fetch', event => {
    // فقط برای درخواست‌های GET
    if (event.request.method !== 'GET') {
        return;
    }
    
    // جلوگیری از کش کردن فایل‌های PHP
    if (event.request.url.includes('.php')) {
        return;
    }
    
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                if (response) {
                    return response;
                }
                
                // اگر آفلاین بود، به صفحه اصلی هدایت کن
                if (!navigator.onLine) {
                    return caches.match('/dashboard/' + self.location.pathname.split('/')[3] + '/') 
                        || caches.match('/dashboard/student/')
                        || new Response('آفلاین هستید. لطفاً اتصال اینترنت خود را بررسی کنید.', {
                            status: 503,
                            statusText: 'Service Unavailable'
                        });
                }
                
                return fetch(event.request);
            })
    );
});