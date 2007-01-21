<?php /* Smarty version 2.6.13, created on 2007-01-10 13:54:21
         compiled from /home/web/beta.ec-cube.net/html/user_data/include/bloc/category.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'sfCutString', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/category.tpl', 39, false),array('modifier', 'escape', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/category.tpl', 39, false),array('modifier', 'default', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/category.tpl', 39, false),)), $this); ?>
<!--▼商品カテゴリーここから-->
<table width="166" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/side/title_cat.jpg" width="166" height="35" alt="商品カテゴリー"></td>
	</tr>
	<tr>
		<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="10" alt=""></td>

		<td align="center" bgcolor="#fff1e3">
			<table width="146" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr>
					<td height="10"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="15" height="1" alt=""></td>
					<td><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="131" height="1" alt=""></td>
				</tr>
				
				<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrTree']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['cnt']['show'] = true;
$this->_sections['cnt']['max'] = $this->_sections['cnt']['loop'];
$this->_sections['cnt']['step'] = 1;
$this->_sections['cnt']['start'] = $this->_sections['cnt']['step'] > 0 ? 0 : $this->_sections['cnt']['loop']-1;
if ($this->_sections['cnt']['show']) {
    $this->_sections['cnt']['total'] = $this->_sections['cnt']['loop'];
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
				<?php $this->assign('level', ($this->_tpl_vars['arrTree'][$this->_sections['cnt']['index']]['level'])); ?>
				
								
				<?php if ($this->_tpl_vars['level'] == 1 && ! $this->_sections['cnt']['first']): ?>
				<tr><td colspan="2" height="15"><img src="<?php echo @URL_DIR; ?>
img/side/line_146.gif" width="146" height="1" alt=""></td></tr>
				<?php endif; ?>
								<?php $this->assign('disp_name', ($this->_tpl_vars['arrTree'][$this->_sections['cnt']['index']]['category_name'])); ?>
				<?php if ($this->_tpl_vars['arrTree'][$this->_sections['cnt']['index']]['display'] == 1): ?>
				<tr>
					<td colspan="2" class="fs12">
						<?php if ($this->_tpl_vars['tpl_category_id'] == $this->_tpl_vars['arrTree'][$this->_sections['cnt']['index']]['category_id'] || $this->_tpl_vars['root_parent_id'] == $this->_tpl_vars['arrTree'][$this->_sections['cnt']['index']]['category_id']): ?>
							<?php unset($this->_sections['n']);
$this->_sections['n']['name'] = 'n';
$this->_sections['n']['loop'] = is_array($_loop=($this->_tpl_vars['level']-1)) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['n']['show'] = true;
$this->_sections['n']['max'] = $this->_sections['n']['loop'];
$this->_sections['n']['step'] = 1;
$this->_sections['n']['start'] = $this->_sections['n']['step'] > 0 ? 0 : $this->_sections['n']['loop']-1;
if ($this->_sections['n']['show']) {
    $this->_sections['n']['total'] = $this->_sections['n']['loop'];
    if ($this->_sections['n']['total'] == 0)
        $this->_sections['n']['show'] = false;
} else
    $this->_sections['n']['total'] = 0;
if ($this->_sections['n']['show']):

            for ($this->_sections['n']['index'] = $this->_sections['n']['start'], $this->_sections['n']['iteration'] = 1;
                 $this->_sections['n']['iteration'] <= $this->_sections['n']['total'];
                 $this->_sections['n']['index'] += $this->_sections['n']['step'], $this->_sections['n']['iteration']++):
$this->_sections['n']['rownum'] = $this->_sections['n']['iteration'];
$this->_sections['n']['index_prev'] = $this->_sections['n']['index'] - $this->_sections['n']['step'];
$this->_sections['n']['index_next'] = $this->_sections['n']['index'] + $this->_sections['n']['step'];
$this->_sections['n']['first']      = ($this->_sections['n']['iteration'] == 1);
$this->_sections['n']['last']       = ($this->_sections['n']['iteration'] == $this->_sections['n']['total']);
?>&nbsp;&nbsp;<?php endfor; endif;  if ($this->_tpl_vars['level'] == 1): ?><img src="<?php echo @URL_DIR; ?>
img/common/arrow_red.gif" width="11" height="14" alt=""><?php endif; ?>
						<?php else: ?>
							<?php unset($this->_sections['n']);
$this->_sections['n']['name'] = 'n';
$this->_sections['n']['loop'] = is_array($_loop=($this->_tpl_vars['level']-1)) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['n']['show'] = true;
$this->_sections['n']['max'] = $this->_sections['n']['loop'];
$this->_sections['n']['step'] = 1;
$this->_sections['n']['start'] = $this->_sections['n']['step'] > 0 ? 0 : $this->_sections['n']['loop']-1;
if ($this->_sections['n']['show']) {
    $this->_sections['n']['total'] = $this->_sections['n']['loop'];
    if ($this->_sections['n']['total'] == 0)
        $this->_sections['n']['show'] = false;
} else
    $this->_sections['n']['total'] = 0;
if ($this->_sections['n']['show']):

            for ($this->_sections['n']['index'] = $this->_sections['n']['start'], $this->_sections['n']['iteration'] = 1;
                 $this->_sections['n']['iteration'] <= $this->_sections['n']['total'];
                 $this->_sections['n']['index'] += $this->_sections['n']['step'], $this->_sections['n']['iteration']++):
$this->_sections['n']['rownum'] = $this->_sections['n']['iteration'];
$this->_sections['n']['index_prev'] = $this->_sections['n']['index'] - $this->_sections['n']['step'];
$this->_sections['n']['index_next'] = $this->_sections['n']['index'] + $this->_sections['n']['step'];
$this->_sections['n']['first']      = ($this->_sections['n']['iteration'] == 1);
$this->_sections['n']['last']       = ($this->_sections['n']['iteration'] == $this->_sections['n']['total']);
?>&nbsp;&nbsp;<?php endfor; endif;  if ($this->_tpl_vars['level'] == 1): ?><img src="<?php echo @URL_DIR; ?>
img/common/arrow_blue.gif" width="11" height="14" alt=""><?php endif; ?>
						<?php endif; ?>
						<?php if ($this->_tpl_vars['tpl_category_id'] == $this->_tpl_vars['arrTree'][$this->_sections['cnt']['index']]['category_id']): ?>
							<a href="<?php echo @URL_DIR; ?>
products/list.php?category_id=<?php echo $this->_tpl_vars['arrTree'][$this->_sections['cnt']['index']]['category_id']; ?>
"><span class="redst"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['disp_name'])) ? $this->_run_mod_handler('sfCutString', true, $_tmp, 20) : sfCutString($_tmp, 20)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
(<?php echo ((is_array($_tmp=@$this->_tpl_vars['arrTree'][$this->_sections['cnt']['index']]['product_count'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
)</span></a>						
						<?php else: ?>							
							<a href="<?php echo @URL_DIR; ?>
products/list.php?category_id=<?php echo $this->_tpl_vars['arrTree'][$this->_sections['cnt']['index']]['category_id']; ?>
"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['disp_name'])) ? $this->_run_mod_handler('sfCutString', true, $_tmp, 20) : sfCutString($_tmp, 20)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
(<?php echo ((is_array($_tmp=@$this->_tpl_vars['arrTree'][$this->_sections['cnt']['index']]['product_count'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
)</a>
						<?php endif; ?>
					</td>
				</tr>
				<?php endif; ?>
				<?php endfor; endif; ?>
			</table>
		</td>
		<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="10" alt=""></td>
	</tr>
	<tr>
		<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/side/flame_bottom02.gif" width="166" height="15" alt=""></td>
	</tr>
	<tr><td height="10"></td></tr>
</table>
<!--▲商品カテゴリーここまで-->