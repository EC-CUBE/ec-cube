<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->

<script type="text/javascript">
<!--
// カートに商品を入れるにチェックが入っているかチェック
function fnIsCartOn(){
    if (document.form1.cart_flg.checked <!--{if $is_update}-->|| <!--{$arrForm.cart_flg}--><!--{/if}-->){
		document.form1.deliv_free_flg.disabled = false;
    } else {
		document.form1.deliv_free_flg.disabled = true;    
    }
}
//-->
</script>
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="">
<input type="hidden" name="campaign_id" value="<!--{$campaign_id}-->" >
<input type="hidden" name="is_update" value="<!--{$is_update}-->" >
<!--{if $is_update}-->
<input type="hidden" name="cart_flg" value="<!--{$arrForm.cart_flg}-->" >
<!--{/if}-->
	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/navi_bg.gif" height="402">
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
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->キャンペーンページ登録</span></td>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<!--▼登録テーブルここから-->
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">	
									<thead>
									<tr>
										<td bgcolor="#f2f1ec" width="140" class="fs12n">キャンペーン名<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="538" class="fs12n"><span class="red"><!--{$arrErr.campaign_name}--></span><input type="text" name="campaign_name" size="60" class="box60"  value="<!--{$arrForm.campaign_name|escape}-->" <!--{if $arrErr.campaign_name}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}--> maxlength="<!--{$smarty.const.STEXT_LEN}-->"/></span>
									</tr>
									</thead>
									<tfoot>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="140">キャンペーン期間<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="538">
											<span class="red"><!--{$arrErr.start_year}--><!--{$arrErr.start_month}--><!--{$arrErr.start_day}--></span>
											開始日時：
											<select name="start_year" <!--{if $arrErr.start_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
												<option value="" selected>----</option>
												<!--{html_options options=$arrYear selected=$arrForm.start_year}-->
											</select>年
											<select name="start_month" <!--{if $arrErr.start_month || $arrErr.start_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
												<option value="" selected>--</option>
												<!--{html_options options=$arrMonth selected=$arrForm.start_month}-->
											</select>月
											<select name="start_day" <!--{if $arrErr.start_day || $arrErr.start_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
												<option value="" selected>--</option>
												<!--{html_options options=$arrDay selected=$arrForm.start_day}-->
											</select>日
											<select name="start_hour" <!--{if $arrErr.start_hour || $arrErr.start_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
												<option value="" selected>--</option>
												<!--{html_options options=$arrHour selected=$arrForm.start_hour}-->
											</select>時
											<select name="start_minute" <!--{if $arrErr.start_minute || $arrErr.start_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
												<option value="" selected>--</option>
												<!--{html_options options=$arrMinutes selected=$arrForm.start_minute}-->
											</select>分<br/><br/><br/>
											<span class="red"><!--{$arrErr.end_year}--><!--{$arrErr.end_month}--><!--{$arrErr.end_day}--></span>	
											停止日時：
											<select name="end_year" <!--{if $arrErr.end_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
												<option value="" selected>----</option>
												<!--{html_options options=$arrYear selected=$arrForm.end_year}-->
											</select>年
											<select name="end_month" <!--{if $arrErr.end_month || $arrErr.end_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
												<option value="" selected>--</option>
												<!--{html_options options=$arrMonth selected=$arrForm.end_month}-->
											</select>月
											<select name="end_day" <!--{if $arrErr.end_day || $arrErr.end_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
												<option value="" selected>--</option>
												<!--{html_options options=$arrDay selected=$arrForm.end_day}-->
											</select>日
											<select name="end_hour" <!--{if $arrErr.end_hour || $arrErr.end_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
												<option value="" selected>--</option>
												<!--{html_options options=$arrHour selected=$arrForm.end_hour}-->
											</select>時
											<select name="end_minute" <!--{if $arrErr.end_minute || $arrErr.end_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
												<option value="" selected>--</option>
												<!--{html_options options=$arrMinutes selected=$arrForm.end_minute}-->
											</select>分<br/><br/><br/>
										</td>
									</tr>									
									<tr>
										<td bgcolor="#f2f1ec" width="140" class="fs12n">ディレクトリ名<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="538">
											<span class="red12"><!--{$arrErr.directory_name}--></span><input type="text" name="directory_name" size="60" class="box60"  value="<!--{$arrForm.directory_name|escape}-->" <!--{if $arrErr.directory_name}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}--> maxlength="<!--{$smarty.const.STEXT_LEN}-->"/></span><br/>
											<span class="fs10">※<!--{$smarty.const.SITE_URL|sfTrimURL}--><!--{$smarty.const.URL_DIR|sfTrimURL}--><!--{$smarty.const.CAMPAIGN_URL}-->入力したディレクリ名/ でアクセス出来るようになります。</span>
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="140" class="fs12n">申込数制御</td>
										<td bgcolor="#ffffff" width="538" class="fs12n"><span class="red"><!--{$arrErr.limit_count}--></span><input type="text" name="limit_count" size="54" class="box6"  value="<!--{$arrForm.limit_count|escape}-->" <!--{if $arrErr.limit_count}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}--> maxlength="<!--{$smarty.const.STEXT_LEN}-->"/>&nbsp;件で終了ページに切り替え
										</td>
									</tr>								
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="140">重複申込制御</td>
										<td bgcolor="#ffffff" width="538"><input type="checkbox" name="orverlapping_flg" id="orverlapping_flg" value="1" <!--{if $arrForm.orverlapping_flg eq 1}--> checked <!--{/if}--> ><label for="orverlapping_flg">重複申込を制御する</label></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="140">カートに商品を入れる</td>
										<td bgcolor="#ffffff" width="538"><input type="checkbox" onclick="fnIsCartOn()" name="cart_flg" id="cart_flg" value="1" <!--{if $arrForm.cart_flg eq 1}--> checked <!--{/if}--> <!--{if $is_update}-->disabled<!--{/if}-->><label for="cart_flg">カートに商品を入れるようにする</label></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="140">送料無料設定</td>
										<td bgcolor="#ffffff" width="538"><input type="checkbox" name="deliv_free_flg" id="deliv_free_flg" value="1" <!--{if $arrForm.deliv_free_flg eq 1}--> checked <!--{/if}--> ><label for="deliv_free_flg">送料無料</label></td>
									</tr>
									</tfoot>
								</table>
								<!--▲登録テーブルここまで-->
								
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
										<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
									</tr>
									<tr>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
										<td bgcolor="#e9e7de" align="center">
										<table border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td><a href="javascript:fnFormModeSubmit('form1', 'regist', '', '');"><img onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0"></a></td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
									</form>
								</table>
								
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
								</table>
								
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->キャンペーン一覧</span></td>										
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<!--▼一覧表示エリアここから-->
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr bgcolor="#f2f1ec" align="center" class="fs12n">
										<td width="205" rowspan="2">キャンペーン名</td>
										<td width="50" rowspan="2">申込人数</td>
										<td width="160" colspan="2">デザイン設定</td>
										<td width="50" rowspan="2">編集</td>
										<td width="50" rowspan="2">削除</td>
										<td width="50" rowspan="2">CSV</td>
									</tr>
									<tr bgcolor="#f2f1ec" align="center" class="fs12n">
										<td width="80">キャンペーン中</td>
										<td width="80">キャンペーン終了</td>
									</tr>
									<!--{section name=cnt loop=$arrCampaign}-->					
									<tr bgcolor="#ffffff" align="center" class="fs12n">
										<td width="205"><!--{$arrCampaign[cnt].campaign_name}--></td>
										<td width="50"><!--{$arrCampaign[cnt].total_count}--></td>
										<td width="80"><a href="<!--{$smarty.const.URL_CAMPAIGN_DESIGN}-->?campaign_id=<!--{$arrCampaign[cnt].campaign_id}-->&status=active">設定</a></td>
										<td width="80"><a href="<!--{$smarty.const.URL_CAMPAIGN_DESIGN}-->?campaign_id=<!--{$arrCampaign[cnt].campaign_id}-->&status=end">設定</a></td>
										<!--{if $arrCampaign[cnt].campaign_id != $arrForm.campaign_id}-->
										<td width="50"><a href="javascript:fnFormModeSubmit('form1', 'update', 'campaign_id', '<!--{$arrCampaign[cnt].campaign_id}-->')">編集</a></td>
										<!--{else}-->
										<td width="50">編集</td>
										<!--{/if}-->
										<td width="50"><a href="javascript:fnFormModeSubmit('form1', 'delete', 'campaign_id', '<!--{$arrCampaign[cnt].campaign_id}-->')">削除</a></td>
										<td width="50"><a href="javascript:fnFormModeSubmit('form1', 'csv', 'campaign_id', '<!--{$arrCampaign[cnt].campaign_id}-->')">CSV</a></td>
									</tr>
									<!--{/section}-->									
								</table>
								<!--▲一覧表示エリアここまで-->
									
								</td>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
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
