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
            alert("Please wait while processing transaction.");
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
        <div class="flow_area">
			<ol>
			<li><span>&gt; STEP1</span><br />Delivery destination</li>
			<li class="large"><span>&gt; STEP2</span><br />Payment method and delivery time</li>
			<li class="active"><span>&gt; STEP3</span><br />Confirmation</li>
			<li class="last"><span>&gt; STEP4</span><br />Order complete</li>
			</ol>
		</div>
        <h2 class="title"><!--{$tpl_title|h}--></h2>

        <p class="information">Do you want to send the order details below?<br />
            Click the "<!--{if $use_module}-->Next<!--{else}-->Completion page<!--{/if}-->" button.</p>

        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />
        <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />

        <div class="btn_area">
            <ul>
                <li>
                    <a class="bt04" href="./payment.php">Go back</a>
                </li>
                    <!--{if $use_module}-->
				<li><button class="bt02" onclick="return fnCheckSubmit();">Next</button></li>
					
                    <!--{else}-->
                <li><button class="bt02" onclick="return fnCheckSubmit();">Completion page</button>
                </li>
                <!--{/if}-->
            </ul>
        </div>

        <table summary="Confirmation of order details">
            <col width="10%" />
            <col width="40%" />
            <col width="20%" />
            <col width="10%" />
            <col width="20%" />
            <tr>
                <th scope="col">Product photo</th>
                <th scope="col">Product name</th>
                <th scope="col">Unit price</th>
                <th scope="col">Quantity</th>
                <th scope="col">Subtotal</th>
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
                            <li><!--{$item.productsClass.class_name1}-->:<!--{$item.productsClass.classcategory_name1}--></li>
                            <!--{/if}-->
                            <!--{if $item.productsClass.classcategory_name2 != ""}-->
                            <li><!--{$item.productsClass.class_name2}-->:<!--{$item.productsClass.classcategory_name2}--></li>
                            <!--{/if}-->
                        </ul>
                    </td>
                    <td class="alignR">
                        &#036; <!--{$item.price|sfCalcIncTax|number_format}-->
                    </td>
                    <td class="alignR"><!--{$item.quantity|number_format}--></td>
                    <td class="alignR">&#036; <!--{$item.total_inctax|number_format}--></td>
                </tr>
            <!--{/foreach}-->
            <tr>
                <th colspan="4" class="alignR" scope="row">Subtotal</th>
                <td class="alignR">&#036; <!--{$tpl_total_inctax[$cartKey]|number_format}--></td>
            </tr>
            <!--{if $smarty.const.USE_POINT !== false}-->
                <tr>
                    <th colspan="4" class="alignR" scope="row">Discount (when using points)</th>
                    <td class="alignR">
                        <!--{assign var=discount value=`$arrForm.use_point*$smarty.const.POINT_VALUE`}-->
                        &#036; -<!--{$discount|number_format|default:0}--></td>
                </tr>
            <!--{/if}-->
            <tr>
                <th colspan="4" class="alignR" scope="row">Shipping fee</th>
                <td class="alignR">&#036; <!--{$arrForm.deliv_fee|number_format}--></td>
            </tr>
            <tr>
                <th colspan="4" class="alignR" scope="row">Processing fee</th>
                <td class="alignR">&#036; <!--{$arrForm.charge|number_format}--></td>
            </tr>
            <tr>
                <th colspan="4" class="alignR" scope="row">Total</th>
                <td class="alignR"><span class="price">&#036; <!--{$arrForm.payment_total|number_format}--></span></td>
            </tr>
        </table>

        <!--{* ログイン済みの会員のみ *}-->
        <!--{if $tpl_login == 1 && $smarty.const.USE_POINT !== false}-->
            <table summary="Confirmation of points" class="delivname">
            <col width="30%" />
            <col width="70%" />
                <tr>
                    <th scope="row">Points before placing order</th>
                    <td><!--{$tpl_user_point|number_format|default:0}-->Pts</td>
                </tr>
                <tr>
                    <th scope="row">Points used</th>
                    <td>-<!--{$arrForm.use_point|number_format|default:0}-->Pts</td>
                </tr>
                <!--{if $arrForm.birth_point > 0}-->
                <tr>
                    <th scope="row">Birthday points</th>
                    <td>+<!--{$arrForm.birth_point|number_format|default:0}-->Pts</td>
                </tr>
                <!--{/if}-->
                <tr>
                    <th scope="row">Points expected to be added at this time</th>
                    <td>+<!--{$arrForm.add_point|number_format|default:0}-->Pts</td>
                </tr>
                <tr>
                <!--{assign var=total_point value=`$tpl_user_point-$arrForm.use_point+$arrForm.add_point`}-->
                    <th scope="row">Points added</th>
                    <td><!--{$total_point|number_format}-->Pts</td>
                </tr>
            </table>
        <!--{/if}-->
        <!--{* ログイン済みの会員のみ *}-->

        <!--お届け先ここから-->
        <!--{* 販売方法判定（ダウンロード販売のみの場合はお届け先を表示しない） *}-->
        <!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
        <!--{foreach item=shippingItem from=$arrShipping name=shippingItem}-->
        <h3>Delivery destination<!--{if $is_multiple}--><!--{$smarty.foreach.shippingItem.iteration}--><!--{/if}--></h3>
        <!--{if $is_multiple}-->
            <table summary="Confirmation of order details">
                <col width="10%" />
                <col width="60%" />
                <col width="20%" />
                <col width="10%" />
                <tr>
                    <th scope="col">Product photos</th>
                    <th scope="col">Product name</th>
                    <th scope="col">Unit price</th>
                    <th scope="col">Quantity</th>
                    <!--{* XXX 購入小計と誤差が出るためコメントアウト
                    <th scope="col">Subtotal</th>
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
                                <!--{$item.productsClass.class_name1}-->:<!--{$item.productsClass.classcategory_name1}--><br />
                            <!--{/if}-->
                            <!--{if $item.productsClass.classcategory_name2 != ""}-->
                                <!--{$item.productsClass.class_name2}-->:<!--{$item.productsClass.classcategory_name2}-->
                            <!--{/if}-->
                        </td>
                        <td class="alignR">
                            &#036; <!--{$item.price|sfCalcIncTax|number_format}-->
                        </td>
                        <td class="alignC"><!--{$item.quantity}--></td>
                        <!--{* XXX 購入小計と誤差が出るためコメントアウト
                        <td class="alignR">&#036; <!--{$item.total_inctax|number_format}--></td>
                        *}-->
                    </tr>
                <!--{/foreach}-->
            </table>
        <!--{/if}-->

        <table summary="Confirm delivery destination" class="delivname">
            <col width="30%" />
            <col width="70%" />
            <tbody>
                <tr>
                    <th scope="row">Name</th>
                    <td><!--{$shippingItem.shipping_name01|h}--> <!--{$shippingItem.shipping_name02|h}--></td>
                </tr>
                <tr>
                    <th scope="row">Postal code</th>
                    <!--{* <td><!--{$shippingItem.shipping_zip01|h}-->-<!--{$shippingItem.shipping_zip02|h}--></td> *}-->
                    <td><!--{$shippingItem.shipping_zipcode|h}--></td>
                </tr>
                <tr>
                    <th scope="row">Address</th>
                    <td><!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01|h}--> <!--{$shippingItem.shipping_addr02|h}--></td>
                </tr>
                <tr>
                    <th scope="row">Phone number</th>
                    <td><!--{$shippingItem.shipping_tel01}-->-<!--{$shippingItem.shipping_tel02}-->-<!--{$shippingItem.shipping_tel03}--></td>
                </tr>
                <tr>
                    <th scope="row">Fax number</th>
                    <td>
                        <!--{if $shippingItem.shipping_fax01 > 0}-->
                            <!--{$shippingItem.shipping_fax01}-->-<!--{$shippingItem.shipping_fax02}-->-<!--{$shippingItem.shipping_fax03}-->
                        <!--{/if}-->
                    </td>
                </tr>
            <!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
                <tr>
                    <th scope="row">Delivery date</th>
                    <td><!--{$shippingItem.shipping_date|default:"No designation"|h}--></td>
                </tr>
                <tr>
                    <th scope="row">Delivery time</th>
                    <td><!--{$shippingItem.shipping_time|default:"No designation"|h}--></td>
                </tr>
            <!--{/if}-->
            </tbody>
        </table>
        <!--{/foreach}-->
        <!--{/if}-->
        <!--お届け先ここまで-->

        <h3>Delivery method/Payment method/Other inquiries</h3>
        <table summary="Delivery method/Payment method/Other inquiries" class="delivname">
            <col width="30%" />
            <col width="70%" />
            <tbody>
            <tr>
                <th scope="row">Delivery method</th>
                <td><!--{$arrDeliv[$arrForm.deliv_id]|h}--></td>
            </tr>
            <tr>
                <th scope="row">Payment method</th>
                <td><!--{$arrForm.payment_method|h}--></td>
            </tr>
            <tr>
                <th scope="row">Other inquiries</th>
                <td><!--{$arrForm.message|h|nl2br}--></td>
            </tr>
            </tbody>
        </table>

        <div class="btn_area">
            <ul>
                <li>
                    <a class="bt04" href="./payment.php">Go back</a>
                </li>
                <!--{if $use_module}-->
                <li><button onclick="return fnCheckSubmit();" class="bt02">Next</button>
                </li>
                <!--{else}-->
                <li><button class="bt02" onclick="return fnCheckSubmit();">Completion page</button>
                </li>
                <!--{/if}-->
        </ul>

        </div>
        </form>
    </div>
</div>
