/**
 * ZHUOER Service Worker
 * 提供离线缓存和静态资源加速
 */

const CACHE_NAME = 'zhuoer-v1';
const STATIC_ASSETS = [
    '/',
];

// 安装：缓存关键资源
self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        }).catch(() => {
            // 静默失败，不阻断安装
        })
    );
});

// 激活：清理旧缓存
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            );
        })
    );
    self.clients.claim();
});

// 拦截请求：缓存优先策略（静态资源）/ 网络优先（动态内容）
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // 只处理同源请求
    if (url.origin !== self.location.origin) {
        return;
    }

    // 静态资源：缓存优先
    if (isStaticAsset(request)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // 页面/HTML：网络优先，失败时回退缓存
    if (request.mode === 'navigate' || request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(networkFirst(request));
        return;
    }
});

function isStaticAsset(request) {
    const staticExts = /\.(css|js|png|jpg|jpeg|gif|svg|webp|woff2?|ttf|ico)$/i;
    return staticExts.test(request.url);
}

// 缓存优先：先读缓存，没有则网络请求并缓存
async function cacheFirst(request) {
    const cache = await caches.open(CACHE_NAME);
    const cached = await cache.match(request);
    if (cached) {
        return cached;
    }
    try {
        const response = await fetch(request);
        if (response.ok) {
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        return new Response('离线中', { status: 503 });
    }
}

// 网络优先：先网络，失败则用缓存
async function networkFirst(request) {
    const cache = await caches.open(CACHE_NAME);
    try {
        const response = await fetch(request);
        if (response.ok) {
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await cache.match(request);
        if (cached) {
            return cached;
        }
        return new Response('页面加载失败，请检查网络连接。', {
            status: 503,
            headers: { 'Content-Type': 'text/html; charset=utf-8' },
        });
    }
}
