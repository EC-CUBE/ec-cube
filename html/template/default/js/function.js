$(function(){	
	
	/////////// Drawer menu
	
	$('.nav-trigger').on('click', function(event){
		event.preventDefault();
		if( $('.drawer-open #drawer').size()==0){
			$('.overlay').addClass('is-visible');
			$('#wrapper').addClass('drawer-open');
		} else {
			$('#wrapper').removeClass('drawer-open');
			$('.overlay').removeClass('is-visible');
			toggleCart('close')
		}
		return false;
	});
	
	//open cart form
	$('.cart-trigger').on('click', function(event){
		event.preventDefault();
		toggleCart();
		$('#wrapper').removeClass('drawer-open');
		
	});

	//close lateral menu on mobile 
	$('.overlay').on('swiperight', function(){
		if($('#wrapper').hasClass('drawer-open')) {
			$('#wrapper').removeClass('drawer-open');
			$('.overlay').removeClass('is-visible');
		}
	});
	$('.overlay').on('click', function(){
		$('#wrapper').removeClass('drawer-open');
		toggleCart('close')
		$('.overlay').removeClass('is-visible');
	});

	function toggleCart(type) {
		if(type=="close") {
			//close cart
			$('.cart').removeClass('is-visible');
			$('.cart-trigger').removeClass('cart-is-visible');
			$('.overlay').removeClass('cart-is-visible');
		} else {
			//toggle cart visibility
			$('.cart').toggleClass('is-visible');
			$('.cart-trigger').toggleClass('cart-is-visible');
			$('.overlay').toggleClass('cart-is-visible');
			($('.cart').hasClass('is-visible')) ? $('.overlay').addClass('is-visible') : $('.overlay').removeClass('is-visible') ;
		}
	}
	

	/////////// category accordion
	$("#category li .toggle").on('click', function(){
		var togglepanel = $(this).parent('a').next('ul');
		if(togglepanel.css("display")=="none"){
			$(this).parent('a').addClass("active");
			togglepanel.slideDown(300);
		}else{
			$(this).parent('a').removeClass("active");
			togglepanel.slideUp(300);
		}
		return false;
	});

	/////////// アコーディオン
	$(".accordion dl dt").on('click', function(){
		if( $(this).parent('dl').children('dd').css('display') == 'none') {
			$(this).addClass('active');
			$(this).parent('dl').children('dd').slideDown(300);
		} else {
			$(this).removeClass('active');
			$(this).parent('dl').children('dd').slideUp(300);
		}
		return false;
	});

	/////////// スムーススクロール
	$('a.anchor').on('click', function() {
		var speed = 400;//スクロール速度 単位：ミリ秒
		var href= $(this).attr("href");
		var destination = $(href == "#" || href == "" ? 'html' : href);
		var position = destination.offset().top;
		$("html,body").animate({scrollTop:position}, speed, 'swing');
		return false;
	});	
		
	/////////// dropdownの中をクリックしても閉じないようにする
	$(".dropdown-menu").click(function(e) {
		e.stopPropagation();
	});
	
	/////////// 追従サイドバー + ページトップフェードイン
		
	// スクロールした時に以下の処理        
	$(window).on("scroll", function() {
		// ページトップフェードイン
		if ($(this).scrollTop() > 300) {
			$('.pagetop').fadeIn();
		} else {
			$('.pagetop').fadeOut();
		}
	
		//PC表示の時のみに適用
		if (window.innerWidth > 767){
			
			if ($('#shopping_confirm').length) {

				var	side = $("#confirm_side"),
					wrap = $("#shopping_confirm"),
					min_move = wrap.offset().top,
					max_move = min_move + wrap.height() - side.height() - 2*parseInt(side.css("top") ),
					margin_bottom = max_move - min_move;
				 
					var scrollTop =  $(window).scrollTop();
					if ( scrollTop > min_move && scrollTop < max_move ){
						var margin_top = scrollTop - min_move ;
						side.css({"margin-top": margin_top});
					} else if ( scrollTop < min_move ){
						side.css({"margin-top":0});
					} else if ( scrollTop > max_move ){
						side.css({"margin-top":margin_bottom});
					}
	
			}			
		}
		return false;
	});
	
		
});


/////////// ロールオーバー
$.fn.rollover = function() {
   return this.each(function() {
      var src = $(this).attr('src');
      if (src.match('_on.')) return;
      var src_on = src.replace(/^(.+)(\.[a-z]+)$/, "$1_on$2");
      $('').attr('src', src_on);
      $(this).hover(
         function() { $(this).attr('src', src_on); },
         function() { $(this).attr('src', src); }
      );
   });
};

// 画像をロールオーバーする箇所(imgタグ)を指定
$(function() {
   $('.rollover').rollover();
});



/////////// 高さ揃え
/**
* jquery.matchHeight-min.js v0.6.0
* http://brm.io/jquery-match-height/
* License: MIT
*/
(function(c){var n=-1,f=-1,g=function(a){return parseFloat(a)||0},r=function(a){var b=null,d=[];c(a).each(function(){var a=c(this),k=a.offset().top-g(a.css("margin-top")),l=0<d.length?d[d.length-1]:null;null===l?d.push(a):1>=Math.floor(Math.abs(b-k))?d[d.length-1]=l.add(a):d.push(a);b=k});return d},p=function(a){var b={byRow:!0,property:"height",target:null,remove:!1};if("object"===typeof a)return c.extend(b,a);"boolean"===typeof a?b.byRow=a:"remove"===a&&(b.remove=!0);return b},b=c.fn.matchHeight=
function(a){a=p(a);if(a.remove){var e=this;this.css(a.property,"");c.each(b._groups,function(a,b){b.elements=b.elements.not(e)});return this}if(1>=this.length&&!a.target)return this;b._groups.push({elements:this,options:a});b._apply(this,a);return this};b._groups=[];b._throttle=80;b._maintainScroll=!1;b._beforeUpdate=null;b._afterUpdate=null;b._apply=function(a,e){var d=p(e),h=c(a),k=[h],l=c(window).scrollTop(),f=c("html").outerHeight(!0),m=h.parents().filter(":hidden");m.each(function(){var a=c(this);
a.data("style-cache",a.attr("style"))});m.css("display","block");d.byRow&&!d.target&&(h.each(function(){var a=c(this),b="inline-block"===a.css("display")?"inline-block":"block";a.data("style-cache",a.attr("style"));a.css({display:b,"padding-top":"0","padding-bottom":"0","margin-top":"0","margin-bottom":"0","border-top-width":"0","border-bottom-width":"0",height:"100px"})}),k=r(h),h.each(function(){var a=c(this);a.attr("style",a.data("style-cache")||"")}));c.each(k,function(a,b){var e=c(b),f=0;if(d.target)f=
d.target.outerHeight(!1);else{if(d.byRow&&1>=e.length){e.css(d.property,"");return}e.each(function(){var a=c(this),b={display:"inline-block"===a.css("display")?"inline-block":"block"};b[d.property]="";a.css(b);a.outerHeight(!1)>f&&(f=a.outerHeight(!1));a.css("display","")})}e.each(function(){var a=c(this),b=0;d.target&&a.is(d.target)||("border-box"!==a.css("box-sizing")&&(b+=g(a.css("border-top-width"))+g(a.css("border-bottom-width")),b+=g(a.css("padding-top"))+g(a.css("padding-bottom"))),a.css(d.property,
f-b))})});m.each(function(){var a=c(this);a.attr("style",a.data("style-cache")||null)});b._maintainScroll&&c(window).scrollTop(l/f*c("html").outerHeight(!0));return this};b._applyDataApi=function(){var a={};c("[data-match-height], [data-mh]").each(function(){var b=c(this),d=b.attr("data-mh")||b.attr("data-match-height");a[d]=d in a?a[d].add(b):b});c.each(a,function(){this.matchHeight(!0)})};var q=function(a){b._beforeUpdate&&b._beforeUpdate(a,b._groups);c.each(b._groups,function(){b._apply(this.elements,
this.options)});b._afterUpdate&&b._afterUpdate(a,b._groups)};b._update=function(a,e){if(e&&"resize"===e.type){var d=c(window).width();if(d===n)return;n=d}a?-1===f&&(f=setTimeout(function(){q(e);f=-1},b._throttle)):q(e)};c(b._applyDataApi);c(window).bind("load",function(a){b._update(!1,a)});c(window).bind("resize orientationchange",function(a){b._update(!0,a)})})(jQuery);

// 高さ揃えの要素を指定
$(function() {
	
	$('.pickup_item').matchHeight({
		byRow: true,
		property: 'height'
	});
	
	$('.product_item .item_name').matchHeight({
		byRow: true,
		property: 'height'
	});
	$('button.thumbnail').matchHeight({
		byRow: true,
		property: 'height'
	});
	$('#login_box > div').matchHeight({
	});
});
