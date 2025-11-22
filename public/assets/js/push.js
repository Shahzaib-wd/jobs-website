if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js')
    .then(reg => {
        return reg.pushManager.getSubscription()
        .then(sub => {
            if (sub === null) {
                return reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array('YOUR_PUBLIC_VAPID_KEY')
                });
            } else {
                return sub;
            }
        });
    })
    .then(subscription => {
        fetch('/push_subscribe.php', {
            method: 'POST',
            body: JSON.stringify(subscription),
            headers: {'Content-Type': 'application/json'}
        });
    })
    .catch(err => console.error('SW registration error:', err));
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    return Uint8Array.from([...rawData].map(char => char.charCodeAt(0)));
}