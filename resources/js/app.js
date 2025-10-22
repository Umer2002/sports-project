import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
// resources/js/app.jsx
// resources/js/app.js



fetch('/user-data')
    .then(response => response.json())
    .then(data => {
        console.log(data); // Handle your user data here
    })
    .catch(error => console.error('Error:', error));

import './video-composer.js'   // or './video-composer.jsx' (match real filename)

import '../css/app.css';
