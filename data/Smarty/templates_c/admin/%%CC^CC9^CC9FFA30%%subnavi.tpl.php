<?php /* Smarty version 2.6.13, created on 2007-01-17 13:46:09
         compiled from system/subnavi.tpl */ ?>
<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " id="menu_navi">
	<!--ナビ-->
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'index'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./index.php" onMouseOver="naviStyleChange('index', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'index'): ?>onMouseOut="naviStyleChange('index', '#636469')"<?php endif; ?> id="index"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">メンバー管理</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'update'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./update.php" onMouseOver="naviStyleChange('update', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'update'): ?>onMouseOut="naviStyleChange('update', '#636469')"<?php endif; ?> id="update"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">アップデート管理</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'module'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./module.php" onMouseOver="naviStyleChange('module', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'module'): ?>onMouseOut="naviStyleChange('module', '#636469')"<?php endif; ?> id="module"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">モジュール管理</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'bkup'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./bkup.php" onMouseOver="naviStyleChange('bkup', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'bkup'): ?>onMouseOut="naviStyleChange('bkup', '#636469')"<?php endif; ?> id="bkup"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">バックアップ管理</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--ナビ-->
</table>