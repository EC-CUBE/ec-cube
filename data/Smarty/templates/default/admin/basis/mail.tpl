<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<script language="JavaScript">
<!--
var flag = 0;

function setFlag(){
	flag = 1;
}
function checkFlagAndSubmit(){
	if ( flag == 1 ){
		if( confirm('内容が変更されています。続行すれば変更内容は破棄されます。\n宜しいでしょうか？' )){
			fnSetvalAndSubmit( 'form1', 'mode', 'id_set' );
		} else {
			return false;
		}
	} else {
		fnSetvalAndSubmit( 'form1', 'mode', 'id_set' );
	}
}

//-->
</script>

<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="regist">
	<tr valign="top">
		<td background="<!--{$TPL_DIR}-->img/contents/navi_bg.gif" height="402">
			<!--▼SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--▲SUB NAVI-->
		</td>
		<td class="mainbg">
			<!--▼登録テーブルここから-->
			<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--メインエリア-->
				<tr>
					<td align="center">
						<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$TPL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<!--{$TPL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->メール設定</span></td>
										<td background="<!--{$TPL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">テンプレート<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<!--{assign var=key value="template_id"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<select name="template_id" onChange="return checkFlagAndSubmit();" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
										<option value="" selected="selected">選択してください</option>
										<!--{html_options options=$arrMailTEMPLATE selected=$arrForm[$key]}-->
										</select>
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">メールタイトル<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<!--{assign var=key value="subject"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<input type="text" name="subject" value="<!--{$arrForm[$key]|escape}-->" onChange="setFlag();" size="30" class="box30" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12">ヘッダー</td>
										<td bgcolor="#ffffff" width="557" class="fs10">
										<!--{assign var=key value="header"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<textarea name="header" cols="75" rows="12" class="area75" onChange="setFlag();" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{$arrForm[$key]|escape}--></textarea><br />
										<span class="red"> （上限<!--{$smarty.const.LTEXT_LEN}-->文字）
										</span>
						
										<div align="right">
											<input type="button" width="110" height="30" value="文字数カウント" onclick="fnCharCount('form1','header','cnt_header');" border="0" name="next" id="next" />
											<br>今までに入力したのは
											<input type="text" name="cnt_header" size="4" class="box4" readonly = true style="text-align:right">
											文字です。
										</div>
						
										</td>
									</tr>
						
									<tr class="fs12n">
										<td bgcolor="#ffffff" colspan="2" align="center" height="40">動的データ挿入部分</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12">フッター</td>
										<td bgcolor="#ffffff" width="557" class="fs10">
										<!--{assign var=key value="footer"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<textarea name="footer" cols="75" rows="12" class="area75" onChange="setFlag();" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{$arrForm[$key]|escape}--></textarea><br />
										<span class="red"> （上限<!--{$smarty.const.LTEXT_LEN}-->文字）</span>
										<div align="right">
											<input type="button" width="110" height="30" value="文字数カウント" onclick="fnCharCount('form1','footer','cnt_footer');" border="0" name="next" id="next" />
											<br>今までに入力したのは
											<input type="text" name="cnt_footer" size="4" class="box4" readonly = true style="text-align:right">
											文字です。
										</div>
										</td>
									</tr>
								</table>

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
										<td><img src="<!--{$TPL_DIR}-->img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
										<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
									</tr>
									<tr>
										<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
										<td bgcolor="#e9e7de" align="center">
										<table border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td><input type="image" onMouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$TPL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" ></td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>
								</td>
								<td background="<!--{$TPL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
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

