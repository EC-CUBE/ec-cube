<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
 <!--▼CONTENTS-->
<table width=760 border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="20"></td></tr>
			<tr>
				<td align="center" bgcolor="#cccccc">
				<table width="690" border="0" cellspacing="0" cellpadding="3" summary=" ">
					<tr><td height="5"></td></tr>
					<tr>
						<td align="center" height="250" bgcolor="#ffffff" class="fs12"><!--★エラーメッセージ--><!--{$tpl_error}--></td>
					</tr>
					<tr><td height="5"></td></tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr align="center">
				<td>
					<div id="button">
						<!--{if $return_top}-->
							<a href="<!--{$smarty.const.URL_DIR}-->index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_toppage.gif','b_toppage');"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_toppage.gif" width="150" height="30" alt="トップページへ" border="0" name="b_toppage"></a>
						<!--{else}-->
							<a href="javascript:history.back()" onmouseOver="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_back_on.gif','b_back');" onmouseOut="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif','b_back');"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="戻る" name="b_back" id="b_back" /></a>
						<!--{/if}-->
					</div>
				</td>
			</tr>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->
