<?php /* Smarty version 2.6.13, created on 2007-01-10 00:00:07
         compiled from complete.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'complete.tpl', 7, false),)), $this); ?>
<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input type="hidden" name="mode" value="<?php echo $this->_tpl_vars['tpl_mode']; ?>
">

<?php $_from = $this->_tpl_vars['arrHidden']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
<input type="hidden" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['item'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<?php endforeach; endif; unset($_from); ?>

<tr><td height="80"></td></tr>
<tr>
	<td align="center" class="fs12">
		<strong>EC CUBE インストールが完了しました。</strong><br>
		<br>
		<a href="<?php echo $this->_tpl_vars['tpl_sslurl']; ?>
admin/">管理画面</a>にログインできます。
	</td>
</tr>
<tr>
	<td align="center" class="fs10">
		先ほど登録したID、パスワードを用いてログインしてください。
	</td>
</tr>
<tr><td height="80"></td></tr>

</table>