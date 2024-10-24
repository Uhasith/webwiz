import './bootstrap';
import '../css/app.css';
import 'https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js';
import 'https://www.gstatic.com/firebasejs/9.22.2/firebase-messaging-compat.js';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { createPinia } from "pinia";
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createI18n } from 'vue-i18n'
import en from '../lang/en.json';
import si from '../lang/si.json';
import ta from '../lang/ta.json';
import Cookies from 'js-cookie';
import store from './store';
import axios from 'axios';
import {initializeApp} from "firebase/app";

const pinia = createPinia();

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/pwabuilder-sw.js')
            .then(registration => {
                console.log('Service Worker registered with scope:', registration.scope);
            }).catch(error => {
            console.log('Service Worker registration failed:', error);
        });
    });
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const i18n = createI18n({
            legacy: false, // Set this to false
            globalInjection: true,
            locale: Cookies.get('language') || 'en',
            messages: {
                en,
                si,
                ta
            }
        });

        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia)
            .use(i18n)
            .use(store)
            .mount(el);
    },
    progress: {
        color: '#5BB98A',
    },
});

import {getMessaging, getToken, onMessage} from "firebase/messaging";
import 'firebase/messaging';


const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
} else {
    console.error('CSRF token not found: check the <meta> tag.');
}


const firebaseConfig = {
    apiKey: "AIzaSyApvF-V6ZR4E997tBmceX0Vsj8KFI2AIDU",
    projectId: "cea-monitor-1792c",
    messagingSenderId: "844265946058",
    appId: "1:844265946058:web:0a87600531ecde861fad33",
    measurementId:"G-88W7RK17R4",
    authDomain:"cea-monitor-1792c.firebaseapp.com",
    storageBucket:"cea-monitor-1792c.appspot.com"
};

const app = initializeApp(firebaseConfig);
function requestPermission() {
    Notification.requestPermission().then((permission) => {
        if (permission === 'granted') {
            console.log('Notification permission granted.');
            firebase.initializeApp(firebaseConfig);
            listenForMessages();
        } else {
            console.log('Unable to get permission to notify.');
        }
    });
}

requestPermission();


if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/firebase-messaging-sw.js')
            .then(registration => {
                console.log('Service Worker firebase registered with scope:', registration.scope);
                console.log(firebaseConfig);

                registration.active.postMessage(firebaseConfig);
                sendTokenToBackend().then(r => console.log("TOKEN SAVED"));
            }).catch(error => {
            console.log('Service Worker registration failed:', error);
        });
    });
}

const messaging = getMessaging(app);

async function sendTokenToBackend() {
    const vapidKey = import.meta.env.VITE_FIREBASE_VAPID_KEY;
    const token = await getToken(messaging, { vapidKey });
    try {
        await axios.post('/public/save-token', { token });
        console.log('Token sent to backend successfully.');
    } catch (error) {
        console.error('Error sending token to backend:', error);
    }
}

export const listenForMessages = () => {
    const messaging = getMessaging();
    onMessage(messaging, (payload) => {
        console.log('Message received in foreground: ', payload);
        const notificationTitle = payload.notification.title;
        const notificationOptions = {
            body: payload.notification.body,
            icon: payload.notification.image, // Ensure this path is correct
        };

        new Notification(notificationTitle, notificationOptions);
    });
};
