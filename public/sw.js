const CACHE = 'servipueblo-v1';

const PRECACHE = [
  '/offline',
];

// Instalar: precachear la página offline
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE).then(cache => cache.addAll(PRECACHE))
  );
  self.skipWaiting();
});

// Activar: limpiar caches viejos
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))
    )
  );
  self.clients.claim();
});

// Fetch: network first, fallback a cache, fallback a /offline para páginas
self.addEventListener('fetch', event => {
  const req = event.request;

  // Solo GET, ignorar admin, API y chat
  if (req.method !== 'GET') return;
  if (req.url.includes('/admin') || req.url.includes('/ai-chat') || req.url.includes('/livewire')) return;

  const isPage = req.headers.get('Accept') && req.headers.get('Accept').includes('text/html');
  const isAsset = req.url.match(/\.(css|js|png|jpg|jpeg|svg|ico|woff2?)$/);

  if (isAsset) {
    // Assets: cache first
    event.respondWith(
      caches.match(req).then(cached => {
        if (cached) return cached;
        return fetch(req).then(res => {
          if (res.ok) {
            const clone = res.clone();
            caches.open(CACHE).then(c => c.put(req, clone));
          }
          return res;
        });
      })
    );
    return;
  }

  if (isPage) {
    // Páginas: network first, offline fallback
    event.respondWith(
      fetch(req)
        .then(res => {
          if (res.ok) {
            const clone = res.clone();
            caches.open(CACHE).then(c => c.put(req, clone));
          }
          return res;
        })
        .catch(() =>
          caches.match(req).then(cached => cached || caches.match('/offline'))
        )
    );
  }
});
