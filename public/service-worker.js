self.addEventListener('push', function(event) {

    let data = {
        title: 'Notifikasi',
        body: '',
        icon: '/favicon.ico',
        badge: '/favicon.ico',
        data: {}
    };

    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data.body = event.data.text();
        }
    }

    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: data.icon,
            badge: data.badge,
            data: data.data
        })
    );
});

self.addEventListener('notificationclick', function(event) {

    event.notification.close();

    const url = event.notification.data?.url || '/dashboard/dayTransaction';

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then(function(clientList) {

            for (const client of clientList) {

                // Jika dashboard sudah terbuka
                if (client.url.includes('/dashboard/dayTransaction')) {

                    client.focus();

                    // refresh halaman
                    client.postMessage({
                        action: 'refresh'
                    });

                    return;
                }
            }

            // Jika belum ada, buka tab baru
            return clients.openWindow(url);
        })
    );
});