<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--▼CONTENTS-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#ffffff" align="center" valign="top" height="200">
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<!--▼SUB NAVI-->
				<td class="fs12n"></td>
				<!--▲SUB NAVI-->
			</tr><tr><td height="25"></td></tr>
		</table>
		
		<!--▼MAIN CONTENTS-->
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td class="fs14n"><strong>■CSVアップロード</strong></td>
			</tr>
			<tr><td height="10"></td></tr>
		</table>
		
		<!--▼登録テーブルここから-->
		<form name="form1" id="form1" method="post" action="" enctype="multipart/form-data">
		<input type="hidden" name="mode" value="csv_upload">
		<table width="740" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">CSVファイル</td>
				<td bgcolor="#ffffff" width="607">
				<span class="red12"><!--{$arrErr.csv_file}--></span>
				<input type="file" name="csv_file" size="60" class="box60" /><span class="red10"> (1行目タイトル行)</span></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">登録情報</td>
				<td bgcolor="#ffffff" width="607">
				<!--{foreach name=title key=key item=item from=$arrTitle}-->
				<!--{$smarty.foreach.title.iteration}-->項目：<!--{$item}--><br>
				<!--{/foreach}-->
				</td>
			</tr>
		</table>
		<!--▲登録テーブルここまで-->
		
		<br />
		<input type="submit" name="subm" value="この内容で登録する" />
				
		<!--▲MAIN CONTENTS-->
		<table width="740" border="0" cellspacing="0" cellpadding="5" summary=" ">
			</tr><tr><td height="25"></td></tr>
		</table>
		
		<!--{if $tpl_errtitle != ""}-->
		<table width="740" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
		<tr><td bgcolor="#ffffff">
			<span class="red12"><!--{$tpl_errtitle}--><br><br></span>
			<!--{foreach key=key item=item from=$arrCSVErr}-->
			<span class="red12"><!--{$item}-->
			<!--{if $key != 'blank'}-->
			[値：<!--{$arrParam[$key]}-->]
			<!--{/if}-->
			<br></span>
			<!--{/foreach}-->
			<pre><!--{$tpl_debug}--></pre>
		</td></tr>
		</table>
		<!--{/if}-->
		
		<!--{if $tpl_oktitle != ""}-->
		<table width="740" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
		<tr class="fs12n"><td bgcolor="#ffffff">
		<!--{$tpl_oktitle}-->
		</td></tr>
		</table>
		<!--{/if}-->
			
		</form>
</table>

<!--▲CONTENTS-->