// 1. Save the files to the user's device
// The "install" event is called when the ServiceWorker starts up.
// All ServiceWorker code must be inside events.
self.addEventListener('install', function(e) {
    console.log('install');

    // waitUntil tells the browser that the install event is not finished until we have
    // cached all of our files
    e.waitUntil(
        // Here we call our cache "myonsenuipwa", but you can name it anything unique
        caches.open('chihuahua-moment').then(cache => {
            // If the request for any of these resources fails, _none_ of the resources will be
            // added to the cache.
            return cache.addAll([
                '/chihuahua/',
                '/chihuahua/index.html',
                '/manifest.json',
                '/chihuahua/css/styles.css',
                '/chihuahua/js/app.js',
                '/chihuahua/favicon.ico',
                '/chihuahua/images/ChihuahuaMomentsLogo.192.png',
                '/chihuahua/images/ChihuahuaMomentsLogo.512.png',
                '/chihuahua/images/loading.gif'
            ]);
        })
    );
});

// 2. Intercept requests and return the cached version instead
self.addEventListener('fetch', function(e) {
    e.respondWith(
        // check if this file exists in the cache
        caches.match(e.request)
        // Return the cached file, or else try to get it from the server
            .then(response => response || fetch(e.request))
    );
});

importScripts('https://www.gstatic.com/firebasejs/7.9.3/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.9.3/firebase-messaging.js');

firebase.initializeApp({
    'messagingSenderId': '800619072131',
    'projectId': 'chihuahua-moments',
    'apiKey': 'AIzaSyDkXrjDI0sAtrlRV0zHTVLHx5uVDQV8eE4',
    'appId': '1:800619072131:web:3c3871cd7892195c3069b6'
});

const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) {
    console.log('Received background message ', payload);

    return self.registration.showNotification(
        'Background Message Title',
        {
            body: 'Background Message body.',
            icon: '/firebase-logo.png'
        }
    );
});

self.addEventListener('activate', event => {
    console.log('service worker now active');
    messaging.getToken().then((currentToken) => {
        if (currentToken) {
            console.log('reveived toke:' + currentToken);
        } else {
            console.log('No Instance ID token available. Request permission to generate one.');
        }
    }).catch((err) => {
        console.log('An error occurred while retrieving token. ', err);
    });
});