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
		<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/contact/title.jpg" width="580" height="40" alt="お問い合わせ"></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td align="center">
				<table width="520" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td align="center" bgcolor="#cccccc">
						<table width="510" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="5"></td></tr>
							<tr>
								<td align="center" bgcolor="#ffffff">
								<!--お問い合わせ完了の文章ここから-->
								<table width="470" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="25"></td></tr>
									<tr>
										<td class="fs14"><span class="redst">お問い合わせ内容の送信が完了いたしました。</span></td>
									</tr>
									<tr><td height="20"></td></tr>
									<tr>
										<td class="fs12">万一、ご回答メールが届かない場合は、トラブルの可能性もありますので大変お手数ではございますがもう一度お問い合わせいただくか、お電話にてお問い合わせください。</td>
									</tr>
									<tr><td height="15"></td></tr>
									<tr>
										<td class="fs12">今後ともご愛顧賜りますようよろしくお願い申し上げます。</td>
									</tr>
									<tr><td height="20"></td></tr>
									<tr>
										<td class="fs12">
											<!--{$arrSiteInfo.company_name|escape}--><br>
											TEL：<!--{$arrSiteInfo.tel01}-->-<!--{$arrSiteInfo.tel02}-->-<!--{$arrSiteInfo.tel03}--> <!--{if $arrSiteInfo.business_hour != ""}-->（受付時間/<!--{$arrSiteInfo.business_hour}-->）<!--{/if}--><br>
											E-mail：<a href="mailto:<!--{$arrSiteInfo.email02|escape}-->"><!--{$arrSiteInfo.email02|escape}--></a>
										</td>
									</tr>
									<tr><td height="20"></td></tr>
									<tr align="center">
										<td>
											<!--{if $is_campaign}-->
											<a href="<!--{$smarty.const.CAMPAIGN_URL}--><!--{$campaign_dir}-->/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_toppage.gif','b_toppage');"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_toppage.gif" width="150" height="30" alt="トップページへ" border="0" name="b_toppage"></a>
											<!--{else}-->
											<a href="<!--{$smarty.const.URL_DIR}-->index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_toppage.gif','b_toppage');"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_toppage.gif" width="150" height="30" alt="トップページへ" border="0" name="b_toppage"></a>
											<!--{/if}-->
										</td>
									</tr>
									<tr><td height="25"></td></tr>
								</table>
								<!--お問い合わせ完了の文章ここまで-->
								</td>
							</tr>
							<tr><td height="5"></td></tr>
						</table>
						</td>
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





