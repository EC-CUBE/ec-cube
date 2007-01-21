<?php /* Smarty version 2.6.13, created on 2007-01-10 00:00:00
         compiled from step3.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'step3.tpl', 26, false),)), $this); ?>
<script type="text/javascript">
<!--
	// モードとキーを指定してSUBMITを行う。
	function fnModeSubmit(mode) {
		switch(mode) {
		case 'drop':
			if(!window.confirm('一度削除したデータは、元に戻せません。\n削除しても宜しいですか？')){
				return;
			}
			break;
		default:
			break;
		}
		document.form1['mode'].value = mode;
		document.form1.submit();	
	}
//-->
</script>

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
<tr><td align="left" class="fs12st">■データベースの初期化</td></tr>
<tr><td align="left" class="fs12"><?php if ($this->_tpl_vars['tpl_db_version'] != ""): ?>接続情報：<?php echo $this->_tpl_vars['tpl_db_version'];  endif; ?></td></tr>
<tr><td align="left" class="fs12">データベースの初期化を開始します</td></tr>
<tr><td align="left" class="fs12">※すでにテーブル等が作成されている場合は中断されます</td></tr>
<?php if ($this->_tpl_vars['tpl_mode'] != 'complete'): ?>
<tr><td align="left" class="fs12"><input type="checkbox" id="skip" name="db_skip" <?php if ($this->_tpl_vars['tpl_db_skip'] == 'on'): ?>checked<?php endif; ?>> <label for="skip">データベースの初期化処理を行わない</label></td></tr>
<?php endif; ?>

<?php if (count ( $this->_tpl_vars['arrErr'] ) > 0 || $this->_tpl_vars['tpl_message'] != ""): ?>
<tr>
	<td bgcolor="#cccccc" class="fs12">
	<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
		<tr>
			<td bgcolor="#ffffff" class="fs12" height="50">
			<?php echo $this->_tpl_vars['tpl_message']; ?>
<br>
			<span class="red"><?php echo $this->_tpl_vars['arrErr']['all']; ?>
</span>
			<?php if ($this->_tpl_vars['arrErr']['all'] != ""): ?>
			<input type="button" onclick="fnModeSubmit('drop');" value="既存データをすべて削除する">
			<?php endif; ?>
			</td>
		</tr>
	</table>
	</td>
</tr>
<?php endif; ?>
</table>

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
	<tr><td height="20"></td></tr>
	<tr>
		<td align="center">
		<a href="#" onmouseover="chgImg('../img/install/back_on.jpg','back')" onmouseout="chgImg('../img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_step2';document.form1.submit();return false;" /><img  width="105" src="../img/install/back.jpg"  height="24" alt="前へ戻る" border="0" name="back"></a>
		<input type="image" onMouseover="chgImgImageSubmit('../img/install/next_on.jpg',this)" onMouseout="chgImgImageSubmit('../img/install/next.jpg',this)" src="../img/install/next.jpg" width="105" height="24" alt="次へ進む" border="0" name="next" onClick="document.body.style.cursor = 'wait';">
		</td>
	</tr>
	<tr><td height="30"></td></tr>
</from>
</table>								