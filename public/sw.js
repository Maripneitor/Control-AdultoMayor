/**
 * VitalSenior - Service Worker PWA (Soporte Offline y Resiliencia)
 * Estrategia: Network First, falling back to cache para /dashboard y navegación clínica
 */

const CACHE_STATIC_NAME = 'vitalsenior-static-v1';
const CACHE_DYNAMIC_NAME = 'vitalsenior-dynamic-v1';

// Recursos estáticos indispensables
const STATIC_ASSETS = [
    './manifest.json',
    'https://cdn.tailwindcss.com',
    'https://cdn.jsdelivr.net/npm/sweetalert2@11'
];

// 1. Instalación del Service Worker
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_STATIC_NAME).then((cache) => {
            console.log('[Service Worker] Pre-cacheando recursos estáticos de interfaz');
            return cache.addAll(STATIC_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

// 2. Activación y limpieza de cachés antiguas
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_STATIC_NAME && key !== CACHE_DYNAMIC_NAME) {
                        console.log('[Service Worker] Eliminando caché obsoleta:', key);
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// 3. Estrategia de interceptación de peticiones (Fetch)
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // Ignorar métodos no GET (ej. POST de SOS, OTP o formularios no se deben cachear)
    if (request.method !== 'GET') {
        return;
    }

    // ESTRATEGIA: "Network First, falling back to cache" para Dashboard y páginas de navegación
    const isNavigation = request.mode === 'navigate' || url.pathname.includes('/dashboard');

    if (isNavigation) {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    // Si hay respuesta válida de red, clonar y actualizar la caché dinámica
                    if (networkResponse && networkResponse.status === 200) {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_DYNAMIC_NAME).then((cache) => {
                            cache.put(request, responseToCache);
                        });
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    console.warn('[Service Worker] Conexión WiFi/Datos no disponible. Cargando Dashboard desde la caché local.');
                    // Fallback a caché: muestra la última vista guardada con la medicación del día
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }

                    // Fallback secundario si se navegó a otra página no cacheada
                    const cachedDashboard = await caches.match('./dashboard');
                    if (cachedDashboard) {
                        return cachedDashboard;
                    }

                    // Pantalla de contingencia offline si no hay caché previa
                    return new Response(
                        `<!DOCTYPE html>
                        <html lang="es">
                        <head>
                            <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>VitalSenior - Modo Sin Conexión</title>
                            <script src="https://cdn.tailwindcss.com"></script>
                        </head>
                        <body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 font-sans text-center">
                            <div class="bg-white p-8 rounded-3xl shadow-xl max-w-sm border border-slate-200">
                                <div class="text-5xl mb-4">📡</div>
                                <h1 class="text-xl font-bold text-slate-800 mb-2">Sin Conexión a Internet</h1>
                                <p class="text-slate-600 text-sm mb-6">No detectamos red WiFi ni datos móviles. Vuelve a intentarlo cuando recuperes señal.</p>
                                <button onclick="window.location.reload()" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl shadow">Reintentar</button>
                            </div>
                        </body>
                        </html>`,
                        { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
                    );
                })
        );
        return;
    }

    // ESTRATEGIA: "Cache First, falling back to network" para scripts, fuentes y assets estáticos
    event.respondWith(
        caches.match(request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }
            return fetch(request).then((networkResponse) => {
                if (networkResponse && networkResponse.status === 200 && request.url.startsWith('http')) {
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_STATIC_NAME).then((cache) => {
                        cache.put(request, responseClone);
                    });
                }
                return networkResponse;
            });
        })
    );
});
