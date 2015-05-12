/*!
 * function.js for EC-CUBE admin
 */

/*
 * Brake point Check
 */

jQuery(document).ready(function($){
	
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

//function fix_scroll() {
//	var s = $(window).scrollTop();
//	var fixedcolumn = $('.pc_view #aside_column');
//	fixedcolumn.css('position','absolute');
//	fixedcolumn.css('top',s + 'px');
//}fix_scroll();



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
		

// SideBar accordion

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

// accordion

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


// dropdownの中をクリックしても閉じないようにする

    $(".dropdown-menu").click(function(e) {
        e.stopPropagation();
    });


// サイドのナビゲーションを固定に

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
