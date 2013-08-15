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
            var images = document.getElementsByTagName("img");

            for (var i=0; i < images.length; i++) {
                if (images[i].getAttribute("src").match("_off.")) {
                    images[i].onmouseover = function() {
                        this.setAttribute("src", this.getAttribute("src").replace("_off.", "_on."));
                    };
                    images[i].onmouseout = function() {
                        this.setAttribute("src", this.getAttribute("src").replace("_on.", "_off."));
                    };
                }
            }
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
        $.mobile.showPageLoadingMsg();
        //送信データを準備
        var postData = {};
        var form = $("#form1");
        form.find(':input').each(function(){
            postData[$(this).attr('name')] = $(this).val();
        });
        postData["mode"] = "add_favorite_sphone";
        postData["favorite_product_id"] = favoriteProductId;

        $.ajax({
            type: "POST",
            url: form.attr('action'),
            data: postData,
            cache: false,
            dataType: "text",
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert(textStatus);
                $.mobile.hidePageLoadingMsg();
            },
            success: function(result){
                if (result == "true") {
                    alert("お気に入りに登録しました");
                    $(".btn_favorite").html("<p>お気に入り登録済み</p>");
                } else {
                    alert("お気に入りの登録に失敗しました");
                }
                $.mobile.hidePageLoadingMsg();
            }
        });
    };

    // グローバルに使用できるようにする
    window.eccube = eccube;

    /*------------------------------------------
     初期化
     ------------------------------------------*/
    $(function(){
        //level?クラスを持つノード全てを走査し初期化
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
})(window);
