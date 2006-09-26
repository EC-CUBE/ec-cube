<!--{*
/*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="deliv_id" value="<!--{$tpl_deliv_id}-->">
	<tr valign="top">
		<td background="/img/contents/navi_bg.gif" height="402">
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
								<td colspan="3"><img src="/img/contents/main_top.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="/img/contents/main_left.jpg"><img src="/img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="/img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="/img/contents/contents_title_left_bg.gif"><img src="/img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->配送業者登録</span></td>
										<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n">
										<td width="100" bgcolor="#f2f1ec">配送業者名<span class="red"> *</span></td>
										<td width="617" bgcolor="#ffffff" colspan="3">
										<!--{assign var=key value="name"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" /></td>
									</tr>
									<tr class="fs12n">
										<td width="100" bgcolor="#f2f1ec">伝票No.URL</td>
										<td width="617" bgcolor="#ffffff" colspan="3">
										<!--{assign var=key value="confirm_url"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" /></td>
									</tr>
									<!--{section name=cnt loop=$smarty.const.DELIVTIME_MAX}-->
									<!--{assign var=type value="`$smarty.section.cnt.index%2`"}-->
									<!--{assign var=keyno value="`$smarty.section.cnt.iteration`"}-->
									<!--{assign var=key value="deliv_time`$smarty.section.cnt.iteration`"}-->
									<!--{assign var=key_next value="deliv_time`$smarty.section.cnt.iteration+1`"}-->
									<!--{if $type == 0}-->
										<!--{if $arrErr[$key] != "" || $arrErr[$key_next] != ""}-->
										<tr class="fs12n">
											<td bgcolor="#ffffff" colspan="4"><span class="red12"><!--{$arrErr[$key]}--><!--{$arrErr[$key_next]}--></span></td>
										</tr>		
										<!--{/if}-->
										<tr class="fs12n">
										<td width="100" bgcolor="#f2f1ec">配送時間<!--{$keyno}--></td>
										<!--{if $smarty.section.cnt.last}-->
										<!--{assign var=colspan value="3"}-->	
										<!--{else}-->
										<!--{assign var=colspan value="1"}-->
										<!--{/if}-->
										<td width="247" bgcolor="#ffffff" colspan="<!--{$colspan}-->">
										<input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="20" class="box20" /></td>
									<!--{else}-->
										<td width="100" bgcolor="#f2f1ec">配送時間<!--{$keyno}--></td>
										<td width="248" bgcolor="#ffffff"><input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" size="20" class="box20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /> </td>
										</tr>
									<!--{/if}-->
									<!--{/section}-->

								</table>
								
								<!--{if $smarty.const.INPUT_DELIV_FEE}-->
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="/img/contents/contents_title_left_bg.gif"><img src="/img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->配送料登録</span></td>
										<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n">
										<td bgcolor="#ffffff" colspan="4">※全国一律送料 <input type="text" name="fee_all" size="10" class="box10" /> 円に設定する　<input type="button" name="renew" value="反映" onclick="fnSetDelivFee(<!--{$smarty.const.DELIVFEE_MAX}-->);" /></td>
									</tr>
									<!--{section name=cnt loop=$smarty.const.DELIVFEE_MAX}-->
									<!--{assign var=type value="`$smarty.section.cnt.index%2`"}-->
									<!--{assign var=keyno value="`$smarty.section.cnt.iteration`"}-->
									<!--{assign var=key value="fee`$smarty.section.cnt.iteration`"}-->
									<!--{assign var=key_next value="fee`$smarty.section.cnt.iteration+1`"}-->
								
									<!--{if $type == 0}-->
										<!--{if $arrErr[$key] != "" || $arrErr[$key_next] != ""}-->
										<tr class="fs12n">
											<td bgcolor="#ffffff" colspan="4"><span class="red12"><!--{$arrErr[$key]}--><!--{$arrErr[$key_next]}--></span></td>
										</tr>		
										<!--{/if}-->
										<tr class="fs12n">
										<td width="100" bgcolor="#f2f1ec"><!--{$arrPref[$keyno]}--></td>
										<!--{if $smarty.section.cnt.last}-->
										<!--{assign var=colspan value="3"}-->	
										<!--{else}-->
										<!--{assign var=colspan value="1"}-->
										<!--{/if}-->
										<td width="247" bgcolor="#ffffff" colspan="<!--{$colspan}-->">
										<input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" size="20" class="box20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /> 円</td>
									<!--{else}-->
										<td width="100" bgcolor="#f2f1ec"><!--{$arrPref[$keyno]}--></td>
										<td width="248" bgcolor="#ffffff"><input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" size="20" class="box20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /> 円</td>
										</tr>
									<!--{/if}-->
									<!--{/section}-->
								</table>
								<!--{/if}-->

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
										<td><img src="/img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
										<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
									</tr>
									<tr>
										<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
										<td bgcolor="#e9e7de" align="center">
										<table border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td>
													<a href="./delivery.php" onmouseover="chgImg('/img/contents/btn_back_on.jpg','back');" onmouseout="chgImg('/img/contents/btn_back.jpg','back');"><img src="/img/contents/btn_back.jpg" width="123" height="24" alt="前のページに戻る" border="0" name="back"></a>
													<input type="image" onMouseover="chgImgImageSubmit('/img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('/img/contents/btn_regist.jpg',this)" src="/img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" >
												</td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>
								</td>
								<td background="/img/contents/main_right.jpg"><img src="/img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
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