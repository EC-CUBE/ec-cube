/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

(function( window, undefined ){

    // 名前空間の重複を防ぐ
    if (window.eccube === undefined) {
        window.eccube = {};
    }

    var eccube = window.eccube;

    eccube.smartRollover = function() {
        if (document.getElementsByTagName) {
            var images = $("img");
            var re = /_off¥./;

            images.each(function(){
                var target = $(this);
                if (target.attr("src").match(re)) {
                    target.on("vmouseover", function(){
                        this.setAttribute("src", this.getAttribute("src").replace("_off.", "_on."));
                    });
                    target.on("vmouseout", function(){
                        this.setAttribute("src", this.getAttribute("src").replace("_on.", "_off."));
                    });
                }
            });
        }
    };

    if (window.addEventListener) {
        window.addEventListener("load", eccube.smartRollover, false);
    } else if (window.attachEvent) {
        window.attachEvent("onload", eccube.smartRollover);
    }

    /*------------------------------------------
     お気に入りを登録する
     ------------------------------------------*/
    eccube.addFavoriteSphone = function(favoriteProductId) {
        eccube.showLoading();
        //送信データを準備
        var postData = {};
        var form = $("#form1");
        form.find(':input').each(function(){
            postData[$(this).attr('name')] = $(this).val();
        });
        postData.mode = "add_favorite_sphone";
        postData.favorite_product_id = favoriteProductId;

        $.ajax({
            type: "POST",
            url: form.attr('action'),
            data: postData,
            cache: false,
            dataType: "text",
            error: function(XMLHttpRequest, textStatus){
                window.alert(textStatus);
                eccube.hideLoading();
            },
            success: function(result){
                if (result === "true") {
                    window.alert("お気に入りに登録しました");
                    $(".btn_favorite").html("<p>お気に入り登録済み</p>");
                } else {
                    window.alert("お気に入りの登録に失敗しました");
                }
                eccube.hideLoading();
            }
        });
    };

    /**
     * ローディング画像を表示する
     */
    eccube.showLoading = function() {
        var over = '<div class="loading-overlay"><span class="loading-image"></span></div>';
        $(over).appendTo('body');
    };

    /**
     * ローディング画像を削除する
     */
    eccube.hideLoading = function() {
        $('.loading-overlay').remove();
    };

    // グローバルに使用できるようにする
    window.eccube = eccube;

    /*------------------------------------------
     初期化
     ------------------------------------------*/
    $(function(){
        //level?クラスを持つノード全てを走査し初期化
        $("#categorytree").find("li").each(function(){
            if ($(this).find("> ul").length) {
                //▶を表示し、リストオープンイベントを追加
                var tgt = $(this).find("> span.category_header");
                var linkObj = $("<a>");
                linkObj.html('＋');
                tgt
                    .click(function(){
                        $(this).siblings("ul").toggle('fast', function(){
                            if ($(this).css('display') === 'none') {
                                tgt.children('a').html('＋');
                            } else {
                                tgt.children('a').html('－');
                            }
                        });
                    })
                    .addClass('plus')
                    .append(linkObj);
            }
        });
    });
})(window);
