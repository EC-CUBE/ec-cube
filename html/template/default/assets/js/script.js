/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports) {

$(function(){
    $(".ec-headerNavSP__itemMenu").on("click",function(){
        $(".ec-layoutRole").toggleClass("is_active")
        $(".ec-drawerRole").toggleClass("is_active")
        $("body").toggleClass("have_curtain")
    })
})
$(function(){
    $(".ec-overlayRole").on("click",function(){
        $("body").removeClass("have_curtain")
        $(".ec-layoutRole").removeClass("is_active")
        $(".ec-drawerRole").removeClass("is_active")
    })
})

$(function(){
    $(".ec-cartNavi").on("click",function(){
        $(".ec-headerRole__cart").toggleClass("is_active")
    })
})
$(function(){
    $(".ec-cartNavi--cancel").on("click",function(){
        $(".ec-headerRole__cart").toggleClass("is_active")
    })
})

$(function(){
    $(".ec-newsline__close").on("click",function(){
        $(this).parents(".ec-newsline").toggleClass("is_active")
    })
})

$(function(){
    $(".ec-orderMail__link").on("click",function(){
        $(".ec-orderMail__body").slideToggle();
    })
})
$(function(){
    $(".ec-orderMail__close").on("click",function(){
        $(".ec-orderMail__body").slideToggle();

    })
})

$(function(){
    $(".is_inDrawer").each(function(){
        var html = $(this).html();
        $(html).appendTo(".ec-drawerRole")
    })
})

$(function(){
    $(".ec-blockTopBtn").on("click",function(){
        $("html,body").animate({"scrollTop":0},500);
    })
})


// Slick Slide
// TODO FIX CLASS NAME
$(function(){
    $('.main_visual').slick({
        dots: true,
        arrows: false,
        autoplay: true,
        speed: 300
    });
});


// Slick Slide
// TODO FIX CLASS NAME
$(function(){
    $('.item_visual').slick({
        dots: false,
        arrows: false,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    dots: true
                }
            }]
    })
});
$(function(){
    $('.item_nav').slick({//サムネイル画像
        dots: false,
        arrows:false,
        slidesToShow: 3,
        focusOnSelect: true,
        asNavFor: '.item_visual',//スライダー部分の要素を記述
    })
});

/***/ })
/******/ ]);