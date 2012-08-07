<!--{*
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
 *}-->

<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/facebox.js"></script>
<link rel="stylesheet" type="text/css" href="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/facebox.css" media="screen" />
<script type="text/javascript">//<![CDATA[
    var send = true;

    function fnCheckSubmit() {
        if(send) {
            send = false;
            return true;
        } else {
            alert("只今、処理中です。しばらくお待ち下さい。");
            return false;
        }
    }

    $(document).ready(function() {
        $('a.expansion').facebox({
            loadingImage : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/loading.gif',
            closeImage   : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/closelabel.png'
        });
    });
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
                    <a href="./payment.php" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_back_on.jpg', 'back04-top')" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_back.jpg', 'back04-top')"><img src="<!--{$TPL_URLPATH}-->img/button/btn_back.jpg" alt="戻る" border="0" name="back04-top" id="back04-top" /></a>
                </li>
                    <!--{if $use_module}-->
                <li>
                    <input type="image" onclick="return fnCheckSubmit();" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_next_on.jpg',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_next.jpg',this)" src="<!--{$TPL_URLPATH}-->img/button/btn_next.jpg" alt="次へ" name="next-top" id="next-top" />
                </li>
                    <!--{else}-->
                <li>
                    <input type="image" onclick="return fnCheckSubmit();" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_order_complete_on.jpg',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_order_complete.jpg',this)" src="<!--{$TPL_URLPATH}-->img/button/btn_order_complete.jpg" alt="ご注文完了ページへ" name="next-top" id="next-top" />
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
                            <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$item.productsClass.name|h}-->" /></a>
                    </td>
                    <td>
                        <ul>
                            <li><strong><!--{$item.productsClass.name|h}--></strong></li>
                            <!--{if $item.productsClass.classcategory_name1 != ""}-->
                            <li><!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--></li>
                            <!--{/if}-->
                            <!--{if $item.productsClass.classcategory_name2 != ""}-->
                            <li><!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}--></li>
                            <!--{/if}-->
                        </ul>
                    </td>
                    <td class="alignR">
                        <!--{$item.price|sfCalcIncTax|number_format}-->円
                    </td>
                    <td class="alignR"><!--{$item.quantity|number_format}--></td>
                    <td class="alignR"><!--{$item.total_inctax|number_format}-->円</td>
                </tr>
            <!--{/foreach}-->
            <tr>
                <th colspan="4" class="alignR" scope="row">小計</th>
                <td class="alignR"><!--{$tpl_total_inctax[$cartKey]|number_format}-->円</td>
            </tr>
            <!--{if $smarty.const.USE_POINT !== false}-->
                <tr>
                    <th colspan="4" class="alignR" scope="row">値引き（ポイントご使用時）</th>
                    <td class="alignR">
                        <!--{assign var=discount value=`$arrForm.use_point*$smarty.const.POINT_VALUE`}-->
                        -<!--{$discount|number_format|default:0}-->円</td>
                </tr>
            <!--{/if}-->
            <tr>
                <th colspan="4" class="alignR" scope="row">送料</th>
                <td class="alignR"><!--{$arrForm.deliv_fee|number_format}-->円</td>
            </tr>
            <tr>
                <th colspan="4" class="alignR" scope="row">手数料</th>
                <td class="alignR"><!--{$arrForm.charge|number_format}-->円</td>
            </tr>
            <tr>
                <th colspan="4" class="alignR" scope="row">合計</th>
                <td class="alignR"><span class="price"><!--{$arrForm.payment_total|number_format}-->円</span></td>
            </tr>
        </table>

        <!--{* ログイン済みの会員のみ *}-->
        <!--{if $tpl_login == 1 && $smarty.const.USE_POINT !== false}-->
            <table summary="ポイント確認" class="delivname">
            <col width="30%" />
            <col width="70%" />
                <tr>
                    <th scope="row">ご注文前のポイント</th>
                    <td><!--{$tpl_user_point|number_format|default:0}-->Pt</td>
                </tr>
                <tr>
                    <th scope="row">ご使用ポイント</th>
                    <td>-<!--{$arrForm.use_point|number_format|default:0}-->Pt</td>
                </tr>
                <!--{if $arrForm.birth_point > 0}-->
                <tr>
                    <th scope="row">お誕生月ポイント</th>
                    <td>+<!--{$arrForm.birth_point|number_format|default:0}-->Pt</td>
                </tr>
                <!--{/if}-->
                <tr>
                    <th scope="row">今回加算予定のポイント</th>
                    <td>+<!--{$arrForm.add_point|number_format|default:0}-->Pt</td>
                </tr>
                <tr>
                <!--{assign var=total_point value=`$tpl_user_point-$arrForm.use_point+$arrForm.add_point`}-->
                    <th scope="row">加算後のポイント</th>
                    <td><!--{$total_point|number_format}-->Pt</td>
                </tr>
            </table>
        <!--{/if}-->
        <!--{* ログイン済みの会員のみ *}-->

        <!--お届け先ここから-->
        <!--{* 販売方法判定（ダウンロード販売のみの場合はお届け先を表示しない） *}-->
        <!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
        <!--{foreach item=shippingItem from=$arrShipping name=shippingItem}-->
        <h3>お届け先<!--{if $is_multiple}--><!--{$smarty.foreach.shippingItem.iteration}--><!--{/if}--></h3>
        <!--{if $is_multiple}-->
            <table summary="ご注文内容確認">
                <col width="10%" />
                <col width="60%" />
                <col width="20%" />
                <col width="10%" />
                <tr>
                    <th scope="col">商品写真</th>
                    <th scope="col">商品名</th>
                    <th scope="col">単価</th>
                    <th scope="col">数量</th>
                    <!--{* XXX 購入小計と誤差が出るためコメントアウト
                    <th scope="col">小計</th>
                    *}-->
                </tr>
                <!--{foreach item=item from=$shippingItem.shipment_item}-->
                    <tr>
                        <td class="alignC">
                            <a
                                <!--{if $item.productsClass.main_image|strlen >= 1}--> href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->" class="expansion" target="_blank"
                                <!--{/if}-->
                            >
                                <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$item.productsClass.name|h}-->" /></a>
                        </td>
                        <td><!--{* 商品名 *}--><strong><!--{$item.productsClass.name|h}--></strong><br />
                            <!--{if $item.productsClass.classcategory_name1 != ""}-->
                                <!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br />
                            <!--{/if}-->
                            <!--{if $item.productsClass.classcategory_name2 != ""}-->
                                <!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}-->
                            <!--{/if}-->
                        </td>
                        <td class="alignR">
                            <!--{$item.price|sfCalcIncTax|number_format}-->円
                        </td>
                        <td class="alignC"><!--{$item.quantity}--></td>
                        <!--{* XXX 購入小計と誤差が出るためコメントアウト
                        <td class="alignR"><!--{$item.total_inctax|number_format}-->円</td>
                        *}-->
                    </tr>
                <!--{/foreach}-->
            </table>
        <!--{/if}-->

        <table summary="お届け先確認" class="delivname">
            <col width="30%" />
            <col width="70%" />
            <tbody>
                <tr>
                    <th scope="row">お名前</th>
                    <td><!--{$shippingItem.shipping_name01|h}--> <!--{$shippingItem.shipping_name02|h}--></td>
                </tr>
                <tr>
                    <th scope="row">お名前(フリガナ)</th>
                    <td><!--{$shippingItem.shipping_kana01|h}--> <!--{$shippingItem.shipping_kana02|h}--></td>
                </tr>
                <tr>
                    <th scope="row">郵便番号</th>
                    <td>〒<!--{$shippingItem.shipping_zip01|h}-->-<!--{$shippingItem.shipping_zip02|h}--></td>
                </tr>
                <tr>
                    <th scope="row">住所</th>
                    <td><!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01|h}--><!--{$shippingItem.shipping_addr02|h}--></td>
                </tr>
                <tr>
                    <th scope="row">電話番号</th>
                    <td><!--{$shippingItem.shipping_tel01}-->-<!--{$shippingItem.shipping_tel02}-->-<!--{$shippingItem.shipping_tel03}--></td>
                </tr>
                <tr>
                    <th scope="row">FAX番号</th>
                    <td>
                        <!--{if $shippingItem.shipping_fax01 > 0}-->
                            <!--{$shippingItem.shipping_fax01}-->-<!--{$shippingItem.shipping_fax02}-->-<!--{$shippingItem.shipping_fax03}-->
                        <!--{/if}-->
                    </td>
                </tr>
            <!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
                <tr>
                    <th scope="row">お届け日</th>
                    <td><!--{$shippingItem.shipping_date|default:"指定なし"|h}--></td>
                </tr>
                <tr>
                    <th scope="row">お届け時間</th>
                    <td><!--{$shippingItem.shipping_time|default:"指定なし"|h}--></td>
                </tr>
            <!--{/if}-->
            </tbody>
        </table>
        <!--{/foreach}-->
        <!--{/if}-->
        <!--お届け先ここまで-->

        <h3>配送方法・お支払方法・その他お問い合わせ</h3>
        <table summary="配送方法・お支払方法・その他お問い合わせ" class="delivname">
            <col width="30%" />
            <col width="70%" />
            <tbody>
            <tr>
                <th scope="row">配送方法</th>
                <td><!--{$arrDeliv[$arrForm.deliv_id]|h}--></td>
            </tr>
            <tr>
                <th scope="row">お支払方法</th>
                <td><!--{$arrForm.payment_method|h}--></td>
            </tr>
            <tr>
                <th scope="row">その他お問い合わせ</th>
                <td><!--{$arrForm.message|h|nl2br}--></td>
            </tr>
            </tbody>
        </table>

        <div class="btn_area">
            <ul>
                <li>
                    <a href="./payment.php" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_back_on.jpg','back<!--{$key}-->');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_back.jpg','back<!--{$key}-->');"><img src="<!--{$TPL_URLPATH}-->img/button/btn_back.jpg" alt="戻る" name="back<!--{$key}-->" /></a>
                </li>
                <!--{if $use_module}-->
                <li>
                    <input type="image" onclick="return fnCheckSubmit();" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_next_on.jpg',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_next.jpg',this)" src="<!--{$TPL_URLPATH}-->img/button/btn_next.jpg" alt="次へ" name="next" id="next" />
                </li>
                <!--{else}-->
                <li>
                    <input type="image" onclick="return fnCheckSubmit();" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_order_complete_on.jpg',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_order_complete.jpg',this)" src="<!--{$TPL_URLPATH}-->img/button/btn_order_complete.jpg" alt="ご注文完了ページへ"  name="next" id="next" />
                </li>
                <!--{/if}-->
        </ul>

        </div>
        </form>
    </div>
</div>
