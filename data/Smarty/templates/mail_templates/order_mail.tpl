<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--{$arrOrder.order_name01}--> <!--{$arrOrder.order_name02}--> 様

<!--{$tpl_header}-->

******************************************************************
　配送情報とご請求金額
******************************************************************

ご注文番号：<!--{$arrOrder.order_id}-->
お支払合計：￥ <!--{$arrOrder.payment_total|number_format|default:0}-->
ご決済方法：<!--{$arrOrder.payment_method}-->
　お届け日：<!--{$arrOrder.deliv_date|default:"指定なし"}-->
お届け時間：<!--{$arrOrder.deliv_time|default:"指定なし"}-->
メッセージ：<!--{$Message_tmp}-->
◎お届け先
　お名前　：<!--{$arrOrder.deliv_name01}--> <!--{$arrOrder.deliv_name02}-->　様
　郵便番号：〒<!--{$arrOrder.deliv_zip01}-->-<!--{$arrOrder.deliv_zip02}-->
　ご住所　：<!--{$arrOrder.deliv_pref}--><!--{$arrOrder.deliv_addr01}--><!--{$arrOrder.deliv_addr02}-->
　電話番号：<!--{$arrOrder.deliv_tel01}-->-<!--{$arrOrder.deliv_tel02}-->-<!--{$arrOrder.deliv_tel03}-->

<!--{if $arrOther.title.value }-->
******************************************************************
　<!--{$arrOther.title.name}-->情報
******************************************************************

<!--{foreach key=key item=item from=$arrOther}-->
<!--{if $key != "title"}-->
<!--{if $item.name != ""}--><!--{$item.name}-->：<!--{/if}--><!--{$item.value}-->
<!--{/if}-->
<!--{/foreach}-->
<!--{/if}-->

******************************************************************
　ご注文商品明細
******************************************************************

<!--{section name=cnt loop=$arrOrderDetail}-->
商品名: <!--{$arrOrderDetail[cnt].product_name}--> <!--{$arrOrderDetail[cnt].classcategory_name1}--> <!--{$arrOrderDetail[cnt].classcategory_name2}-->
商品コード: <!--{$arrOrderDetail[cnt].product_code}-->
数量：<!--{$arrOrderDetail[cnt].quantity}--> 個
金額：￥ <!--{$arrOrderDetail[cnt].price|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->

<!--{/section}-->
-----------------------------------------------------------
小　計 ￥ <!--{$arrOrder.subtotal|number_format|default:0}--> (うち消費税 ￥<!--{$arrOrder.tax|number_format|default:0}-->）
値引き ￥ <!--{$arrOrder.use_point+$arrOrder.discount|number_format|default:0}-->
送　料 ￥ <!--{$arrOrder.deliv_fee|number_format|default:0}-->
手数料 ￥ <!--{$arrOrder.charge|number_format|default:0}-->
===============================================================
合　計 ￥ <!--{$arrOrder.payment_total|number_format|default:0}-->
===============================================================

<!--{* ご注文前のポイント {$tpl_user_point} pt *}-->
ご使用ポイント <!--{$arrOrder.use_point|default:0}--> pt
今回加算される加算ポイント <!--{$arrOrder.add_point|default:0}--> pt
保持ポイント <!--{$arrCustomer.point|default:0}--> pt

<!--{$tpl_footer}-->