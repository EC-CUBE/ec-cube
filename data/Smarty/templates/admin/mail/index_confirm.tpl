
<!--▼CONTENTS-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#ffffff" align="center" valign="top" height="400">
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<!--▼SUB NAVI-->
				<td class="fs12n"><!--{include file=$tpl_subnavi}--></td>
				<!--▲SUB NAVI-->
			</tr><tr><td height="25"></td></tr>
		</table>

		<!--▼MAIN CONTENTS-->
				
		
		<!--▼絞り込み設定ここから-->
		<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
		<!--{foreach key=key item=val from=$arrHidden}-->	
			<input type="hidden" name="<!--{$key}-->" value="<!--{$val|escape}-->">
		<!--{/foreach}-->
		
		<!--{if ! $all_flag}-->
		<br />
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td class="fs14n"><strong>■配信条件</strong></td>
			</tr>
			<tr><td height="10"></td></tr>
		</table>
		<br />		
		<!--▼検索テーブルここから-->
		<table width="740" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">顧客名</td>
				<td bgcolor="#ffffff" width="248"><!--{$list_data.name|escape|default:"(未設定)"}-->	</td>
				<td bgcolor="#f0f0f0" width="110">顧客名（カナ）</td>
				<td bgcolor="#ffffff" width="249"><!--{$list_data.kana|escape|default:"(未設定)"}-->	</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">都道府県</td>
				<td bgcolor="#ffffff" width="248"><!--{$arrPref[$list_data.pref]|escape|default:"(未設定)"}--></td>
				<td bgcolor="#f0f0f0" width="110">TEL</td>
				<td bgcolor="#ffffff" width="249"><!--{$list_data.tel|escape|default:"(未設定)"}--></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">性別</td>
				<td bgcolor="#ffffff" width="248">
					<!--{if $list_data.sex}-->
						<!--{foreach item=sub from=$list_data.sex}-->
							<!--{$arrSex[$sub]}-->
						<!--{/foreach}-->
					<!--{else}-->
						(未設定)
					<!--{/if}-->
				</td>
				<td bgcolor="#f0f0f0" width="110">会員・非会員</td>
				<td bgcolor="#ffffff" width="249"><!--{if $list_data.customer eq "1"}-->会員<!--{elseif $list_data.customer eq "2"}-->非会員<!--{/if}--></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">メールアドレス</td>
				<td bgcolor="#ffffff" width="607" colspan="3"><!--{$list_data.email|escape|default:"(未設定)"}--></td>
			</tr>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">誕生月</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
					<!--{$list_data.birth_month|escape|default:"(未設定)"}--><!--{if $list_data.birth_month}-->月<!--{/if}-->
				</td>
			</tr>	
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">生年月日</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
					<!--{if $list_data.b_start_year && $list_data.b_start_month && $list_data.b_start_day}-->
						<!--{$list_data.b_start_year}-->年<!--{$list_data.b_start_month}-->月<!--{$list_data.b_start_day}-->日
					<!--{else}-->
						（未設定）
					<!--{/if}-->
					　-　
					<!--{if $list_data.b_end_year && $list_data.b_end_month && $list_data.b_end_day}-->
						<!--{$list_data.b_end_year}-->年<!--{$list_data.b_end_month}-->月<!--{$list_data.b_end_day}-->日
					<!--{else}-->
						（未設定）
					<!--{/if}-->
				</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">職業</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
					<!--{if $list_data.job}-->
						<!--{foreach item=sub from=$list_data.job}-->
							<!--{$arrJob[$sub]|escape}-->
						<!--{/foreach}-->
					<!--{else}-->
						(未設定)
					<!--{/if}-->
				</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">登録・更新日</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
					<!--{if $list_data.start_year && $list_data.start_month && $list_data.start_day}-->
						<!--{$list_data.start_year}-->年<!--{$list_data.start_month}-->月<!--{$list_data.start_day}-->日
					<!--{else}-->
						（未設定）
					<!--{/if}-->
					　-　
					<!--{if $list_data.end_year && $list_data.end_month && $list_data.end_day}-->
						<!--{$list_data.end_year}-->年<!--{$list_data.end_month}-->月<!--{$list_data.end_day}-->日
					<!--{else}-->
						（未設定）
					<!--{/if}-->
				</td>
			</tr>			
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">メール配信形式<span class="red"> *</span></td>
				<td bgcolor="#ffffff" width="248">
					<!--{$arrHtmlmail[$list_data.htmlmail]|default:"両方"}-->
				<td bgcolor="#f0f0f0" width="110">購入回数</td>
				<td bgcolor="#ffffff" width="249">
					<!--{if $list_data.buy_times_from}--><!--{$list_data.buy_times_from|escape}-->回<!--{else}-->（未設定）<!--{/if}-->　-　
					<!--{if $list_data.buy_times_to}--><!--{$list_data.buy_times_to|escape}-->回<!--{else}-->（未設定）<!--{/if}-->
				</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">最終購入日</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
					<!--{if $list_data.buy_start_year && $list_data.buy_start_month && $list_data.buy_start_day}-->
						<!--{$list_data.buy_start_year}-->年<!--{$list_data.buy_start_month}-->月<!--{$list_data.buy_start_day}-->日
					<!--{else}-->
						（未設定）
					<!--{/if}-->
					　-　
					<!--{if $list_data.buy_end_year && $list_data.buy_end_month && $list_data.buy_end_day}-->
						<!--{$list_data.buy_end_year}-->年<!--{$list_data.buy_end_month}-->月<!--{$list_data.buy_end_day}-->日
					<!--{else}-->
						（未設定）
					<!--{/if}-->
				</td>
			</tr>
		</table>
		<!--▲検索テーブルここまで-->
		<!--{/if}-->
		
		<br />
		<br />
		<span class="fs12">
		<!--{if $dataCnt > 0}-->
			<!--{$dataCnt}-->件が該当しました。
		<!--{else}-->
			該当するデータはありません。
		<!--{/if}-->
		</span>
		<br />
		<br />
		<input type="hidden" name="mode" value="input">
		<input type="submit" name="back01" value="戻る" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'back', '' )"/>
		　<!--{if $search_data}--><input type="submit" name="subm" value="次へ" /><!--{/if}-->
		</form>		
		<!--▲MAIN CONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->