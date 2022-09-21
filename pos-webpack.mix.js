let mix = require('laravel-mix');                           // If you are new to this then please visit https://laravel.com/docs/5.5/mix
const webpack = require('webpack');


var plugin =  'resources/plugins/';

mix.js('resources/js/pos/app.js', 'public/pos_assets/js/app.js')
  .extract(['vue', 'jquery']);
mix.sass('resources/sass/pos.scss', 'public/pos_assets/css');

mix.combine([
    plugin + 'moment/moment.min.js',
    plugin + 'toastr/toastr.min.js',
    // plugin + 'pinpad/jquery-ui.js',
    // plugin + 'pinpad/jquery.ui.pinpad.js',
    // plugin + 'pinpad/jquery-ui-pinpad-extension.js',
],'public/pos_assets/js/custom.js');

if (mix.inProduction()) {                       // In production environtment use versioning
    mix.version();
}

