<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
*}-->
<!--▼CONTENTS-->
<table width="556" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="login.php">
	<tr><td height="182"></td></tr>
	<tr>
		<td colspan="3"><img src="<!--{$TPL_DIR}-->img/login/top.jpg" width="556" height="37" alt=""></td>
	</tr>
	<tr>
		<td><img src="<!--{$TPL_DIR}-->img/login/logo.jpg" width="230" height="172" alt="EC CUBE"></td>
		<td background="<!--{$TPL_DIR}-->img/login/bg02.jpg">
		<table width="280" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td width="110"><img src="<!--{$TPL_DIR}-->img/login/id.jpg" width="22" height="5" alt="ID"></td>
				<td width="195"><input type="text" name="login_id" size="20" class="box25" /></td>
			</tr>
			<tr><td height="7"></td></tr>
			<tr>
				<td><img src="<!--{$TPL_DIR}-->img/login/pass.jpg" width="61" height="5" alt="PASSWORD"></td>
				<td ><input type="password" name="password" size="20" class="box25"/></td>
			</tr>
			<tr><td height="10"></td></tr>
			<!--エラーメッセージここから-->
			<tr>
				<td></td>
				<td class="white10"><!--{$tpl_error}--></td>
			</tr>
			<!--エラーメッセージここまで-->
			<tr><td height="15"></td></tr>
			<tr>
				<td colspan="2" align="center"><input type="image" onMouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/login/button_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/login/button.jpg',this)" src="<!--{$TPL_DIR}-->img/login/button.jpg" width="77" height="23" alt="LOGIN" border="0" name="subm"></td>
			</tr>
		</table>
		</td>
		<td background="<!--{$TPL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$TPL_DIR}-->img/login/right.jpg" width="46" height="172" alt=""></td>
	</tr>
	<tr>
		<td colspan="3"><img src="<!--{$TPL_DIR}-->img/login/bottom.jpg" width="556" height="42" alt=""></td>
	</tr>
	<tr><td height="2"></td></tr>
	<tr>
		<td colspan="3" class="fs10n">&nbsp;Copyright &copy; 2000-2007 LOCKON CO.,LTD. All Rights Reserved.</td>
	</tr>
</form>
</table>

<!--▲CONTENTS-->

<SCRIPT Language="JavaScript">
<!--
document.form1.login_id.focus();
// -->
</SCRIPT>
