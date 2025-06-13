const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .styles([
        'resources/css/app.css', // Add other CSS files here if needed
    ], 'public/css/app.css') // Specify the full path including the file name
    .version()
    .disableNotifications(); // Disable notifications