<!--{*
 * Copyright (c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$TPL_DIR}-->img/mypage/title.jpg" width="700" height="40" alt="MYページ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr valign="top">
				<td>
				<!--▼NAVI-->
					<!--{include file = $tpl_navi}-->
				<!--▲NAVI-->
				</td>
				<td align="right">
				<table width="515" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td><!--★タイトル--><img src="<!--{$TPL_DIR}-->img/mypage/subtitle04.gif" width="515" height="32" alt="退会手続き"></td>
					</tr>
					<tr><td height="15"></td></tr>
					<tr>
						<td align="center" bgcolor="#cccccc">
						<table width="505" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="5"></td></tr>
							<tr>
								<td align="center" bgcolor="#ffffff">
								<!--表示ここから-->
								<table width="465" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="30"></td></tr>
									<tr>
										<td><span class="fs14"><span class="redst">退会手続きが完了いたしました。</span></span><br>
										<span class="fs12">MYページをご利用いただき誠にありがとうございました。<br>
										またのご利用を心よりお待ち申し上げます。</span></td>
									</tr>
									<tr><td height="15"></td></tr>
									<tr>
										<td align="center" bgcolor="#f0f0f0">
										<table width="445" border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr><td height="10"></td></tr>
											<tr>
												<td class="fs12"><!--{$arrSiteInfo.company_name|escape}--><br>
												TEL：<!--{$arrSiteInfo.tel01}-->-<!--{$arrSiteInfo.tel02}-->-<!--{$arrSiteInfo.tel03}--> <!--{if $arrSiteInfo.business_hour != ""}-->（受付時間/<!--{$arrSiteInfo.business_hour}-->）<!--{/if}--><br>
												E-mail：<a href="mailto:<!--{$arrSiteInfo.email02|escape}-->"><!--{$arrSiteInfo.email02|escape}--></a></td>
											</tr>
											<tr><td height="10"></td></tr>
										</table>
										</td>
									</tr>
									<tr><td height="30"></td></tr>
								</table>
								<!--表示ここまで-->
								</td>
							</tr>
							<tr><td height="5"></td></tr>
						</table>
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td align="center"><a href="<!--{$smarty.const.URL_DIR}-->index.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/b_toppage_on.gif','toppage');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/b_toppage.gif','toppage');"><img src="<!--{$TPL_DIR}-->img/common/b_toppage.gif" width="150" height="30" alt="トップページへ" border="0" name="toppage"></a></td>
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
