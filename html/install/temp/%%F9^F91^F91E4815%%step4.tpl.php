<?php /* Smarty version 2.6.13, created on 2007-01-10 00:00:05
         compiled from step4.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'step4.tpl', 7, false),)), $this); ?>
<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input type="hidden" name="mode" value="<?php echo $this->_tpl_vars['tpl_mode']; ?>
">
<input type="hidden" name="step" value="0">
<input type="hidden" name="db_skip" value=<?php echo $this->_tpl_vars['tpl_db_skip']; ?>
>
<input type="hidden" name="senddata_site_url" value="<?php echo $this->_tpl_vars['tpl_site_url']; ?>
">
<input type="hidden" name="senddata_shop_name" value="<?php echo $this->_tpl_vars['tpl_shop_name']; ?>
">
<input type="hidden" name="senddata_cube_ver" value="<?php echo $this->_tpl_vars['tpl_cube_ver']; ?>
">
<input type="hidden" name="senddata_php_ver" value="<?php echo $this->_tpl_vars['tpl_php_ver']; ?>
">
<input type="hidden" name="senddata_db_ver" value="<?php echo $this->_tpl_vars['tpl_db_ver']; ?>
">
<?php $_from = $this->_tpl_vars['arrHidden']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
<input type="hidden" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['item'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<?php endforeach; endif; unset($_from); ?>

<tr><td height="30"></td></tr>
<tr><td align="left" class="fs12st">■サイト情報について</td></tr>
<tr><td align="left" class="fs12">EC-CUBEのシステム向上及び、デバッグのため以下の情報のご提供をお願いいたします。</td></tr>
<tr>
	<td bgcolor="#cccccc" class="fs12">
	<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
		<tr>
			<td bgcolor="#ffffff" class="fs12" height="50">
				- サイトURL：<?php echo $this->_tpl_vars['tpl_site_url']; ?>
<br/>
				- 店舗名：<?php echo $this->_tpl_vars['tpl_shop_name']; ?>
<br/>
				- EC-CUBEバージョン：<?php echo $this->_tpl_vars['tpl_cube_ver']; ?>
<br/>
				- PHP情報：<?php echo $this->_tpl_vars['tpl_php_ver']; ?>
<br/>
				- DB情報：<?php echo $this->_tpl_vars['tpl_db_ver']; ?>
<br/>
			</td>
		</tr>
	</table>
	</td>
</tr>
<tr><td align="left" class="fs12"><input type="radio" id="ok" name="send_info" checked value=true><label for="ok">はい(推奨)</label>　<input type="radio" id="ng" name="send_info" value=false><label for="ng">いいえ</label></td></tr>
</table>

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
	<tr><td height="20"></td></tr>
	<tr>
		<td align="center">
		<a href="#" onmouseover="chgImg('../img/install/back_on.jpg','back')" onmouseout="chgImg('../img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_step3';document.form1.submit();return false;" /><img  width="105" src="../img/install/back.jpg"  height="24" alt="前へ戻る" border="0" name="back"></a>
		<input type="image" onMouseover="chgImgImageSubmit('../img/install/next_on.jpg',this)" onMouseout="chgImgImageSubmit('../img/install/next.jpg',this)" src="../img/install/next.jpg" width="105" height="24" alt="次へ進む" border="0" name="next">
		</td>
	</tr>
	<tr><td height="30"></td></tr>
</from>
</table>								