<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td align="right" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$TPL_DIR}-->img/aboutshopping/transaction_title.jpg" width="580" height="40" alt="特定商取引に関する法律に基づく表記"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td bgcolor="#cccccc" align="center">
				<table width="" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="135" bgcolor="#f0f0f0" class="fs12">販売業者</td>
						<td width="402" bgcolor="#ffffff" class="fs12"><!--{$arrRet.law_company|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">運営責任者</td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrRet.law_manager|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">住所</td>
						<td bgcolor="#ffffff" class="fs12">〒<!--{$arrRet.law_zip01|escape}-->-<!--{$arrRet.law_zip02|escape}--><br><!--{$arrPref[$arrRet.law_pref]|escape}--><!--{$arrRet.law_addr01|escape}--><!--{$arrRet.law_addr02|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">電話番号</td>
						<td bgcolor="#ffffff" class="fs12n"><!--{$arrRet.law_tel01|escape}-->-<!--{$arrRet.law_tel02|escape}-->-<!--{$arrRet.law_tel03|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">FAX番号</td>
						<td bgcolor="#ffffff" class="fs12n"><!--{$arrRet.law_fax01|escape}-->-<!--{$arrRet.law_fax02|escape}-->-<!--{$arrRet.law_fax03|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">メールアドレス</td>
						<td bgcolor="#ffffff" class="fs12n"><a href="mailto:<!--{$arrRet.law_email}-->"><!--{$arrRet.law_email}--></a></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">URL</td>
						<td bgcolor="#ffffff" class="fs12n"><a href="<!--{$arrRet.law_url}-->"><!--{$arrRet.law_url}--></a></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">商品以外の必要代金</td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrRet.law_term01|escape|nl2br}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">注文方法</td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrRet.law_term02|escape|nl2br}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">支払方法</td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrRet.law_term03|escape|nl2br}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">支払期限</td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrRet.law_term04|escape|nl2br}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">引渡し時期</td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrRet.law_term05|escape|nl2br}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">返品・交換について</td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrRet.law_term06|escape|nl2br}--></td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->
