<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--��������盧������-->
<table width="166" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/side/title_search.jpg" width="166" height="35" alt="�������"></td>
	</tr>
	<tr>
		<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
		<td align="center" bgcolor="#ffffff">
		<!--�����ե�����-->
		<table width="146" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<form name="search_form" id="search_form" method="get" action="<!--{$smarty.const.URL_DIR}-->products/list.php">
		<input type="hidden" name="mode" value="search">
			<tr><td height="10"></td></tr>
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/side/search_cat.gif" width="104" height="10" alt="���ʥ��ƥ��꤫������"></td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td>
					<select name="category_id">
					<option label="���٤Ƥξ���" value="">���Ƥξ���</option>
					<!--{html_options options=$arrCatList selected=$category_id}-->
					</select>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/side/search_name.gif" width="66" height="10" alt="����̾������"></td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td><input type="text" name="name" size="18" class="box18" maxlength="50" value="<!--{$smarty.get.name|escape}-->"/></td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td align="center">
					<input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/side/button_search_on.gif',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/side/button_search.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/side/button_search.gif" width="51" height="22" alt="����" border="0" name="search">
				</td>
			</tr>
		</form>
		</table>
		<!--�����ե�����-->
		</td>
		<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
	</tr>
	<tr>
		<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/side/flame_bottom03.gif" width="166" height="15" alt=""></td>
	</tr>
	<tr><td height="10"></td></tr>
</table>
<!--��������盧���ޤ�-->