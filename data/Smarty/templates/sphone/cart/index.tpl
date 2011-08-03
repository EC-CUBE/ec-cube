<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
 *}-->
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
<script type="text/javascript">//<![CDATA[
$(document).ready(function() {
    $('a.expansion').fancybox();
});
//]]></script>

<!--▼コンテンツここから -->
<section id="undercolumn">


<h2 class="title"><!--{$tpl_title|h}--></h2>
<!--{if $smarty.const.USE_POINT !== false}-->
  <!--★ポイント案内★-->
  <div class="information">
    <p class="fb">商品の合計金額は「<span class="price"><!--{$tpl_all_total_inctax|number_format}-->円</span>」です。</p>

    <!--{if $tpl_login}-->
       <p class="point_announce"><span class="user_name"><!--{$tpl_name|h}--> 様</span>の、現在の所持ポイントは「<span class="point"><!--{$tpl_user_point|number_format|default:0}--> pt</span>」です。<br />
         ポイントは商品購入時に<span class="price">1pt＝<!--{$smarty.const.POINT_VALUE}-->円</span>として使用することができます。</p>
    <!--{else}-->
          <p class="point_announce">ポイント制度をご利用になられる場合は、ログインが必要です。</p>
    <!--{/if}-->
    
  </div>
<!--{/if}-->

<!--{if strlen($tpl_error) != 0}-->
    <p class="attention"><!--{$tpl_error|h}--></p>
<!--{/if}-->

<!--{if strlen($tpl_message) != 0}-->
    <p class="attention"><!--{$tpl_message|h|nl2br}--></p>
<!--{/if}-->

<!--▼フォームここから -->
<div class="form_area">

<!--{* カゴの中に商品がある場合にのみ表示 *}-->
<!--{if count($cartKeys) > 1}-->
  <p class="attentionSt">
  <!--{foreach from=$cartKeys item=key name=cartKey}--><!--{$arrProductType[$key]}--><!--{if !$smarty.foreach.cartKey.last}-->、<!--{/if}--><!--{/foreach}-->は同時購入できません。お手数ですが、個別に購入手続きをお願い致します。</p>
<!--{/if}-->

<!--{if count($cartItems) > 0}-->

    <!--{foreach from=$cartKeys item=key}-->
    
        <!--☆送料無料アナウンス右にスライドボタン -->
        <!--{if $key != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
            <!--{if $arrInfo.free_rule > 0}-->
                <div class="bubbleBox">
                <div class="bubble_announce clearfix">
                <p><a rel="external" href="<!--{$tpl_prev_url|h}-->">
                <!--{if !$arrData[$key].is_deliv_free}-->
                    あと「<span class="price"><!--{$tpl_deliv_free[$key]|number_format}-->円</span>」で<span class="price">送料無料！！</span>
                <!--{else}-->
                    現在、「<span class="price">送料無料</span>」です！！
                <!--{/if}-->
                  <br />
                商品を追加しますか?</a></p>
                </div>
                  <div class="bubble_arrow_line"><!--矢印空タグ --></div>
                <div class="bubble_arrow"><!--矢印空タグ --></div>
                </div>
            <!--{/if}-->
        <!--{/if}-->

        <form name="form<!--{$key}-->" id="form<!--{$key}-->" method="post" action="<!--{$smarty.const.CART_URLPATH|h}-->">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <!--{if 'sfGMOCartDisplay'|function_exists}-->
                <!--{'sfGMOCartDisplay'|call_user_func}-->
            <!--{/if}-->

            <input type="hidden" name="mode" value="confirm" />
            <input type="hidden" name="cart_no" value="" />
            <input type="hidden" name="cartKey" value="<!--{$key}-->" />

            <div class="formBox">
            
                <!--{if count($cartKeys) > 1}-->
                 <div class="box_header">
                  <h3><!--{$arrProductType[$key]}--></h3>
                  </div>
                  <div class="totalmoney_area">
                  <!--{$arrProductType[$key]}-->の合計金額は「<span class="price"><!--{$tpl_total_inctax[$key]|number_format}-->円</span>」です。
                  </div>
                <!--{/if}-->

                <!--▼カートの中の商品一覧 -->
                <div class="cartinarea clearfix">
                <!--{foreach from=$cartItems[$key] item=item}-->
                    <!--▼商品 -->
                    <div class="cartitemBox">
                    <!--{if $item.productsClass.main_image|strlen >= 1}-->
                        <a rel="external" href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->" target="_blank">
                        <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->&amp;width=80&amp;height=80" alt="<!--{$item.productsClass.name|h}-->" class="photoL" /></a>
                    <!--{else}-->
                        <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->&amp;width=80&amp;height=80" alt="<!--{$item.productsClass.name|h}-->" class="photoL" />
                    <!--{/if}-->
                     <div class="cartinContents">
                     <div>
                      <p><em><!--{$item.productsClass.name|h}--></em><br />
                      <!--{if $item.productsClass.classcategory_name1 != ""}-->
                          <span class="mini"><!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--></span><br />
                      <!--{/if}-->
                      <!--{if $item.productsClass.classcategory_name2 != ""}-->
                          <span class="mini"><!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}--></span><br />
                      <!--{/if}-->
                      <span class="mini">価格:</span><!--{$item.productsClass.price02|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
                      </p>
                    <p class="btn_delete">
                    <img src="<!--{$TPL_URLPATH}-->img/button/btn_delete.png" onClick="fnFormModeSubmit('form<!--{$key}-->', 'delete', 'cart_no', '<!--{$item.cart_no}-->');" class="pointer" width="21" height="20" alt="削除" /></p>
                     </div>
                    <ul>
                     <li><input type="number" name="quantity" class="cartin_quantity text data-role-none" value="<!--{$item.quantity}-->" maxlength="9" style="" onchange="fnFormModeSubmit('form<!--{$key}-->', 'setQuantity','cart_no','<!--{$item.cart_no}-->');" /></li>
                      <li class="result"><span class="mini">小計：</span><!--{$item.total_inctax|number_format}-->円</li>
                    </ul>
                    </div>
                     
                    </div>
                    <!--▲商品 -->
                 <!--{/foreach}-->
                </div>
                <!--▲カートの中の商品一覧ここまで -->

                <div class="total_area">
                <p><span class="mini">合計：</span><span class="price fb"><!--{$arrData[$key].total-$arrData[$key].deliv_fee|number_format}--> 円</span></p>
                <!--{if $smarty.const.USE_POINT !== false}-->
                <!--{if $arrData[$key].birth_point > 0}-->
                    <p><span class="mini">お誕生月ポイント：</span> <!--{$arrData[$key].birth_point|number_format}--> Pt</p>
                <!--{/if}-->
                <p><span class="mini">今回加算ポイント：</span> <!--{$arrData[$key].add_point|number_format}--> Pt</p>
                <!--{/if}-->
                </div>
              <!--{if strlen($tpl_error) == 0}-->
                <div class="btn_area_btm">
                <input type="hidden" name="cartKey" value="<!--{$key}-->" />
                <input type="submit" value="ご購入手続きへ" alt="ご購入手続きへ" name="confirm" class="btn data-role-none" />
                </div>
              <!--{/if}-->
            </div><!--▲formBox -->
        </form>
    <!--{/foreach}-->
<!--{else}-->
    <p class="empty"><em>※ 現在カート内に商品はございません。</em></p>
<!--{/if}-->

<p><a rel="external" href="<!--{$smarty.const.ROOT_URLPATH}-->" class="btn_sub">お買い物を続ける</a></p>

</div><!--▲form_area -->

</section>
<!--▼検索バー -->
<section id="search_area">
<form method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="search" name="name" id="search" value="" placeholder="キーワードを入力" class="searchbox" >
</form>
</section>
<!--▲検索バー -->
<!--▲コンテンツここまで -->
