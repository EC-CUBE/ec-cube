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

/////////// database choice
	var hideParameters = function() {
		$(".required").hide();
		$("#install_step4_database_host, "
		  + "#install_step4_database_port, "
		  + "#install_step4_database_name, "
		  + "#install_step4_database_user, "
		  + "#install_step4_database_password").attr("disabled", "disabled");
	}
	,
	showParameters = function() {
		$(".required").show();
		$("#install_step4_database_host, "
		  + "#install_step4_database_port, "
		  + "#install_step4_database_name, "
		  + "#install_step4_database_user, "
		  + "#install_step4_database_password").removeAttr("disabled");
	};
	var database = $("#install_step4_database").val();
	if (database == 'pdo_sqlite') {
		hideParameters();
	} else {
		showParameters();
	}
	$("#install_step4_database").change(function() {
		var database = $(this).val();
		if (database == 'pdo_sqlite') {
			hideParameters();
		} else {
			showParameters();
		}
	});

/////////// 特定の条件下でのみ入力を許可する
    // ロードバランサー、プロキシ設定
    $("[name*='[trusted_proxies_connection_only]']").change(function() {
        if ($(this).prop("checked")) {
            $("[name*='[trusted_proxies]']").prop("readonly", "readonly");
        } else {
            $("[name*='[trusted_proxies]']").prop("readonly", null);
        }
    });



});
