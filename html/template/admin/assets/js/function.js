/*!
 * function.js for EC-CUBE admin
 */

jQuery(document).ready(function($){

    /*
     * Brake point Check
     */


    $(window).on('load , resize', function(){
        $('body').removeClass('pc_view md_view sp_view');
        if(window.innerWidth < 768){
            $('body').addClass('sp_view');
            $('#wrapper').removeClass('sidebar-open'); // for Drawer menu
        } else if (window.innerWidth < 992) {
            $('body').addClass('md_view');
            $('#wrapper').addClass('sidebar-open'); // for Drawer menu
        } else {
            $('body').addClass('pc_view');
            $('#wrapper').addClass('sidebar-open'); // for Drawer menu
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




/////////// 検索条件をクリア
    $('.search-clear').click(function(event){
        event.preventDefault(event);
        $('.search-box-inner input, .search-box-inner select').each(function(){
            if (this.type == "checkbox" || this.type == "radio") {
                this.checked = false;
            } else {
                if (this.type == "hidden") {
                    if (!this.name.match(/_token/i)) {
                        $(this).val("");
                    }
                } else {
                    $(this).val("");
                }
            }
        });
    });

});
