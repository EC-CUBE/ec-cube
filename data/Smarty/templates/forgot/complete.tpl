<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="content-script-type" content="text/javascript">
<meta http-equiv="content-style-type" content="text/css">
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->user_data/css/contents.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->css/common.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$tpl_css}-->" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>
<title><!--{$arrSiteInfo.shop_name}-->/パスワードを忘れた方(完了ページ)</title>
</head>

<body bgcolor="#f0f0f0" text="#555555" link="#3a75af" vlink="#3a75af" alink="#3a75af" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="preLoadImg()">
<noscript>
<link rel="stylesheet" href="../css/common.css" type="text/css" />
</noscript>

<div align="center">
<table width="550" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<form name="form1" id="form1" method="post" action="./confirm.php">
	<tr><td height="15"></td></tr>
	<tr><td bgcolor="#ffa85c"><img src="<!--{$smarty.const.URL_DIR}-->misc/_.gif" width="1" height="5" alt=""></td></tr>
	<tr>
		<td align="center" bgcolor="#ffffff">
		<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="15"></td></tr>
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/forget/title.jpg" width="500" height="40" alt="パスワードを忘れた方"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">パスワードの発行が完了いたしました。ログインには下記のパスワードをご利用ください。<br>
				※下記パスワードは、MYページの「会員登録内容変更」よりご変更いただけます。</td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td align="center" bgcolor="#cccccc">
				<table width="490" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="5"></td></tr>
					<tr>
						<td align="center" height="120" bgcolor="#ffffff" class="fs18n"><!--★パスワード表示★--><span class="redst"><!--{$temp_password}--></span></td>
					</tr>
					<tr><td height="5"></td></tr>
				</table>
				</td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td align="center"><a href="javascript:window.close()" onmouseOver="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_close_on.gif','close');" onmouseOut="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_close.gif','close');"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_close.gif" width="150" height="30" alt="閉じる" name="close" id="close" /></a></td>
			</tr>
			<tr><td height="30"></td></tr>
		</table>			
		</td>
	</tr>

	<tr><td bgcolor="#ffa85c"><img src="<!--{$smarty.const.URL_DIR}-->misc/_.gif" width="1" height="5" alt=""></td></tr>
	<tr><td height="20"></td></tr>
</form>	
</table>
</div>

</body>
</html>
