<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--現在のカゴの中ここから-->
  <h2>
    <img src="<!--{$TPL_DIR}-->img/side/title_cartin.jpg" width="166" height="35" alt="現在のカゴの中" />
  </h2>
  <div id="cartarea">
    <p class="item">商品数：<!--{$arrCartList.0.TotalQuantity|number_format|default:0}-->点</p>
    <p>合計：<span class="price"><!--{$arrCartList.0.ProductsTotal|number_format|default:0}-->円</span><br />
    <!--{$arrCartList.0.free_rule}-->
    <!-- カゴの中に商品がある場合にのみ表示 -->
    <!--{if $arrCartList.0.TotalQuantity > 0 and $arrCartList.0.free_rule > 0}-->
      <!--{if $arrCartList.0.deliv_free > 0}-->
      送料手数料無料まであと<!--{$arrCartList.0.deliv_free|number_format|default:0}-->円（税込）です。
      <!--{else}-->
      現在、送料は「<span class="price">無料</span>」です。
      <!--{/if}-->
    <!--{/if}-->
    </p>
    <p class="btn">
      <a href="<!--{$smarty.const.URL_DIR}-->cart/index.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/side/button_cartin_on.gif','button_cartin');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/side/button_cartin.gif','button_cartin');">
        <img src="<!--{$TPL_DIR}-->img/side/button_cartin.gif" width="87" height="22" alt="カゴの中を見る" border="0" name="button_cartin" id="button_cartin "/>
      </a>
     </p>
  </div>
<!--現在のカゴの中ここまで-->
