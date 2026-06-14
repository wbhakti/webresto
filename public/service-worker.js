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

    const url = event.notification.data?.url || '/dashboard';

    event.waitUntil(
        clients.openWindow(url)
    );
});