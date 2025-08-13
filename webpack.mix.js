const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
.js('resources/js/scanditScanner.js', 'public/js') // Add this line
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);
// --- Add these lines for Scandit SDK ---
mix.copyDirectory(
    'node_modules/@scandit/web-datacapture-barcode/build/js/scandit-engine',
    'public/js/scandit-engine' // Adjust destination path as needed
);