//mainNavArea　toggle
var mainNavArea = function(){
    $(function () {
        $(".c-headerBar__toggleBtn").on('click',function () {
            $(".c-mainNavArea").toggleClass("is-active");
            $(".c-curtain").toggleClass("is-active");
        });

        $(".c-curtain").on('click',function () {
            $(".c-mainNavArea").toggleClass("is-active");
            $(".c-curtain").toggleClass("is-active");
        });
    })
};
mainNavArea();

//Bootstrap ツールチップ
var toolTip = function(){
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
};

toolTip();

//popover ポップオーバー
// header
var popoverHeader = function(){
    $(function () {
        $('.c-headerBar__userMenu').popover({
            container: 'body'
        })
    })
};
popoverHeader();
// all page
var popoverAll = function(){
    $(function () {
        $('[data-toggle="popover"]').popover()
    })
};
popoverAll();

//collapseIconChange　collapseと連動するアイコン変化
var collapseIconMinus = function(){
    $(function () {
        $('.ec-collapse').on('shown.bs.collapse', function () {
            var id = $(this).attr("id");
            var icon = $("[href='#"+id+"']").find('i')
            icon.removeClass("fa-plus-square-o")
            icon.addClass("fa-minus-square-o")
        })
    })
};
collapseIconMinus();

var collapseIconPlus = function(){
    $(function () {
        $('.ec-collapse').on('hidden.bs.collapse', function () {
            var id = $(this).attr("id");
            var icon = $("[href='#"+id+"']").find('i')
            icon.removeClass("fa-minus-square-o")
            icon.addClass("fa-plus-square-o")
        })
    })
};
collapseIconPlus();


//cardCollapseIconChange　カードコンポーネントのcollapseと連動するアイコン変化
var cardCollapseIconDown = function(){
    $(function () {
        $('.ec-cardCollapse').on('hidden.bs.collapse', function () {
            var id = $(this).attr("id");
            var icon = $("[href='#"+id+"']").find('i')
            icon.removeClass("fa-angle-up")
            icon.addClass("fa-angle-down")
        })
    })
};
cardCollapseIconDown();

var cardCollapseIconUp = function(){
    $(function () {
        $('.ec-cardCollapse').on('shown.bs.collapse', function () {
            var id = $(this).attr("id");
            var icon = $("[href='#"+id+"']").find('i')
            icon.removeClass("fa-angle-down")
            icon.addClass("fa-angle-up")
        })
    })
};
cardCollapseIconUp();

// toggle bulk button
var toggleBtnBulk = function(checkboxSelector, btnSelector) {
    $(function () {
        if ($(checkboxSelector + ':checked').length) {
            $(btnSelector).fadeIn('fast').addClass('d-block').removeClass('d-none');
        } else {
            $(btnSelector).fadeOut('fast', function() {
                $(this).addClass('d-none').removeClass('d-block');
            })
        }
    });
};


