<?php /* Smarty version 2.6.13, created on 2007-01-10 18:34:50
         compiled from mail/subnavi.tpl */ ?>
<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " id="menu_navi">
	<!--ナビ-->
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'index'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./index.php" onMouseOver="naviStyleChange('index', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'index'): ?>onMouseOut="naviStyleChange('index', '#636469')"<?php endif; ?> id="index"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">配信内容設定</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'template'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./template.php" onMouseOver="naviStyleChange('template', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'template'): ?>onMouseOut="naviStyleChange('template', '#636469')"<?php endif; ?> id="template"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">テンプレート設定</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'history'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./history.php" onMouseOver="naviStyleChange('history', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'history'): ?>onMouseOut="naviStyleChange('history', '#636469')"<?php endif; ?> id="history"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">配信履歴</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--ナビ-->
</table>