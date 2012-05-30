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

<!--{$arrOrder.order_name01}--> <!--{$arrOrder.order_name02}--> 様

<!--{$tpl_header}-->

************************************************
　ご請求金額
************************************************

ご注文番号：<!--{$arrOrder.order_id}-->
お支払合計：￥ <!--{$arrOrder.payment_total|number_format|default:0}-->
ご決済方法：<!--{$arrOrder.payment_method}-->
メッセージ：<!--{$Message_tmp}-->

<!--{if $arrOther.title.value}-->
************************************************
　<!--{$arrOther.title.name}-->情報
************************************************

<!--{foreach key=key item=item from=$arrOther}-->
<!--{if $key != "title"}-->
<!--{if $item.name != ""}--><!--{$item.name}-->：<!--{/if}--><!--{$item.value}-->
<!--{/if}-->
<!--{/foreach}-->
<!--{/if}-->

************************************************
　ご注文商品明細
************************************************

<!--{section name=cnt loop=$arrOrderDetail}-->
商品コード: <!--{$arrOrderDetail[cnt].product_code}-->
商品名: <!--{$arrOrderDetail[cnt].product_name}--> <!--{$arrOrderDetail[cnt].classcategory_name1}--> <!--{$arrOrderDetail[cnt].classcategory_name2}-->
単価：￥ <!--{$arrOrderDetail[cnt].price|sfCalcIncTax|number_format}-->
数量：<!--{$arrOrderDetail[cnt].quantity}-->

<!--{/section}-->
-------------------------------------------------
小　計 ￥ <!--{$arrOrder.subtotal|number_format|default:0}--> (うち消費税 ￥<!--{$arrOrder.tax|number_format|default:0}-->）
値引き ￥ <!--{$arrOrder.use_point*$smarty.const.POINT_VALUE+$arrOrder.discount|number_format|default:0}-->
送　料 ￥ <!--{$arrOrder.deliv_fee|number_format|default:0}-->
手数料 ￥ <!--{$arrOrder.charge|number_format|default:0}-->
============================================
合　計 ￥ <!--{$arrOrder.payment_total|number_format|default:0}-->

<!--{if count($arrShipping) >= 1}-->
************************************************
　配送情報
************************************************

<!--{foreach item=shipping name=shipping from=$arrShipping}-->
◎お届け先<!--{if count($arrShipping) > 1}--><!--{$smarty.foreach.shipping.iteration}--><!--{/if}-->

　お名前　：<!--{$shipping.shipping_name01}--> <!--{$shipping.shipping_name02}-->　様
　郵便番号：〒<!--{$shipping.shipping_zip01}-->-<!--{$shipping.shipping_zip02}-->
　住所　　：<!--{$arrPref[$shipping.shipping_pref]}--><!--{$shipping.shipping_addr01}--><!--{$shipping.shipping_addr02}-->
　電話番号：<!--{$shipping.shipping_tel01}-->-<!--{$shipping.shipping_tel02}-->-<!--{$shipping.shipping_tel03}-->
　FAX番号 ：<!--{if $shipping.shipping_fax01 > 0}--><!--{$shipping.shipping_fax01}-->-<!--{$shipping.shipping_fax02}-->-<!--{$shipping.shipping_fax03}--><!--{/if}-->

　お届け日：<!--{$shipping.shipping_date|date_format:"%Y/%m/%d"|default:"指定なし"}-->
　お届け時間：<!--{$shipping.shipping_time|default:"指定なし"}-->

<!--{foreach item=item name=item from=$shipping.shipment_item}-->
商品コード: <!--{$item.product_code}-->
商品名: <!--{$item.product_name}--> <!--{$item.classcategory_name1}--> <!--{$item.classcategory_name2}-->
単価：￥ <!--{$item.price|sfCalcIncTax|number_format}-->
数量：<!--{$item.quantity}-->

<!--{/foreach}-->
<!--{/foreach}-->
<!--{/if}-->
<!--{if $arrOrder.customer_id && $smarty.const.USE_POINT !== false}-->
============================================
<!--{* ご注文前のポイント {$tpl_user_point} pt *}-->
ご使用ポイント <!--{$arrOrder.use_point|default:0|number_format}--> pt
今回加算される予定のポイント <!--{$arrOrder.add_point|default:0|number_format}--> pt
現在の所持ポイント <!--{$arrCustomer.point|default:0|number_format}--> pt
<!--{/if}-->
<!--{$tpl_footer}-->
