$(function() {
    $('.ec-headerNavSP__itemMenu').on('click', function() {
        $('.ec-layoutRole').toggleClass('is_active');
        $('.ec-drawerRole').toggleClass('is_active');
        $('body').toggleClass('have_curtain');
    });

    $('.ec-overlayRole').on('click', function() {
        $('body').removeClass('have_curtain');
        $('.ec-layoutRole').removeClass('is_active');
        $('.ec-drawerRole').removeClass('is_active');
    });

    $(document).on('click', '.ec-cartNavi', function() {
        $('.ec-headerRole__cart').toggleClass('is_active');
    });

    $(document).on('click', '.ec-cartNavi--cancel', function() {
        $('.ec-headerRole__cart').toggleClass('is_active');
    });

    $('.ec-newsline__close').on('click', function() {
        $(this).parents('.ec-newsline').toggleClass('is_active');
    });

    $('.ec-orderMail__link').on('click', function() {
        $('.ec-orderMail__body').slideToggle();
    });

    $('.ec-orderMail__close').on('click', function() {
        $('.ec-orderMail__body').slideToggle();
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

    $('.item_nav').slick({//サムネイル画像
        dots: false,
        arrows: false,
        slidesToShow: 3,
        focusOnSelect: true,
        asNavFor: '.item_visual' //スライダー部分の要素を記述
    });
});
