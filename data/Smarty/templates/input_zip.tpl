<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<!--{*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="content-script-type" content="text/javascript">
<meta http-equiv="content-style-type" content="text/css">
<link rel="stylesheet" href="/user_data/css/contents.css" type="text/css" media="all" />
<link rel="stylesheet" href="/css/common.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$tpl_css}-->" type="text/css" media="all" />
<script type="text/javascript" src="/js/css.js"></script>
<script type="text/javascript" src="/js/navi.js"></script>
<script type="text/javascript" src="/js/win_op.js"></script>
<script type="text/javascript" src="/js/site.js"></script>
<title><!--{$arrSiteInfo.shop_name}-->/住所検索</title>
</head>

<body bgcolor="#ffe9e6" text="#555555" link="#3a75af" vlink="#3a75af" alink="#3a75af" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="preLoadImg(); <!--{$tpl_onload}--> <!--{$tpl_start}-->">
<noscript>
<link rel="stylesheet" href="/css/common.css" type="text/css" >
</noscript>
<div align="center">

<!--▼CONTENTS-->
<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td height="15"></td></tr>
	<tr><td bgcolor="#ffa85c"><img src="/misc/_.gif" width="1" height="5" alt=""></td></tr>
	<tr>
		<td bgcolor="#ffffff" align="center">
		<!--▼MAIN ONTENTS-->
		<table width="460" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="15"></td></tr>
			<tr>
				<td><img src="/img/common/zip_title.jpg" width="460" height="40" alt="住所検索"></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td align="center" bgcolor="#cccccc">
				<table width="450" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<form name="form1" id="form1" method="post" action="">
				<input type="hidden" name="state" value="<!--{$tpl_state}-->">
				<input type="hidden" name="city" value="<!--{$tpl_city}-->">
				<input type="hidden" name="town" value="<!--{$tpl_town}-->">
					<tr><td height="5"></td></tr>
					<tr>
						<td align="center" height="150" bgcolor="#ffffff" class="fs12"><!--{$tpl_message}--></td>
					</tr>
					<tr><td height="5"></td></tr>
				</fom>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td align="center"><a href="javascript:window.close()" onmouseover="chgImg('/img/common/b_close_on.gif','b_close');" onmouseout="chgImg('/img/common/b_close.gif','b_close');"><img src="/img/common/b_close.gif" width="140" height="30" alt="閉じる" border="0" name="b_close"></a></td>
			</tr>
			<tr><td height="30"></td></tr>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
	<tr><td bgcolor="#ffa85c"><img src="/misc/_.gif" width="1" height="5" alt=""></td></tr>
</table>
<!--▲CONTENTS-->
</div>
</body>
</html>