<?php /* Smarty version 2.6.13, created on 2007-01-10 00:09:50
         compiled from products/subnavi.tpl */ ?>
<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " id="menu_navi">
	<!--ナビ-->
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'index'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./index.php" onMouseOver="naviStyleChange('index', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'index'): ?>onMouseOut="naviStyleChange('index', '#636469')"<?php endif; ?> id="index"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">商品マスタ</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'product'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./product.php" onMouseOver="naviStyleChange('product', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'product'): ?>onMouseOut="naviStyleChange('product', '#636469')"<?php endif; ?> id="product"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">商品登録</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'upload_csv'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./upload_csv.php" onMouseOver="naviStyleChange('upload_csv', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'upload_csv'): ?>onMouseOut="naviStyleChange('upload_csv', '#636469')"<?php endif; ?> id="upload_csv"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">商品登録CSV</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'class'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./class.php" onMouseOver="naviStyleChange('class', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'class'): ?>onMouseOut="naviStyleChange('class', '#636469')"<?php endif; ?> id="class"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">規格管理</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'category'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./category.php" onMouseOver="naviStyleChange('category', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'category'): ?>onMouseOut="naviStyleChange('category', '#636469')"<?php endif; ?> id="category"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">カテゴリー管理</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'product_rank'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./product_rank.php" onMouseOver="naviStyleChange('product_rank', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'product_rank'): ?>onMouseOut="naviStyleChange('product_rank', '#636469')"<?php endif; ?> id="product_rank"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">商品並び替え</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'review'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./review.php" onMouseOver="naviStyleChange('review', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'review'): ?>onMouseOut="naviStyleChange('review', '#636469')"<?php endif; ?> id="review"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">レビュー管理</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--ナビ-->
</table>