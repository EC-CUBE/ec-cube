<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->">
<meta http-equiv="content-script-type" content="text/javascript">
<meta http-equiv="content-style-type" content="text/css">
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->user_data/css/contents.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->css/common.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$tpl_css}-->" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>
<title><!--{$arrSiteInfo.shop_name}-->/�ѥ���ɤ�˺�줿��(���ϥڡ���)</title>
</head>

<body bgcolor="#f0f0f0" text="#555555" link="#3a75af" vlink="#3a75af" alink="#3a75af" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="preLoadImg('<!--{$smarty.const.URL_DIR}-->')">
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->css/common.css" type="text/css">
</noscript>
<div align="center">
	<form action="<!--{$smarty.server.PHP_SELF|escape}-->" method="post" name="form1" />
	<input type="hidden" name="mode" value="mail_check" />
	
	<table width="550" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<tr><td height="15"></td></tr>
		<tr><td bgcolor="#ffa85c"><img src="<!--{$smarty.const.URL_DIR}-->misc/_.gif" width="1" height="5" alt=""></td></tr>
		<tr>
			<td align="center" bgcolor="#ffffff">
			<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr><td height="15"></td></tr>
				<tr>
					<td><img src="<!--{$smarty.const.URL_DIR}-->img/forget/title.jpg" width="500" height="40" alt="�ѥ���ɤ�˺�줿��"></td>
				</tr>
				<tr><td height="15"></td></tr>
				<tr>
					<td class="fs12">����Ͽ���Υ᡼�륢�ɥ쥹�����Ϥ��ơּ��ءץܥ���򥯥�å����Ƥ���������<br>
					<span class="red">���������ѥ���ɤ�ȯ�Ԥ������ޤ��Τǡ���˺��ˤʤä��ѥ���ɤϤ����ѤǤ��ʤ��ʤ�ޤ���</span></td>
				</tr>
				<tr><td height="15"></td></tr>
				<tr>
					<td align="center" bgcolor="#cccccc">
					<table width="490" border="0" cellspacing="0" cellpadding="0" summary=" ">
						<tr><td height="5"></td></tr>
						<tr>
							<td align="center" height="120" bgcolor="#ffffff" class="fs12">�᡼�륢�ɥ쥹����&nbsp;<!--���᡼�륢�ɥ쥹���ϡ�--><input type="text" name="email" value="<!--{$tpl_login_email|escape}-->" size="50" size="40" class="box40" style="<!--{$errmsg|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
						</tr>
						<tr><td height="5"></td></tr>
					</table>
					</td>
				</tr>
				<!--{if $errmsg}-->
				<tr>
					<td class="fs12" align="left"><span class="red"><!--{$errmsg}--></span></td>
				</tr>
				<!--{/if}-->
				<tr><td height="15"></td></tr>
				<tr>
					<td align="center"><input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_next_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_next.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/common/b_next.gif" width="150" height="30" alt="����" border="0" name="next" id="next" /></td>
				</tr>
				<tr><td height="30"></td></tr>
			</table>
			</td>
		</tr>
		<tr><td bgcolor="#ffa85c"><img src="<!--{$smarty.const.URL_DIR}-->misc/_.gif" width="1" height="5" alt=""></td></tr>
		<tr><td height="20"></td></tr>
	</table>	
	</form>
</div>

</body>
</html>