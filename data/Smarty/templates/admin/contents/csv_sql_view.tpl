<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--　-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="content-script-type" content="text/javascript">
<meta http-equiv="content-style-type" content="text/css">
<link rel="stylesheet" href="<!--{$smarty.const.URL_ADMIN_CSS}-->contents.css" type="text/css" />
<script type="text/javascript" src="/js/css.js"></script>
<script type="text/javascript" src="/js/navi.js"></script>
<style type="text/css">
body {
	background: #fff url(/img/login/bg.jpg);
	background-repeat: repeat-x;
}
</style>

<title>EC CUBE 管理者画面</title>
</head>

<body bgcolor="#ffffff" text="#494E5F" link="#006699" vlink="#006699" alink="#006699" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="preLoadImg()">
<noscript>
<link rel="stylesheet" href="/admin/css/common.css" type="text/css" >
</noscript>
<div align="center">

<!--▼CONTENTS-->
<table width="556" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td height="15"></td></tr>
	<tr>
		<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/error_top.jpg" width="562" height="14" alt=""></td>
	</tr>
	<tr>
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
		<td bgcolor="#cccccc">
		<table width="534" border="0" cellspacing="0" cellpadding="0" summary=" " bgcolor="#cccccc">
			<tr>
				<td bgcolor="#cccccc" align="center">
				<table width="" border="0" cellspacing="1" cellpadding="5" summary=" ">
					<tr align="left">
						<td bgcolor="#f2f1ec" width="84" align="center" class="fs12n">SQL文</td>
						<td bgcolor="#ffffff" width="450" height="300" valign="top" class="fs12n"><strong><!--{$sql|escape|nl2br}--></strong></td>
					</tr>
					<tr align="left">
						<td bgcolor="#f2f1ec" width="84" align="center" class="fs12n">エラー内容</td>
						<td bgcolor="#ffffff" width="450" height="100" valign="top" class="fs12n"><strong>
						<!--{if $sqlerr != "" }-->
							<!--{$sqlerr|escape|nl2br}-->
						<!--{else}-->エラーはありません
						<!--{/if}--></strong></td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
	</tr>
	<tr>
		<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/error_bottom.jpg" width="562" height="14" alt=""></td>
	</tr>
	<tr><td height="2"></td></tr>
</table>
<!--▲CONTENTS-->

</body>
</html>