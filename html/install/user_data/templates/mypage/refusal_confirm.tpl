<!--{*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="complete">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/title.jpg" width="700" height="40" alt="MYページ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr valign="top">
				<td>
				<!--▼NAVI-->
					<!--{include file=$tpl_navi}-->
				<!--▲NAVI-->
				</td>
				<td align="right">
				<table width="515" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td><!--★タイトル--><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/subtitle04.gif" width="515" height="32" alt="退会手続き"></td>
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
									<tr><td height="55"></td></tr>
									<tr>
										<td align="center" class="fs12n">退会手続きを実行してもよろしいでしょうか？</td>
									</tr>
									<tr><td height="55"></td></tr>
									<tr>
										<td align="center">
											<a href="./refusal.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/mypage/b_no_on.gif','refusal_no');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/mypage/b_no.gif','refusal_no');"><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/b_no.gif" width="180" height="30" alt="いいえ、退会しません" name="refusal_no" id="refusal_no" /></a>　
											<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/mypage/b_yes_on.gif',this);" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/mypage/b_yes.gif',this);" src="<!--{$smarty.const.URL_DIR}-->img/mypage/b_yes.gif" width="180" height="30" alt="はい、はい、退会します" name="refusal_yes" id="refusal_yes" />
										</td>
									</tr>
									<tr><td height="10"></td></tr>
									<tr>
										<td class="fs10"><span class="red">※退会手続きが完了した時点で、現在保存されている購入履歴や、お届け先等の情報はすべてなくなりますのでご注意ください。</span></td>
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


