<!--{*
 * Copyright ��� 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<table cellspacing="0" cellpadding="0" summary=" " id="navi">
	<tr><td height="15"></td></tr>
	<tr>
		<!--{if $tpl_page_category == "aboutshopping"}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->aboutshopping/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/left/shopping_on.jpg" width="170" height="29" alt="���㤤ʪ�ˤĤ���" /></a></td>
		<!--{else}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->aboutshopping/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/shopping_on.jpg','shopping');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/shopping.jpg','shopping');"><img src="<!--{$smarty.const.URL_DIR}-->img/left/shopping.jpg" width="170" height="29" alt="���㤤ʪ�ˤĤ���" name="shopping" id="shopping" /></a></td>
		<!--{/if}-->
	</tr>
	<tr>
		<!--{if $tpl_page_category == "flow"}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->flow/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/left/flow_on.jpg" width="170" height="30" alt="���㤤ʪ��ή��" /></a></td>
		<!--{else}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->flow/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/flow_on.jpg','flow');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/flow.jpg','flow');"><img src="<!--{$smarty.const.URL_DIR}-->img/left/flow.jpg" width="170" height="30" alt="���㤤ʪ��ή��" name="flow" id="flow" /></a></td>
		<!--{/if}-->
	</tr>
	<tr>
		<!--{if $tpl_page_category == "faq"}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->faq/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/left/faq_on.jpg" width="170" height="30" alt="�褯�������" /></a></td>
		<!--{else}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->faq/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/faq_on.jpg','faq');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/faq.jpg','faq');"><img src="<!--{$smarty.const.URL_DIR}-->img/left/faq.jpg" width="170" height="30" alt="�褯�������" name="faq" id="faq" /></a></td>
		<!--{/if}-->
	</tr>
	<tr>
		<!--{if $tpl_page_category == "mailmagazine"}-->
		<!--{* 2006/04/20 KAKINAKA-UPD:���ޥ���Ͽ�Υ���������Ͽ���ѹ� START *}-->
		<!--{* <td><a href="<!--{$smarty.const.URL_DIR}-->mailmagazine/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/left/mailmagazine_on.jpg" width="170" height="30" alt="���ޥ���Ͽ�����" /></a></td> *}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->entry/kiyaku.php"><img src="<!--{$smarty.const.URL_DIR}-->img/left/mailmagazine_on.jpg" width="170" height="30" alt="���ޥ���Ͽ�����" /></a></td>
		<!--{else}-->
		<!--{* <td><a href="<!--{$smarty.const.URL_DIR}-->mailmagazine/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/mailmagazine_on.jpg','mailmagazine');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/mailmagazine.jpg','mailmagazine');"><img src="<!--{$smarty.const.URL_DIR}-->img/left/mailmagazine.jpg" width="170" height="30" alt="���ޥ���Ͽ�����" name="mailmagazine" id="mailmagazine" /></a></td> *}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->entry/kiyaku.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/mailmagazine_on.jpg','mailmagazine');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/mailmagazine.jpg','mailmagazine');"><img src="<!--{$smarty.const.URL_DIR}-->img/left/mailmagazine.jpg" width="170" height="30" alt="���ޥ���Ͽ�����" name="mailmagazine" id="mailmagazine" /></a></td>
		<!--{/if}-->
		<!--{* 2006/04/20 KAKINAKA-UPD:���ޥ���Ͽ�Υ���������Ͽ���ѹ� END *}-->
	</tr>
	<tr>
		<!--{if $tpl_page_category == "point"}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->point/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/left/point_on.jpg" width="170" height="30" alt="�ݥ�������٤ˤĤ���" /></a></td>	
		<!--{else}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->point/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/point_on.jpg','point');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/point.jpg','point');"><img src="<!--{$smarty.const.URL_DIR}-->img/left/point.jpg" width="170" height="30" alt="�ݥ�������٤ˤĤ���" name="point" id="point" /></a></td>
		<!--{/if}-->
	</tr>
	<!--
	<tr>
		<td><a href="<!--{$smarty.const.URL_DIR}-->flow/index.php#fax" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/fax_on.jpg','fax');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/fax.jpg','fax');"><img src="<!--{$smarty.const.URL_DIR}-->img/left/fax.jpg" width="170" height="30" alt="FAX��ʸ�ˤĤ���" name="fax" id="fax" /></a></td>
	</tr>
	-->
	<tr>
		<!--{if $tpl_page_category == "order"}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->order/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/left/order_on.jpg" width="170" height="30" alt="���꾦���ˡ�˴ؤ���ˡΧ" /></a></td>
		<!--{else}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->order/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/order_on.jpg','order');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/order.jpg','order');"><img src="<!--{$smarty.const.URL_DIR}-->img/left/order.jpg" width="170" height="30" alt="���꾦���ˡ�˴ؤ���ˡΧ" name="order" id="order" /></a></td>
		<!--{/if}-->
	</tr>
</table>