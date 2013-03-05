/*------------------------------------------
初期化
------------------------------------------*/
//level?クラスを持つノード全てを走査し初期化
$(function(){
    $("#categorytree li").each(function(){
        if ($(this).children("ul").length) {
            //▶を表示し、リストオープンイベントを追加
            var tgt = $(this).children('span.category_header');
            var linkObj = $("<a>");
            linkObj.text('＋');
            tgt
                .click(function(){
                    $(this).siblings("ul").toggle('fast', function(){
                        if ($(this).css('display') === 'none') {
                            tgt.children('a').text('＋');
                        } else {
                            tgt.children('a').text('－');
                        }
                    });
                })
                .addClass('plus')
                .append(linkObj);
        }
    });
});