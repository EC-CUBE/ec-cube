<?php /* Smarty version 2.6.13, created on 2007-01-10 17:17:43
         compiled from order/subnavi.tpl */ ?>
<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " id="menu_navi">
	<!--ナビ-->
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'index'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./index.php" onMouseOver="naviStyleChange('index', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'index'): ?>onMouseOut="naviStyleChange('index', '#636469')"<?php endif; ?> id="index"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">受注管理</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'status'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./status.php" onMouseOver="naviStyleChange('status', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'status'): ?>onMouseOut="naviStyleChange('status', '#636469')"<?php endif; ?> id="status"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ステータス管理</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<?php if ($this->_tpl_vars['tpl_subno'] == 'status'): ?>
		<?php $_from = $this->_tpl_vars['arrORDERSTATUS']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
			<tr><td class=<?php if ($this->_tpl_vars['key'] != $this->_tpl_vars['SelectedStatus'] && $this->_tpl_vars['key'] != $this->_tpl_vars['defaultstatus']): ?>"subnavi"<?php else: ?>"subnavi-on"<?php endif; ?>><a href="#" onclick="document.form1.search_pageno.value='1'; fnModeSubmit('search','status','<?php echo $this->_tpl_vars['key']; ?>
' );" onMouseOver="naviStyleChange('status_sub<?php echo $this->_tpl_vars['key']; ?>
', '#b7b7b7')" <?php if ($this->_tpl_vars['key'] != $this->_tpl_vars['SelectedStatus'] && $this->_tpl_vars['key'] != $this->_tpl_vars['defaultstatus']): ?>onMouseOut="naviStyleChange('status_sub<?php echo $this->_tpl_vars['key']; ?>
', '#818287')"<?php endif; ?> id="status_sub<?php echo $this->_tpl_vars['key']; ?>
"><span class="subnavi_text"><?php echo $this->_tpl_vars['item']; ?>
</span></a></td></tr>
			<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<?php endforeach; endif; unset($_from); ?>
	<?php endif; ?>
	<!--ナビ-->
</table>