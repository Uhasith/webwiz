importScripts('https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.22.2/firebase-messaging-compat.js');

const firebaseConfig = {
    apiKey: "AIzaSyApvF-V6ZR4E997tBmceX0Vsj8KFI2AIDU",
    authDomain: "cea-monitor-1792c.firebaseapp.com",
    projectId: "cea-monitor-1792c",
    storageBucket: "cea-monitor-1792c.appspot.com",
    messagingSenderId: "844265946058",
    appId: "1:844265946058:web:0a87600531ecde861fad33",
    measurementId: "G-88W7RK17R4"
};

firebase.initializeApp(firebaseConfig);
self.addEventListener('push', function(event) {
    const data = (event.data.json()).notification;
    console.log('[firebase-messaging-sw.js] Received background message ',data);
    self.registration.showNotification(data.title, {
        body: data.body,
        icon: data.image
    });
});
