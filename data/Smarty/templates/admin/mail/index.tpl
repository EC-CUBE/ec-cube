<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form_search" id="form_search" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="search">
	<tr valign="top">
		<td background="/img/contents/navi_bg.gif" height="402">
			<!-- サブナビ -->
			<!--{include file=$tpl_subnavi}-->
		</td>
		<td class="mainbg">
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
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->配信先検索条件設定</span></td>
								<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>
						
						<!--検索条件設定テーブルここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
				
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">顧客名</td>
								<td bgcolor="#ffffff" width="194">
									<!--{if $arrErr.name}--><span class="red12"><!--{$arrErr.name}--></span><br><!--{/if}-->
									<input type="text" name="name" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$list_data.name|escape}-->" size="30" class="box30"  style="<!--{$arrErr.name|sfGetErrorColor}-->" />
								</td>
								<td bgcolor="#f2f1ec" width="110">顧客名（カナ）</td>
								<td bgcolor="#ffffff" width="195">
									<!--{if $arrErr.kana}--><span class="red12"><!--{$arrErr.kana}--></span><br><!--{/if}-->
									<input type="text" name="kana" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$list_data.kana|escape}-->" size="30" class="box30"  style="<!--{$arrErr.kana|sfGetErrorColor}-->" />
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">都道府県</td>
								<td bgcolor="#ffffff" width="194">
									<!--{if $arrErr.pref}--><span class="red12"><!--{$arrErr.pref}--></span><br><!--{/if}-->
									<select name="pref">
										<option value="" selected="selected"  style="<!--{$arrErr.pref|sfGetErrorColor}-->">都道府県を選択</option>
										<!--{html_options options=$arrPref selected=$list_data.pref}-->
									</select>
								</td>
								<td bgcolor="#f2f1ec" width="110">TEL</td>
								<td bgcolor="#ffffff" width="195">
									<!--{if $arrErr.tel}--><span class="red12"><!--{$arrErr.tel}--></span><br><!--{/if}-->
									<input type="text" name="tel" maxlength="<!--{$smarty.const.TEL_LEN}-->" value="<!--{$list_data.tel|escape}-->" size="30" class="box30" style="<!--{$arrErr.tel|sfGetErrorColor}-->" />
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">性別</td>
								<td bgcolor="#ffffff" width="194">
									<!--{html_checkboxes name="sex" options=$arrSex separator="&nbsp;" selected=$list_data.sex}-->
								</td>
								<td bgcolor="#f2f1ec" width="110">誕生月</td>
								<td bgcolor="#ffffff" width="195">
									<!--{if $arrErr.birth_month}--><span class="red12"><!--{$arrErr.birth_month}--></span><br><!--{/if}-->
									<select name="birth_month" style="<!--{$arrErr.birth_month|sfGetErrorColor}-->" >
										<option value="" selected="selected">--</option>
										<!--{html_options options=$objDate->getMonth() selected=$list_data.birth_month|escape}-->
									</select>月
								</td>				
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">配信形式</td>
								<td bgcolor="#ffffff" width="194">
									<!--{if $arrErr.htmlmail}--><span class="red12"><!--{$arrErr.htmlmail}--></span><br /><!--{/if}-->
									<!--{html_radios name="htmlmail" options=$arrHtmlmail separator="&nbsp;" selected=$list_data.htmlmail}-->
								</td>
								<td bgcolor="#f2f1ec" width="110">購入回数</td>
								<td bgcolor="#ffffff" width="195">
									<!--{if $arrErr.buy_times_from || $arrErr.buy_times_to}--><span class="red12"><!--{$arrErr.buy_times_from}--><!--{$arrErr.buy_times_to}--></span><br><!--{/if}-->
									<input type="text" name="buy_times_from" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$list_data.buy_times_from|escape}-->" size="6" class="box6" style="<!--{$arrErr.buy_times_from|sfGetErrorColor}-->" /> 回 〜 
									<input type="text" name="buy_times_to" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$list_data.buy_times_to|escape}-->" size="6" class="box6" style="<!--{$arrErr.buy_times_to|sfGetErrorColor}-->" /> 回
								</td>
							</tr>
				
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">購入商品コード</td>
								<td bgcolor="#ffffff" width="194">
								<!--{if $arrErr.buy_product_code}--><span class="red12"><!--{$arrErr.buy_product_code}--></span><!--{/if}-->
								<input type="text" name="buy_product_code" value="<!--{$list_data.buy_product_code}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.buy_product_code|sfGetErrorColor}-->" >
								</td>
				
								<td bgcolor="#f2f1ec" width="110">購入金額</td>
								<td bgcolor="#ffffff" width="195">
									<!--{if $arrErr.buy_total_from || $arrErr.buy_total_to}-->
										<span class="red12"><!--{$arrErr.buy_total_from}--><!--{$arrErr.buy_total_to}--></span><br>
									<!--{/if}-->
									<input type="text" name="buy_total_from" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$list_data.buy_total_from|escape}-->" size="6" class="box6" <!--{if $arrErr.buy_total_from || $arrErr.buy_total_to}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 円 〜
									<input type="text" name="buy_total_to" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$list_data.buy_total_to|escape}-->" size="6" class="box6" <!--{if $arrErr.buy_total_from || $arrErr.buy_total_to}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 円
								</td>
							</tr>
							<!--{*非会員はメルマガ非対応
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">種別</td>
								<td bgcolor="#ffffff" width="607" colspan="3">
								<!--{html_checkboxes name="customer" options=$arrCustomerType separator="&nbsp;" selected=$list_data.customer}-->
								</td>
							</tr>
							*}-->
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">メールアドレス</td>
								<td bgcolor="#ffffff" colspan="3">
									<!--{if $arrErr.email}--><span class="red12"><!--{$arrErr.email}--></span><!--{/if}-->
									<span style="<!--{$arrErr.email|sfGetErrorColor}-->">
									<input type="text" name="email" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$list_data.email|escape}-->" size="60" class="box60"  style="<!--{$arrErr.email|sfGetErrorColor}-->"/>
									</span>
								</td>
							</tr>
							
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">職業</td>
								<td bgcolor="#ffffff" colspan="3">
									<!--{if $arrErr.job}--><span class="red12"><!--{$arrErr.job}--></span><!--{/if}-->
									<!--{html_checkboxes name="job" options=$arrJob separator="&nbsp;" selected=$list_data.job}-->
								</td>
							</tr>
				
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">生年月日</td>
								<td bgcolor="#ffffff" colspan="3">
									<!--{if $arrErr.b_start_year || $arrErr.b_end_year}--><span class="red12"><!--{$arrErr.b_start_year}--><!--{$arrErr.b_end_year}--></span><br><!--{/if}-->
									<select name="b_start_year" style="<!--{$arrErr.b_start_year|sfGetErrorColor}-->">
										<option value="" selected="selected">----</option>
										<!--{html_options options=$objDate->getYear($smarty.const.BIRTH_YEAR) selected=$list_data.b_start_year}-->
									</select>年
									<select name="b_start_month" style="<!--{$arrErr.b_start_year|sfGetErrorColor}-->">
										<option value="" selected="selected">--</option>
										<!--{html_options options=$objDate->getMonth() selected=$list_data.b_start_month}-->
									</select>月
									<select name="b_start_day" style="<!--{$arrErr.b_start_year|sfGetErrorColor}-->">
										<option value="" selected="selected">--</option>
										<!--{html_options options=$objDate->getDay() selected=$list_data.b_start_day}-->
									</select>日&nbsp;〜&nbsp;
									<select name="b_end_year" style="<!--{$arrErr.b_end_year|sfGetErrorColor}-->">
										<option value="" selected="selected">----</option>
										<!--{html_options options=$objDate->getYear($smarty.const.BIRTH_YEAR) selected=$list_data.b_end_year}-->
									</select>年
									<select name="b_end_month" style="<!--{$arrErr.b_end_year|sfGetErrorColor}-->">
										<option value="" selected="selected">--</option>
										<!--{html_options options=$objDate->getMonth() selected=$list_data.b_end_month}-->
									</select>月
									<select name="b_end_day" style="<!--{$arrErr.b_end_year|sfGetErrorColor}-->">
										<option value="" selected="selected">--</option>
										<!--{html_options options=$objDate->getDay() selected=$list_data.b_end_day}-->
									</select>日
								</td>
							</tr>	
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">登録日</td>
								<td bgcolor="#ffffff" colspan="3">
									<!--{if $arrErr.start_year || $arrErr.end_year}--><span class="red12"><!--{$arrErr.start_year}--><!--{$arrErr.end_year}--></span><br><!--{/if}-->
									<select name="start_year"  style="<!--{$arrErr.start_year|sfGetErrorColor}-->">
										<option value="" selected="selected">----</option>
										<!--{html_options options=$objDate->getYear($smarty.const.RELEASE_YEAR) selected=$list_data.start_year}-->
									</select>年
									<select name="start_month" style="<!--{$arrErr.start_year|sfGetErrorColor}-->">
										<option value="" selected="selected">--</option>
										<!--{html_options options=$objDate->getMonth() selected=$list_data.start_month}-->
									</select>月
									<select name="start_day" style="<!--{$arrErr.start_year|sfGetErrorColor}-->">
										<option value="" selected="selected">--</option>
										<!--{html_options options=$objDate->getDay() selected=$list_data.start_day}-->
									</select>日&nbsp;〜&nbsp;
									<select name="end_year"  style="<!--{$arrErr.end_year|sfGetErrorColor}-->">
										<option value="" selected="selected">----</option>
										<!--{html_options options=$objDate->getYear($smarty.const.RELEASE_YEAR) selected=$list_data.end_year}-->
									</select>年
									<select name="end_month" style="<!--{$arrErr.end_year|sfGetErrorColor}-->">
										<option value="" selected="selected">--</option>
										<!--{html_options options=$objDate->getMonth() selected=$list_data.end_month}-->
									</select>月
									<select name="end_day" style="<!--{$arrErr.end_year|sfGetErrorColor}-->">
										<option value="" selected="selected">--</option>
										<!--{html_options options=$objDate->getDay() selected=$list_data.end_day}-->
									</select>日
								</td>
							</tr>			
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">最終購入日</td>
								<td bgcolor="#ffffff" colspan="3" width="499">
									<!--{if $arrErr.buy_start_year || $arrErr.buy_end_year}--><span class="red12"><!--{$arrErr.buy_start_year}--><!--{$arrErr.buy_end_year}--></span><br><!--{/if}-->
									<select name="buy_start_year" style="<!--{$arrErr.buy_start_year|sfGetErrorColor}-->">
										<option value="" selected="selected">----</option>
										<!--{html_options options=$objDate->getYear($smarty.const.RELEASE_YEAR)  selected=$list_data.buy_start_year}-->
									</select>年
									<select name="buy_start_month" style="<!--{$arrErr.buy_start_year|sfGetErrorColor}-->">
										<option value="" selected="selected">--</option>
										<!--{html_options options=$objDate->getMonth() selected=$list_data.buy_start_month}-->
									</select>月
									<select name="buy_start_day" style="<!--{$arrErr.buy_start_year|sfGetErrorColor}-->">
										<option value="" selected="selected">--</option>
										<!--{html_options options=$objDate->getDay() selected=$list_data.buy_start_day}-->
									</select>日&nbsp;〜&nbsp;
									<select name="buy_end_year" style="<!--{$arrErr.buy_end_year|sfGetErrorColor}-->">
										<option value="" selected="selected">----</option>
										<!--{html_options options=$objDate->getYear($smarty.const.RELEASE_YEAR)  selected=$list_data.buy_end_year}-->
									</select>年
									<select name="buy_end_month" style="<!--{$arrErr.buy_end_year|sfGetErrorColor}-->">
										<option value="" selected="selected">--</option>
										<!--{html_options options=$objDate->getMonth()  selected=$list_data.buy_end_month}-->
									</select>月
									<select name="buy_end_day" style="<!--{$arrErr.buy_end_year|sfGetErrorColor}-->">
										<option value="" selected="selected">--</option>
										<!--{html_options options=$objDate->getDay() selected=$list_data.buy_end_day}-->
									</select>日
								</td>
							</tr>
				
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">購入商品名</td>
								<td bgcolor="#ffffff" width="194">
									<!--{if $arrErr.buy_product_name}--><span class="red12"><!--{$arrErr.buy_product_name}--></span><!--{/if}-->
									<span style="<!--{$arrErr.buy_product_name|sfGetErrorColor}-->">
									<input type="text" name="buy_product_name" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$list_data.buy_product_name|escape}-->" size="30" class="box30"  style="<!--{$arrErr.buy_product_name|sfGetErrorColor}-->"/>
									</span>
								</td>
								<td bgcolor="#f2f1ec" width="110">カテゴリ</td>
								<td bgcolor="#ffffff" width="195">
									<select name="category_id" style="<!--{if $arrErr.category_id != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->">
										<option value="">選択してください</option>
										<!--{html_options options=$arrCatList selected=$list_data.category_id}-->
									</select>
								</td>
							</tr>
				
						</table>
						<!--検索条件設定テーブルここまで-->

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
										<td><input type="image" name="subm" onMouseover="chgImgImageSubmit('/img/contents/btn_search_on.jpg',this)" onMouseout="chgImgImageSubmit('/img/contents/btn_search.jpg',this)" src="/img/contents/btn_search.jpg" width="123" height="24" alt="この条件で検索する" border="0"></td>
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
		</td>
	</tr>
</form>	
</table>
<!--★★メインコンテンツ★★-->

<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete' or $smarty.post.mode == 'back') }-->

<!--★★検索結果一覧★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="">
<input type="hidden" name="search_pageno" value="<!--{$tpl_pageno}-->">
<input type="hidden" name="result_email" value="">
<!--{foreach key=key item=val from=$arrHidden}-->	
	<input type="hidden" name="<!--{$key}-->" value="<!--{$val|escape}-->">
<!--{/foreach}-->
	<tr><td colspan="2"><img src="/img/contents/search_line.jpg" width="878" height="12" alt=""></td></tr>
	<tr bgcolor="cbcbcb">
		<td>
		<table border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="/img/contents/search_left.gif" width="19" height="22" alt=""></td>
				<td>
				<!--検索結果-->
				<table border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td><img src="/img/contents/reselt_left_top.gif" width="22" height="5" alt=""></td>
						<td background="/img/contents/reselt_top_bg.gif"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
						<td><img src="/img/contents/reselt_right_top.gif" width="22" height="5" alt=""></td>
					</tr>
					<tr>
						<td background="/img/contents/reselt_left_bg.gif"><img src="/img/contents/reselt_left_middle.gif" width="22" height="12" alt=""></td>
						<td bgcolor="#393a48" class="white10">検索結果一覧　<span class="reselt"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。</td>
						<td background="/img/contents/reselt_right_bg.gif"><img src="/img/common/_.gif" width="22" height="8" alt=""></td>
					</tr>
					<tr>
						<td><img src="/img/contents/reselt_left_bottom.gif" width="22" height="5" alt=""></td>
						<td background="/img/contents/reselt_bottom_bg.gif"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
						<td><img src="/img/contents/reselt_right_bottom.gif" width="22" height="5" alt=""></td>
					</tr>
				</table>
				<!--検索結果-->
				<!--{if $smarty.const.ADMIN_MODE == '1'}-->
				<input type="button" name="subm" value="検索結果をすべて削除" onclick="fnModeSubmit('delete_all','','');" />
				<!--{/if}-->
				</td>
				<td><img src="/img/common/_.gif" width="8" height="1" alt=""></td>
				<td><input type="submit" name="subm" value="配信内容を設定する" onclick="document.form1['mode'].value='input';"/></td>
			</tr>
		</table>
		</td>
		<td align="right">
			<!--{include file=$tpl_pager}-->
		</td>
	</tr>
	<tr><td bgcolor="cbcbcb" colspan="2"><img src="/img/common/_.gif" width="1" height="5" alt=""></td></tr>
</table>

<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#f0f0f0" align="center">

		<!--{if count($arrResults) > 0}-->		

			<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr><td height="12"></td></tr>
				<tr>
					<td bgcolor="#cccccc">
					<!--検索結果表示テーブル-->
					<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
						<tr bgcolor="#636469" align="center" class="fs10n">
							<td width="20"><span class="white">#</span></td>
							<td width="80"><span class="white">会員番号</span></td>
							<td width="80"><span class="white">受注番号</span></td>
							<td width="120"><span class="white">名前</span></td>				
							<td width="120"><span class="white">メールアドレス</span></td>	
							<td width="120"><span class="white">希望配信</span></td>
							<td width="120"><span class="white">登録日</span></td>
							<td width="40"><span class="white">削除</span></td>		
						</tr>
						<!--{section name=i loop=$arrResults}-->
						<tr bgcolor="#FFFFFF" class="fs10n">
							<td align="center"><!--{$smarty.section.i.iteration}--></td>
							<td align="center"><!--{$arrResults[i].customer_id|default:"非会員"}--></td>
			
							<!--{assign var=key value="`$arrResults[i].customer_id`"}-->
							<td align="center">
							<!--{foreach key=key item=val from=$arrCustomerOrderId[$key]}-->
							<a href="#" onclick="fnOpenWindow('../order/edit.php?order_id=<!--{$val}-->','order_disp','800','900'); return false;" ><!--{$val}--></a><br />
							<!--{foreachelse}-->
							-
							<!--{/foreach}-->
							</td>
							
							<td><!--{$arrResults[i].name01|escape}--> <!--{$arrResults[i].name02|escape}--></td>
							<td><!--{$arrResults[i].email|escape}--></td>
							<!--{assign var="key" value="`$arrResults[i].mail_flag`"}-->
							<td align="center"><!--{$arrMAILMAGATYPE[$key]}--></td>
							<td><!--{$arrResults[i].create_date|sfDispDBDate}--></td>
							<!--{if $arrResults[i].customer_id != ""}-->
							<td align="center">-</td>
							<!--{else}-->
							<td align="center"><a href="<!--{$smarty.server.PHP_SELF}-->" onclick="fnFormModeSubmit('form1','delete','result_email','<!--{$arrResults[i].email|escape}-->'); return false;">削除</a></td>	
							<!--{/if}-->
						</tr>
						<!--{/section}-->
					</table>
					<!--検索結果表示テーブル-->
					</td>
				</tr>
			</table>
		<!--{/if}-->

		</td>
	</tr>
</form>
</table>		
<!--★★検索結果一覧★★-->		

<!--{/if}-->
