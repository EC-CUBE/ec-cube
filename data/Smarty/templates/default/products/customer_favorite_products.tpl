<!--{* お気に入り追加機能ON && 会員がログイン中 ⇒ お気に入りに追加するボタンを表示させる *}-->
<!--{if $smarty.const.OPTION_FAVOFITE_PRODUCT == 1 && $tpl_login === true}-->
    <!--{assign var=add_favorite value="add_favorite`$add_favorite_product_id`"}-->
    <!--{if $arrErr[$add_favorite]}-->
        <div class="attention"><!--{$arrErr[$add_favorite]}--></div>
    <!--{/if}-->
    <!--{if !$arrProduct.favorite_count}-->
        <a href="javascript:fnModeSubmit('add_favorite','favorite_product_id','<!--{$arrProduct.product_id|escape}-->');" onmouseover="chgImg('<!--{$TPL_DIR}-->img/products/add_favolite_product_on.gif','add_favolite_product');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/products/add_favolite_product.gif','add_favolite_product');">
            <img src="<!--{$TPL_DIR}-->img/products/add_favolite_product.gif" width="115" height="20" alt="お気に入りに追加" name="add_favolite_product" id="add_favolite_product" /></a>
    <!--{/if}-->
<!--{/if}-->
