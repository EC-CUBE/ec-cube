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
<link rel="stylesheet" href="/user_data/css/contents.css" type="text/css" media="all" />
<link rel="stylesheet" href="/css/common.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$tpl_css}-->" type="text/css" media="all" />
<script type="text/javascript" src="/js/css.js"></script>
<script type="text/javascript" src="/js/navi.js"></script>
<script type="text/javascript" src="/js/win_op.js"></script>
<script type="text/javascript" src="/js/site.js"></script>
<title><!--{$arrSiteInfo.shop_name}-->/お客様の声書き込み（確認ページ）</title>
</head>

<body bgcolor="#ffe9e6" text="#555555" link="#3a75af" vlink="#3a75af" alink="#3a75af" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="preLoadImg()">
<noscript>
<link rel="stylesheet" href="../css/common.css" type="text/css">
</noscript>
<div align="center">
<table width="550" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td height="15"></td></tr>
	<tr><td bgcolor="#ffa85c"><img src="../misc/_.gif" width="1" height="5" alt=""></td></tr>
	<tr>
		<td align="center" bgcolor="ffffff">
		<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
		<input type="hidden" name="mode" value="complete">
		<!--{foreach from=$arrForm key=key item=item}-->
		<!--{if $key ne "mode"}-->
		<input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->"><!--{/if}-->
		<!--{/foreach}-->
			<tr><td height="15"></td></tr>
			<tr>
				<td><img src="../img/products/review_title.jpg" width="500" height="40" alt="お客様の声書き込み"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td bgcolor="#cccccc">
				<table width="500" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="107" bgcolor="#f0f0f0" class="fs12n">商品名</td>
						<td width="350" bgcolor="#ffffff" class="fs12n"><!--{$arrForm.name|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">投稿者名<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12n"><!--{$arrForm.reviewer_name|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">ホームページアドレス</td>
						<td bgcolor="#ffffff" class="fs12n"><!--{$arrForm.reviewer_url}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">性別</td>
						<td bgcolor="#ffffff" class="fs12n"><!--{if $arrForm.sex eq 1 }-->男性<!--{elseif $arrForm.sex eq 2 }-->女性<!--{/if}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">おすすめレベル<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrRECOMMEND[$arrForm.recommend_level]}--></span></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">タイトル<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrForm.title|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">コメント<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrForm.comment|escape|nl2br}--></td>
					</tr>
				</table>
				</td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td align="center">
					<input type="image" onclick=" mode.value='return';" onmouseover="chgImgImageSubmit('/img/common/b_back_on.gif',this)" onmouseout="chgImgImageSubmit('/img/common/b_back.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="戻る"  name="back" id="back" />
					<input type="image" onmouseover="chgImgImageSubmit('/img/common/b_send_on.gif',this)" onmouseout="chgImgImageSubmit('/img/common/b_send.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/common/b_send.gif" width="150" height="30" alt="送信"  name="send" id="send" />
				</td>
			</tr>
			<tr><td height="30"></td></tr>
		</form>
		</table>
		</td>
	</tr>
	<tr><td bgcolor="#ffa85c"><img src="../misc/_.gif" width="1" height="5" alt=""></td></tr>
	<tr><td height="20"></td></tr>
</table>
</div>
</body>
</html>
