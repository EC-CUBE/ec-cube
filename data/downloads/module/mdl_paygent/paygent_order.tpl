<!--{if $arrDisp.memo01 == $smarty.const.MDL_PAYGENT_ID && $arrDisp.memo08 == $smarty.const.PAYGENT_CREDIT}-->
<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
	<input type="hidden" name="paygent_type" value="">
	<tr class="fs12n">
		<td bgcolor="#f2f1ec" width="717" colspan="4">▼ペイジェント</td>
	</tr>
	<tr class="fs12n">
		<td bgcolor="#f2f1ec" width="110">カードステータス</td>
		<!--{if $paygent_return != ""}-->
			<!--{if $paygent_return.return == true}-->
			<!--{assign var=message value=`$arrDispKind[$paygent_return.kind]`に成功しました。}-->
			<!--{else}-->			
			<!--{assign var=message value=`$arrDispKind[$paygent_return.kind]`に失敗しました。}-->
			<!--{/if}-->		
		<!--{else}-->
		<!--{assign var=message value=`$arrDispKind[$arrDisp.memo09]`}-->
		<!--{/if}-->
		
		<td bgcolor="#ffffff"><!--{$message|default:"(未処理)"}--></td>
	</tr>
	<tr class="fs12n">
		<td bgcolor="#f2f1ec" width="110">カード電文送信</td>
		<td bgcolor="#ffffff">
		<!--{*
		<input type="button" onclick="fnModeSubmit('paygent_order','paygent_type','auth_cancel'); return false;" value="オーソリキャンセル">
		*}-->
		<!--{* 未処理の時のみ有効 *}-->		
		<!--{if $arrDisp.memo09 == "" && $paygent_return.return == ""}-->
		<input type="button" onclick="fnModeSubmit('paygent_order','paygent_type','card_commit'); return false;" value="売上">
		<!--{else}-->
		<input type="button" value="売上" disabled="true">
		<!--{/if}-->
		<!--{* 売上状態の時のみ有効 *}-->
		<!--{if $arrDisp.memo09 == $smarty.const.PAYGENT_CARD_COMMIT && $paygent_return.return == "" ||
				($paygent_return.kind == $smarty.const.PAYGENT_CARD_COMMIT &&  $paygent_return.return == true) }-->
		<input type="button" onclick="fnModeSubmit('paygent_order','paygent_type','card_commit_cancel'); return false;" value="売上キャンセル">
		<!--{else}-->
		<input type="button" value="売上キャンセル" disabled="true">			
		<!--{/if}-->
		</td>
	</tr>
</table>

<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
</table>
<!--{/if}-->