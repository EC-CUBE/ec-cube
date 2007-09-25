<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<div id="under02column">
  <div id="under02column_cart">
    <h2 class="title">
      <img src="<!--{$TPL_DIR}-->img/cart/title.jpg" width="700" height="40" alt="現在のカゴの中" />
    </h2>
    <p class="totalmoneyarea">
      <!--{if $tpl_login}-->
      <!--メインコメント-->
        <!--{$tpl_name|escape}--> 様の、現在の所持ポイントは「<em><!--{$tpl_user_point|number_format|default:0}--> pt</em>」です。<br />
      <!--{else}-->
        <!--メインコメント-->ポイント制度をご利用になられる場合は、会員登録後ログインしていだだきますようお願い致します。<br />
      <!--{/if}-->
      ポイントは商品購入時に1pt＝<!--{$smarty.const.POINT_VALUE}-->円として使用することができます。<br />
      <!--{* カゴの中に商品がある場合にのみ表示 *}-->
      <!--{if count($arrProductsClass) > 0 }-->
        お買い上げ商品の合計金額は「<em><!--{$tpl_total_pretax|number_format}-->円</em>」です。
        <!--{if $arrInfo.free_rule > 0}-->
          <!--{if $arrData.deliv_fee|number_format > 0}-->
            あと「<em><!--{$tpl_deliv_free|number_format}-->円</em>」で送料無料です！！
          <!--{else}-->
            現在、「<em>送料無料</em>」です！！
          <!--{/if}-->
        <!--{/if}-->
      <!--{/if}-->
    </p>

   <!--{if $tpl_message != ""}-->
    <p class="attention"><!--{$tpl_message|escape}--></p>
   <!--{/if}-->

   <!--{if count($arrProductsClass) > 0}-->
   <form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
     <input type="hidden" name="mode" value="confirm" />
     <input type="hidden" name="cart_no" value="" />
     <table summary="商品情報">
       <tr>
         <th>削除</th>
         <th>商品写真</th>
         <th>商品名</th>
         <th>単価</th>
         <th>個数</th>
         <th>小計</th>
       </tr>
      <!--{section name=cnt loop=$arrProductsClass}-->
       <tr>
         <td><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnChangeAction('<!--{$smarty.server.PHP_SELF|escape}-->'); fnModeSubmit('delete', 'cart_no', '<!--{$arrProductsClass[cnt].cart_no}-->'); return false;">削除</a>
         </td>
         <td class="phototd">
           <a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="win01('../products/detail_image.php?product_id=<!--{$arrProductsClass[cnt].product_id}-->&image=main_image','detail_image','<!--{$arrProductsClass[cnt].tpl_image_width}-->','<!--{$arrProductsClass[cnt].tpl_image_height}-->'); return false;" target="_blank">
           <img src="<!--{$smarty.const.URL_DIR}-->resize_image.php?image=<!--{$arrProductsClass[cnt].main_list_image}-->&amp;width=65&amp;height=65" alt="<!--{$arrProductsClass[cnt].name|escape}-->" />
           </a>
         </td>
         <td><!--{* 商品名 *}--><strong><!--{$arrProductsClass[cnt].name|escape}--></strong><br />
           <!--{if $arrProductsClass[cnt].classcategory_name1 != ""}-->
             <!--{$arrProductsClass[cnt].class_name1}-->：<!--{$arrProductsClass[cnt].classcategory_name1}--><br />
           <!--{/if}-->
           <!--{if $arrProductsClass[cnt].classcategory_name2 != ""}-->
             <!--{$arrProductsClass[cnt].class_name2}-->：<!--{$arrProductsClass[cnt].classcategory_name2}-->
           <!--{/if}-->
         </td>
         <td class="pricetd">
         <!--{if $arrProductsClass[cnt].price02 != ""}-->
           <!--{$arrProductsClass[cnt].price02|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
         <!--{else}-->
           <!--{$arrProductsClass[cnt].price01|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
         <!--{/if}-->
         </td>
         <td id="quantity"><!--{$arrProductsClass[cnt].quantity}-->
           <ul id="quantity_level">
            <li><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnChangeAction('<!--{$smarty.server.PHP_SELF|escape}-->'); fnModeSubmit('up','cart_no','<!--{$arrProductsClass[cnt].cart_no}-->'); return false"><img src="<!--{$TPL_DIR}-->img/cart/plus.gif" width="16" height="16" alt="＋" /></a></li>
            <li><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnChangeAction('<!--{$smarty.server.PHP_SELF|escape}-->'); fnModeSubmit('down','cart_no','<!--{$arrProductsClass[cnt].cart_no}-->'); return false"><img src="<!--{$TPL_DIR}-->img/cart/minus.gif" width="16" height="16" alt="-" /></a></li>
           </ul>
         </td>
         <td class="pricetd"><!--{$arrProductsClass[cnt].total_pretax|number_format}-->円</td>
     </tr>
     <!--{/section}-->
     <tr>
       <th colspan="5" class="resulttd">小計</th>
       <td class="pricetd"><!--{$tpl_total_pretax|number_format}--></td>
    </tr>
    <tr>
      <th colspan="5" class="resulttd">合計</th>
      <td class="pricetd"><em><!--{$arrData.total-$arrData.deliv_fee|number_format}-->円</em></td>
    </tr>
    <!--{if $arrData.birth_point > 0}-->
    <tr>
      <th colspan="5" class="resulttd">お誕生月ポイント</th>
      <td class="pricetd"><!--{$arrData.birth_point|number_format}-->pt</td>
    </tr>
    <!--{/if}-->
    <tr>
      <th colspan="5" class="resulttd">今回加算ポイント</th>
      <td class="pricetd"><!--{$arrData.add_point|number_format}-->pt</td>
    </tr>
  </table>
  <p class="mini">※商品写真は参考用写真です。ご注文のカラーと異なる写真が表示されている場合でも、商品番号に記載されているカラー表示で間違いございませんのでご安心ください。</p>
  <div class="tblareabtn">
    <p>
      <img src="<!--{$TPL_DIR}-->img/cart/text.gif" width="390" height="30" alt="上記内容でよろしければ「レジへ行く」ボタンをクリックしてください。" />
    </p>

   <!--{if $tpl_prev_url != ""}-->
     <p>
       <a href="<!--{$tpl_prev_url}-->" onmouseover="chgImg('<!--{$TPL_DIR}-->img/cart/b_pageback_on.gif','back');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/cart/b_pageback.gif','back');">
         <img src="<!--{$TPL_DIR}-->img/cart/b_pageback.gif" width="150" height="30" alt="買い物を続ける" name="back" id="back" />
       </a>&nbsp;&nbsp;
   <!--{/if}-->
       <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/cart/b_buystep_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/cart/b_buystep.gif',this)" src="<!--{$TPL_DIR}-->img/cart/b_buystep.gif" class="box150"  alt="購入手続きへ" name="confirm" />
     </p>
  </div>
</form>
<!--{else}-->
<p class="empty"><em>※ 現在カート内に商品はございません。</em></p>
<!--{/if}-->
</div>
</div>
<!--▲CONTENTS-->
