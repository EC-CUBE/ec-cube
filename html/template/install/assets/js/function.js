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




});
