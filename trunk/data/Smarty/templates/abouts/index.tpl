<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--▼CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td align="right" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/abouts/title.jpg" width="580" height="40" alt="当サイトについて"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td bgcolor="#cccccc" align="center">
				<table width="" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<!--{if $arrSiteInfo.shop_name != ""}-->
					<tr>
						<td width="135" bgcolor="#f0f0f0" class="fs12">店名</td>
						<td width="402" bgcolor="#ffffff" class="fs12"><!--{$arrSiteInfo.shop_name|escape}--></td>
					</tr>
					<!--{/if}-->
					<!--{if $arrSiteInfo.company_name != ""}-->
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">会社名</td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrSiteInfo.company_name|escape}--></td>
					</tr>
					<!--{/if}-->
					<!--{if $arrSiteInfo.zip01 != ""}-->
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">住所</td>
						<td bgcolor="#ffffff" class="fs12">〒<!--{$arrSiteInfo.zip01|escape}-->-<!--{$arrSiteInfo.zip02|escape}--><br><!--{$arrSiteInfo.pref|escape}--><!--{$arrSiteInfo.addr01|escape}--><!--{$arrSiteInfo.addr02|escape}--></td>
					</tr>
					<!--{/if}-->
					<!--{if $arrSiteInfo.tel01 != ""}-->
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">電話番号</td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrSiteInfo.tel01|escape}-->-<!--{$arrSiteInfo.tel02|escape}-->-<!--{$arrSiteInfo.tel03|escape}--></td>
					</tr>
					<!--{/if}-->
					<!--{if $arrSiteInfo.fax01 != ""}-->
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">FAX番号</td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrSiteInfo.fax01|escape}-->-<!--{$arrSiteInfo.fax02|escape}-->-<!--{$arrSiteInfo.fax03|escape}--></td>
					</tr>
					<!--{/if}-->
					<!--{if $arrSiteInfo.business_hour != ""}-->
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">営業時間</td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrSiteInfo.business_hour|escape}--></td>
					</tr>
					<!--{/if}-->
					<!--{if $arrSiteInfo.email02 != ""}-->
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">メールアドレス</td>
						<td bgcolor="#ffffff" class="fs12"><a href="mailto:<!--{$arrSiteInfo.email02|escape}-->"><!--{$arrSiteInfo.email02|escape}--></a></td>
					</tr>
					<!--{/if}-->
					<!--{if $arrSiteInfo.good_traded != ""}-->
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">取扱商品</td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrSiteInfo.good_traded|escape|nl2br}--></td>
					</tr>
					<!--{/if}-->
					<!--{if $arrSiteInfo.message != ""}-->
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">メッセージ</td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrSiteInfo.message|escape|nl2br}--></td>
					</tr>
					<!--{/if}-->
				</table>
				</td>
			</tr>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->

