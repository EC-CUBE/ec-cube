<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--　-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->admin/css/contents.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/admin.js"></script>
<!--{include file='css/contents.tpl'}-->
<title><!--{$tpl_subtitle}--></title>
<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();

function lfnCheckPayment(){
	var fm = document.form1;
	var val = 0;
	
	payment = new Array('payment[]');

	for(pi = 0; pi < payment.length; pi++) {
		// クレジットの場合
		list = new Array('credit[]');
		if(fm[payment[pi]][0].checked){
			fnChangeDisabled(list, false);
		}else{
			fnChangeDisabled(list);
		}
		
		// コンビニの場合
		list = new Array('convenience[]','conveni_limit_date');
		if(fm[payment[pi]][1].checked){
			fnChangeDisabled(list, false);
		}else{
			fnChangeDisabled(list);
		}
		
		// ATM決済の場合
		list = new Array('atm_limit_date', 'payment_detail');
		if(fm[payment[pi]][2].checked){
			fnChangeDisabled(list, false);
		}else{
			fnChangeDisabled(list);
		}
		
		// 銀行ネットの場合
		list = new Array('claim_kanji', 'claim_kana', 'asp_payment_term', 'copy_right', 'free_memo');
		if(fm[payment[pi]][3].checked){
			fnChangeDisabled(list, false);
		}else{
			fnChangeDisabled(list);
		}
	}
}

function fnChangeDisabled(list, disable) {
	len = list.length;

	if(disable == null) { disable = true; }
	
	for(i = 0; i < len; i++) {
		if(document.form1[list[i]]) {
			// ラジオボタン、チェックボックス等の配列に対応
			max = document.form1[list[i]].length
			if(max > 1) {
				for(j = 0; j < max; j++) {
					// 有効、無効の切り替え
					document.form1[list[i]][j].disabled = disable;
				}
			} else {
				// 有効、無効の切り替え
				document.form1[list[i]].disabled = disable;
			}
		}
	}
}

function win_open(URL){
	var WIN;
	WIN = window.open(URL);
	WIN.focus();
}
//-->
</script>
</head>


<body bgcolor="#ffffff" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload='lfnCheckPayment(); <!--{$tpl_onload}-->'>
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->admin/css/common.css" type="text/css" />
</noscript>

<div align="center">
<!--★★メインコンテンツ★★-->
<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
<input type="hidden" name="mode" value="edit">
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
								<td bgcolor="#cccccc" >
									<table width="442" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="442" height="7" alt=""></td>
										</tr>
										<tr>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
											<td bgcolor="#636469" width="402" class="fs14n"><span class="white"><!--コンテンツタイトル--><!--{$tpl_subtitle}--></span></td>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="442" height="7" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="442" height="10" alt=""></td>
										</tr>
									</table>

									<table width="442" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="442" height="10" alt=""></td></tr>
									</table>
									
									<!--{if $arrErr.err != ""}-->
									<table width="442" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr class="fs12n">
											<td width="442" bgcolor="#ffffff"><span class="red12"><!--{$arrErr.err}--></span><td>
										</tr>
									</table>
									<!--{/if}-->
									
									<table width="442" border="0" cellspacing="1" cellpadding="8" summary=" ">
										<tr class="fs12n">
											<td width="100" bgcolor="#f3f3f3">マーチャントID<span class="red">※</span></td>
											<td width="300" bgcolor="#ffffff">
											<!--{assign var=key value="merchant_id"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<input type="text" name="<!--{$key}-->" style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key].value}-->" class="box20" maxlength="<!--{$smarty.const.STEXT_LEN}-->">
											</td>
										</tr>
										<tr class="fs12n">
											<td width="" bgcolor="#f3f3f3">接続ID<span class="red">※</span></td>
											<td width="" bgcolor="#ffffff">
											<!--{assign var=key value="connect_id"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<input type="text" name="<!--{$key}-->" style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key].value}-->" class="box20" maxlength="<!--{$smarty.const.STEXT_LEN}-->">
											</td>
										</tr>
										<tr class="fs12n">
											<td width="" bgcolor="#f3f3f3">接続パスワード<span class="red">※</span></td>
											<td width="" bgcolor="#ffffff">
											<!--{assign var=key value="connect_password"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<input type="password" name="<!--{$key}-->" style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key].value}-->" class="box20" maxlength="<!--{$smarty.const.STEXT_LEN}-->">
											</td>
										</tr>
										<tr class="fs12n">
											<td width="" bgcolor="#f3f3f3">利用決済<span class="red">※</span></td>
											<td width="" bgcolor="#ffffff">
											<!--{assign var=key value="payment"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<!--{html_checkboxes_ex name="$key" options=$arrPayment selected=$arrForm[$key].value style=$arrErr[$key]|sfGetErrorColor onclick="lfnCheckPayment();"}-->
											</td>
										</tr>
										
										<tr class="fs12n">
											<td colspan="2" width="90" bgcolor="#f3f3f3">▼コンビニ設定</td>
										</tr>
																				
										<tr class="fs12n">
											<td width="" bgcolor="#f3f3f3">支払期限日</td>
											<td width="" bgcolor="#ffffff">
											<!--{assign var=key value="conveni_limit_date"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											購入日より<input type="text" name="<!--{$key}-->" style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key].value}-->" size="2" maxlength="2">日
											</td>
										</tr>
										
										<tr class="fs12n">
											<td colspan="2" width="90" bgcolor="#f3f3f3">▼ATM決済設定</td>
										</tr>
										
										<tr class="fs12n">
											<td width="" bgcolor="#f3f3f3">支払期限日</td>
											<td width="" bgcolor="#ffffff">
											<!--{assign var=key value="atm_limit_date"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											購入日より<input type="text" name="<!--{$key}-->" style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key].value}-->" size="2" maxlength="2">日
											</td>
										</tr>
										<tr class="fs12n">
											<td width="" bgcolor="#f3f3f3">表示店舗名（カナ）</td>
											<td width="" bgcolor="#ffffff">
											<!--{assign var=key value="payment_detail"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<input type="text" name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key].value}-->" class="box30" maxlength="<!--{$smarty.const.STEXT_LEN}-->"><br>
											<span class="fs12">※ 入金時に画面表示される確認用の説明文「○○○オンラインショップ」等</span>
											</td>
										</tr>
										
										<tr class="fs12n">
											<td colspan="2" width="90" bgcolor="#f3f3f3">▼銀行ネット決済設定</td>
										</tr>
										<tr class="fs12n">
											<td width="" bgcolor="#f3f3f3">支払期限日</td>
											<td width="" bgcolor="#ffffff">
											<!--{assign var=key value="asp_payment_term"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											購入日より<input type="text" name="<!--{$key}-->" style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key].value}-->" size="2" maxlength="2">日
											</td>
										</tr>
										<tr class="fs12n">
											<td width="" bgcolor="#f3f3f3">表示店舗名（漢字）</td>
											<td width="" bgcolor="#ffffff">
											<!--{assign var=key value="claim_kanji"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<input type="text" name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key].value}-->" class="box30" maxlength="<!--{$smarty.const.STEXT_LEN}-->"><br>
											<span class="fs12">※ 入金時に画面表示される確認用の説明文「○○○オンラインショップ」等</span>
											</td>
										</tr>
										<tr class="fs12n">
											<td width="" bgcolor="#f3f3f3">表示店舗名（カナ）</td>
											<td width="" bgcolor="#ffffff">
											<!--{assign var=key value="claim_kana"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<input type="text" name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key].value}-->" class="box30" maxlength="<!--{$smarty.const.STEXT_LEN}-->"><br>
											<span class="fs12">※ 入金時に画面表示される確認用の説明文「○○○オンラインショップ」等</span>
											</td>
										</tr>
										<tr class="fs12n">
											<td width="" bgcolor="#f3f3f3">決済ページ用コピーライト(半角英数)</td>
											<td width="" bgcolor="#ffffff">
											<!--{assign var=key value="copy_right"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<input type="text" name="<!--{$key}-->" style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key].value}-->" class="box30" maxlength="<!--{$smarty.const.STEXT_LEN}-->"><br>
											<span class="fs12">※ 入金時に画面表示される確認用の説明文「○○○オンラインショップ」等</span>
											</td>
										</tr>										
											<!--{assign var=key value="free_memo"}-->
										<tr class="fs12n">
											<td width="" bgcolor="#f3f3f3">決済ページ用説明文(全角)</td>
											<td width="" bgcolor="#ffffff">
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<textarea name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key].value}-->" cols="40" rows="2" maxlength="<!--{$smarty.const.STEXT_LEN}-->"><!--{$arrForm[$key].value}--></textarea><br>
											<span class="fs12">※ 入金時に画面表示される確認用の説明文「○○○オンラインショップ」等</span>
											</td>
										</tr>
										<!--{assign var=key value="service"}-->
										<!--{if $arrErr[$key] != ""}-->
										<tr class="fs12n">
											<td bgcolor="#ffffff" colspan=2>
											<span class="red12"><!--{$arrErr[$key]}--></span>
											</td>
										</tr>
										<!--{/if}-->
									</table>

									<table width="442" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
											<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_top.gif" width="440" height="7" alt=""></td>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
										</tr>
										<tr>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
											<td bgcolor="#e9e7de" align="center">
											<table border="0" cellspacing="0" cellpadding="0" summary=" ">
												<tr>
													<td><input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" onClick="document.body.style.cursor = 'wait';"></td>
												</tr>
											</table>
											</td>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="442" height="8" alt=""></td>
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


