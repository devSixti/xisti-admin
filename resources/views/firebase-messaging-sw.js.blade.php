/*
 * XISTI — Firebase Cloud Messaging service worker (generated from admin config).
 */
importScripts('https://www.gstatic.com/firebasejs/8.7.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.7.1/firebase-messaging.js');

firebase.initializeApp(@json(array_filter([
    'apiKey' => config('firebase-web.api_key'),
    'authDomain' => config('firebase-web.auth_domain'),
    'databaseURL' => config('firebase-web.database_url'),
    'projectId' => config('firebase-web.project_id'),
    'storageBucket' => config('firebase-web.storage_bucket'),
    'messagingSenderId' => config('firebase-web.messaging_sender_id'),
    'appId' => config('firebase-web.app_id'),
    'measurementId' => config('firebase-web.measurement_id'),
], fn ($value) => filled($value))));

const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function (payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    const notificationTitle = payload.data.title;
    const notificationOptions = {
        body: payload.data.body,
        icon: '/assets/images/website-logo-icon/xisti-favicon.png',
    };
    return self.registration.showNotification(notificationTitle, notificationOptions);
});
