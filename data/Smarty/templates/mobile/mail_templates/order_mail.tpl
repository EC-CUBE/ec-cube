<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->


����������Ȥ�������
����ʸ�ֹ桧<!--{$arrOrder.order_id}-->
����ʧ��ס��� <!--{$arrOrder.payment_total|number_format|default:0}-->
�������ˡ��<!--{$arrOrder.payment_method}-->
�����Ϥ�����<!--{$arrOrder.deliv_date|default:"����ʤ�"}-->
���Ϥ����֡�<!--{$arrOrder.deliv_time|default:"����ʤ�"}-->
�����Ϥ���
����̾������<!--{$arrOrder.deliv_name01}--> <!--{$arrOrder.deliv_name02}-->����
��͹���ֹ桧��<!--{$arrOrder.deliv_zip01}-->-<!--{$arrOrder.deliv_zip02}-->
�������ꡡ��<!--{$arrOrder.deliv_pref}--><!--{$arrOrder.deliv_addr01}--><!--{$arrOrder.deliv_addr02}-->
�������ֹ桧<!--{$arrOrder.deliv_tel01}-->-<!--{$arrOrder.deliv_tel02}-->-<!--{$arrOrder.deliv_tel03}-->

������ʸ��������
<!--{section name=cnt loop=$arrOrderDetail}-->
����̾: <!--{$arrOrderDetail[cnt].product_name}--> <!--{$arrOrderDetail[cnt].classcategory_name1}--> <!--{$arrOrderDetail[cnt].classcategory_name2}-->
���̡�<!--{$arrOrderDetail[cnt].quantity}--> ��
��ۡ��� <!--{$arrOrderDetail[cnt].price|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->

<!--{/section}-->
