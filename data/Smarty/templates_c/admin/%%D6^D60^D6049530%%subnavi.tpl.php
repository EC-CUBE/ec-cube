<?php /* Smarty version 2.6.13, created on 2007-01-17 13:46:11
         compiled from total/subnavi.tpl */ ?>
<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " id="menu_navi">
	<!--ナビ-->
	<tr><td class=<?php if (! ( $this->_tpl_vars['arrForm']['page']['value'] == 'term' || $this->_tpl_vars['arrForm']['page']['value'] == '' )): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./index.php?page=term" onMouseOver="naviStyleChange('term', '#a5a5a5')" <?php if (! ( $this->_tpl_vars['arrForm']['page']['value'] == 'term' || $this->_tpl_vars['arrForm']['page']['value'] == '' )): ?>onMouseOut="naviStyleChange('term', '#636469')"<?php endif; ?> id="term"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">期間別集計</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if (! ( $this->_tpl_vars['arrForm']['page']['value'] == 'products' )): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./index.php?page=products" onMouseOver="naviStyleChange('products', '#a5a5a5')" <?php if (! ( $this->_tpl_vars['arrForm']['page']['value'] == 'products' )): ?>onMouseOut="naviStyleChange('products', '#636469')"<?php endif; ?> id="products"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">商品別集計</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if (! ( $this->_tpl_vars['arrForm']['page']['value'] == 'age' )): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./index.php?page=age" onMouseOver="naviStyleChange('age', '#a5a5a5')" <?php if (! ( $this->_tpl_vars['arrForm']['page']['value'] == 'age' )): ?>onMouseOut="naviStyleChange('age', '#636469')"<?php endif; ?> id="age"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">年代別集計</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if (! ( $this->_tpl_vars['arrForm']['page']['value'] == 'job' )): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./index.php?page=job" onMouseOver="naviStyleChange('job', '#a5a5a5')" <?php if (! ( $this->_tpl_vars['arrForm']['page']['value'] == 'job' )): ?>onMouseOut="naviStyleChange('job', '#636469')"<?php endif; ?> id="job"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">職業別集計</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if (! ( $this->_tpl_vars['arrForm']['page']['value'] == 'member' )): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./index.php?page=member" onMouseOver="naviStyleChange('member', '#a5a5a5')" <?php if (! ( $this->_tpl_vars['arrForm']['page']['value'] == 'member' )): ?>onMouseOut="naviStyleChange('member', '#636469')"<?php endif; ?> id="member"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">会員別集計</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--ナビ-->
</table>