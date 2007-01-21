<?php /* Smarty version 2.6.13, created on 2007-01-15 19:58:43
         compiled from customer/subnavi.tpl */ ?>
	<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " id="menu_navi">
		<!--ナビ-->
		<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'index'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./index.php" onMouseOver="naviStyleChange('index', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'index'): ?>onMouseOut="naviStyleChange('index', '#636469')"<?php endif; ?> id="index"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">顧客マスタ</span></a></td></tr>
		<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
		<!--ナビ-->
	</table>