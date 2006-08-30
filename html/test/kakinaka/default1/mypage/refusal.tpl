<!--▼CONTENTS-->
<table width="100" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="confirm">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="/img/mypage/title.jpg" width="700" height="40" alt="MYページ"></td>
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
						<td><!--★タイトル--><img src="/img/mypage/subtitle04.gif" width="515" height="32" alt="退会手続き"></td>
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
										<td class="fs12">
										会員を退会された場合には、現在保存されている購入履歴や、お届け先などの情報は、すべて削除されますがよろしいでしょうか？</td>
									</tr>
									<tr><td height="30"></td></tr>
									<tr>
										<td align="center">
											<input type="image" onmouseover="chgImgImageSubmit('/img/mypage/b_refuse_on.gif',this);" onmouseout="chgImgImageSubmit('/img/mypage/b_refuse.gif',this);" src="/img/mypage/b_refuse.gif" width="180" height="30" alt="会員退会を行う" name="refusal" id="refusal" />
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

