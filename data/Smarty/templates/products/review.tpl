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
<title><!--{$arrSiteInfo.shop_name}-->/お客様の声書き込み（入力ページ）</title>
</head>

<body bgcolor="#ffe9e6" text="#555555" link="#3a75af" vlink="#3a75af" alink="#3a75af" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="preLoadImg()">
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->css/common.css" type="text/css">
</noscript>
<div align="center">
<table width="550" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td height="15"></td></tr>
	<tr><td bgcolor="#ffa85c"><img src="<!--{$smarty.const.URL_DIR}-->misc/_.gif" width="1" height="5" alt=""></td></tr>
	<tr>
		<td align="center" bgcolor="ffffff">
		<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
		<input type="hidden" name="mode" value="confirm">
		<input type="hidden" name="product_id" value="<!--{$arrForm.product_id}-->">
			<tr><td height="15"></td></tr>
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/products/review_title.jpg" width="500" height="40" alt="お客様の声書き込み"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">以下の商品について、お客様のご意見、ご感想をどしどしお寄せください。<br>
				「<span class="red">※</span>」印は入力必須項目です。<br>
				ご入力後、一番下の「確認ページへ」ボタンをクリックしてください。</td>
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
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.reviewer_name}--></span><input type="text" name="reviewer_name" value="<!--{$arrForm.reviewer_name|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.reviewer_name|SfGetErrorColor}-->" size="40" class="box40" /></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">ホームページアドレス</td>
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><input type="text" name="reviewer_url" value="<!--{$arrForm.reviewer_url}-->" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" style="<!--{$arrErr.reviewer_url|SfGetErrorColor}-->" size="40" class="box40" /></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">性別</td>
						<td bgcolor="#ffffff" class="fs12n"><input type="radio" name="sex" id="man" value="1"><label for="man" <!--{if $arrForm.sex eq 1}-->checked<!--{/if}-->>男性</label>　<input type="radio" name="sex" id="woman" value="2" <!--{if $arrForm.sex eq 2}-->checked<!--{/if}-->><label for="woman">女性</label></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">おすすめレベル<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12n">
							<span class="red"><!--{$arrErr.recommend_level}--></span>
							<select name="recommend_level" style="<!--{$arrErr.recommend_level|sfGetErrorColor}-->" >
							<option value="" selected>選択してください</option>
							<!--{html_options options=$arrRECOMMEND selected=$arrForm.recommend_level}-->
							</select>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">タイトル<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12"><span class="red"><!--{$arrErr.title}--></span>
						<input type="text" name="title" value="<!--{$arrForm.title|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.title|SfGetErrorColor}-->" size="40" class="box40"></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">コメント<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12"><span class="red"><!--{$arrErr.comment}--></span>
						<textarea name="comment" value="<!--{$arrForm.comment|escape}-->" maxlength="<!--{$smarty.const.LTEXT_LEN}--> "style="<!--{$arrErr.comment|SfGetErrorColor}-->" class="area40"><!--{$arrForm.comment|escape}--></textarea></td>
					</tr>
				</table>
				</td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td align="center">
				<input type="image" onMouseover="chgImgImageSubmit('/img/common/b_confirm_on.gif',this)" onMouseout="chgImgImageSubmit('/img/common/b_confirm.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/common/b_confirm.gif" width="150" height="30" alt="確認ページへ" name="conf" id="conf">
				</td>
			</tr>
			<tr><td height="30"></td></tr>
		</form>
		</table>
		</td>
	</tr>
	<tr><td bgcolor="#ffa85c"><img src="<!--{$smarty.const.URL_DIR}-->misc/_.gif" width="1" height="5" alt=""></td></tr>
	<tr><td height="20"></td></tr>
</table>
</div>
</body>
</html>