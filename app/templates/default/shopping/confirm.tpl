<!--{*
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
 *}-->

<script type="text/javascript">//<![CDATA[
    var sent = false;

    function fnCheckSubmit() {
        if (sent) {
            alert("只今、処理中です。しばらくお待ち下さい。");
            return false;
        }
        sent = true;
        return true;
    }
//]]></script>

<!--CONTENTS-->
<div id="undercolumn">
    <div id="undercolumn_shopping">
        <p class="flow_area"><img src="<!--{$TPL_URLPATH}-->img/picture/img_flow_03.jpg" alt="購入手続きの流れ" /></p>
        <h2 class="title"><!--{$tpl_title|h}--></h2>

        <p class="information">下記ご注文内容で送信してもよろしいでしょうか？<br />
            よろしければ、「<!--{if $use_module}-->次へ<!--{else}-->ご注文完了ページへ<!--{/if}-->」ボタンをクリックしてください。</p>

        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="confirm" />
            <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />

            <div class="btn_area">
                <ul>
                    <li>
                        <a href="<!--{$smarty.const.CART_URLPATH}-->"><img class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_back.jpg" alt="戻る" name="back<!--{$key}-->" /></a>
                    </li>
                        <!--{if $use_module}-->
                    <li>
                        <input type="image" onclick="return fnCheckSubmit();" class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_next.jpg" alt="次へ" name="next-top" id="next-top" />
                    </li>
                        <!--{else}-->
                    <li>
                        <input type="image" onclick="return fnCheckSubmit();" class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_order_complete.jpg" alt="ご注文完了ページへ" name="next-top" id="next-top" />
                    </li>
                    <!--{/if}-->
                </ul>
            </div>

            <table summary="ご注文内容確認">
                <col width="10%" />
                <col width="40%" />
                <col width="20%" />
                <col width="10%" />
                <col width="20%" />
                <tr>
                    <th scope="col">商品写真</th>
                    <th scope="col">商品名</th>
                    <th scope="col">単価</th>
                    <th scope="col">数量</th>
                    <th scope="col">小計</th>
                </tr>
                <!--{foreach from=$arrCartItems item=item}-->
                    <tr>
                        <td class="alignC">
                            <a
                                <!--{if $item.productsClass.main_image|strlen >= 1}--> href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->" class="expansion" target="_blank"
                                <!--{/if}-->
                            >
                                <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->" style="max-width: 65px;max-height: 65px;" alt="<!--{$item.productsClass.name|h}-->" /></a>
                        </td>
                        <td>
                            <ul>
                                <li><strong><!--{$item.productsClass.name|h}--></strong></li>
                                <!--{if $item.productsClass.classcategory_name1 != ""}-->
                                <li><!--{$item.productsClass.class_name1|h}-->：<!--{$item.productsClass.classcategory_name1|h}--></li>
                                <!--{/if}-->
                                <!--{if $item.productsClass.classcategory_name2 != ""}-->
                                <li><!--{$item.productsClass.class_name2|h}-->：<!--{$item.productsClass.classcategory_name2|h}--></li>
                                <!--{/if}-->
                            </ul>
                        </td>
                        <td class="alignR">
                            <!--{$item.price_inctax|n2s}-->円
                        </td>
                        <td class="alignR"><!--{$item.quantity|n2s}--></td>
                        <td class="alignR"><!--{$item.total_inctax|n2s}-->円</td>
                    </tr>
                <!--{/foreach}-->
                <tr>
                    <th colspan="4" class="alignR" scope="row">小計</th>
                    <td class="alignR"><!--{$tpl_total_inctax|n2s}-->円</td>
                </tr>
                <tr>
                    <th colspan="4" class="alignR" scope="row">送料</th>
                    <td class="alignR"><!--{$arrForm.deliv_fee|n2s}-->円</td>
                </tr>
                <tr>
                    <th colspan="4" class="alignR" scope="row">手数料</th>
                    <td class="alignR"><!--{$arrForm.charge|n2s}-->円</td>
                </tr>
                <tr>
                    <th colspan="4" class="alignR" scope="row">合計</th>
                    <td class="alignR"><span class="price"><!--{$arrForm.payment_total|n2s}-->円</span></td>
                </tr>
            </table>

            <!-- ▼ポイント使用 -->
            <!--{if $tpl_login == 1 && $smarty.const.USE_POINT !== false}-->
                <table summary="ポイント利用">
                    <col width="20%" />
                    <col width="80%" />
                    <tr>
                        <th>ポイントのご利用</th>
                        <td>
                            <span class="user_name"><!--{$arrForm.order_name01|h}--> <!--{$arrForm.order_name02|h}-->様</span>の、現在の所持ポイントは「<span class="point"><!--{$tpl_user_point|default:0|n2s}-->Pt</span>」です。1ポイントを<!--{$smarty.const.POINT_VALUE|n2s}-->円</span>として使用する事ができます。<br />
                            <input type="radio" id="point_off" name="point_check" value="2" <!--{$arrForm.point_check.value|sfGetChecked:2}--> onclick="eccube.togglePointForm();" /><label for="point_off">ポイントを使用しない</label>&nbsp;
                            <!--{assign var=key value="use_point"}-->
                            <input type="radio" id="point_on" name="point_check" value="1" <!--{$arrForm.point_check.value|sfGetChecked:1}--> onclick="eccube.togglePointForm();" /><label for="point_on">ポイントを<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|default:$tpl_user_point}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box60" />&nbsp;Ptを使用する。</label>
<span class="attention"><!--{$arrErr[$key]}--></span>
                    </div>
                    </tr>
                </table>
            <!--{/if}-->
            <!-- ▲ポイント使用 -->

            <h3>配送方法・お支払方法</h3>
            <table summary="配送方法・お支払方法" class="delivname">
                <col width="20%" />
                <col width="80%" />
                <tbody>
                <tr>
                    <th scope="row">配送方法</th>
                    <td>
                        <!--{assign var=key value="deliv_id"}-->
                        <!--{if $tpl_is_single_deliv == true}-->
                            <!--{assign var=deliv value=$arrForm[$key]}-->
                            <!--{$arrDeliv.$deliv|h}-->
                            <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" />
                        <!--{else}-->
                            <span class="attention"><!--{$arrErr[$key]}--></span>
                            <select name="<!--{$key}-->" id="<!--{$key}-->" onchange="eccube.setModeAndSubmit('select_deliv', '', '');" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                                <!--{html_options options=$arrDeliv selected=$arrForm[$key]}-->
                            </select>
                        <!--{/if}-->
                    </td>
                </tr>
                <tr>
                    <th scope="row">お支払方法</th>
                    <td>
                        <!--{assign var=key value="payment_id"}-->
                        <span class="attention"><!--{$arrErr[$key]}--></span>
                        <select name="<!--{$key}-->" id="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                            <!--{html_options options=$arrPayment selected=$arrForm[$key]}-->
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>

            <!--{* ▼お届け先 *}-->
            <h3>お届け先情報&nbsp;
                    <!--{if !$is_multiple}-->
                        <a href="./deliv.php">変更する</a>&nbsp;
                    <!--{/if}-->
                    <!--{if $smarty.const.USE_MULTIPLE_SHIPPING !== false}-->
                        <a href="./multiple.php"><img class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_several_address.jpg" alt="お届け先を複数指定する" /></a>
                    <!--{/if}-->
            </h3>
            <!--{foreach item=shippingItem from=$arrShipping name=shippingItem}-->
                <!--{if $is_multiple}--><h2>お届け先<!--{$smarty.foreach.shippingItem.iteration}--><!--{/if}--></h2>
                <!--{if $is_multiple}-->
                    <table summary="ご注文内容確認">
                        <col width="10%" />
                        <col width="60%" />
                        <col width="20%" />
                        <col width="10%" />
                        <tr>
                            <th scope="col">商品写真</th>
                            <th scope="col">商品名</th>
                            <th scope="col">数量</th>
                            <th scope="col">小計</th>
                        </tr>
                        <!--{foreach item=item from=$shippingItem.shipment_item}-->
                            <tr>
                                <td class="alignC">
                                    <a
                                        <!--{if $item.productsClass.main_image|strlen >= 1}--> href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->" class="expansion" target="_blank"
                                        <!--{/if}-->
                                    >
                                        <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->" style="max-width: 65px;max-height: 65px;" alt="<!--{$item.productsClass.name|h}-->" /></a>
                                </td>
                                <td><!--{* 商品名 *}--><strong><!--{$item.productsClass.name|h}--></strong><br />
                                    <!--{if $item.productsClass.classcategory_name1 != ""}-->
                                        <!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br />
                                    <!--{/if}-->
                                    <!--{if $item.productsClass.classcategory_name2 != ""}-->
                                        <!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}-->
                                    <!--{/if}-->
                                </td>
                                <td class="alignC"><!--{$item.quantity}--></td>
                                <td class="alignR">
                                    <!--{$item.total_inctax|n2s}-->円
                                </td>
                            </tr>
                        <!--{/foreach}-->
                    </table>
                <!--{/if}-->
                <table summary="お届け先確認" class="delivname">
                    <col width="20%" />
                    <col width="80%" />
                    <tbody>
                        <tr>
                            <th scope="row">お届け先</th>
                            <td>
                                <!--{if $shippingItem.shipping_company_name}--><!--{$shippingItem.shipping_company_name|h}--><br /><!--{/if}-->
                                <!--{$shippingItem.shipping_name01|h}--> <!--{$shippingItem.shipping_name02|h}-->
                                <!--{if $shippingItem.shipping_kana01}-->(<!--{$shippingItem.shipping_kana01|h}--> <!--{$shippingItem.shipping_kana02|h}-->)<!--{/if}--><br />
                                <!--{if $smarty.const.FORM_COUNTRY_ENABLE}-->
                                    <!--{$shippingItem.shipping_zipcode|h}--><br />
                                    <!--{$arrCountry[$shippingItem.shipping_country_id]|h}--><br />
                                <!--{else}-->
                                    〒<!--{$shippingItem.shipping_zip01|h}-->-<!--{$shippingItem.shipping_zip02|h}--><br />
                                    <!--{$arrPref[$shippingItem.shipping_pref]}-->
                                <!--{/if}-->
                                <!--{$shippingItem.shipping_addr01|h}--><!--{$shippingItem.shipping_addr02|h}-->
                                TEL:<!--{$shippingItem.shipping_tel01}-->-<!--{$shippingItem.shipping_tel02}-->-<!--{$shippingItem.shipping_tel03}-->
                                <!--{if $shippingItem.shipping_fax01 > 0}-->
                                    <br />FAX:<!--{$shippingItem.shipping_fax01}-->-<!--{$shippingItem.shipping_fax02}-->-<!--{$shippingItem.shipping_fax03}-->
                                <!--{/if}-->
                            </td>
                        </tr>
                        <!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
                            <!--{assign var=index value=$shippingItem.shipping_id}-->
                            <tr>
                                <th scope="row">お届け日</th>
                                <td>
                                    <!--★お届け日★-->
                                    <!--{assign var=key value="deliv_date`$index`"}-->
                                    <span class="attention"><!--{$arrErr[$key]}--></span>
                                    お届け日：
                                    <!--{if !$arrDelivDate}-->
                                        ご指定頂けません。
                                    <!--{else}-->
                                        <select name="<!--{$key}-->" id="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                                            <option value="" selected="">指定なし</option>
                                            <!--{assign var=shipping_date_value value=$arrForm[$key].value|default:$shippingItem.shipping_date}-->
                                            <!--{html_options options=$arrDelivDate selected=$shipping_date_value}-->
                                        </select>&nbsp;
                                    <!--{/if}-->
                                    <!--★お届け時間★-->
                                    <!--{assign var=key value="deliv_time_id`$index`"}-->
                                    <span class="attention"><!--{$arrErr[$key]}--></span>
                                    お届け時間：
                                    <select name="<!--{$key}-->" id="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                                        <option value="" selected="">指定なし</option>
                                        <!--{assign var=shipping_time_value value=$arrForm[$key].value|default:$shippingItem.time_id}-->
                                        <!--{html_options options=$arrDelivTime selected=$shipping_time_value}-->
                                    </select>
                                </td>
                            </tr>
                        <!--{/if}-->
                    </tbody>
                </table>
            <!--{/foreach}-->
            <!--{* ▲お届け先 *}-->

            <div class="pay_area01">
                <h3>お問い合わせ</h3>
                <table summary="お問い合わせ">
                    <col width="20%" />
                    <col width="80%" />
                    <tr>
                        <th>お問い合わせ</th>
                        <td>
                            <!--{assign var=key value="message"}-->
                            <span class="attention"><!--{$arrErr[$key]}--></span>
                            <textarea name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" cols="70" rows="8" class="txtarea" wrap="hard" placeholder="お問い合わせ事項がございましたら、こちらにご入力ください。"><!--{"\n"}--><!--{$arrForm[$key]|h}--></textarea>
                            <p class="attention"> (<!--{$smarty.const.LTEXT_LEN}-->文字まで)</p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="btn_area">
                <ul>
                    <li>
                        <a href="<!--{$smarty.const.CART_URLPATH}-->"><img class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_back.jpg" alt="戻る" name="back<!--{$key}-->" /></a>
                    </li>
                    <!--{if $use_module}-->
                    <li>
                        <input type="image" onclick="return fnCheckSubmit();" class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_next.jpg" alt="次へ" name="next" id="next" />
                    </li>
                    <!--{else}-->
                    <li>
                        <input type="image" onclick="return fnCheckSubmit();" class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_order_complete.jpg" alt="ご注文完了ページへ"  name="next" id="next" />
                    </li>
                    <!--{/if}-->
                </ul>
            </div>
        </form>
    </div>
</div>