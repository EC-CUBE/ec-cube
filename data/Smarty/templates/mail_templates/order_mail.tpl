
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

<!--{if $arrConv.cv_type != ""}-->
******************************************************************
　コンビニ決済情報
******************************************************************

コンビニの種類：<!--{$arrCONVENIENCE[$arrConv.cv_type]|escape}-->
<!--{if $arrConv.cv_payment_url != ""}-->振込票URL(PC)：<!--{$arrConv.cv_payment_url}--><!--{"\n"}--><!--{/if}-->
<!--{if $arrConv.cv_payment_mobile_url != ""}-->振込票URL(モバイル)：<!--{$arrConv.cv_payment_mobile_url}--><!--{"\n"}--><!--{/if}-->
<!--{if $arrConv.cv_receipt_no != ""}-->振込票番号：<!--{$arrConv.cv_receipt_no}--><!--{"\n"}--><!--{/if}-->
<!--{if $arrConv.cv_company_code != ""}-->企業コード：<!--{$arrConv.cv_company_code}--><!--{"\n"}--><!--{/if}-->
<!--{if $arrConv.cv_order_no != ""}-->受付番号：<!--{$arrConv.cv_order_no}--><!--{"\n"}--><!--{/if}-->
支払期限:<!--{$arrConv.cv_payment_limit}--><!--{"\n"}-->
<!--{$arrCONVENIMESSAGE[$arrConv.cv_type]}-->
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
値引き ￥ <!--{`$arrOrder.use_point` + `$arrOrder.discount`}--><!--{$arrOrder.use_point + $arrOrder.discount|number_format|default:0}-->
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