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

$(function() {
    $(".ec-itemNav ul a").click(function(){$(this).siblings().slideToggle();})
});