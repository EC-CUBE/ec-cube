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

    /**
     * jQuery オブジェクト / DOM 要素 / セレクタ文字列を DOM 要素に正規化する
     */
    function _toEl(obj) {
        if (!obj) return null;
        if (obj instanceof Element) return obj;
        // jQuery オブジェクト (array-like で [0] が Element)
        if (obj[0] instanceof Element) return obj[0];
        if (typeof obj === 'string') return document.querySelector(obj);
        return null;
    }

    // グローバルに使用できるようにする
    window.eccube = eccube;

    /**
     * 規格2のプルダウンを設定する.
     * $form, $sele1, $sele2 は DOM 要素または jQuery オブジェクトを受け付ける
     */
    eccube.setClassCategories = function(form, product_id, sele1, sele2, selected_id2) {
        var formEl  = _toEl(form);
        var sele1El = _toEl(sele1);
        var sele2El = _toEl(sele2);

        if (!sele1El) return;

        var classcat_id1 = sele1El.value || '';

        if (!sele2El) return;

        // 規格2の選択肢をクリア
        while (sele2El.firstChild) {
            sele2El.removeChild(sele2El.firstChild);
        }

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
                var id   = classcat2[key].classcategory_id2;
                var name = classcat2[key].name;
                var option = document.createElement('option');
                option.value = id != null ? id : '';
                option.textContent = name;
                if (id === selected_id2) {
                    option.selected = true;
                }
                sele2El.appendChild(option);
            }
        }

        eccube.checkStock(
            formEl,
            product_id,
            sele1El.value || '__unselected2',
            sele2El.value || ''
        );
    };

    /**
     * 規格の選択状態に応じて, フィールドを設定する.
     * $form は DOM 要素または jQuery オブジェクトを受け付ける
     */
    var price02_origin = [];
    eccube.checkStock = function(form, product_id, classcat_id1, classcat_id2) {
        var formEl = _toEl(form);

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

        var formParent = formEl ? formEl.parentElement : document;

        if (typeof classcat2 === 'undefined') {
            // 商品コード
            var productCode = document.querySelector('.product-code-default');
            if (typeof this.product_code_origin === 'undefined') {
                this.product_code_origin = productCode ? productCode.textContent : '';
            }
            if (productCode) productCode.textContent = this.product_code_origin;

            // 在庫(品切れ)
            var cartbtn = formParent.querySelector('.add-cart');
            if (typeof this.product_cart_origin === 'undefined') {
                this.product_cart_origin = cartbtn ? cartbtn.innerHTML : '';
            }
            if (cartbtn) {
                cartbtn.disabled = false;
                cartbtn.innerHTML = this.product_cart_origin;
            }

            // 通常価格
            var price01 = formParent.querySelector('.price01-default');
            if (typeof this.price01_origin === 'undefined') {
                this.price01_origin = price01 ? price01.innerHTML : '';
            }
            if (price01) price01.innerHTML = this.price01_origin;

            // 販売価格
            var price02 = formParent.querySelector('.price02-default');
            if (typeof price02_origin[product_id] === 'undefined') {
                price02_origin[product_id] = price02 ? price02.innerHTML : '';
            }
            if (price02) price02.innerHTML = price02_origin[product_id];

            // 商品規格
            if (formEl) {
                formEl.querySelectorAll('[id^=ProductClass]').forEach(function(el) {
                    el.value = '';
                });
            }

        } else {
            // 商品コード
            var productCode = document.querySelector('.product-code-default');
            if (classcat2 && typeof classcat2.product_code !== 'undefined') {
                if (productCode) productCode.textContent = classcat2.product_code;
            } else {
                if (productCode) productCode.textContent = this.product_code_origin;
            }

            // 在庫(品切れ)
            var cartbtn = formParent.querySelector('.add-cart');
            if (typeof this.product_cart_origin === 'undefined') {
                this.product_cart_origin = cartbtn ? cartbtn.innerHTML : '';
            }
            if (cartbtn) {
                if (classcat2 && classcat2.stock_find === false) {
                    cartbtn.disabled = true;
                    cartbtn.textContent = eccube_lang['front.product.out_of_stock'];
                } else {
                    cartbtn.disabled = false;
                    cartbtn.innerHTML = this.product_cart_origin;
                }
            }

            // 通常価格
            var price01 = formParent.querySelector('.price01-default');
            if (typeof this.price01_origin === 'undefined') {
                this.price01_origin = price01 ? price01.innerHTML : '';
            }
            if (price01) {
                if (classcat2 && typeof classcat2.price01_inc_tax !== 'undefined' && String(classcat2.price01_inc_tax).length >= 1) {
                    price01.textContent = classcat2.price01_inc_tax_with_currency;
                } else {
                    price01.innerHTML = this.price01_origin;
                }
            }

            // 販売価格
            var price02 = formParent.querySelector('.price02-default');
            if (typeof price02_origin[product_id] === 'undefined') {
                price02_origin[product_id] = price02 ? price02.innerHTML : '';
            }
            if (price02) {
                if (classcat2 && typeof classcat2.price02_inc_tax !== 'undefined' && String(classcat2.price02_inc_tax).length >= 1) {
                    price02.textContent = classcat2.price02_inc_tax_with_currency;
                } else {
                    price02.innerHTML = price02_origin[product_id];
                }
            }

            // ポイント
            if (formEl) {
                var pointDefault  = formEl.querySelector('[id^=point_default]');
                var pointDynamic  = formEl.querySelector('[id^=point_dynamic]');
                if (classcat2 && typeof classcat2.point !== 'undefined' && String(classcat2.point).length >= 1) {
                    if (pointDynamic) { pointDynamic.textContent = classcat2.point; pointDynamic.style.display = ''; }
                    if (pointDefault) pointDefault.style.display = 'none';
                } else {
                    if (pointDynamic) pointDynamic.style.display = 'none';
                    if (pointDefault) pointDefault.style.display = '';
                }
            }

            // 商品規格
            if (formEl) {
                formEl.querySelectorAll('[id^=ProductClass]').forEach(function(el) {
                    if (classcat2 && typeof classcat2.product_class_id !== 'undefined' && String(classcat2.product_class_id).length >= 1) {
                        el.value = classcat2.product_class_id;
                    } else {
                        el.value = '';
                    }
                });
            }
        }
    };


    /**
     * Initialize.
     */
    document.addEventListener('DOMContentLoaded', function() {
        // 規格1選択時
        document.querySelectorAll('select[name=classcategory_id1]').forEach(function(sel) {
            sel.addEventListener('change', function() {
                var form       = this.closest('form');
                var product_id = form.querySelector('input[name=product_id]').value;
                var sele1      = this;
                var sele2      = form.querySelector('select[name=classcategory_id2]');

                // 規格1のみの場合
                if (!sele2) {
                    eccube.checkStock(form, product_id, sele1.value, null);
                // 規格2ありの場合
                } else {
                    eccube.setClassCategories(form, product_id, sele1, sele2);
                }
            });
        });

        // 規格2選択時
        document.querySelectorAll('select[name=classcategory_id2]').forEach(function(sel) {
            sel.addEventListener('change', function() {
                var form       = this.closest('form');
                var product_id = form.querySelector('input[name=product_id]').value;
                var sele1      = form.querySelector('select[name=classcategory_id1]');
                var sele2      = this;
                eccube.checkStock(form, product_id, sele1.value, sele2.value);
            });
        });
    });
})(window);
