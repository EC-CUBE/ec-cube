<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<script type="text/javascript">
<!--

function func_return(){
	document.form1.mode.value = "return";
	document.form1.submit();
}

//-->
</script>

<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="complete">
<!--{foreach from=$arrForm key=key item=item}-->
	<!--{if $key ne "mode" && $key ne "subm"}-->
	<input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->">
	<!--{/if}-->
<!--{/foreach}-->
<!-- 検索条件の保持 -->
<!--{foreach from=$arrSearchData key="key" item="item"}-->
	<!--{if $key ne "job" && $key ne "sex"}--><input type="hidden" name="search_data[<!--{$key|escape}-->]" value="<!--{$item|escape}-->"><!--{/if}-->
<!--{/foreach}-->
<!--{foreach from=$arrSearchData.job key="key" item="item"}-->
<input type="hidden" name="search_data[job][]" value=<!--{$item}-->>
<!--{/foreach}-->
<!--{foreach from=$arrSearchData.sex key="key" item="item"}-->
<input type="hidden" name="search_data[sex][]" value=<!--{$item}-->>
<!--{/foreach}-->
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->顧客編集</span></td>
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
										<td bgcolor="#f2f1ec" width="190">顧客ID<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><!--{$arrForm.customer_id|escape}--></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">会員状態<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><!--{if $arrForm.status == 1}-->仮会員<!--{else}-->本会員<!--{/if}--></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">お名前<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><!--{$arrForm.name01|escape}--><!--{$arrForm.name02|escape}-->　様</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">フリガナ<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><!--{$arrForm.kana01|escape}--><!--{$arrForm.kana02|escape}-->　様</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">郵便番号<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527">〒 <!--{$arrForm.zip01|escape}--> - <!--{$arrForm.zip02|escape}--></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190" class="fs12">ご住所<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><!--{$arrPref[$arrForm.pref]|escape}--><!--{$arrForm.addr01|escape}--><!--{$arrForm.addr02|escape}--></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">メールアドレス<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><!--{$arrForm.email|escape}--></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">お電話番号<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><!--{$arrForm.tel01|escape}--> - <!--{$arrForm.tel02|escape}--> - <!--{$arrForm.tel03|escape}--></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">FAX</td>
										<td bgcolor="#ffffff" width="527"><!--{if strlen($arrForm.fax01) > 0}--><!--{$arrForm.fax01|escape}--> - <!--{$arrForm.fax02|escape}--> - <!--{$arrForm.fax03|escape}--><!--{else}-->未登録<!--{/if}--></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">ご性別<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><!--{if $$arrForm.sex eq 1}-->男性<!--{else}-->女性<!--{/if}--></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">ご職業</td>
										<td bgcolor="#ffffff" width="527"><!--{$arrJob[$arrForm.job]|escape|default:"未登録"}--></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">生年月日</td>
										<td bgcolor="#ffffff" width="527"><!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}--><!--{$arrForm.year|escape}-->年<!--{$arrForm.month|escape}-->月<!--{$arrForm.day|escape}-->日<!--{else}-->未登録<!--{/if}--></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">パスワード<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><!--{$passlen}--></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="190" class="fs12n">パスワードを忘れたときのヒント<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527">
										<table width="527" border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td class="fs12n">質問： <!--{$arrReminder[$arrForm.reminder]|escape}--></td>
											</tr>
											<tr><td height="5"></td></tr>
											<tr><td class="fs12n">答え： <!--{$arrForm.reminder_answer|escape}--></td>
											</tr>
										</table>
										</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">メールマガジン<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><!--{if $arrForm.mail_flag eq 1}-->HTML<!--{elseif $arrForm.mail_flag eq 2}-->テキスト<!--{else}-->希望しない<!--{/if}--></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="960">SHOP用メモ</td>
										<td bgcolor="#ffffff" width="527"><!--{$arrForm.note|escape|nl2br|default:"未登録"}--></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">所持ポイント</td>
										<td bgcolor="#ffffff" width="527"><!--{$arrForm.point|escape|default:"0"}--> pt</td>
									</tr>
								</table>

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
													<a href="#" onmouseover="chgImg('/img/contents/btn_back_on.jpg','back');" onmouseout="chgImg('/img/contents/btn_back.jpg','back');"  onclick="func_return(); return false;"><img src="/img/contents/btn_back.jpg" width="123" height="24" alt="検索画面に戻る" border="0" name="back"></a>
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
