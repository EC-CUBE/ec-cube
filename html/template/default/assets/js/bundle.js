const $ = window.$ = window.jQuery = require('jquery');

// slick-carousel 2.x の CommonJS エントリは factory を返すため、jQuery を渡して呼び出す。
require('slick-carousel')(window, $);
require('slick-carousel/slick/slick.css');
require('slick-carousel/slick/slick-theme.css');

require('bootstrap');
