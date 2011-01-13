<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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


<!--{* ▼CONTENTS *}-->
	<div id="login-form-wrap">


		<div id="login-form">
	<form name="form1" id="form1" method="post" action="login.php">
			<h1><img src="<!--{$TPL_DIR}-->img/contents/admin_login_logo.jpg" width="140" height="150" alt="EC-CUBE管理画面" /></h1>
			<div id="input-form">
				<p><label for="login_id">ID</label></p>
				<input type="text" name="login_id" size="20" class="box25" />
				<p><label for="password">PASSWORD</label></p>
				<input type="password" name="password" size="20" class="box25" />
				<p><a class="btn-tool-format" href="javascript:;" onclick="document.form1.submit();"><span>LOGIN</span></a></p>
			</div>
	</form>
		</div>

	</div>
	<div id="copyright">Copyright &copy; 2000-2010 LOCKON CO.,LTD. All Rights Reserved.</div>

<!--{* エラーメッセージここから *}-->
<!--{if $tpl_error}-->
<div id="loginInputError" style="display:none;">
  <!--{$tpl_error}-->
</div>
<script type="text/javascript">
<!--
$(funtion(){
  tb_show('ERROR!','#TB_inline?height=200&width=400&inlineId=loginInputError','false');
});
// -->
</script>
<!--{/if}-->
<!--{* エラーメッセージここまで *}-->
<!--{* ▲CONTENTS *}-->

<script type="text/javascript">
<!--
document.form1.login_id.focus();
$(function() {
  $('#form1').append('<input type="submit" style="position: absolute; top: -1000px" />');
});
// -->
</script>
