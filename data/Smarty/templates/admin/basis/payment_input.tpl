<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--　-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=EUC-JP" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_ADMIN_CSS}-->contents.css" type="text/css" />
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/admin.js"></script>
<title>ECサイト管理者ページ</title>
<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();
//-->
</script>
</head>

<body bgcolor="#ffffff" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="<!--{$tpl_onload}-->">
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_ADMIN_CSS}-->common.css" type="text/css" />
</noscript>

<div align="center">
<!--★★メインコンテンツ★★-->
<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="payment_id" value="<!--{$tpl_payment_id}-->">
<input type="hidden" name="image_key" value="">
<input type="hidden" name="fix" value="<!--{$arrForm.fix.value}-->">
<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->
	<tr valign="top">
		<td class="mainbg">
			<!--▼登録テーブルここから-->
			<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--メインエリア-->
				<tr>
					<td align="center">
						<table width="470" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_top.jpg" width="470" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
									
									<table width="440" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="440" height="7" alt=""></td>
										</tr>
										<tr>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
											<td bgcolor="#636469" width="400" class="fs14n"><span class="white"><!--コンテンツタイトル-->支払方法登録・編集</span></td>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="440" height="7" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="440" height="10" alt=""></td>
										</tr>
									</table>
	
									<table width="440" border="0" cellspacing="1" cellpadding="8" summary=" ">
										<tr class="fs12n">
											<td width="100" bgcolor="#f0f0f0">支払方法<span class="red"> *</span></td>
											<td width="340" bgcolor="#ffffff">
											<!--{assign var=key value="payment_method"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" size="30" class="box30" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
											</td>
										</tr>
										<tr class="fs12n">
											<td width="100" bgcolor="#f0f0f0">手数料<span class="red"> *</span></td>
											<td width="340" bgcolor="#ffffff">
											<!--{assign var=key value="charge"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" size="10" class="box10" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
											 円</td>
										</tr>
										<tr class="fs12n">
											<td width="100" bgcolor="#f0f0f0">利用条件(円)</td>
											<td width="340" bgcolor="#ffffff">
											<!--{assign var=key_from value="rule"}-->
											<!--{assign var=key_to value="upper_rule"}-->
											<span class="red12"><!--{$arrErr[$key_from]}--></span>
											<span class="red12"><!--{$arrErr[$key_to]}--></span>
											<input type="text" name="<!--{$arrForm[$key_from].keyname}-->" value="<!--{$arrForm[$key_from].value|escape}-->" size="10" class="box10" maxlength="<!--{$arrForm[$key_from].length}-->" style="<!--{$arrErr[$key_from]|sfGetErrorColor}-->" />
											 円
											〜
											<input type="text" name="<!--{$arrForm[$key_to].keyname}-->" value="<!--{$arrForm[$key_to].value|escape}-->" size="10" class="box10" maxlength="<!--{$arrForm[$key_to].length}-->" style="<!--{$arrErr[$key_to]|sfGetErrorColor}-->" />
											 円</td>
										</tr>
										<tr class="fs12n">
											<td width="100" bgcolor="#f0f0f0">配送サービス<span class="red"> *</span></td>
											<td width="340" bgcolor="#ffffff">
											<!--{assign var=key value="deliv_id"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<select name="deliv_id" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
											<option value="">指定しない</option>
											<!--{html_options options=$arrDelivList selected=$arrForm[$key].value}-->
											</select></td>
										</tr>
										<tr class="fs12n">
											<td width="100" bgcolor="#f0f0f0">ロゴ画像</td>
											<td width="340" bgcolor="#ffffff">
											<!--{assign var=key value="payment_image"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<!--{if $arrFile[$key].filepath != ""}-->
											<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->">　<a href="" onclick="fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;">[画像の取り消し]</a><br>
											<!--{/if}-->
											<input type="file" name="<!--{$key}-->" size="25" class="box25" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
											<input type="button" name="btn" onclick="fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->')" value="アップロード"></td>
										</tr>						
									</table>
	
									<table width="440" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
											<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_top.gif" width="438" height="7" alt=""></td>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
										</tr>
										<tr>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
											<td bgcolor="#e9e7de" align="center">
											<table border="0" cellspacing="0" cellpadding="0" summary=" ">
												<tr>
													<td><input type="image" onMouseover="chgImgImageSubmit('/img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('/img/contents/btn_regist.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" ></td>
												</tr>
											</table>
											</td>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="440" height="8" alt=""></td>
										</tr>
									</table>
								
								</td>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bottom.jpg" width="470" height="14" alt=""></td>
							</tr>
							<tr><td height="30"></td></tr>
						</table>
					</td>
				</tr>
				<!--メインエリア-->
			</table>
			<!--▲登録テーブルここまで-->
		</td>
	</tr>
</form>
</table>
<!--★★メインコンテンツ★★-->
</div>

</body>
</html>