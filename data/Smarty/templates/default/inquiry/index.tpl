<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<head>
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->css/contents.css" type="text/css">
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->css/common.css" type="text/css">
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/win_op.js"></script>
<title><!--{$arrSiteInfo.shop_name}-->/アンケート　<!--{$QUESTION.title|escape}--></title>
</head>
<body bgcolor="#ffffff" text="#555555" link="#0099cc" vlink="#CC0000" alink="#993399" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">


<!--▲TITLE-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td height="40" bgcolor="#f6f6f6" align="center">
		<table width="710" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td height="30" bgcolor="ff0000"><img src="../misc/_.gif" width="7" height="1" alt=""></td>
				<td height="30"><img src="../misc/_.gif" width="8" height="1" alt=""></td>
				<td height="30"width="695" class="red"><strong><span class="fs18n"><!--{$QUESTION.title|escape}--></span></strong></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#e2e2e2"><img src="../misc/_.gif" width="10" height="1" alt=""></td>
	</tr>
	<tr><td height="10"></td></tr>
	<tr>
		<td align="center" valign="top">
		<table width="600" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
			<input type="hidden" name="question_id" value="<!--{$question_id}-->">
			<tr>
				<td class="fs12"><!--{$QUESTION.contents|escape|nl2br}-->
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<!--{if $errmsg}--><tr><td class="fs12n"><span class="red"><br>入力エラーが発生致しました。各項目のエラーメッセージをご確認の上、正しく入力してください。</span></td></tr><!--{/if}-->	
			<tr>
				<td bgcolor="#cccccc">
				<table width="600" border="0" cellspacing="1" cellpadding="10" summary=" ">
				<!--{include file=inquiry/inquiry.tpl}-->
				</table>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td class="fs12n"><span class="red">※</span>印は入力必須項目です。</td>
			</tr>
			<tr><td height="5"></td></tr>
			<input type="hidden" name="mode" value="confirm">
			<tr>
				<td bgcolor="#cccccc">
				<table width="600" border="0" cellspacing="1" cellpadding="10">
					
					<tr>
						<td class="fs12n" bgcolor="#ebf9ff" width="150"><span class="red">※</span>お名前</td>
						<td class="fs12n"bgcolor="#ffffff" width="407">
							<span class="red"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
										<input type="text" name="name01" value="<!--{$arrForm.name01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="20" class="box20" <!--{if $arrErr.name01}--><!--{sfSetErrorStyle}--><!--{/if}--> />
							&nbsp;&nbsp;<input type="text" name="name02" value="<!--{$arrForm.name02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="20" class="box20" <!--{if $arrErr.name02}--><!--{sfSetErrorStyle}--><!--{/if}--> />
						</td>
					</tr>
					<tr>
						<td class="fs12n" bgcolor="#ebf9ff" width="150"><span class="red">※</span>フリガナ</td>
						<td class="fs12n" bgcolor="#ffffff" width="407">
							<span class="red"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>
										<input type="text" name="kana01" value="<!--{$arrForm.kana01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="20" class="box20" <!--{if $arrErr.kana01}--><!--{sfSetErrorStyle}--><!--{/if}--> />
							&nbsp;&nbsp;<input type="text" name="kana02" value="<!--{$arrForm.kana02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="20" class="box20" <!--{if $arrErr.kana02}--><!--{sfSetErrorStyle}--><!--{/if}--> />
						</td>
					</tr>
					<tr>
						<td class="fs12n" bgcolor="#ebf9ff" width="150"><span class="red">※</span>郵便番号</td>
						<td bgcolor="#ffffff" width="407">
						<table width="407" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n" width="267">
									<span class="red"><!--{$arrErr.zip01}--><!--{$arrErr.zip02}--></span>
									〒&nbsp;<input type="text" name="zip01" value="<!--{$arrForm.zip01|escape}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" size="6" class="box6" maxlength="3"  <!--{if $arrErr.zip01}--><!--{sfSetErrorStyle}--><!--{/if}--> />
										&nbsp;-&nbsp;
										<input type="text" name="zip02" value="<!--{$arrForm.zip02|escape}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" size="6" class="box6" maxlength="4"  <!--{if $arrErr.zip02}--><!--{sfSetErrorStyle}--><!--{/if}--> />&nbsp;<input type="button" name="address_input" value="住所入力" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'zip01', 'zip02', 'pref', 'addr01'); return false;" />
								</td>								
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td class="fs12n" bgcolor="#ebf9ff" width="150"><span class="red">※</span>ご住所</td>
						<td bgcolor="#ffffff">
						<table width="407" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n" colspan="2">
								<span class="red"><!--{$arrErr.pref}--><!--{$arrErr.addr01}--><!--{$arrErr.addr02}--></span>
								<select name="pref" <!--{if $arrErr.pref}--><!--{sfSetErrorStyle}--><!--{/if}-->>
									<option value="" selected>選択してください</option>
									<!--{html_options options=$arrPref selected=$arrForm.pref}-->
								</select>
								</td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td width="207">
									<input type="text" name="addr01" value="<!--{$arrForm.addr01|escape}-->" size="35" class="box35" <!--{if $arrErr.addr01}--><!--{sfSetErrorStyle}--><!--{/if}--> />
								</td>
								<td width="200"><span class="fs10n">ご住所1（市区町村名）</span></td>
							</tr>
							<tr><td height="3"></td></tr>
							<tr>
								<td class="fs12n">
									<input type="text" name="addr02" value="<!--{$arrForm.addr02|escape}-->" size="35" class="box35" <!--{if $arrErr.addr02}--><!--{sfSetErrorStyle}--><!--{/if}--> />
								</td>
								<td><span class="fs10n">ご住所2（番地、建物、マンション名）</span><br></td>
							</tr>
							<tr>
								<td><span class="fs10n"><span class="red">住所は必ず2つに分けて入力してください。マンション名は必ず入力してください。</span></span></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td class="fs12n" bgcolor="#ebf9ff" width="150"><span class="red">※</span>お電話番号</td>
						<td class="fs12n" bgcolor="#ffffff" width="407">
							<span class="red"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span>
							<input type="text" name="tel01" value="<!--{$arrForm.tel01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel01}--><!--{sfSetErrorStyle}--><!--{/if}--> />&nbsp;-&nbsp;
							<input type="text" name="tel02" value="<!--{$arrForm.tel02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel02}--><!--{sfSetErrorStyle}--><!--{/if}--> />&nbsp;-&nbsp;
							<input type="text" name="tel03" value="<!--{$arrForm.tel03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel03}--><!--{sfSetErrorStyle}--><!--{/if}--> />
						</td>
					</tr>
					<tr>
						<td class="fs12n" bgcolor="#ebf9ff" width="150"><span class="red">※</span>メールアドレス</td>
						<td bgcolor="#ffffff">
						<table width="407" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td class="fs12n" colspan="2">
									<span class="red"><!--{$arrErr.email}--></span>
									<input type="text" name="email" value="<!--{$arrForm.email|escape}-->" size="35" class="box35" <!--{if $arrErr.email}--><!--{sfSetErrorStyle}--><!--{/if}--> />
								</td>
							</tr>
							<tr><td height="3"></td></tr>
							<tr>
								<td class="fs12n" width="227">
									<span class="red"><!--{$arrErr.email02}--></span>
									<input type="text" name="email02" value="<!--{$arrForm.email02|escape}-->" size="35" class="box35" <!--{if $arrErr.email02}--><!--{sfSetErrorStyle}--><!--{/if}--> />
								</td>
								<td width="180" class="fs10"><span class="red">確認のため2度入力してください。</span></td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td align="center"><input type="submit" name="subm1" value="確認ページへ"></td>
			</tr>
			</form>
		</table>
		<br>			

		</td>
	</tr>
</table>

</body>
</html>