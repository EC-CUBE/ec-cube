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

<div id="mypagecolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <!--{include file=$tpl_navi}-->
    <div id="mycontents_area">
        <h3><!--{$tpl_subtitle|h}--></h3>
        <div class="mycondition_area clearfix">
            <p>
                <span class="st">Date and time of purchase:&nbsp;</span><!--{$tpl_arrOrderData.create_date|sfDispDBDate}--><br />
                <span class="st">Order number:&nbsp;</span><!--{$tpl_arrOrderData.order_id}--><br />
                <span class="st">Payment method:&nbsp;</span><!--{$tpl_arrOrderData.payment_method|h}-->
            </p>
            <form action="order.php" method="post">
                <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
                <p class="btn">
                    <input type="hidden" name="order_id" value="<!--{$tpl_arrOrderData.order_id|h}-->">
					<button class="bt01">Place another order with these purchase details</button>
                </p>
            </form>
        </div>

        <table summary="Details of purchased product">
            <col width="15%" />
            <col width="25%" />
            <col width="20%" />
            <col width="15%" />
            <col width="10%" />
            <col width="15%" />
            <tr>
                <th class="alignC">Product code</th>
                <th class="alignC">Product name</th>
                <th class="alignC">Product type</th>
                <th class="alignC">Unit price</th>
                <th class="alignC">Quantity</th>
                <th class="alignC">Subtotal</th>
            </tr>
            <!--{foreach from=$tpl_arrOrderDetail item=orderDetail}-->
                <tr>
                    <td><!--{$orderDetail.product_code|h}--></td>
                    <td><a<!--{if $orderDetail.enable}--> href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$orderDetail.product_id|u}-->"<!--{/if}-->><!--{$orderDetail.product_name|h}--></a><br />
                        <!--{if $orderDetail.classcategory_name1 != ""}-->
                            <!--{$orderDetail.classcategory_name1|h}--><br />
                        <!--{/if}-->
                        <!--{if $orderDetail.classcategory_name2 != ""}-->
                            <!--{$orderDetail.classcategory_name2|h}-->
                        <!--{/if}-->
                    </td>
                    <td class="alignC">
                    <!--{if $orderDetail.product_type_id == $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
                        <!--{if $orderDetail.is_downloadable}-->
                            <a target="_self" href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/download.php?order_id=<!--{$tpl_arrOrderData.order_id}-->&product_id=<!--{$orderDetail.product_id}-->&product_class_id=<!--{$orderDetail.product_class_id}-->">Download
</a>
                        <!--{else}-->
                            <!--{if $orderDetail.payment_date == "" && $orderDetail.effective == "0"}-->
                                <!--{$arrProductType[$orderDetail.product_type_id]}--><BR />(Transaction being confirmed)
                            <!--{else}-->
                                <!--{$arrProductType[$orderDetail.product_type_id]}--><BR />(Expired)
                            <!--{/if}-->
                        <!--{/if}-->
                    <!--{else}-->
                        <!--{$arrProductType[$orderDetail.product_type_id]}-->
                    <!--{/if}-->
                    </td>
                    <!--{assign var=price value=`$orderDetail.price`}-->
                    <!--{assign var=quantity value=`$orderDetail.quantity`}-->
                    <td class="alignR">&#036; <!--{$price|sfCalcIncTax|number_format|h}--></td>
                    <td class="alignR"><!--{$quantity|h}--></td>
                    <td class="alignR">&#036; <!--{$price|sfCalcIncTax|sfMultiply:$quantity|number_format}--></td>
                </tr>
            <!--{/foreach}-->
            <tr>
                <th colspan="5" class="alignR">Subtotal</th>
                <td class="alignR">&#036; <!--{$tpl_arrOrderData.subtotal|number_format}--></td>
            </tr>
            <!--{assign var=point_discount value="`$tpl_arrOrderData.use_point*$smarty.const.POINT_VALUE`"}-->
            <!--{if $point_discount > 0}-->
            <tr>
                <th colspan="5" class="alignR">Point discount</th>
                <td class="alignR">&#036; &minus;<!--{$point_discount|number_format}--></td>
            </tr>
            <!--{/if}-->
            <!--{assign var=key value="discount"}-->
            <!--{if $tpl_arrOrderData[$key] != "" && $tpl_arrOrderData[$key] > 0}-->
            <tr>
                <th colspan="5" class="alignR">Discount</th>
                <td class="alignR">&#036; &minus;<!--{$tpl_arrOrderData[$key]|number_format}--></td>
            </tr>
            <!--{/if}-->
            <tr>
                <th colspan="5" class="alignR">Shipping fee</th>
                <td class="alignR">&#036; <!--{assign var=key value="deliv_fee"}--><!--{$tpl_arrOrderData[$key]|number_format|h}--></td>
            </tr>
            <tr>
                <th colspan="5" class="alignR">Processing fee</th>
                <!--{assign var=key value="charge"}-->
                <td class="alignR">&#036; <!--{$tpl_arrOrderData[$key]|number_format|h}--></td>
            </tr>
            <tr>
                <th colspan="5" class="alignR">Total</th>
                <td class="alignR"><span class="price">&#036; <!--{$tpl_arrOrderData.payment_total|number_format}--></span></td>
            </tr>
        </table>

        <!-- 使用ポイントここから -->
        <!--{if $smarty.const.USE_POINT !== false}-->
            <table summary="Points used">
                <col width="30%" />
                <col width="70%" />
                <tr>
                    <th class="alignL">Points used</th>
                    <td><!--{assign var=key value="use_point"}--><!--{$tpl_arrOrderData[$key]|number_format|default:0}--> pts</td>
                </tr>
                <tr>
                    <th class="alignL">Points added at this time</th>
                    <td><!--{$tpl_arrOrderData.add_point|number_format|default:0}--> pts</td>
                </tr>
            </table>
        <!--{/if}-->
        <!-- 使用ポイントここまで -->

        <!--{foreach item=shippingItem name=shippingItem from=$arrShipping}-->
            <h3>Delivery destination<!--{if $isMultiple}--><!--{$smarty.foreach.shippingItem.iteration}--><!--{/if}--></h3>
            <!--{if $isMultiple}-->
                <table summary="Confirm delivery contents">
                    <col width="30%" />
                    <col width="40%" />
                    <col width="20%" />
                    <col width="10%" />
                    <tr>
                        <th class="alignC">Product code</th>
                        <th class="alignC">Product name</th>
                        <th class="alignC">Unit price</th>
                        <th class="alignC">Quantity</th>
                        <!--{* XXX 購入小計と誤差が出るためコメントアウト
                        <th>Subtotal</th>
                        *}-->
                    </tr>
                    <!--{foreach item=item from=$shippingItem.shipment_item}-->
                        <tr>
                            <td><!--{$item.productsClass.product_code|h}--></td>
                            <td><!--{* 商品名 *}--><!--{$item.productsClass.name|h}--><br />
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
            <table summary="Delivery destination" class="delivname">
                    <col width="30%" />
                    <col width="70%" />
                    <tr>
                        <th class="alignL">Name</th>
                        <td><!--{$shippingItem.shipping_name01|h}-->&nbsp;<!--{$shippingItem.shipping_name02|h}--></td>
                    </tr>
                    <tr>
                        <th class="alignL">Postal code</th>
                        <!--{* <td><!--{$shippingItem.shipping_zip01}-->-<!--{$shippingItem.shipping_zip02}--></td> *}-->
                        <td><!--{$shippingItem.shipping_zipcode}--></td>
                    </tr>
                    <tr>
                        <th class="alignL">Address</th>
                        <td><!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01|h}--><!--{$shippingItem.shipping_addr02|h}--></td>
                    </tr>
                    <tr>
                        <th class="alignL">Phone number</th>
                        <td><!--{$shippingItem.shipping_tel01}-->-<!--{$shippingItem.shipping_tel02}-->-<!--{$shippingItem.shipping_tel03}--></td>
                    </tr>
                    <tr>
                        <th class="alignL">Fax number</th>
                        <td>
                            <!--{if $shippingItem.shipping_fax01 > 0}-->
                                <!--{$shippingItem.shipping_fax01}-->-<!--{$shippingItem.shipping_fax02}-->-<!--{$shippingItem.shipping_fax03}-->
                            <!--{/if}-->
                        </td>
                    </tr>
                    <tr>
                        <th class="alignL">Delivery date</th>
                        <td><!--{$shippingItem.shipping_date|default:'No designation'|h}--></td>
                    </tr>
                    <tr>
                        <th class="alignL">Delivery time</th>
                        <td><!--{$shippingItem.shipping_time|default:'No designation'|h}--></td>
                    </tr>
                </tbody>
            </table>
        <!--{/foreach}-->

        <br />

        <h3>E-mail delivery history</h3>
        <table>
            <tr>
                <th class="alignC">Processing date</th>
                <th class="alignC">Notification e-mail</th>
                <th class="alignC">Item name</th>
            </tr>
            <!--{section name=cnt loop=$tpl_arrMailHistory}-->
            <tr class="center">
                <td class="alignC"><!--{$tpl_arrMailHistory[cnt].send_date|sfDispDBDate|h}--></td>
                <!--{assign var=key value="`$tpl_arrMailHistory[cnt].template_id`"}-->
                <td class="alignC"><!--{$arrMAILTEMPLATE[$key]|h}--></td>
                <td><a href="#" onclick="win02('./mail_view.php?send_id=<!--{$tpl_arrMailHistory[cnt].send_id}-->','mail_view','650','800'); return false;"><!--{$tpl_arrMailHistory[cnt].subject|h}--></a></td>
            </tr>
            <!--{/section}-->
        </table>

        <div class="btn_area">
            <ul>
                <li>
                    <a class="bt04" href="./<!--{$smarty.const.DIR_INDEX_PATH}-->">Go back</a>
                </li>
            </ul>
        </div>

    </div>
</div>
