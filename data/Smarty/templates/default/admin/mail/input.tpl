<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<!--{foreach key=key item=val from=$arrHidden}-->	
	<input type="hidden" name="<!--{$key}-->" value="<!--{$val|escape}-->">
<!--{/foreach}-->
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->配信設定：配信内容設定</span></td>
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
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="200">テンプレート選択<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="507">
										<!--{if $arrErr.template_id}--><span class="red12"><!--{$arrErr.template_id}--></span><!--{/if}-->
										<select name="template_id" onchange="return fnInsertValAndSubmit( document.form1, 'mode', 'template', '' ) " style="<!--{$arrErr.template_id|sfGetErrorColor}-->">
										<option value="" selected="selected">選択してください</option>
										<!--{html_options options=$arrTemplate selected=$list_data.template_id}-->
										</select>
										</td>
									
									<!--{* バッチモードの場合のみ表示 *}-->
									<!--{if $smarty.const.MELMAGA_BATCH_MODE}-->
									<tr class="fs12n">
										<td bgcolor="#f2f1ec">配信時間設定<span class="red"> *</span></td>
										<td bgcolor="#ffffff">
										<!--{if $arrErr.send_year || $arrErr.send_month || $arrErr.send_day || $arrErr.send_hour || $arrErr.send_minutes}--><span class="red12"><!--{$arrErr.send_year}--><!--{$arrErr.send_month}--><!--{$arrErr.send_day}--><!--{$arrErr.send_hour}--><!--{$arrErr.send_minutes}--></span><br><!--{/if}-->
										<select name="send_year" style="<!--{$arrErr.send_year|sfGetErrorColor}-->">
										<!--{html_options options=$arrYear selected=$arrNowDate.year}-->
										</select>年
										<select name="send_month" style="<!--{$arrErr.send_month|sfGetErrorColor}-->">
										<!--{html_options options=$objDate->getMonth() selected=$arrNowDate.month}-->
										</select>月
										<select name="send_day" style="<!--{$arrErr.send_day|sfGetErrorColor}-->">
										<!--{html_options options=$objDate->getDay() selected=$arrNowDate.day}-->
										</select>日
										<select name="send_hour" style="<!--{$arrErr.send_hour|sfGetErrorColor}-->">
										<!--{html_options options=$objDate->getHour() selected=$arrNowDate.hour}-->
										</select>時
										<select name="send_minutes" style="<!--{$arrErr.send_minutes|sfGetErrorColor}-->">
										<!--{html_options options=$objDate->getMinutesInterval() selected=$arrNowDate.minutes}-->
										</select>分</td>
									</tr>
									<!--{/if}-->
								</table>

								<!--{if $list_data.template_id}-->
									<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr><td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
									</table>
	
	
									<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
										<tr>
											<td bgcolor="#f2f1ec" class="fs12n">Subject<span class="red"> *</span></td>
											<td bgcolor="#ffffff" class="fs12n">
											<!--{if $arrErr.subject}--><span class="red12"><!--{$arrErr.subject}--></span><!--{/if}-->
											<input type="text" name="subject" size="65" class="box65" <!--{if $arrErr.subject}--><!--{sfSetErrorStyle}--><!--{/if}--> value="<!--{$list_data.subject|escape}-->" />
											</td>
										</tr>
										<tr>
											<td bgcolor="#f2f1ec" colspan="2" class="fs12n">本文<span class="red"> *</span>（名前差し込み時は {name} といれてください）</td>
										</tr>
										<tr>
											<td bgcolor="#ffffff" colspan="2" class="fs12n">
											<!--{if $arrErr.body}--><span class="red12"><!--{$arrErr.body}--></span><!--{/if}-->
											<textarea name="body" cols="90" rows="40" class="area90" <!--{if $arrErr.body}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{$list_data.body|escape}--></textarea>
											</td>
										</tr>
									</table>
								<!--{/if}-->

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
												<td>
													<input type="hidden" name="mode" value="template">
													<input type="hidden" name="mail_method" value="<!--{$list_data.mail_method}-->">

													<input type="image" onMouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_search_back_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_search_back.jpg',this)" src="<!--{$TPL_DIR}-->img/contents/btn_search_back.jpg" width="123" height="24" alt="検索画面に戻る" border="0" name="subm02" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'back', '' )">
													<input type="image" onMouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_confirm_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_confirm.jpg',this)" src="<!--{$TPL_DIR}-->img/contents/btn_confirm.jpg" width="123" height="24" alt="確認ページへ" border="0" name="subm03" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'regist_confirm', '' )" >
												</td>
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
