/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

(function(window, undefined) {

    // 名前空間の重複を防ぐ
    if (window.eccube === undefined) {
        window.eccube = {};
    }

    var eccube = window.eccube;

    // グローバルに使用できるようにする
    window.eccube = eccube;

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
                this.product_cart_origin = $cartbtn.html();
            }
            $cartbtn.prop('disabled', false);
            $cartbtn.html(this.product_cart_origin);

            // 通常価格
            var $price01 = $form.parent().find('.price01-default').first();
            if (typeof this.price01_origin === 'undefined') {
                // 初期値を保持しておく
                this.price01_origin = $price01.html();
            }
            $price01.html(this.price01_origin);

            // 販売価格
            var $price02 = $form.parent().find('.price02-default').first();
            if (typeof price02_origin[product_id] === 'undefined') {
                // 初期値を保持しておく
                price02_origin[product_id] = $price02.html();
            }
            $price02.html(price02_origin[product_id]);

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
                this.product_cart_origin = $cartbtn.html();
            }
            if (classcat2 && classcat2.stock_find === false) {
                $cartbtn.prop('disabled', true);
                $cartbtn.text(eccube_lang['front.product.out_of_stock']);
            } else {
                $cartbtn.prop('disabled', false);
                $cartbtn.html(this.product_cart_origin);
            }

            // 通常価格
            var $price01 = $form.parent().find('.price01-default').first();
            if (typeof this.price01_origin === 'undefined') {
                // 初期値を保持しておく
                this.price01_origin = $price01.html();
            }
            if (classcat2 && typeof classcat2.price01_inc_tax !== 'undefined' && String(classcat2.price01_inc_tax).length >= 1) {
                $price01.text(classcat2.price01_inc_tax_with_currency);
            } else {
                $price01.html(this.price01_origin);
            }

            // 販売価格
            var $price02 = $form.parent().find('.price02-default').first();
            if (typeof price02_origin[product_id] === 'undefined') {
                // 初期値を保持しておく
                price02_origin[product_id] = $price02.html();
            }
            if (classcat2 && typeof classcat2.price02_inc_tax !== 'undefined' && String(classcat2.price02_inc_tax).length >= 1) {
                $price02.text(classcat2.price02_inc_tax_with_currency);
            } else {
                $price02.html(price02_origin[product_id]);
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
    });
})(window);
