<!--▼CONTENTS-->
<table width="100%" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc" width="1"><img src="/img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff" width="9"><img src="/img/_.gif" width="9" height="1" alt="" /></td>

		<td bgcolor="#ffffff" align="left"> 
		<!--▼MAIN CONTENTS-->
		<!--パンクズ-->
		<div id="pan"><span class="fs12n"><a href="/index.php">トップページ</a>＞<span class="redst">マイページトップ</span></span></div>
		<!--パンクズ-->
		<table cellspacing="0" cellpadding="0" summary=" " id="container">
			<tr><td height="10"></td></tr>
			<tr valign="top">

				<!--▼LEFT CONTENTS-->
				<td id="left">
				<!--▼バナー-->
					<!--{include file=$tpl_banner}-->
				<!--▲バナー-->
				
				<!--▼商品検索-->
					<!--{include_php file=$tpl_search_products_php}-->
				<!--▲商品検索-->
				
				<!--▼左ナビ-->
					<!--{include file=$tpl_leftnavi}-->
				<!--▲左ナビ-->
				<!--▼左ナビ-->
					<!--{include file=$tpl_leftnavi}-->
				<!--▲左ナビ-->
				</td>
				<!--▲LEFT CONTENTS-->

				<!--▼RIGHT CONTENTS-->
				<td id="right">
				<!--タイトル-->
				<div id="maintitle"><img src="/img/mypage/title.jpg" width="570" height="40" alt="マイページ" /></div>
				<!--タイトル-->
				<!--▼MYページナビ-->
				<!--{include file=$tpl_navi}-->
				<!--▲MYページナビ-->
				<div id="pt-frame">
				<div id="in"><!--{$CustomerName1|escape}-->　<!--{$CustomerName2|escape}-->様の現在の所持ポイントは</div>
				<div id="gray"><span class="red"><!--{$CustomerPoint|escape|default:"0"}-->pt</span>です。</div>
				</div>
				<div id="subtitle"><img src="/img/mypage/subtitle01.gif" width="150" height="17" alt="今までの購入履歴" /></div>
				<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
				<input type="hidden" name="order_id" value="" >
				<input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->">
				
				<!--{if $tpl_linemax > 0}-->
					<table width="550" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="5"></td></tr>
					<tr class="fs12"><td align="center"><!--{$tpl_linemax}-->件の購入履歴があります。</td></tr>
					<tr><td height="5"></td></tr>
					<tr class="fs12">
					<td align="center">
					<!--▼ページナビ-->
					<!--{$tpl_strnavi}-->
					<!--▲ページナビ-->
					</td>
					</tr>
					<tr><td height="5"></td></tr>
					<tr><td height="5"></td></tr>
					</table>
					
					<table cellspacing="1" cellpadding="10" summary=" " id="frame">
					
						<tr class="fs12n">
							<td class="left01b">購入日時</td>
							<td class="left02b">注文番号</td>
							<td class="left03b">お支払い方法</td>
							<td class="left04b">合計金額</td>
						</tr>
						<!--{section name=cnt loop=$arrOrder}-->
						<tr class="fs12n">
							<td class="left01w"><!--{$arrOrder[cnt].create_date|sfDispDBDate}--></td>
							<td class="left02w"><a href="#" onclick="fnChangeAction('./history.php'); fnKeySubmit('order_id','<!--{$arrOrder[cnt].order_id}-->');"><!--{$arrOrder[cnt].order_id}--></a></td>
							<!--{assign var=payment_id value="`$arrOrder[cnt].payment_id`"}-->
							<td class="left03w"><!--{$arrPayment[$payment_id]|escape}--></td>
							<td class="left04w"><!--{$arrOrder[cnt].payment_total|number_format}-->円</td>
						</tr>
						<!--{/section}-->
					</table>
					<table>
					<tr><td height="5"></td></tr>
					<tr class="fs12">
					<td align="center">
					<!--▼ページナビ-->
					<!--{$tpl_strnavi}-->
					<!--▲ページナビ-->
					</td>
					</tr>
					<tr><td height="10"></td></tr>
					</table>
					</form>
					</td>
				<!--{else}-->
					<table border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="5"></td></tr>
					<tr class="fs12"><td align="center">購入履歴はありません。</td></tr>
					</table>
				<!--{/if}-->
				<!--▲RIGHT CONTENTS-->

				<!--▼LEFT CONTENTS-->
				<td id="left">
				<!--▼左ナビ-->
					<!--{include file=$tpl_leftnavi}-->
				<!--▲左ナビ-->
				</td>
				<!--▲LEFT CONTENTS-->
			</tr>
		</table>
		
		<!--▲MAIN CONTENTS-->

		</td>
		<td bgcolor="#ffffff" width="10"><img src="/img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#cccccc" width="1"><img src="/img/_.gif" width="1" height="10" alt="" /></td>
	</tr>
</table>
<!--▲CONTENTS-->