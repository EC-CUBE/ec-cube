<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->


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

<!--{if $arrOther.title.value }-->
******************************************************************
��<!--{$arrOther.title.name}-->����
******************************************************************

<!--{foreach key=key item=item from=$arrOther}-->
<!--{if $key != "title"}-->
<!--{if $item.name != ""}--><!--{$item.name}-->��<!--{/if}--><!--{$item.value}-->
<!--{/if}-->
<!--{/foreach}-->
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
�Ͱ��� �� <!--{$arrOrder.use_point+$arrOrder.discount|number_format|default:0}-->
������ �� <!--{$arrOrder.deliv_fee|number_format|default:0}-->
����� �� <!--{$arrOrder.charge|number_format|default:0}-->
===============================================================
�硡�� �� <!--{$arrOrder.payment_total|number_format|default:0}-->
===============================================================

<!--{* ����ʸ���Υݥ���� {$tpl_user_point} pt *}-->
�����ѥݥ���� <!--{$arrOrder.use_point|default:0}--> pt
����û������û��ݥ���� <!--{$arrOrder.add_point|default:0}--> pt
�ݻ��ݥ���� <!--{$arrCustomer.point|default:0}--> pt

