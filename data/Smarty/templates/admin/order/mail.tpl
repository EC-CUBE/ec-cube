<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="order_id" value="<!--{$tpl_order_id}-->">
<!--{foreach key=key item=item from=$arrSearchHidden}-->
	<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->
	<tr valign="top">
		<td background="/img/contents/navi_bg.gif" height="402">
			<!--▼SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--▲SUB NAVI-->
		</td>
		<td class="mainbg" >
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
						
						<!--登録テーブルここから-->
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="3"><img src="/img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td background="/img/contents/contents_title_left_bg.gif"><img src="/img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->メール配信</span></td>
								<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>
						
						<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr align="center">
								<td bgcolor="#f2f1ec" width="140" class="fs12n">処理日</td>
								<td bgcolor="#f2f1ec" width="300" class="fs12n">通知メール</td>
								<td bgcolor="#f2f1ec" width="300" class="fs12n">件名</td>
							</tr>
							<!--{section name=cnt loop=$arrMailHistory}-->
							<tr align="center">
								<td bgcolor="#ffffff" width="140" class="fs12n"><!--{$arrMailHistory[cnt].send_date|sfDispDBDate|escape}--></td>
								<!--{assign var=key value="`$arrMailHistory[cnt].template_id`"}-->
								<td bgcolor="#ffffff" width="300" class="fs12n"><!--{$arrMAILTEMPLATE[$key]|escape}--></td>
								<td bgcolor="#ffffff" width="300" class="fs12n"><a href="<!--{$smarty.server.PHP_SELF}-->" onclick="win01('./mail_view.php?send_id=<!--{$arrMailHistory[cnt].send_id}-->','mail_view','650','800'); return false;"><!--{$arrMailHistory[cnt].subject|escape}--></a></td>
							</tr>
							<!--{/section}-->
						</table>

						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
						</table>

						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<tr>
								<td bgcolor="#f2f1ec" width="160" class="fs12n">テンプレート<span class="red"> *</span></td>
								<td bgcolor="#ffffff" width="557" class="fs10n">
								<!--{assign var=key value="template_id"}-->
								<span class="red12"><!--{$arrErr[$key]}--></span>
								<select name="template_id" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" onchange="fnModeSubmit('change', '', '');">
								<option value="" selected="selected">選択してください</option>
								<!--{html_options options=$arrMAILTEMPLATE selected=$arrForm[$key].value|escape}-->
								</select>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" width="160" class="fs12n">メールタイトル<span class="red"> *</span></td>
								<td bgcolor="#ffffff" width="557" class="fs10n">
								<!--{assign var=key value="subject"}-->
								<span class="red12"><!--{$arrErr[$key]}--></span>
								<input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" /></td>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" width="160" class="fs12">ヘッダー</td>
								<td bgcolor="#ffffff" width="557" class="fs10">
								<!--{assign var=key value="header"}-->
								<span class="red12"><!--{$arrErr[$key]}--></span>
								<textarea  name="<!--{$arrForm[$key].keyname}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" cols="75" rows="12" class="area75"><!--{$arrForm[$key].value|escape}--></textarea></td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#ffffff" colspan="2" align="center" height="40">動的データ挿入部分</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" width="160" class="fs12">フッター</td>
								<td bgcolor="#ffffff" width="557" class="fs10">
								<!--{assign var=key value="footer"}-->
								<span class="red12"><!--{$arrErr[$key]}--></span>
								<textarea  name="<!--{$arrForm[$key].keyname}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" cols="75" rows="12" class="area75"><!--{$arrForm[$key].value|escape}--></textarea></td>
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
											<input type="button" name="back" value="検索結果へ戻る" onclick="fnChangeAction('<!--{$smarty.const.URL_SEARCH_ORDER}-->'); fnModeSubmit('search','',''); return false;" />
											<input type="submit" name="subm" value="送信内容を確認"/>
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

						<!--登録テーブルここまで-->
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
		</td>
	</tr>
</form>
</table>
<!--★★メインコンテンツ★★-->		
