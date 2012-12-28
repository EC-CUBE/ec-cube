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
                          checkStock($form, product_id, $sele1.val(), '0');
                      // 規格2ありの場合
                      } else {
                          setClassCategories($form, product_id, $sele1, $sele2);
                      }
                  });

      // 規格2選択時
      $('select[name=classcategory_id2]')
          .change(function() {
                      var $form = $(this).parents('form');
                      var product_id = $form.find('input[name=product_id]').val();
                      var $sele1 = $form.find('select[name=classcategory_id1]');
                      var $sele2 = $(this);
                      checkStock($form, product_id, $sele1.val(), $sele2.val());
                  });
});
/**
 * 規格2のプルダウンを設定する.
 */
function setClassCategories($form, product_id, $sele1, $sele2, selected_id2) {
    if ($sele1 && $sele1.length) {
        var classcat_id1 = $sele1.val() ? $sele1.val() : '';
        if ($sele2 && $sele2.length) {
            // 規格2の選択肢をクリア
            $sele2.children().remove();

            var classcat2;

            // 商品一覧時
            if (typeof productsClassCategories != 'undefined') {
                classcat2 = productsClassCategories[product_id][classcat_id1];
            }
            // 詳細表示時
            else {
                classcat2 = classCategories[classcat_id1];
            }

            // 規格2の要素を設定                      
            for (var key in classcat2) {
                var id = classcat2[key]['classcategory_id2'];
                var name = classcat2[key]['name'];
                var option = $('<option />').val(id ? id : '').text(name);
                if (id == selected_id2) {
                    option.attr('selected', true);
                }
                $sele2.append(option);
            }
            checkStock($form, product_id, $sele1.val() ? $sele1.val() : '__unselected2',
                       $sele2.val() ? $sele2.val() : '');
        }
    }
}

/**
 * 規格の選択状態に応じて, フィールドを設定する.
 */
function checkStock($form, product_id, classcat_id1, classcat_id2) {

    classcat_id2 = classcat_id2 ? classcat_id2 : '';

    var classcat2;

    // 商品一覧時
    if (typeof productsClassCategories != 'undefined') {
        classcat2 = productsClassCategories[product_id][classcat_id1]['#' + classcat_id2];
    }
    // 詳細表示時
    else {
        classcat2 = classCategories[classcat_id1]['#' + classcat_id2];
    }

    // 商品コード
    var $product_code_default = $form.find('[id^=product_code_default]');
    var $product_code_dynamic = $form.find('[id^=product_code_dynamic]');
    if (classcat2
        && typeof classcat2['product_code'] != 'undefined') {
        $product_code_default.hide();
        $product_code_dynamic.show();
        $product_code_dynamic.text(classcat2['product_code']);
    } else {
        $product_code_default.show();
        $product_code_dynamic.hide();
    }

    // 在庫(品切れ)
    var $cartbtn_default = $form.find('[id^=cartbtn_default]');
    var $cartbtn_dynamic = $form.find('[id^=cartbtn_dynamic]');
    if (classcat2 && classcat2['stock_find'] === false) {

        $cartbtn_dynamic.text(fnT('j_products_001')).show();
        $cartbtn_default.hide();
    } else {
        $cartbtn_dynamic.hide();
        $cartbtn_default.show();
    }

    // 通常価格
    var $price01_default = $form.find('[id^=price01_default]');
    var $price01_dynamic = $form.find('[id^=price01_dynamic]');
    if (classcat2
        && typeof classcat2['price01'] != 'undefined'
        && String(classcat2['price01']).length >= 1) {

        $price01_dynamic.text(classcat2['price01']).show();
        $price01_default.hide();
    } else {
        $price01_dynamic.hide();
        $price01_default.show();
    }

    // 販売価格
    var $price02_default = $form.find('[id^=price02_default]');
    var $price02_dynamic = $form.find('[id^=price02_dynamic]');
    if (classcat2
        && typeof classcat2['price02'] != 'undefined'
        && String(classcat2['price02']).length >= 1) {

        $price02_dynamic.text(classcat2['price02']).show();
        $price02_default.hide();
    } else {
        $price02_dynamic.hide();
        $price02_default.show();
    }

    // ポイント
    var $point_default = $form.find('[id^=point_default]');
    var $point_dynamic = $form.find('[id^=point_dynamic]');
    if (classcat2
        && typeof classcat2['point'] != 'undefined'
        && String(classcat2['point']).length >= 1) {

        $point_dynamic.text(classcat2['point']).show();
        $point_default.hide();
    } else {
        $point_dynamic.hide();
        $point_default.show();
    }

    // 商品規格
    var $product_class_id_dynamic = $form.find('[id^=product_class_id]');
    if (classcat2
        && typeof classcat2['product_class_id'] != 'undefined'
        && String(classcat2['product_class_id']).length >= 1) {

        $product_class_id_dynamic.val(classcat2['product_class_id']);
    } else {
        $product_class_id_dynamic.val('');
    }
}
