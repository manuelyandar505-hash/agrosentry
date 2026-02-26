const CACHE_NAME = 'agrosentry-v1';
const ASSETS_TO_CACHE = [
  './',
  './index.php',
  './manifest.json',
  './icon-192.png',
  './icon-512.png'
];

// Instalación del Service Worker
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(ASSETS_TO_CACHE))
  );
});

// Activación y limpieza
self.addEventListener('activate', (event) => {
  console.log('AGROSENTRY Service Worker activado.');
});

// Estrategia: Red primero, luego Cache
self.addEventListener('fetch', (event) => {
  event.respondWith(
    fetch(event.request).catch(() => caches.match(event.request))
  );
});