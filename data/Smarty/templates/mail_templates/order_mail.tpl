
<!--{$arrOrder.order_name01}--> <!--{$arrOrder.order_name02}--> ��

<!--{$tpl_header}-->

******************************************************************
����������Ȥ�������
******************************************************************

����ʸ�ֹ桧<!--{$arrOrder.order_id}-->
����ʧ��ס��� <!--{$arrOrder.payment_total|number_format|default:0}-->
�������ˡ��<!--{$arrOrder.payment_method}-->
�����Ϥ�����<!--{$arrOrder.deliv_date|default:"����ʤ�"}-->
���Ϥ����֡�<!--{$arrOrder.deliv_time|default:"����ʤ�"}-->
��å�������<!--{$Message_tmp}-->
�����Ϥ���
����̾������<!--{$arrOrder.deliv_name01}--> <!--{$arrOrder.deliv_name02}-->����
��͹���ֹ桧��<!--{$arrOrder.deliv_zip01}-->-<!--{$arrOrder.deliv_zip02}-->
�������ꡡ��<!--{$arrOrder.deliv_pref}--><!--{$arrOrder.deliv_addr01}--><!--{$arrOrder.deliv_addr02}-->
�������ֹ桧<!--{$arrOrder.deliv_tel01}-->-<!--{$arrOrder.deliv_tel02}-->-<!--{$arrOrder.deliv_tel03}-->

<!--{if $arrConv.cv_type != ""}-->
******************************************************************
������ӥ˷�Ѿ���
******************************************************************

����ӥˤμ��ࡧ<!--{$arrCONVENIENCE[$arrConv.cv_type]|escape}-->
<!--{if $arrConv.cv_payment_url != ""}-->����ɼURL(PC)��<!--{$arrConv.cv_payment_url}--><!--{"\n"}--><!--{/if}-->
<!--{if $arrConv.cv_payment_mobile_url != ""}-->����ɼURL(��Х���)��<!--{$arrConv.cv_payment_mobile_url}--><!--{"\n"}--><!--{/if}-->
<!--{if $arrConv.cv_receipt_no != ""}-->����ɼ�ֹ桧<!--{$arrConv.cv_receipt_no}--><!--{"\n"}--><!--{/if}-->
<!--{if $arrConv.cv_company_code != ""}-->��ȥ����ɡ�<!--{$arrConv.cv_company_code}--><!--{"\n"}--><!--{/if}-->
<!--{if $arrConv.cv_order_no != ""}-->�����ֹ桧<!--{$arrConv.cv_order_no}--><!--{"\n"}--><!--{/if}-->
��ʧ����:<!--{$arrConv.cv_payment_limit}--><!--{"\n"}-->
<!--{$arrCONVENIMESSAGE[$arrConv.cv_type]}-->
<!--{/if}-->

******************************************************************
������ʸ��������
******************************************************************

<!--{section name=cnt loop=$arrOrderDetail}-->
����̾: <!--{$arrOrderDetail[cnt].product_name}--> <!--{$arrOrderDetail[cnt].classcategory_name1}--> <!--{$arrOrderDetail[cnt].classcategory_name2}-->
���ʥ�����: <!--{$arrOrderDetail[cnt].product_code}-->
���̡�<!--{$arrOrderDetail[cnt].quantity}--> ��
��ۡ��� <!--{$arrOrderDetail[cnt].price|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->

<!--{/section}-->
-----------------------------------------------------------
������ �� <!--{$arrOrder.subtotal|number_format|default:0}--> (���������� ��<!--{$arrOrder.tax|number_format|default:0}-->��
�Ͱ��� �� <!--{assign key=disc value=`$arrOrder.use_point+$arrOrder.discount`}--><!--{$disc}--><!--{$arrOrder.use_point + $arrOrder.discount|number_format|default:0}-->
������ �� <!--{$arrOrder.deliv_fee|number_format|default:0}-->
����� �� <!--{$arrOrder.charge|number_format|default:0}-->
===============================================================
�硡�� �� <!--{$arrOrder.payment_total|number_format|default:0}-->
===============================================================

<!--{* ����ʸ���Υݥ���� {$tpl_user_point} pt *}-->
�����ѥݥ���� <!--{$arrOrder.use_point|default:0}--> pt
����û������û��ݥ���� <!--{$arrOrder.add_point|default:0}--> pt
�ݻ��ݥ���� <!--{$arrCustomer.point|default:0}--> pt

<!--{$tpl_footer}-->