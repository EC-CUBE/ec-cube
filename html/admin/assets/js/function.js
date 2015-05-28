/*!
 * function.js for EC-CUBE admin
 */

jQuery(document).ready(function($){
	

/*
 * Brake point Check
 */

	
	$(window).on('load , resize', function(){
		if(window.innerWidth < 768){		
			$('body').addClass('sp_view');
			$('body').removeClass('md_view');
			$('body').removeClass('pc_view');
			$('#wrapper').removeClass('sidebar-open'); // for Drawer menu
		} else if (window.innerWidth < 992) {
			$('body').removeClass('sp_view');
			$('body').addClass('md_view');
			$('body').removeClass('pc_view');
			$('#wrapper').addClass('sidebar-open'); // for Drawer menu
		} else {
			$('body').removeClass('sp_view');
			$('body').removeClass('md_view');
			$('body').addClass('pc_view');
			$('#wrapper').addClass('sidebar-open'); // for Drawer menu
//			$(window).on('scroll',fix_scroll);
		}
		return false;
	});


/*
 * Drawer menu
 */

	$('.bt_drawermenu').on('click', function(){
		if( $('.sidebar-open #side').size()==0){
			$('#wrapper').addClass('sidebar-open');
		} else {
			$('#wrapper').removeClass('sidebar-open');
		}
		return false;
	});
		

/////////// SideBar accordion

	$("#side li .toggle").click(function(){
		if($("+ul",this).css("display")=="none"){
			$(this).parent('li').addClass("active");
			$("+ul",this).slideDown(300);
		}else{
			$(this).parent('li').removeClass("active");
			$("+ul",this).slideUp(300);
		}
        return false;
	});

/////////// accordion

	$(".accordion .toggle").click(function(){
		if($("+.accpanel",this).css("display")=="none"){
			$(this).addClass("active");
			$("+.accpanel",this).slideDown(300);
		}else{
			$(this).removeClass("active");
			$("+.accpanel",this).slideUp(300);
		}
        return false;
	});


/////////// dropdownの中をクリックしても閉じないようにする

    $(".dropdown-menu").click(function(e) {
        e.stopPropagation();
    });



/////////// 追従サイドバー
	
	// スクロールした時に以下の処理        
	$(window).on("scroll", function() {
		//PC表示の時のみに適用
		if (window.innerWidth > 993){
			
			if ($('#aside_wrap').length) {
			
				var	side = $("#aside_column"),
					wrap = $("#aside_wrap"),
					heightH = $("#header").outerHeight(),
					min_move = wrap.offset().top,
					max_move = wrap.offset().top + wrap.height() - side.height() - 2*parseInt(side.css("top") ),
					margin_bottom = max_move - min_move;
				 
					var scrollTop =  $(window).scrollTop();
					if( scrollTop > min_move && scrollTop < max_move ){
						var margin_top = scrollTop - min_move ;
						side.css({"margin-top": margin_top + heightH + 10});
					} else if( scrollTop < min_move ){
						side.css({"margin-top":0});
					}else if( scrollTop > max_move ){
						side.css({"margin-top":margin_bottom});
					}
			}
			
		}
			
		return false;
	});


//	var fixedcolumn = $('#aside_column'),
//	offset = fixedcolumn.offset();
//
//	$(window).scroll(function () {
//	  if($(window).scrollTop() > offset.top - 80) {
//		fixedcolumn.addClass('fixed');
//	  } else {
//		fixedcolumn.removeClass('fixed');
//	  }
//	});


});
