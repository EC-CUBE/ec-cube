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
Dear <!--{$arrOrder.order_name01}--> <!--{$arrOrder.order_name02}-->

<!--{$tpl_header}-->

************************************************
 Billing amount
************************************************

Order number:<!--{$arrOrder.order_id}-->
Payment total:$ <!--{$arrOrder.payment_total|number_format|default:0}-->
Payment method:<!--{$arrOrder.payment_method}-->
Message:<!--{$Message_tmp}-->

<!--{if $arrOther.title.value}-->
************************************************
 <!--{$arrOther.title.name}--> Information
************************************************

<!--{foreach key=key item=item from=$arrOther}-->
<!--{if $key != "title"}-->
<!--{if $item.name != ""}--><!--{$item.name}-->:<!--{/if}--><!--{$item.value}-->
<!--{/if}-->
<!--{/foreach}-->
<!--{/if}-->

************************************************
 Details of ordered product 
************************************************

<!--{section name=cnt loop=$arrOrderDetail}-->
Product code: <!--{$arrOrderDetail[cnt].product_code}-->
Product name: <!--{$arrOrderDetail[cnt].product_name}--> <!--{$arrOrderDetail[cnt].classcategory_name1}--> <!--{$arrOrderDetail[cnt].classcategory_name2}-->
Unit price:$ <!--{$arrOrderDetail[cnt].price|sfCalcIncTax|number_format}-->
Quantity:<!--{$arrOrderDetail[cnt].quantity}-->

<!--{/section}-->
-------------------------------------------------
Subtotal $ <!--{$arrOrder.subtotal|number_format|default:0}--> (Of which, sales tax $ <!--{$arrOrder.tax|number_format|default:0}-->)
Discount $ <!--{$arrOrder.use_point*$smarty.const.POINT_VALUE+$arrOrder.discount|number_format|default:0}-->
Shipping fee $ <!--{$arrOrder.deliv_fee|number_format|default:0}-->
Processing fee $ <!--{$arrOrder.charge|number_format|default:0}-->
============================================
Total $ <!--{$arrOrder.payment_total|number_format|default:0}-->

<!--{if count($arrShipping) >= 1}-->
************************************************
 Delivery information
************************************************

<!--{foreach item=shipping name=shipping from=$arrShipping}-->
Delivery destination <!--{if count($arrShipping) > 1}--><!--{$smarty.foreach.shipping.iteration}--><!--{/if}-->

 Name:<!--{$shipping.shipping_name01}--> <!--{$shipping.shipping_name02}-->
 Postal code:<!--{* <!--{$shipping.shipping_zip01}-->-<!--{$shipping.shipping_zip02}--> *}--><!--{$shipping.shipping_zipcode}-->
 Address:<!--{$arrPref[$shipping.shipping_pref]}--><!--{$shipping.shipping_addr01}--><!--{$shipping.shipping_addr02}-->
 Phone number:<!--{$shipping.shipping_tel01}-->-<!--{$shipping.shipping_tel02}-->-<!--{$shipping.shipping_tel03}-->
 Fax number :<!--{if $shipping.shipping_fax01 > 0}--><!--{$shipping.shipping_fax01}-->-<!--{$shipping.shipping_fax02}-->-<!--{$shipping.shipping_fax03}--><!--{/if}-->

 Delivery date:<!--{$shipping.shipping_date|date_format:"%Y/%m/%d"|default:"No designation"}-->
 Delivery time:<!--{$shipping.shipping_time|default:"No designation"}-->

<!--{foreach item=item name=item from=$shipping.shipment_item}-->
Product code: <!--{$item.product_code}-->
Product name: <!--{$item.product_name}--> <!--{$item.classcategory_name1}--> <!--{$item.classcategory_name2}-->
Unit price:$ <!--{$item.price|sfCalcIncTax|number_format}-->
Quantity:<!--{$item.quantity}-->

<!--{/foreach}-->
<!--{/foreach}-->
<!--{/if}-->
<!--{if $arrOrder.customer_id && $smarty.const.USE_POINT !== false}-->
============================================
<!--{* ご注文前のポイント {$tpl_user_point} pts *}-->
Points used <!--{$arrOrder.use_point|default:0|number_format}--> pts
Points expected to be added at this time <!--{$arrOrder.add_point|default:0|number_format}--> pts
Current amount of points registered <!--{$arrCustomer.point|default:0|number_format}--> pts
<!--{/if}-->
<!--{$tpl_footer}-->
