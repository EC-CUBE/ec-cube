<?php /* Smarty version 2.6.13, created on 2007-01-10 13:54:21
         compiled from /home/web/beta.ec-cube.net/html/user_data/include/bloc/news.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/news.tpl', 22, false),array('modifier', 'nl2br', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/news.tpl', 23, false),)), $this); ?>
<table width="400" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/top/news.jpg" width="400" height="29" alt="新着情報"></td>
	</tr>
	<tr>
		<td colspan="3"><span class="fs10">☆★☆ 新着情報は<a href="<?php echo @URL_DIR; ?>
rss/index.php" target="_blank">RSS</a>で配信しています。★☆★ </span></td>
	</tr>
	<tr>
		<td height="10"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="16" height="1" alt=""></td>
		<td><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="90" height="1" alt=""></td>
		<td><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="294" height="1" alt=""></td>
	</tr>

	<?php unset($this->_sections['data']);
$this->_sections['data']['name'] = 'data';
$this->_sections['data']['loop'] = is_array($_loop=$this->_tpl_vars['arrNews']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['data']['show'] = true;
$this->_sections['data']['max'] = $this->_sections['data']['loop'];
$this->_sections['data']['step'] = 1;
$this->_sections['data']['start'] = $this->_sections['data']['step'] > 0 ? 0 : $this->_sections['data']['loop']-1;
if ($this->_sections['data']['show']) {
    $this->_sections['data']['total'] = $this->_sections['data']['loop'];
    if ($this->_sections['data']['total'] == 0)
        $this->_sections['data']['show'] = false;
} else
    $this->_sections['data']['total'] = 0;
if ($this->_sections['data']['show']):

            for ($this->_sections['data']['index'] = $this->_sections['data']['start'], $this->_sections['data']['iteration'] = 1;
                 $this->_sections['data']['iteration'] <= $this->_sections['data']['total'];
                 $this->_sections['data']['index'] += $this->_sections['data']['step'], $this->_sections['data']['iteration']++):
$this->_sections['data']['rownum'] = $this->_sections['data']['iteration'];
$this->_sections['data']['index_prev'] = $this->_sections['data']['index'] - $this->_sections['data']['step'];
$this->_sections['data']['index_next'] = $this->_sections['data']['index'] + $this->_sections['data']['step'];
$this->_sections['data']['first']      = ($this->_sections['data']['iteration'] == 1);
$this->_sections['data']['last']       = ($this->_sections['data']['iteration'] == $this->_sections['data']['total']);
?>
	<tr valign="top">
		<td><img src="<?php echo @URL_DIR; ?>
img/top/news_icon.gif" width="16" height="16" alt=""></td>
		<td class="fs10"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrNews'][$this->_sections['data']['index']]['news_date_disp'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
		<td class="fs10"><?php if ($this->_tpl_vars['arrNews'][$this->_sections['data']['index']]['news_url']): ?><a href="<?php echo $this->_tpl_vars['arrNews'][$this->_sections['data']['index']]['news_url']; ?>
" <?php if ($this->_tpl_vars['arrNews'][$this->_sections['data']['index']]['link_method'] == '2'): ?>target="_blank"<?php endif; ?> ><?php endif;  echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrNews'][$this->_sections['data']['index']]['news_title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp));  if ($this->_tpl_vars['arrNews'][$this->_sections['data']['index']]['news_url']): ?></a><?php endif; ?><br/><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrNews'][$this->_sections['data']['index']]['news_comment'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</td>
	</tr>
	<?php if (! $this->_sections['data']['last']): ?>
	<tr><td colspan="3" height="20"><img src="<?php echo @URL_DIR; ?>
img/common/line_400.gif" width="400" height="1" alt=""></td></tr>
	<?php endif; ?>
	<?php endfor; endif; ?>

	<tr><td height="35" colspan="3"></td></tr>
</table>