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

function setTopButton(topURL) {
    if(!topURL){
        topURL = "/";
    }
    var buttonText = "TOPへ";
    var buttonId = "btn-top";

    //ボタンの生成・設定
    var btn = document.createElement('div');
    var a = document.createElement('a');
    btn.id = buttonId;
    btn.onclick = function(){location=topURL;};
    a.href = topURL;
    a.innerText = buttonText;

    /* 背景色の設定 ---------------------*/
    //最初の見出しの背景色を取得、設定
    var obj = document.getElementsByTagName('h2')[0];
    var col = document.defaultView.getComputedStyle(obj,null).getPropertyValue('background-color');
    btn.style.backgroundColor = col;

    //省略表示用テキストの生成
    var spn = document.createElement('span');
    spn.innerText = obj.innerText;
    obj.innerText = "";
    spn.style.display = "inline-block";
    spn.style.maxWidth = "50%";
    spn.style.overflow = "hidden";
    spn.style.textOverflow = "ellipsis";
    obj.appendChild(spn);

    //ボタンを追加
    btn.appendChild(a);
    document.getElementsByTagName('body')[0].appendChild(btn);;
}

function smartRollover() {
    if (document.getElementsByTagName) {
        var images = document.getElementsByTagName("img");

        for (var i=0; i < images.length; i++) {
            if (images[i].getAttribute("src").match("_off.")) {
                images[i].onmouseover = function() {
                    this.setAttribute("src", this.getAttribute("src").replace("_off.", "_on."));
                }
                images[i].onmouseout = function() {
                    this.setAttribute("src", this.getAttribute("src").replace("_on.", "_off."));
                }
            }
        }
    }
}

if (window.addEventListener) {
    window.addEventListener("load", smartRollover, false);
} else if (window.attachEvent) {
    window.attachEvent("onload", smartRollover);
}

/*------------------------------------------
 お気に入りを登録する
 ------------------------------------------*/
function fnAddFavoriteSphone(favoriteProductId) {
    $.mobile.showPageLoadingMsg();
    //送信データを準備
    var postData = {};
    $("#form1").find(':input').each(function(){
        postData[$(this).attr('name')] = $(this).val();
    });
    postData["mode"] = "add_favorite_sphone";
    postData["favorite_product_id"] = favoriteProductId;

    $.ajax({
        type: "POST",
        url: $("#form1").attr('action'),
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
}
