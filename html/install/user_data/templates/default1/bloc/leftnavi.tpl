<!--{*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<table cellspacing="0" cellpadding="0" summary=" " id="navi">
	<tr><td height="15"></td></tr>
	<tr>
		<!--{if $tpl_page_category == "aboutshopping"}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->aboutshopping/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/left/shopping_on.jpg" width="170" height="29" alt="お買い物について" /></a></td>
		<!--{else}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->aboutshopping/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/shopping_on.jpg','shopping');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/shopping.jpg','shopping');"><img src="<!--{$smarty.const.URL_DIR}-->img/left/shopping.jpg" width="170" height="29" alt="お買い物について" name="shopping" id="shopping" /></a></td>
		<!--{/if}-->
	</tr>
	<tr>
		<!--{if $tpl_page_category == "flow"}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->flow/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/left/flow_on.jpg" width="170" height="30" alt="お買い物の流れ" /></a></td>
		<!--{else}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->flow/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/flow_on.jpg','flow');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/flow.jpg','flow');"><img src="<!--{$smarty.const.URL_DIR}-->img/left/flow.jpg" width="170" height="30" alt="お買い物の流れ" name="flow" id="flow" /></a></td>
		<!--{/if}-->
	</tr>
	<tr>
		<!--{if $tpl_page_category == "faq"}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->faq/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/left/faq_on.jpg" width="170" height="30" alt="よくある質問" /></a></td>
		<!--{else}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->faq/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/faq_on.jpg','faq');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/faq.jpg','faq');"><img src="<!--{$smarty.const.URL_DIR}-->img/left/faq.jpg" width="170" height="30" alt="よくある質問" name="faq" id="faq" /></a></td>
		<!--{/if}-->
	</tr>
	<tr>
		<!--{if $tpl_page_category == "mailmagazine"}-->
		<!--{* 2006/04/20 KAKINAKA-UPD:メルマガ登録のリンク先を会員登録に変更 START *}-->
		<!--{* <td><a href="<!--{$smarty.const.URL_DIR}-->mailmagazine/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/left/mailmagazine_on.jpg" width="170" height="30" alt="メルマガ登録・解除" /></a></td> *}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->entry/kiyaku.php"><img src="<!--{$smarty.const.URL_DIR}-->img/left/mailmagazine_on.jpg" width="170" height="30" alt="メルマガ登録・解除" /></a></td>
		<!--{else}-->
		<!--{* <td><a href="<!--{$smarty.const.URL_DIR}-->mailmagazine/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/mailmagazine_on.jpg','mailmagazine');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/mailmagazine.jpg','mailmagazine');"><img src="<!--{$smarty.const.URL_DIR}-->img/left/mailmagazine.jpg" width="170" height="30" alt="メルマガ登録・解除" name="mailmagazine" id="mailmagazine" /></a></td> *}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->entry/kiyaku.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/mailmagazine_on.jpg','mailmagazine');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/mailmagazine.jpg','mailmagazine');"><img src="<!--{$smarty.const.URL_DIR}-->img/left/mailmagazine.jpg" width="170" height="30" alt="メルマガ登録・解除" name="mailmagazine" id="mailmagazine" /></a></td>
		<!--{/if}-->
		<!--{* 2006/04/20 KAKINAKA-UPD:メルマガ登録のリンク先を会員登録に変更 END *}-->
	</tr>
	<tr>
		<!--{if $tpl_page_category == "point"}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->point/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/left/point_on.jpg" width="170" height="30" alt="ポイント制度について" /></a></td>	
		<!--{else}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->point/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/point_on.jpg','point');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/point.jpg','point');"><img src="<!--{$smarty.const.URL_DIR}-->img/left/point.jpg" width="170" height="30" alt="ポイント制度について" name="point" id="point" /></a></td>
		<!--{/if}-->
	</tr>
	<!--
	<tr>
		<td><a href="<!--{$smarty.const.URL_DIR}-->flow/index.php#fax" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/fax_on.jpg','fax');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/fax.jpg','fax');"><img src="<!--{$smarty.const.URL_DIR}-->img/left/fax.jpg" width="170" height="30" alt="FAX注文について" name="fax" id="fax" /></a></td>
	</tr>
	-->
	<tr>
		<!--{if $tpl_page_category == "order"}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->order/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/left/order_on.jpg" width="170" height="30" alt="特定商取引法に関する法律" /></a></td>
		<!--{else}-->
		<td><a href="<!--{$smarty.const.URL_DIR}-->order/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/order_on.jpg','order');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/left/order.jpg','order');"><img src="<!--{$smarty.const.URL_DIR}-->img/left/order.jpg" width="170" height="30" alt="特定商取引法に関する法律" name="order" id="order" /></a></td>
		<!--{/if}-->
	</tr>
</table>