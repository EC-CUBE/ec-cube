<?php /* Smarty version 2.6.13, created on 2007-01-10 13:54:21
         compiled from /home/web/beta.ec-cube.net/html/user_data/include/bloc/best5.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'sfRmDupSlash', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/best5.tpl', 21, false),array('modifier', 'escape', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/best5.tpl', 21, false),array('modifier', 'sfPreTax', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/best5.tpl', 30, false),array('modifier', 'number_format', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/best5.tpl', 30, false),array('modifier', 'nl2br', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/best5.tpl', 38, false),array('modifier', 'count', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/best5.tpl', 49, false),)), $this); ?>
<!--▼おすすめ情報ここから-->
<table width="400" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td colspan="2"><img src="<?php echo @URL_DIR; ?>
img/top/osusume.jpg" width="400" height="29" alt="おすすめ情報"></td>
	</tr>
	<tr><td height="10"></td></tr>

	<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrBestProducts']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['cnt']['step'] = ((int)2) == 0 ? 1 : (int)2;
$this->_sections['cnt']['show'] = true;
$this->_sections['cnt']['max'] = $this->_sections['cnt']['loop'];
$this->_sections['cnt']['start'] = $this->_sections['cnt']['step'] > 0 ? 0 : $this->_sections['cnt']['loop']-1;
if ($this->_sections['cnt']['show']) {
    $this->_sections['cnt']['total'] = min(ceil(($this->_sections['cnt']['step'] > 0 ? $this->_sections['cnt']['loop'] - $this->_sections['cnt']['start'] : $this->_sections['cnt']['start']+1)/abs($this->_sections['cnt']['step'])), $this->_sections['cnt']['max']);
    if ($this->_sections['cnt']['total'] == 0)
        $this->_sections['cnt']['show'] = false;
} else
    $this->_sections['cnt']['total'] = 0;
if ($this->_sections['cnt']['show']):

            for ($this->_sections['cnt']['index'] = $this->_sections['cnt']['start'], $this->_sections['cnt']['iteration'] = 1;
                 $this->_sections['cnt']['iteration'] <= $this->_sections['cnt']['total'];
                 $this->_sections['cnt']['index'] += $this->_sections['cnt']['step'], $this->_sections['cnt']['iteration']++):
$this->_sections['cnt']['rownum'] = $this->_sections['cnt']['iteration'];
$this->_sections['cnt']['index_prev'] = $this->_sections['cnt']['index'] - $this->_sections['cnt']['step'];
$this->_sections['cnt']['index_next'] = $this->_sections['cnt']['index'] + $this->_sections['cnt']['step'];
$this->_sections['cnt']['first']      = ($this->_sections['cnt']['iteration'] == 1);
$this->_sections['cnt']['last']       = ($this->_sections['cnt']['iteration'] == $this->_sections['cnt']['total']);
?>
	<tr valign="top">
		<td>
		
		<?php if ($this->_tpl_vars['arrBestProducts'][$this->_sections['cnt']['index']]['main_list_image'] != ""):  $this->assign('image_path', (@IMAGE_SAVE_DIR)."/".($this->_tpl_vars['arrBestProducts'][$this->_sections['cnt']['index']]['main_list_image']));  else:  $this->assign('image_path', (@NO_IMAGE_DIR));  endif; ?>
		
		<table width="190" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr valign="top">
				<td align="center" valign="middle"><a href="<?php echo @URL_DIR; ?>
products/detail.php?product_id=<?php echo $this->_tpl_vars['arrBestProducts'][$this->_sections['cnt']['index']]['product_id']; ?>
"><img src="<?php echo @SITE_URL; ?>
resize_image.php?image=<?php echo ((is_array($_tmp=$this->_tpl_vars['image_path'])) ? $this->_run_mod_handler('sfRmDupSlash', true, $_tmp) : sfRmDupSlash($_tmp)); ?>
&width=48&height=48" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrBestProducts'][$this->_sections['cnt']['index']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"></a></td>
				<td align="right">
				<table width="132" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<?php $this->assign('price01', ($this->_tpl_vars['arrBestProducts'][$this->_sections['cnt']['index']]['price01_min'])); ?>
						<?php $this->assign('price02', ($this->_tpl_vars['arrBestProducts'][$this->_sections['cnt']['index']]['price02_min'])); ?>
						<td><span class="fs12"><a href="<?php echo @URL_DIR; ?>
products/detail.php?product_id=<?php echo $this->_tpl_vars['arrBestProducts'][$this->_sections['cnt']['index']]['product_id']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrBestProducts'][$this->_sections['cnt']['index']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a></span><br>
						<span class="red"><span class="fs12">価格</span><span class="fs10">(税込)</span></span><span class="redst"><span class="fs12">：
						<?php if ($this->_tpl_vars['price02'] == ""): ?>
						<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['price01'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

						<?php else: ?>
						<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['price02'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

						<?php endif; ?>
						円</span></span></td>
					</tr>
					<tr><td height="5"></td></tr>
					<tr>
						<td class="fs10"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrBestProducts'][$this->_sections['cnt']['index']]['comment'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		
		</td>
		<td align="right">
		
		<?php $this->assign('cnt2', ($this->_sections['cnt']['iteration']*$this->_sections['cnt']['step']-1)); ?>
		<?php if (((is_array($_tmp=$this->_tpl_vars['arrBestProducts'][$this->_tpl_vars['cnt2']])) ? $this->_run_mod_handler('count', true, $_tmp) : count($_tmp)) > 0): ?>
			<?php if ($this->_tpl_vars['arrBestProducts'][$this->_tpl_vars['cnt2']]['main_list_image'] != ""):  $this->assign('image_path', (@IMAGE_SAVE_DIR)."/".($this->_tpl_vars['arrBestProducts'][$this->_tpl_vars['cnt2']]['main_list_image']));  else:  $this->assign('image_path', (@NO_IMAGE_DIR));  endif; ?>
			<table width="190" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr valign="top">
					<td align="center" valign="middle"><a href="<?php echo @URL_DIR; ?>
products/detail.php?product_id=<?php echo $this->_tpl_vars['arrBestProducts'][$this->_tpl_vars['cnt2']]['product_id']; ?>
"><img src="<?php echo @SITE_URL; ?>
resize_image.php?image=<?php echo ((is_array($_tmp=$this->_tpl_vars['image_path'])) ? $this->_run_mod_handler('sfRmDupSlash', true, $_tmp) : sfRmDupSlash($_tmp)); ?>
&width=48&height=48" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrBestProducts'][$this->_tpl_vars['cnt2']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"></a></td>
					<td align="right">
					<table width="132" border="0" cellspacing="0" cellpadding="0" summary=" ">
						<tr>
							<?php $this->assign('price01', ($this->_tpl_vars['arrBestProducts'][$this->_tpl_vars['cnt2']]['price01_min'])); ?>
							<?php $this->assign('price02', ($this->_tpl_vars['arrBestProducts'][$this->_tpl_vars['cnt2']]['price02_min'])); ?>
							<td><span class="fs12"><a href="<?php echo @URL_DIR; ?>
products/detail.php?product_id=<?php echo $this->_tpl_vars['arrBestProducts'][$this->_tpl_vars['cnt2']]['product_id']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrBestProducts'][$this->_tpl_vars['cnt2']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a></span><br>
							<span class="red"><span class="fs12">価格</span><span class="fs10">(税込)</span></span><span class="redst"><span class="fs12">：
							<?php if ($this->_tpl_vars['price02'] == ""): ?>
							<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['price01'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

							<?php else: ?>
							<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['price02'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

							<?php endif; ?>
							円</span></span></td>
						</tr>
						<tr><td height="5"></td></tr>
						<tr>
							<td class="fs10"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrBestProducts'][$this->_tpl_vars['cnt2']]['comment'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
		<?php endif; ?>
	
	<?php if (! $this->_sections['cnt']['last']): ?>
	<tr>
		<td height="20"><img src="<?php echo @URL_DIR; ?>
img/common/line_190.gif" width="190" height="1" alt=""></td>
		<td align="right"><img src="<?php echo @URL_DIR; ?>
img/common/line_190.gif" width="190" height="1" alt=""></td>
	</tr>
	<?php endif; ?>
	
	<?php endfor; endif; ?>
<tr><td height="35"></td></tr>
</table>
<!--▲おすすめ情報ここまで-->
		
		