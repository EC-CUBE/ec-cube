<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
<script type="text/javascript" src="<!--{$smarty.const.URL_PATH}-->js/jquery.facebox/facebox.js"></script>
<link rel="stylesheet" type="text/css" href="<!--{$smarty.const.URL_PATH}-->js/jquery.facebox/facebox.css" media="screen" />
<script type="text/javascript">//<![CDATA[
$(document).ready(function() {
    $('a.expansion').facebox({
        loadingImage : '<!--{$smarty.const.URL_PATH}-->js/jquery.facebox/loading.gif',
        closeImage   : '<!--{$smarty.const.URL_PATH}-->js/jquery.facebox/closelabel.png'
    });
});
//]]></script>

<!--▼CONTENTS-->
<div id="under02column">
    <div id="under02column_cart">
        <h2 class="title"><!--{$tpl_title|h}--></h2>
        <!--{if $smarty.const.USE_POINT !== false || count($arrProductsClass) > 0}-->
            <p class="totalmoneyarea">
                <!--★ポイント案内★-->
                <!--{if $smarty.const.USE_POINT !== false}-->
                    <!--{if $tpl_login}-->
                        <!--{$tpl_name|h}--> 様の、現在の所持ポイントは「<em><!--{$tpl_user_point|number_format|default:0}--> pt</em>」です。<br />
                    <!--{else}-->
                        ポイント制度をご利用になられる場合は、会員登録後ログインしてくださいますようお願い致します。<br />
                    <!--{/if}-->
                    ポイントは商品購入時に1pt＝<!--{$smarty.const.POINT_VALUE}-->円として使用することができます。<br />
                <!--{/if}-->
                
                <!--{* カゴの中に商品がある場合にのみ表示 *}-->
                <!--{if count($cartKeys) > 1}-->
                    <em><!--{foreach from=$cartKeys item=key name=cartKey}--><!--{$arrProductType[$key]}--><!--{if !$smarty.foreach.cartKey.last}-->、<!--{/if}--><!--{/foreach}-->は同時購入できません。お手数ですが、個別に購入手続きをお願い致します。</em>
                <!--{/if}-->
            </p>
        <!--{/if}-->

    <!--{if strlen($tpl_error) != 0}-->
        <p class="attention"><!--{$tpl_error|h}--></p>
    <!--{/if}-->

    <!--{if strlen($tpl_message) != 0}-->
        <p class="attention"><!--{$tpl_message|h|nl2br}--></p>
    <!--{/if}-->

    <!--{if count($cartItems) > 0}-->

    <!--{foreach from=$cartKeys item=key}-->
        <form name="form<!--{$key}-->" id="form<!--{$key}-->" method="post" action="?">

            <input type="hidden" name="mode" value="confirm" />
            <input type="hidden" name="cart_no" value="" />
            <input type="hidden" name="cartKey" value="<!--{$key}-->" />
            <!--{if count($cartKeys) > 1 }-->
            <h3><!--{$arrProductType[$key]}--></h3>
                <p>
                    <!--{$arrProductType[$key]}-->の合計金額は「<em><!--{$tpl_total_inctax[$key]|number_format}-->円</em>」です。
                    <!--{if $arrInfo.free_rule > 0}-->
                        <!--{if $arrData[$key].deliv_fee > 0}-->
                            あと「<em><!--{$tpl_deliv_free[$key]|number_format}-->円</em>」で送料無料です！！
                        <!--{else}-->
                            現在、「<em>送料無料</em>」です！！
                        <!--{/if}-->
                    <!--{/if}-->
                </p>
            <!--{else}-->
                <p>
                    お買い上げ商品の合計金額は「<em><!--{$tpl_total_inctax[$key]|number_format}-->円</em>」です。
                    <!--{if $arrInfo.free_rule > 0}-->
                        <!--{if $arrData[$key].deliv_fee > 0}-->
                            あと「<em><!--{$tpl_deliv_free[$key]|number_format}-->円</em>」で送料無料です！！
                        <!--{else}-->
                            現在、「<em>送料無料</em>」です！！
                        <!--{/if}-->
                    <!--{/if}-->
                </p>
            <!--{/if}-->

            <table summary="商品情報">
                <tr>
                    <th>削除</th>
                    <th>商品写真</th>
                    <th>商品名</th>
                    <th>単価</th>
                    <th>数量</th>
                    <th>小計</th>
                </tr>
                <!--{foreach from=$cartItems[$key] item=item}-->
                    <tr style="<!--{if $item.error}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->">
                        <td><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->', 'delete', 'cart_no', '<!--{$item.cart_no}-->'); return false;">削除</a>
                        </td>
                        <td class="phototd">
                        <a
                            <!--{if $item.productsClass.main_image|strlen >= 1}-->
                                href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->"
                                class="expansion"
                                target="_blank"
                            <!--{/if}-->
                        >
                            <img src="<!--{$smarty.const.URL_PATH}-->resize_image.php?image=<!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$item.productsClass.name|h}-->" /></a>
                        </td>
                        <td><!--{* 商品名 *}--><strong><!--{$item.productsClass.name|h}--></strong><br />
                            <!--{if $item.productsClass.classcategory_name1 != ""}-->
                                <!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br />
                            <!--{/if}-->
                            <!--{if $item.productsClass.classcategory_name2 != ""}-->
                                <!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}-->
                            <!--{/if}-->
                        </td>
                        <td class="pricetd">
                            <!--{$item.productsClass.price02|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
                        </td>
                        <td id="quantity"><!--{$item.quantity}-->
                            <ul id="quantity_level">
                                <li><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','up','cart_no','<!--{$item.cart_no}-->'); return false"><img src="<!--{$TPL_DIR}-->img/button/btn_plus.gif" width="16" height="16" alt="＋" /></a></li>
                                <li><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','down','cart_no','<!--{$item.cart_no}-->'); return false"><img src="<!--{$TPL_DIR}-->img/button/btn_minus.gif" width="16" height="16" alt="-" /></a></li>
                            </ul>
                        </td>
                        <td class="pricetd"><!--{$item.total_inctax|number_format}-->円</td>
                     </tr>
                 <!--{/foreach}-->
                 <tr>
                     <th colspan="5" class="resulttd">小計</th>
                     <td class="pricetd"><!--{$tpl_total_inctax[$key]|number_format}-->円</td>
                 </tr>
                 <tr>
                     <th colspan="5" class="resulttd">合計</th>
                     <td class="pricetd"><em><!--{$arrData[$key].total-$arrData[$key].deliv_fee|number_format}-->円</em></td>
                 </tr>
                 <!--{if $smarty.const.USE_POINT !== false}-->
                     <!--{if $arrData[$key].birth_point > 0}-->
                         <tr>
                             <th colspan="5" class="resulttd">お誕生月ポイント</th>
                             <td class="pricetd"><!--{$arrData[$key].birth_point|number_format}-->pt</td>
                         </tr>
                     <!--{/if}-->
                     <tr>
                         <th colspan="5" class="resulttd">今回加算ポイント</th>
                         <td class="pricetd"><!--{$arrData[$key].add_point|number_format}-->pt</td>
                    </tr>
                <!--{/if}-->
            </table>
            <div class="tblareabtn">
                <!--{if strlen($tpl_error) == 0}-->
                    <p>上記内容でよろしければ「購入手続きへ」ボタンをクリックしてください。</p>
                <!--{/if}-->

                <p>
                    <!--{if $tpl_prev_url != ""}-->
                        <a href="<!--{$tpl_prev_url}-->" onmouseover="chgImg('<!--{$TPL_DIR}-->img/button/btn_back_on.gif','back');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/button/btn_back.gif','back');">
                            <img src="<!--{$TPL_DIR}-->img/button/btn_back.gif" width="150" height="30" alt="買い物を続ける" name="back" /></a>&nbsp;&nbsp;
                    <!--{/if}-->
                    <!--{if strlen($tpl_error) == 0}-->
                        <input type="hidden" name="cartKey" value="<!--{$key}-->" />
                        <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_buystep_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_buystep.gif',this)" src="<!--{$TPL_DIR}-->img/button/btn_buystep.gif" width="130" height="30" alt="購入手続きへ" name="confirm" />
                    <!--{/if}-->
                 </p>
            </div>
        </form>
    <!--{/foreach}-->
    <!--{else}-->
        <p class="empty"><em>※ 現在カート内に商品はございません。</em></p>
    <!--{/if}-->
    </div>
</div>
<!--▲CONTENTS-->
