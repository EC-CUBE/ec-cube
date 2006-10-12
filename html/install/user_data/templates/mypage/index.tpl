<!--{*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="order_id" value="" >
<input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/title.jpg" width="700" height="40" alt="MYページ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr valign="top">
				<td>
					<!--{if $tpl_navi != ""}-->
						<!--{include file=$tpl_navi}-->
					<!--{else}-->
						<!--{include file=`$smarty.const.ROOT_DIR`data/Smarty/templates/mypage/navi.tpl}-->
					<!--{/if}-->
				</td>
				<td align="right">
				<table width="515" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td><!--★タイトル--><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/subtitle01.gif" width="515" height="32" alt="購入履歴一覧"></td>
					</tr>
					<tr><td height="15"></td></tr>
					
					<!--{if $tpl_linemax > 0}-->
					
					<tr>
						<td class="fs12n"><!--{$tpl_linemax}-->件の購入履歴があります。</td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr>
						<td class="fs12n" align="center">
							<!--▼ページナビ-->
							<!--{$tpl_strnavi}-->
							<!--▲ページナビ-->
						</td>
					</tr>
					<tr>
						<td bgcolor="#cccccc" align="center">
						<!--表示エリアここから-->
						<table width="515" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr align="center" bgcolor="#f0f0f0">
								<td width="140" class="fs12n">購入日時</td>
								<td width="70" class="fs12n">注文番号</td>
								<td width="90" class="fs12n">お支払い方法</td>
								<td width="70" class="fs12n">合計金額</td>
								<td width="39" class="fs12n">詳細</td>
							</tr>
							<!--{section name=cnt loop=$arrOrder}-->
							<tr bgcolor="#ffffff">
								<td class="fs12"><!--{$arrOrder[cnt].create_date|sfDispDBDate}--></td>
								<td align="center" class="fs12"><!--{$arrOrder[cnt].order_id}--></td>
								<!--{assign var=payment_id value="`$arrOrder[cnt].payment_id`"}-->
								<td align="center" class="fs12"><!--{$arrPayment[$payment_id]|escape}--></td>
								<td align="right" class="fs12"><!--{$arrOrder[cnt].payment_total|number_format}-->円</td>
								<td align="center" class="fs12"><a href="#" onclick="fnChangeAction('./history.php'); fnKeySubmit('order_id','<!--{$arrOrder[cnt].order_id}-->');">詳細</a></td>
							</tr>
							<!--{/section}-->
						</table>
						<!--表示エリアここまで-->
						</td>
					</tr>
					<tr>
						<td class="fs12n" align="center">
							<!--▼ページナビ-->
							<!--{$tpl_strnavi}-->
							<!--▲ページナビ-->
						</td>
					</tr>
					<!--{else}-->
					<tr>
						<td class="fs12n" align="center">
					<table border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="5"></td></tr>
					<tr class="fs12"><td align="center">購入履歴はありません。</td></tr>
					</table>
						</td>
					</tr>
					<!--{/if}-->
				</table>
				</td>
			</tr>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</form>
</table>
<!--▲CONTENTS-->

