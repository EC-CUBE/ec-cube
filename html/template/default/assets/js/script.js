$(function() {
    $(".ec-headerNavSP").on("click",function(){
      $(".ec-layoutRole").toggleClass("is_active");
      $(".ec-drawerRole").toggleClass("is_active");
      $(".ec-drawerRoleClose").toggleClass("is_active");
      $("body").toggleClass("have_curtain");
    });

    $(".ec-overlayRole").on("click",function(){
      $("body").removeClass("have_curtain");
      $(".ec-layoutRole").removeClass("is_active");
      $(".ec-drawerRole").removeClass("is_active");
      $(".ec-drawerRoleClose").removeClass("is_active");
    });

    $(".ec-drawerRoleClose").on("click",function(){
      $("body").removeClass("have_curtain");
      $(".ec-layoutRole").removeClass("is_active");
      $(".ec-drawerRole").removeClass("is_active");
      $(".ec-drawerRoleClose").removeClass("is_active");
    });

    // TODO: カート展開時のアイコン変更処理
    $('.ec-headerRole__cart').on('click', '.ec-cartNavi', function() {
        // $('.ec-cartNavi').toggleClass('is-active');
        $('.ec-cartNaviIsset').toggleClass('is-active');
        $(".ec-cartNaviNull").toggleClass("is-active")
    });

    $('.ec-headerRole__cart').on('click', '.ec-cartNavi--cancel', function() {
        // $('.ec-cartNavi').toggleClass('is-active');
        $('.ec-cartNaviIsset').toggleClass('is-active');
        $(".ec-cartNaviNull").toggleClass("is-active")
    });

    $(".ec-newsRole__newsCloseBtn").on("click",function(){
      $(this).parents(".ec-newsRole__newsItem").toggleClass("is_active")
    });

    // マイページ　購入履歴　メール配信履歴一覧の折りたたみ
    $('.ec-orderMail__link').on('click', function() {
        $(this).siblings('.ec-orderMail__body').slideToggle();
    });

    $('.ec-orderMail__close').on('click', function() {
        $(this).parent().slideToggle();
    });

    $('.is_inDrawer').each(function() {
        var html = $(this).html();
        $(html).appendTo('.ec-drawerRole');
    });

    $('.ec-blockTopBtn').on('click', function() {
        $('html,body').animate({'scrollTop': 0}, 500);
    });

    // スマホのドロワーメニュー内の下層カテゴリ表示
    // TODO FIXME スマホのカテゴリ表示方法
    $('.ec-itemNav ul a').click(function() {
        var child = $(this).siblings();
        if(child.length > 0 ){
            if(child.is(':visible')){
                return true;
            }else{
                child.slideToggle();
                return false;
            }
        }
    })
});

// Slick Slide
// TODO FIX CLASS NAME
$(function() {
    $('.main_visual').slick({
        dots: true,
        arrows: false,
        autoplay: true,
        speed: 300
    });

    $('.item_visual').slick({
        dots: false,
        arrows: false,
        responsive: [{
            breakpoint: 768,
            settings: {
                dots: true
            }
        }]
    });

    $('.slideThumb').on("click",function(){
      var index = $(this).attr("data-index");
      $(".item_visual").slick("slickGoTo",index,false);
    })
});
