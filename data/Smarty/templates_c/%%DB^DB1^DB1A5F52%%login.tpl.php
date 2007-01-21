<?php /* Smarty version 2.6.13, created on 2007-01-10 13:54:21
         compiled from /home/web/beta.ec-cube.net/html/user_data/include/bloc/login.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/login.tpl', 15, false),array('modifier', 'number_format', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/login.tpl', 31, false),array('modifier', 'default', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/login.tpl', 31, false),array('modifier', 'sfTrimURL', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/login.tpl', 50, false),array('modifier', 'sfGetChecked', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/login.tpl', 54, false),)), $this); ?>
<!--▼ログインここから-->
<?php if ($_POST['url'] == ""): ?>
	<?php $this->assign('url', ($_SERVER['REQUEST_URI'])); ?>
<?php else: ?>
	<?php $this->assign('url', ($_POST['url'])); ?>
<?php endif; ?>
<table width="166" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="login_form" id="login_form" method="post" action="<?php echo @URL_DIR; ?>
frontparts/login_check.php" onsubmit="return fnCheckLogin('login_form')">
<input type="hidden" name="mode" value="login">
<input type="hidden" name="url" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['url'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
	<tr>
		<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/side/title_login.jpg" width="166" height="35" alt="ログイン"></td>
	</tr>
	<tr>
		<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="10" alt=""></td>
		<td align="center" bgcolor="#ffffff">
		<!--ログインフォーム-->
		<table width="146" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td height="10"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="50" height="1" alt=""></td>
				<td><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="96" height="1" alt=""></td>
			</tr>
			<?php if ($this->_tpl_vars['tpl_login']): ?>
				<tr>
					<td align="center" colspan="3" class="fs12">ようこそ <br> <?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_name1'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
　<?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_name2'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 様<br />
					所持ポイント：<span class="redst"> <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['tpl_user_point'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
 pt</span></td>
				</tr>
				<?php if (! $this->_tpl_vars['tpl_disable_logout']): ?>
				<tr>
					<td colspan="3" align="center"><a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="fnFormModeSubmit('login_form', 'logout', '', ''); return false;"><img src="<?php echo @URL_DIR; ?>
img/header/logout.gif" width="44" height="21" alt="ログアウト" /></a></td>
				</tr>
				<?php endif; ?>
			<?php else: ?>
				<tr>
					<td><img src="<?php echo @URL_DIR; ?>
img/side/icon_mail.gif" width="40" height="21" alt="メールアドレス"></td>
					<td><input type="text" name="login_email" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_login_email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="10" class="box10" /></td>
				</tr>
				<tr><td height="5"></td></tr>
				<tr>
					<td><img src="<?php echo @URL_DIR; ?>
img/side/icon_pw.gif" width="40" height="22" alt="パスワード"></td>
					<td><input type="password" name="login_pass" size="12" class="box12" /></td>
				</tr>
				<tr><td height="5"></td></tr>
				<tr>
					<td colspan="2" class="fs10n" align="right"><a href="<?php echo ((is_array($_tmp=@SSL_URL)) ? $this->_run_mod_handler('sfTrimURL', true, $_tmp) : sfTrimURL($_tmp)); ?>
/forgot/index.php" onclick="win01('<?php echo ((is_array($_tmp=@SSL_URL)) ? $this->_run_mod_handler('sfTrimURL', true, $_tmp) : sfTrimURL($_tmp)); ?>
/forgot/index.php','forget','600','400'); return false;" target="_blank">パスワードを忘れた方はこちら</a></td>
				</tr>
				<tr><td height="10"></td></tr>
				<tr>
					<td width="50"><input type="checkbox" name="login_memory" value="1" <?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_login_memory'])) ? $this->_run_mod_handler('sfGetChecked', true, $_tmp, 1) : sfGetChecked($_tmp, 1)); ?>
/><img src="<?php echo @URL_DIR; ?>
img/header/memory.gif" width="18" height="9" alt="記憶" /></td>
					<td align="center"><input type="image" onMouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/side/button_login_on.gif',this)" onMouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/side/button_login.gif',this)" src="<?php echo @URL_DIR; ?>
img/side/button_login.gif" width="51" height="22" alt="ログイン" border="0" name="subm"></td>
				</tr>
			<?php endif; ?>
		</table>
		<!--ログインフォーム-->
		</td>
		<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="10" alt=""></td>
	</tr>
	<tr>
		<td colspan="3" height="14"><img src="<?php echo @URL_DIR; ?>
img/side/flame_bottom03.gif" width="166" height="15" alt=""></td>
	</tr>
	<tr><td height="10"></td></tr>
</form>
</table>
<!--▲ログインここまで-->