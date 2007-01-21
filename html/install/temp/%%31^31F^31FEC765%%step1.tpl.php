<?php /* Smarty version 2.6.13, created on 2007-01-09 23:59:39
         compiled from step1.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'step1.tpl', 7, false),array('modifier', 'sfGetErrorColor', 'step1.tpl', 25, false),)), $this); ?>
<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input type="hidden" name="mode" value="<?php echo $this->_tpl_vars['tpl_mode']; ?>
">
<input type="hidden" name="step" value="0">

<?php $_from = $this->_tpl_vars['arrHidden']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
<input type="hidden" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['item'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<?php endforeach; endif; unset($_from); ?>

<tr><td height="30"></td></tr>
<tr><td align="left" class="fs12st">■ECサイトの設定</td></tr>
<tr>
	<td bgcolor="#cccccc">
	<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">店名<span class="red">※</span></td>
			<td bgcolor="#ffffff" width="332">
			<?php $this->assign('key', 'shop_name'); ?>
			<span class="red"><span class="fs12n"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span></span>
			<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="40" class="box40" />
			<br><span class="fs10">※あなたの店名をご記入ください。</span>
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">管理者：メールアドレス<span class="red">※</span></td>
			<td bgcolor="#ffffff" width="332">
			<?php $this->assign('key', 'admin_mail'); ?>
			<span class="red"><span class="fs12n"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span></span>
			<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="40" class="box40" />
			<br><span class="fs10">※受注メールなどの宛先になります。：(例)example@ec-cube.net</span>
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150"><span class="fs12n">管理者：ログインID<span class="red">※</span></span><br/><span class="fs10">半角英数字・15文字以内</span></td>
			<td bgcolor="#ffffff" width="332">
			<?php $this->assign('key', 'login_id'); ?>
			<span class="red"><span class="fs12n"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span></span>
			<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="40" class="box40" />
			<br><span class="fs10">※管理者画面にログインするためのIDです。</span>
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150"><span class="fs12n">管理者：パスワード<span class="red">※</span></span><br/><span class="fs10">半角英数字・15文字以内</span></td>
			<td bgcolor="#ffffff" width="332">
			<?php $this->assign('key', 'login_pass'); ?>
			<span class="red"><span class="fs12n"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span></span>
			<input type="password" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
;" size="40" class="box40" />
			<br><span class="fs10">※管理者画面にログインするためのパスワードです。</span>
			</td>
		</tr>
	</table>
	</td>
</tr>
<tr><td height="20"></td></tr>
<tr><td align="left" class="fs12st">■WEBサーバの設定</td></tr>
<tr>
	<td bgcolor="#cccccc">
	<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">		
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">HTMLパス<span class="red">※</span></td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<?php $this->assign('key', 'install_dir'); ?>
			<span class="red"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
			<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="40" class="box40" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">URL(通常)<span class="red">※</span></td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<?php $this->assign('key', 'normal_url'); ?>
			<span class="red"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
			<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="40" class="box40" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">URL(セキュア)<span class="red">※</span></td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<?php $this->assign('key', 'secure_url'); ?>
			<span class="red"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
			<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="40" class="box40" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">共通ドメイン</td>
			<td bgcolor="#ffffff" width="332">	
			<?php $this->assign('key', 'domain'); ?>
			<span class="red"><span class="fs12n"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span></span>
			<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="40" class="box40" />
			<br><span class="fs10">※通常URLとセキュアURLでサブドメインが異なる場合に指定します。</span>
			</td>
		</tr>
	</table>
	</td>
</tr>
</table>

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
	<tr><td height="20"></td></tr>
	<tr>
		<td align="center">
		<a href="#" onmouseover="chgImg('../img/install/back_on.jpg','back')" onmouseout="chgImg('../img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_step0';document.form1.submit();return false;" /><img  width="105" src="../img/install/back.jpg"  height="24" alt="前へ戻る" border="0" name="back"></a>
		<input type="image" onMouseover="chgImgImageSubmit('../img/install/next_on.jpg',this)" onMouseout="chgImgImageSubmit('../img/install/next.jpg',this)" src="../img/install/next.jpg" width="105" height="24" alt="次へ進む" border="0" name="next">
		</td>
	</tr>
	<tr><td height="30"></td></tr>
</from>
</table>