<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼HEADER-->
<div id="header">
	<h1><a href="<!--{$smarty.const.SITE_URL}-->"><em><!--{$arrSiteInfo.shop_name}-->/<!--{$tpl_title}--></em></a></h1>

	<div id="information">
		<ul>
			<li><a href="./mypage/index.php" onmouseover="chgImg('<!--{$smarty.const.USER_DIR}-->/templates/<!--{$smarty.const.TEMPLATE_NAME}-->/img/header/mypage_on.gif','mypage');" onmouseout="chgImg('<!--{$smarty.const.USER_DIR}-->/templates/<!--{$smarty.const.TEMPLATE_NAME}-->/img/header/mypage.gif','mypage');"><img src="<!--{$smarty.const.USER_DIR}-->/templates/<!--{$smarty.const.TEMPLATE_NAME}-->/img/header/mypage.gif" width="95" height="20" alt="ログイン情報変更" name="mypage"></a></li>
			<li><a href="./entry/index.php" onmouseover="chgImg('<!--{$smarty.const.USER_DIR}-->/templates/<!--{$smarty.const.TEMPLATE_NAME}-->/img/header/member_on.gif','member');" onmouseout="chgImg('<!--{$smarty.const.USER_DIR}-->/templates/<!--{$smarty.const.TEMPLATE_NAME}-->/img/header/member.gif','member');"><img src="<!--{$smarty.const.USER_DIR}-->/templates/<!--{$smarty.const.TEMPLATE_NAME}-->/img/header/member.gif" width="95" height="20" alt="会員登録" name="member"></a></li>
			<li><a href="./cart/index.php" onmouseover="chgImg('<!--{$smarty.const.USER_DIR}-->/templates/<!--{$smarty.const.TEMPLATE_NAME}-->/img/header/cartin_on.gif','cartin');" onmouseout="chgImg('<!--{$smarty.const.USER_DIR}-->/templates/<!--{$smarty.const.TEMPLATE_NAME}-->/img/header/cartin.gif','cartin');"><img src="<!--{$smarty.const.USER_DIR}-->/templates/<!--{$smarty.const.TEMPLATE_NAME}-->/img/header/cartin.gif" width="95" height="20" alt="カゴの中を見る" name="cartin"></a></li>
		</ul>
	</div>
</div>
<!--▲HEADER-->
