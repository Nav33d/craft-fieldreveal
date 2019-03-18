let mix = require('laravel-mix');
let tailwindcss = require('tailwindcss');

mix.js('src/assetbundles/src/js/fieldreveal.js', 'src/assetbundles/dist/js')
    .sass('src/assetbundles/src/sass/fieldreveal.scss', 'src/assetbundles/dist/css')
    .options({
        processCssUrls: false,
        postCss: [ tailwindcss('./tailwind.js') ],
    })
    .copy('src/assetbundles/src/img/', 'src/assetbundles/dist/img/');