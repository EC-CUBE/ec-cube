<?php /* Smarty version 2.6.13, created on 2007-01-10 16:32:34
         compiled from /home/web/beta.ec-cube.net/html/user_data/include/campaign/testtesttest/active//contents.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/home/web/beta.ec-cube.net/html/user_data/include/campaign/testtesttest/active//contents.tpl', 59, false),array('modifier', 'sfGetErrorColor', '/home/web/beta.ec-cube.net/html/user_data/include/campaign/testtesttest/active//contents.tpl', 61, false),array('modifier', 'default', '/home/web/beta.ec-cube.net/html/user_data/include/campaign/testtesttest/active//contents.tpl', 84, false),array('function', 'html_options', '/home/web/beta.ec-cube.net/html/user_data/include/campaign/testtesttest/active//contents.tpl', 63, false),)), $this); ?>
<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#cccccc">
		<table width="630" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="5"></td></tr>
			<tr>
				<td align="center" bgcolor="#ffffff">
				<table width="604" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="13"></td></tr>
					<tr>
						<td>キャンペーン開催中</td>
					</tr>
					<tr><td height="20"></td></tr>
				</table>
				
				<table width="530" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td class="fs12">キャンペーン内容</td>
					</tr>
					<tr><td height="10"></td></tr>
				</table>
				</td>
			</tr>
			<tr><td height="5"></td></tr>
		</table>
		</td>
	</tr>
</table>
<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
<tr><td height="20"></td></tr>
</table>
<div id="cart_tag_30050">
<?php $this->assign('id', $this->_tpl_vars['arrProducts'][30050]['product_id']); ?>
<!--▼買い物かご-->
<table width="420" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td height=5></td></tr>
	<tr valign="top" align="right" id="price">
		<td id="right" colspan=2>
			<table cellspacing="0" cellpadding="0" summary=" " id="price">
				<tr>
					<td align="center">
					<table width="285" cellspacing="0" cellpadding="0" summary=" ">
						<?php if ($this->_tpl_vars['tpl_classcat_find1'][$this->_tpl_vars['id']]): ?>
						<?php $this->assign('class1', "classcategory_id".($this->_tpl_vars['id'])."_1"); ?>
						<?php $this->assign('class2', "classcategory_id".($this->_tpl_vars['id'])."_2"); ?>
						<tr><td colspan="2" height="10" align="center" class="fs12"><span class="redst"><?php if ($this->_tpl_vars['arrErr'][$this->_tpl_vars['class1']] != ""): ?>※ <?php echo $this->_tpl_vars['tpl_class_name1'][$this->_tpl_vars['id']]; ?>
を入力して下さい。<?php endif; ?></span></td></tr>
						<tr>
							<td align="right" class="fs12st"><?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_class_name1'][$this->_tpl_vars['id']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
： </td>
							<td>
								<select name="<?php echo $this->_tpl_vars['class1']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['class1']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" onchange="lnSetSelect('<?php echo $this->_tpl_vars['class1']; ?>
', '<?php echo $this->_tpl_vars['class2']; ?>
', '<?php echo $this->_tpl_vars['id']; ?>
','');">
								<option value="">選択してください</option>
								<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrClassCat1'][$this->_tpl_vars['id']],'selected' => $this->_tpl_vars['arrForm'][$this->_tpl_vars['class1']]), $this);?>

								</select>
							</td>
						</tr>
						<?php endif; ?>
						<?php if ($this->_tpl_vars['tpl_classcat_find2'][$this->_tpl_vars['id']]): ?>
						<tr><td colspan="2" height="5" align="center" class="fs12"><span class="redst"><?php if ($this->_tpl_vars['arrErr'][$this->_tpl_vars['class2']] != ""): ?>※ <?php echo $this->_tpl_vars['tpl_class_name2'][$this->_tpl_vars['id']]; ?>
を入力して下さい。<?php endif; ?></span></td></tr>
						<tr>
							<td align="right" class="fs12st"><?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_class_name2'][$this->_tpl_vars['id']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
： </td>
							<td>
								<select name="<?php echo $this->_tpl_vars['class2']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['class2']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
								<option value="">選択してください</option>
								</select>
							</td>
						</tr>
						<?php endif; ?>
						<?php $this->assign('quantity', "quantity".($this->_tpl_vars['id'])); ?>		
						<tr><td colspan="2" height="10" align="center" class="fs12"><span class="redst"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['quantity']]; ?>
</span></td></tr>
						<tr>
							<td align="right" width="115" class="fs12st">個数： 
								<?php if ($this->_tpl_vars['arrErr']['quantity'] != ""): ?><br/><span class="redst"><?php echo $this->_tpl_vars['arrErr']['quantity']; ?>
</span><?php endif; ?>
								<input type="text" name="<?php echo $this->_tpl_vars['quantity']; ?>
" size="3" class="box3" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['arrForm'][$this->_tpl_vars['quantity']])) ? $this->_run_mod_handler('default', true, $_tmp, 1) : smarty_modifier_default($_tmp, 1)); ?>
" maxlength=<?php echo @INT_LEN; ?>
 style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['quantity']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" >
							</td>
							<td width="170" align="center">
								<a href="" onclick="fnChangeAction('<?php echo ((is_array($_tmp=$_SERVER['REQUEST_URI'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
#product<?php echo $this->_tpl_vars['id']; ?>
'); fnModeSubmit('cart','product_id','<?php echo $this->_tpl_vars['id']; ?>
'); return false;" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/products/b_cartin_on.gif','cart<?php echo $this->_tpl_vars['id']; ?>
');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/products/b_cartin.gif','cart<?php echo $this->_tpl_vars['id']; ?>
');"><img src="<?php echo @URL_DIR; ?>
img/products/b_cartin.gif" width="115" height="25" alt="カゴに入れる" name="cart<?php echo $this->_tpl_vars['id']; ?>
" id="cart<?php echo $this->_tpl_vars['id']; ?>
" /></a>
							</td>
						</tr>
						<tr><td height="10"></td></tr>
					</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>