/*
 Give the service worker access to Firebase Messaging.
 Note that you can only use Firebase Messaging here. Other Firebase libraries
 are not available in the service worker.
*/

importScripts('https://www.gstatic.com/firebasejs/8.7.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.7.1/firebase-messaging.js');

// For Firebase JS SDK v7.20.0 and later, measurementId is optional
/*
 Initialize the Firebase app in the service worker by passing in
 your app's Firebase config object.
 https://firebase.google.com/docs/web/setup#config-object
*/

firebase.initializeApp({
    apiKey: "AIzaSyB3o6J8zZ2q5C_7hKG4LLVIytpdSLaQ87w",
    authDomain: "app-zimo.firebaseapp.com",
    databaseURL: "https://app-zimo-default-rtdb.firebaseio.com",
    projectId: "app-zimo",
    storageBucket: "app-zimo.firebasestorage.app",
    messagingSenderId: "51312882624",
    appId: "1:51312882624:web:df5251384bd1b0b745ce25",
    measurementId: "G-6X4VP1J1JR"
});

/*
 Retrieve an instance of Firebase Messaging so that it can handle background messages.
*/
const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        // icon: payload.notification.icon,
    };
    self.registration.showNotification(notificationTitle,
        notificationOptions);
});
