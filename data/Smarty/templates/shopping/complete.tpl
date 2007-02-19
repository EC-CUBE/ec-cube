<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--ขงCONTENTS-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--ขงMAIN ONTENTS-->
		<!--นุฦ?ผ?ณคญคฮฮฎค?->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/flow04.gif" width="700" height="36" alt="นุฦ?ผ?ณคญคฮฮฎค?></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<!--นุฦ?ผ?ณคญคฮฮฎค?->
			
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/complete_title.jpg" width="700" height="40" alt="คดรรุธดฐฮป"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		
		<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td align="center" bgcolor="#cccccc">
				<table width="630" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="5"></td></tr>
					<tr>
						<td align="center" bgcolor="#ffffff">
							<!-- ขงคฝคฮยพท๐ผัพ๖ฬüฆ๚หฝผจคนค??๎ฆฯษฝผจ -->
							<!--{if $arrOther.title.value }-->
							<table  width="590" cellspacing="0" cellpadding="0" summary=" ">
								<tr>
									<td>
									<table cellspacing="0" cellpadding="0" summary=" " id="comp">
										<tr><td height="20"></td></tr>
										<tr>
											<td class="fs12">ขฃ<!--{$arrOther.title.name}-->พ๖ฬ?br />
											<!--{foreach key=key item=item from=$arrOther}-->
											<!--{if $key != "title"}--><!--{if $item.name != ""}--><!--{$item.name}-->กง<!--{/if}--><!--{$item.value|nl2br}--><br/><!--{/if}-->
											<!--{/foreach}-->
										</tr>
									</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr><td height="5"></td></tr>
					<tr>
						<td align="center" bgcolor="#ffffff">
							<!--{/if}-->						
							<!-- ขฅฅณฅüงำคหท๐ผัคฮพ?๎ฆหคฯษฝผจ -->
						
							<!--คดรรุธดฐฮปคฮสธพฯคณคณคซค?->
							<table width="590" border="0" cellspacing="0" cellpadding="0" summary=" ">
								<tr><td height="25"></td></tr>
								<tr>
									<td class="fs12"><span class="redst"><!--{$arrInfo.shop_name|escape}-->คฮพฆษสค๚ฆดนุฦ?คคคฟคภคญกขคขค๔ฆฌคศคฆคดคถคคคÞคทคฟกฃ</span></td>
								</tr>
								<tr><td height="20"></td></tr>
								<tr>
									<td class="fs12">คฟคภคคคÞกขคดรรุธคฮณฮวงฅโฃผฅ?๚ฆชม๚ฆ๔ฆตคปคฦคคคฟคภคญคÞคทคฟกฃ <br>
									ห??ขคดณฮวงฅโฃผฅ?ฌฦฯคซคสคคพ?๎ฆฯกขฅศฅ๒งึฅ?ฮฒฤวฝภญคไฆขค๔ฆÞคนคฮควย๎ฬัคชผ?þฆวคฯคดคถคคคÞคนคฌคไฆฆฐ?ูคชฬ่ฆคน๎ฆ?ปคคคฟคภคฏคซกขคชลลฯรคหคฦคชฬ่ฆคน๎ฆ?ปคฏคภคตคคคÞคปกฃ </td>
								</tr>
								<tr><td height="15"></td></tr>
								<tr>
									<td class="fs12">บฃธ๊ฆศคไฆดฐฆธÜป๚ฆ๔ฆÞคนค๐ฆฆค๐ฆพฌทคฏคชด๔ฆคฟฝคทพ๊ฆฒคÞคนกฃ</td>
								</tr>
								<tr><td height="20"></td></tr>
								<tr>
									<td class="fs12"><!--{$arrInfo.shop_name|escape}--><br>
									TELกง<!--{$arrInfo.tel01}-->-<!--{$arrInfo.tel02}-->-<!--{$arrInfo.tel03}--> <!--{if $arrInfo.business_hour != ""}-->กสผ๖หีป?ดึ/<!--{$arrInfo.business_hour}-->กห<!--{/if}--><br>
									E-mailกง<a href="mailto:<!--{$arrInfo.email02|escape}-->"><!--{$arrInfo.email02|escape}--></a></td>
								</tr>
								<tr><td height="25"></td></tr>
							</table>
							<!--คดรรุธดฐฮปคฮสธพฯคณคณคÞคว-->
						</td>
					</tr>
					<tr><td height="5"></td></tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr align="center">
				<td>
					<!--{if $is_campaign}-->
					<a href="<!--{$smarty.const.CAMPAIGN_URL}--><!--{$campaign_dir}-->/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_toppage.gif','b_toppage');"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_toppage.gif" width="150" height="30" alt="ฅศฅรฅืฅฺกผฅธคุ" border="0" name="b_toppage"></a>
					<!--{else}-->
					<a href="<!--{$smarty.const.URL_DIR}-->index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_toppage.gif','b_toppage');"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_toppage.gif" width="150" height="30" alt="ฅศฅรฅืฅฺกผฅธคุ" border="0" name="b_toppage"></a>
					<!--{/if}-->
				</td>
			</tr>
		</table>
		<!--ขฅMAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--ขฅCONTENTS-->
