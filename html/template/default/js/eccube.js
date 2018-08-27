/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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

(function(window, undefined) {


    // 名前空間の重複を防ぐ
    if (window.eccube === undefined) {
        window.eccube = {};
    }

    var eccube = window.eccube;

    eccube.defaults = {
        formId: 'form1',
        windowFeatures: {
            scrollbars: 'yes',
            resizable: 'yes',
            toolbar: 'no',
            location: 'no',
            directories: 'no',
            status: 'no',
            focus: true,
            formTarget: ''
        }
    };

    eccube.openWindow = function(URL, name, width, height, option) {
        var features = "width=" + width + ",height=" + height;
        if (option === undefined) {
            option = eccube.defaults.windowFeatures;
        } else {
            option = $.extend(eccube.defaults.windowFeatures, option);
        }
        features = features + ",scrollbars=" + option.scrollbars +
            ",resizable=" + option.resizable +
            ",toolbar=" + option.toolbar +
            ",location=" + option.location +
            ",directories=" + option.directories +
            ",status=" + option.status;
        if (option.hasOwnProperty('menubar')) {
            features = features + ",menubar=" + option.menubar;
        }
        var WIN = window.open(URL, name, features);
        if (option.formTarget !== "") {
            document.forms[option.formTarget].target = name;
        }
        if (option.focus) {
            WIN.focus();
        }
    };

    // 親ウィンドウの存在確認.
    eccube.isOpener = function() {
        var ua = navigator.userAgent;
        if (window.opener) {
            if (ua.indexOf('MSIE 4') !== -1 && ua.indexOf('Win') !== -1) {
                if (window.opener.hasOwnProperty('closed')) {
                    return !window.opener.closed;
                } else {
                    return false;
                }
            } else {
                return typeof window.opener.document === 'object';
            }
        } else {
            return false;
        }
    };

    // 郵便番号入力呼び出し.
    eccube.getAddress = function(php_url, tagname1, tagname2, input1, input2) {
        var zip1 = document.form1[tagname1].value;
        var zip2 = document.form1[tagname2].value;

        if (zip1.length === 3 && zip2.length === 4) {
            $.get(
                php_url,
                {zip1: zip1, zip2: zip2, input1: input1, input2: input2},
                function(data) {
                    var arrData = data.split("|");
                    if (arrData.length > 1) {
                        eccube.putAddress(input1, input2, arrData[0], arrData[1], arrData[2]);
                    } else {
                        window.alert(data);
                    }
                }
            );
        } else {
            window.alert("郵便番号を正しく入力して下さい。");
        }
    };

    // 郵便番号から検索した住所を渡す.
    eccube.putAddress = function(input1, input2, state, city, town) {
        if (state !== "") {
            // 項目に値を入力する.
            document.form1[input1].selectedIndex = state;
            document.form1[input2].value = city + town;
        }
    };

    eccube.setFocus = function(name) {
        if (document.form1.hasOwnProperty(name)) {
            document.form1[name].focus();
        }
    };

    // モードとキーを指定してSUBMITを行う。
    eccube.setModeAndSubmit = function(mode, keyname, keyid) {
        switch (mode) {
            case 'delete_category':
                if (!window.confirm('選択したカテゴリとカテゴリ内の全てのカテゴリを削除します')) {
                    return;
                }
                break;
            case 'delete':
                if (!window.confirm('一度削除したデータは、元に戻せません。\n削除しても宜しいですか？')) {
                    return;
                }
                break;
            case 'confirm':
                if (!window.confirm('登録しても宜しいですか')) {
                    return;
                }
                break;
            case 'delete_all':
                if (!window.confirm('検索結果を全て削除しても宜しいですか')) {
                    return;
                }
                break;
            default:
                break;
        }
        document.form1.mode.value = mode;
        if (keyname !== undefined && keyname !== "" && keyid !== undefined && keyid !== "") {
            document.form1[keyname].value = keyid;
        }
        document.form1.submit();
    };

    eccube.fnFormModeSubmit = function(form, mode, keyname, keyid) {
        switch (mode) {
            case 'delete':
                if (!window.confirm('一度削除したデータは、元に戻せません。\n削除しても宜しいですか？')) {
                    return;
                }
                break;
            case 'cartDelete':
                if (!window.confirm('カゴから商品を削除しても宜しいでしょうか？')) {
                    return;
                }
                mode = 'delete';
                break;
            case 'confirm':
                if (!window.confirm('登録しても宜しいですか')) {
                    return;
                }
                break;
            case 'regist':
                if (!window.confirm('登録しても宜しいですか')) {
                    return;
                }
                break;
            default:
                break;
        }
        var values = {mode: mode};
        if (keyname !== undefined && keyname !== "" && keyid !== undefined && keyid !== "") {
            values[keyname] = keyid;
        }
        eccube.submitForm(values, form);
    };

    eccube.setValueAndSubmit = function(form, key, val, msg) {
        var ret;
        if (msg !== undefined) {
            ret = window.confirm(msg);
        } else {
            ret = true;
        }
        if (ret) {
            var values = {};
            values[key] = val;
            eccube.submitForm(values, form);
        }
        return false;
    };

    eccube.setValue = function(key, val, form) {
        var formElement = eccube.getFormElement(form);
        formElement.find("*[name=" + key + "]").val(val);
    };

    eccube.changeAction = function(url, form) {
        var formElement = eccube.getFormElement(form);
        formElement.attr("action", url);
    };

    // ページナビで使用する。
    eccube.movePage = function(pageno, mode, form) {
        var values = {pageno: pageno};
        if (mode !== undefined) {
            values.mode = mode;
        }
        eccube.submitForm(values, form);
    };

    /**
     * フォームを送信する.
     *
     * @param values
     * @param form
     */
    eccube.submitForm = function(values, form) {
        var formElement = eccube.getFormElement(form);
        if (values !== undefined && typeof values === "object") {
            $.each(values, function(index, value) {
                eccube.setValue(index, value, formElement);
            });
        }
        formElement.submit();
    };

    /**
     * フォームを特定してエレメントを返す.
     *
     * @param form
     * @returns {*}
     */
    eccube.getFormElement = function(form) {
        var formElement;
        if (form !== undefined && typeof form === "string" && form !== "") {
            formElement = $("form#" + form);
        } else if (form !== undefined && typeof form === "object") {
            formElement = form;
        } else {
            formElement = $("form#" + eccube.defaults.formId);
        }
        return formElement;
    };

    // ポイント入力制限。
    eccube.togglePointForm = function() {
        if (document.form1.point_check) {
            var list = ['use_point'];
            var color;
            var flag;

            if (!document.form1.point_check[0].checked) {
                color = "#dddddd";
                flag = true;
            } else {
                color = "";
                flag = false;
            }

            var len = list.length;
            for (var i = 0; i < len; i++) {
                if (document.form1[list[i]]) {
                    var current_color = document.form1[list[i]].style.backgroundColor;
                    if (color !== "#dddddd" && (current_color === "#ffe8e8" || current_color === "rgb(255, 232, 232)")) {
                        continue;
                    }
                    document.form1[list[i]].disabled = flag;
                    document.form1[list[i]].style.backgroundColor = color;
                }
            }
        }
    };

    // 別のお届け先入力制限。
    eccube.toggleDeliveryForm = function() {
        if (!document.form1) {
            return;
        }
        if (document.form1.deliv_check) {
            var list = [
                'shipping_name01',
                'shipping_name02',
                'shipping_kana01',
                'shipping_kana02',
                'shipping_pref',
                'shipping_postal_code',
                'shipping_addr01',
                'shipping_addr02',
                'shipping_phone_number',
                'shipping_company_name',
                'shipping_country_id',
                'shipping_zipcode',
            ];

            if (!document.form1.deliv_check.checked) {
                eccube.changeDisabled(list, '#dddddd');
            } else {
                eccube.changeDisabled(list, '');
            }
        }
    };

    // 最初に設定されていた色を保存しておく。
    eccube.savedColor = [];

    eccube.changeDisabled = function(list, color) {
        var len = list.length;

        for (var i = 0; i < len; i++) {
            if (document.form1[list[i]]) {
                if (color === "") {
                    // 有効にする。
                    document.form1[list[i]].removeAttribute('disabled');
                    document.form1[list[i]].style.backgroundColor = eccube.savedColor[list[i]];
                } else {
                    // 無効にする。
                    document.form1[list[i]].setAttribute('disabled', 'disabled');
                    eccube.savedColor[list[i]] = document.form1[list[i]].style.backgroundColor;
                    document.form1[list[i]].style.backgroundColor = color;//"#f0f0f0";
                }
            }
        }
    };

    // ログイン時の入力チェック
    eccube.checkLoginFormInputted = function(form, emailKey, passKey) {
        var formElement = $("form#" + form);
        var checkItems = [];

        if (typeof emailKey === 'undefined') {
            checkItems[0] = 'login_email';
        } else {
            checkItems[0] = emailKey;
        }
        if (typeof passKey === 'undefined') {
            checkItems[1] = 'login_pass';
        } else {
            checkItems[1] = passKey;
        }

        var max = checkItems.length;
        var errorFlag = false;

        //　必須項目のチェック
        for (var cnt = 0; cnt < max; cnt++) {
            if (formElement.find("input[name=" + checkItems[cnt] + "]").val() === "") {
                errorFlag = true;
                break;
            }
        }

        // 必須項目が入力されていない場合
        if (errorFlag === true) {
            window.alert('メールアドレス/パスワードを入力して下さい。');
            return false;
        } else {
            return true;
        }
    };

    //親ウィンドウのページを変更する.
    eccube.changeParentUrl = function(url) {
        // 親ウィンドウの存在確認
        if (eccube.isOpener()) {
            window.opener.location.href = url;
        } else {
            window.close();
        }
    };

    //文字数をカウントする。
    //引数1：フォーム名称
    //引数2：文字数カウント対象
    //引数3：カウント結果格納対象
    eccube.countChars = function(form, sch, cnt) {
        var formElement = $("form#" + form);
        formElement.find("input[name=" + cnt + "]").val(formElement.find("*[name=" + sch + "]").val().length);
    };

    // テキストエリアのサイズを変更する.
    eccube.toggleRows = function(buttonSelector, textAreaSelector, max, min) {
        if ($(textAreaSelector).attr('rows') <= min) {
            $(textAreaSelector).attr('rows', max);
            $(buttonSelector).text('縮小');
        } else {
            $(textAreaSelector).attr('rows', min);
            $(buttonSelector).text('拡大');
        }
    };

    /**
     * 規格2のプルダウンを設定する.
     */
    eccube.setClassCategories = function($form, product_id, $sele1, $sele2, selected_id2) {
        if ($sele1 && $sele1.length) {
            var classcat_id1 = $sele1.val() ? $sele1.val() : '';
            if ($sele2 && $sele2.length) {
                // 規格2の選択肢をクリア
                $sele2.children().remove();

                var classcat2;

                if (eccube.hasOwnProperty('productsClassCategories')) {
                    // 商品一覧時
                    classcat2 = eccube.productsClassCategories[product_id][classcat_id1];
                } else {
                    // 詳細表示時
                    classcat2 = eccube.classCategories[classcat_id1];
                }

                // 規格2の要素を設定
                for (var key in classcat2) {
                    if (classcat2.hasOwnProperty(key)) {
                        var id = classcat2[key].classcategory_id2;
                        var name = classcat2[key].name;
                        var option = $('<option />').val(id ? id : '').text(name);
                        if (id === selected_id2) {
                            option.attr('selected', true);
                        }
                        $sele2.append(option);
                    }
                }
                eccube.checkStock($form, product_id, $sele1.val() ? $sele1.val() : '__unselected2',
                    $sele2.val() ? $sele2.val() : '');
            }
        }
    };

    /**
     * 規格の選択状態に応じて, フィールドを設定する.
     */
    var price02_origin = [];
    eccube.checkStock = function($form, product_id, classcat_id1, classcat_id2) {

        classcat_id2 = classcat_id2 ? classcat_id2 : '';

        var classcat2;

        if (eccube.hasOwnProperty('productsClassCategories')) {
            // 商品一覧時
            classcat2 = eccube.productsClassCategories[product_id][classcat_id1]['#' + classcat_id2];
        } else {
            // 詳細表示時
            if (typeof eccube.classCategories[classcat_id1] !== 'undefined') {
                classcat2 = eccube.classCategories[classcat_id1]['#' + classcat_id2];
            }
        }

        if (typeof classcat2 === 'undefined') {
            // 商品コード
            var $product_code = $('.product-code-default');
            if (typeof this.product_code_origin === 'undefined') {
                // 初期値を保持しておく
                this.product_code_origin = $product_code.text();
            }
            $product_code.text(this.product_code_origin);

            // 在庫(品切れ)
            var $cartbtn = $form.parent().find('.add-cart').first();
            if (typeof this.product_cart_origin === 'undefined') {
                // 初期値を保持しておく
                this.product_cart_origin = $cartbtn.text();
            }
            $cartbtn.prop('disabled', false);
            $cartbtn.text(this.product_cart_origin);

            // 通常価格
            var $price01 = $form.parent().find('.price01-default').first();
            if (typeof this.price01_origin === 'undefined') {
                // 初期値を保持しておく
                this.price01_origin = $price01.text();
            }
            $price01.text(this.price01_origin);

            // 販売価格
            var $price02 = $form.parent().find('.price02-default').first();
            if (typeof price02_origin[product_id] === 'undefined') {
                // 初期値を保持しておく
                price02_origin[product_id] = $price02.text();
            }
            $price02.text(price02_origin[product_id]);

            // 商品規格
            var $product_class_id_dynamic = $form.find('[id^=ProductClass]');
            $product_class_id_dynamic.val('');

        } else {
            // 商品コード
            var $product_code = $('.product-code-default');
            if (classcat2 && typeof classcat2.product_code !== 'undefined') {
                $product_code.text(classcat2.product_code);
            } else {
                $product_code.text(this.product_code_origin);
            }

            // 在庫(品切れ)
            var $cartbtn = $form.parent().find('.add-cart').first();
            if (typeof this.product_cart_origin === 'undefined') {
                // 初期値を保持しておく
                this.product_cart_origin = $cartbtn.text();
            }
            if (classcat2 && classcat2.stock_find === false) {
                $cartbtn.prop('disabled', true);
                $cartbtn.text('ただいま品切れ中です');
            } else {
                $cartbtn.prop('disabled', false);
                $cartbtn.text(this.product_cart_origin);
            }

            // 通常価格
            var $price01 = $form.parent().find('.price01-default').first();
            if (typeof this.price01_origin === 'undefined') {
                // 初期値を保持しておく
                this.price01_origin = $price01.text();
            }
            if (classcat2 && typeof classcat2.price01_inc_tax !== 'undefined' && String(classcat2.price01_inc_tax).length >= 1) {
                $price01.text('￥' + classcat2.price01_inc_tax);
            } else {
                $price01.text(this.price01_origin);
            }

            // 販売価格
            var $price02 = $form.parent().find('.price02-default').first();
            if (typeof price02_origin[product_id] === 'undefined') {
                // 初期値を保持しておく
                price02_origin[product_id] = $price02.text();
            }
            if (classcat2 && typeof classcat2.price02_inc_tax !== 'undefined' && String(classcat2.price02_inc_tax).length >= 1) {
                $price02.text('￥' + classcat2.price02_inc_tax);
            } else {
                $price02.text(price02_origin[product_id]);
            }

            // ポイント
            var $point_default = $form.find('[id^=point_default]');
            var $point_dynamic = $form.find('[id^=point_dynamic]');
            if (classcat2 && typeof classcat2.point !== 'undefined' && String(classcat2.point).length >= 1) {

                $point_dynamic.text(classcat2.point).show();
                $point_default.hide();
            } else {
                $point_dynamic.hide();
                $point_default.show();
            }

            // 商品規格
            var $product_class_id_dynamic = $form.find('[id^=ProductClass]');
            if (classcat2 && typeof classcat2.product_class_id !== 'undefined' && String(classcat2.product_class_id).length >= 1) {
                $product_class_id_dynamic.val(classcat2.product_class_id);
            } else {
                $product_class_id_dynamic.val('');
            }
        }
    };

    // グローバルに使用できるようにする
    window.eccube = eccube;

    /**
     * Initialize.
     */
    $(function() {
        // 規格1選択時
        $('select[name=classcategory_id1]')
            .change(function() {
                var $form = $(this).parents('form');
                var product_id = $form.find('input[name=product_id]').val();
                var $sele1 = $(this);
                var $sele2 = $form.find('select[name=classcategory_id2]');

                // 規格1のみの場合
                if (!$sele2.length) {
                    eccube.checkStock($form, product_id, $sele1.val(), null);
                    // 規格2ありの場合
                } else {
                    eccube.setClassCategories($form, product_id, $sele1, $sele2);
                }
            });

        // 規格2選択時
        $('select[name=classcategory_id2]')
            .change(function() {
                var $form = $(this).parents('form');
                var product_id = $form.find('input[name=product_id]').val();
                var $sele1 = $form.find('select[name=classcategory_id1]');
                var $sele2 = $(this);
                eccube.checkStock($form, product_id, $sele1.val(), $sele2.val());
            });

        // マウスオーバーで画像切り替え
        $(".hover_change_image").each(function() {
            var target = $(this);
            var srcOrig = target.attr("src");
            var srcOver = srcOrig.substr(0, srcOrig.lastIndexOf('.')) + '_on' + srcOrig.substr(srcOrig.lastIndexOf('.'));
            target.hover(
                function() {
                    target.attr("src", srcOver);
                },
                function() {
                    target.attr("src", srcOrig);
                }
            );
        });

    });
})(window);
