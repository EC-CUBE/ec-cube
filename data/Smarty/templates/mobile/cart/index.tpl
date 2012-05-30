<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
*}-->

<!--{strip}-->
    <!--{* カゴの中に商品がある場合にのみ表示 *}-->
    <!--{if count($cartKeys) > 1}-->
        <font color="#FF0000"><!--{foreach from=$cartKeys item=key name=cartKey}--><!--{$arrProductType[$key]}--><!--{if !$smarty.foreach.cartKey.last}-->、<!--{/if}--><!--{/foreach}-->
        は同時購入できません。お手数ですが、個別に購入手続きをお願い致します。<br></font>
        <br>
    <!--{/if}-->

    <!--{if strlen($tpl_error) != 0}-->
        <font color="#FF0000"><!--{$tpl_error|h}--></font><br>
    <!--{/if}-->

    <!--{if strlen($tpl_message) != 0}-->
        <!--{$tpl_message|h|nl2br}--><br>
    <!--{/if}-->

    <!--{if count($cartItems) > 0}-->
        <!--{foreach from=$cartKeys item=key}-->
            <form name="form<!--{$key}-->" id="form<!--{$key}-->" method="post" action="?" utn>
                <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
            	<input type="hidden" name="mode" value="confirm">
            	<input type="hidden" name="cart_no" value="">
            	<input type="hidden" name="cartKey" value="<!--{$key}-->">
            	<!--ご注文内容ここから-->
                <!--{if count($cartKeys) > 1}-->
                    <hr>
                    ■<!--{$arrProductType[$key]}-->
                    <hr>
                <!--{/if}-->
                <!--{foreach from=$cartItems[$key] item=item}-->
            		◎<!--{* 商品名 *}--><!--{$item.productsClass.name|h}--><br>
                    <!--{* 規格名1 *}--><!--{if $item.productsClass.classcategory_name1 != ""}--><!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br><!--{/if}-->
                    <!--{* 規格名2 *}--><!--{if $item.productsClass.classcategory_name2 != ""}--><!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}--><br><!--{/if}-->
            		<!--{* 販売価格 *}-->
            		<!--{$item.price|sfCalcIncTax|number_format}-->円
            		× <!--{$item.quantity}--><br>
            		<br>
            		<!--{* 数量 *}-->
            		数量:<!--{$item.quantity}-->
            		<a href="?mode=up&amp;cart_no=<!--{$item.cart_no}-->&amp;cartKey=<!--{$key}-->">＋</a>
            		<a href="?mode=down&amp;cart_no=<!--{$item.cart_no}-->&amp;cartKey=<!--{$key}-->">－</a>
            		<a href="?mode=delete&amp;cart_no=<!--{$item.cart_no}-->&amp;cartKey=<!--{$key}-->">削除</a><br>
            		<!--{* 合計 *}-->
            		小計:<!--{$item.total_inctax|number_format}-->円<br>
            		<div align="right"><a href="<!--{$smarty.const.MOBILE_P_DETAIL_URLPATH}--><!--{$item.productsClass.product_id|u}-->">→商品詳細へ</a></div>
            		<HR>
            	<!--{/foreach}-->
            	<font color="#FF0000">
            	商品合計:<!--{$tpl_total_inctax[$key]|number_format}-->円<br>
            	合計:<!--{$arrData[$key].total-$arrData[$key].deliv_fee|number_format}-->円<br>
            	</font>
                <br>
                <!--{if $key != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
                    <!--{if $arrInfo.free_rule > 0}-->
                        <!--{if !$arrData[$key].is_deliv_free}-->
                            あと「<font color="#FF0000"><!--{$tpl_deliv_free[$key]|number_format}-->円</font>」で<font color="#FF0000">送料無料</font>です！！<br>
                        <!--{else}-->
                            現在、「<font color="#FF0000">送料無料</font>」です！！<br>
                        <!--{/if}-->
                        <br>
                    <!--{/if}-->
                <!--{/if}-->

            	<!--{if $smarty.const.USE_POINT !== false}-->
                    <!--{if $arrData[$key].birth_point > 0}-->
                		お誕生月ﾎﾟｲﾝﾄ<br>
                		<!--{$arrData[$key].birth_point|number_format}-->pt<br>
                	<!--{/if}-->
                    今回加算ﾎﾟｲﾝﾄ<br>
                    <!--{$arrData[$key].add_point|number_format}-->pt<br>
                    <br>
            	<!--{/if}-->

            	<center><input type="submit" value="注文する"></center>
            </form>

            <br>
            <!--{if $tpl_prev_url != ""}-->
                <a href="<!--{$tpl_prev_url|h}-->">[emoji:69]お買物を続ける</a><br>
                <br>
            <!--{/if}-->
        <!--{/foreach}-->
    <!--{else}-->
    	※現在ｶｰﾄ内に商品はございません｡<br>
        <br>
    <!--{/if}-->

    <!--{if $smarty.const.USE_POINT !== false}-->
        <hr>
        <!--{if $tpl_login}-->
            <!--{$tpl_name|h}--> 様の、現在の所持ポイントは「<font color="#FF0000"><!--{$tpl_user_point|number_format|default:0}--> pt</font>」です。<br>
        <!--{else}-->
            ポイント制度をご利用になられる場合は、会員登録後ログインしてくださいますようお願い致します。
        <!--{/if}-->
        ポイントは商品購入時に1ptを<!--{$smarty.const.POINT_VALUE}-->円として使用することができます。
    <!--{/if}-->
<!--{/strip}-->
