<?php
require_once '../../../../require.php';
header('Content-Type: application/x-javascript');
?>
/*
 * Thickbox 3.1 - One Box To Rule Them All.
 * By Cody Lindley (http://www.codylindley.com)
 * Copyright (c) 2007 cody lindley
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
*/

/*
 * ownersstore.js
 *
 * オーナーズストア通信用ライブラリ.
 * CSSやjavascriptのオーバーレイ処理はThickboxのものを使っています.
 *
*/

(function() {
// オーナーズストア通信スクリプトのパス
var upgrade_url = '<?php echo ROOT_URLPATH ?>upgrade/<?php echo DIR_INDEX_PATH; ?>';

// ロード中メッセージ「サーバーと通信中です」
var loading_message = '\u30b5\u30fc\u30d0\u30fc\u3068\u901a\u4fe1\u4e2d\u3067\u3059';

// ロード中画像の先読み
var loading_img = new Image();
loading_img.src = '<?php echo ROOT_URLPATH . USER_DIR ?>packages/default/img/ajax/loading.gif';

var OwnersStore = function() {}
OwnersStore.prototype = {
    // detect Mac and Firefox use.
    detectMacFF: function() {
        var ua = navigator.userAgent.toLowerCase();
        if (ua.indexOf('mac') != -1 && ua.indexOf('firefox') != -1) {
            return true;
        }
    },
    // remove ajax window
    remove: function() {
        $("#TB_window").fadeOut(
            'fast',
            function(){
                $('#TB_window,#TB_overlay,#TB_HideSelect').trigger('unload').unbind().remove();
            }
        );
        $("#TB_load").remove();
        //if IE 6
        if (typeof document.body.style.maxHeight == 'undefined') {
            $('body', 'html').css({height: 'auto', width: 'auto'});
            $('html').css('overflow', "");
        }
        return false;
    },
    // show loading page
    show_loading: function() {
        //if IE 6
        if (typeof document.body.style.maxHeight === 'undefined') {
            $('body','html').css({height: "100%", width: "100%"});
            $('html').css('overflow','hidden');
            //iframe to hide select elements in ie6
            if (document.getElementById('TB_HideSelect') === null) {
                $('body').append("<iframe id='TB_HideSelect'></iframe><div id='TB_overlay'></div><div id='TB_window'></div>");
                $("#TB_overlay").click(this.remove);
            }
        //all others
        } else {
            if (document.getElementById('TB_overlay') === null) {
                $('body').append("<div id='TB_overlay'></div><div id='TB_window'></div>");
                $("#TB_overlay").click(this.remove);
            }
        }

        if (this.detectMacFF()) {
            //use png overlay so hide flash
            $("#TB_overlay").addClass('TB_overlayMacFFBGHack');
        } else {
            //use background and opacity
            $("#TB_overlay").addClass('TB_overlayBG');
        }

        //add and show loader to the page
        $('body').append(
              "<div id='TB_load'>"
            + "  <p style='color:#ffffff'>" + loading_message + "</p>"
            + "  <img src='" + loading_img.src + "' />"
            + "</div>"
        );
        $('#TB_load').show();
    },

    // show results
    show_result: function(resp, status, product_id) {
        var title    = resp.status || 'ERROR';
        var contents = resp.msg || '';

        var TB_WIDTH = 400;
        var TB_HEIGHT = 300;
        var ajaxContentW = TB_WIDTH - 20;
        var ajaxContentH = TB_HEIGHT - 45;

        if ($("#TB_window").css('display') != 'block') {
            $("#TB_window").append(
                "<div id='TB_title'>"
              + "  <div id='TB_ajaxWindowTitle'></div>"
              + "  <div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton' onclick='OwnersStore.remove();'>close</a></div>"
              + "</div>"
              + "<div id='TB_ajaxContent' style='width:" + ajaxContentW + "px;height:" + ajaxContentH + "px'>"
              + "</div>"
            );
         //this means the window is already up, we are just loading new content via ajax
        } else {
            $("#TB_ajaxContent")[0].style.width = ajaxContentW +'px';
            $("#TB_ajaxContent")[0].style.height = ajaxContentH +'px';
            $("#TB_ajaxContent")[0].scrollTop = 0;
        }

        $("#TB_load").remove();
        $("#TB_window").css({marginLeft: '-' + parseInt((TB_WIDTH / 2),10) + 'px', width: TB_WIDTH + 'px'});

        // take away IE6
        if (!(jQuery.browser.msie && jQuery.browser.version < 7)) {
            $("#TB_window").css({marginTop: '-' + parseInt((TB_HEIGHT / 2),10) + 'px'});
        }

        $("#TB_ajaxWindowTitle").html(title);
        $("#TB_ajaxContent").html(contents);
        $("#TB_window").css({display:'block'});

        // DL成功時に設定ボタンを表示
        if (resp.status == 'SUCCESS' && product_id) {
            $('#ownersstore_settings_default' + product_id).hide(); // --を非表示
            $('#ownersstore_settings' + product_id).show();         // 設定ボタン表示
            $('#ownersstore_download' + product_id).html('\u30C0\u30A6\u30F3\u30ED\u30FC\u30C9');     // アップデートボタンを「ダウンロード」へ変換
            $('#ownersstore_version' + product_id).html(resp.data.version);
        }
    },

    // exexute install or update
    download: function(product_id) {
        this.show_loading();
        var show = this.show_result;
        $.post(
            upgrade_url,
            {mode: 'download', product_id: product_id},
            function(resp, status) {
                show(resp, status, product_id);
            },
            'json'
        )
    },

    // get products list
    products_list: function() {
        this.show_loading();
        var show = this.show_result;
        var remove = this.remove;
        $().ajaxError(this.show_result);
        $.post(
            upgrade_url,
            {mode: 'products_list'},
            function(resp, status) {
                if (resp.status == 'SUCCESS') {
                    remove();
                    $('#ownersstore_index').hide();
                    $('#ownersstore_products_list').html(resp.msg);
                } else {
                    show(resp, status);
                }
            },
            'json'
        )
    }
}
window.OwnersStore = new OwnersStore();
})();
