<?php /* Smarty version 2.6.13, created on 2007-01-15 19:58:47
         compiled from design/subnavi.tpl */ ?>
<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " id="menu_navi">
	<!--ナビ-->
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'layout'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./index.php" onMouseOver="naviStyleChange('layout', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'layout'): ?>onMouseOut="naviStyleChange('layout', '#636469')"<?php endif; ?> id="layout"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">レイアウト設定</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'main_edit'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./main_edit.php" onMouseOver="naviStyleChange('main_edit', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'main_edit'): ?>onMouseOut="naviStyleChange('main_edit', '#636469')"<?php endif; ?> id="main_edit"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ページ詳細設定</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'bloc'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./bloc.php"	onMouseOver="naviStyleChange('bloc', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'bloc'): ?>onMouseOut="naviStyleChange('bloc', '#636469')"<?php endif; ?> id="bloc"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ブロック編集</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'header'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./header.php"	onMouseOver="naviStyleChange('header', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'header'): ?>onMouseOut="naviStyleChange('header', '#636469')"<?php endif; ?> id="header"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ﾍｯﾀﾞｰ/ﾌｯﾀｰ設定</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'css'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./css.php"	onMouseOver="naviStyleChange('css', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'css'): ?>onMouseOut="naviStyleChange('css', '#636469')"<?php endif; ?> id="css"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">CSS編集</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>

	<tr><td class=<?php if ($this->_tpl_vars['tpl_subno'] != 'template'): ?>"navi"<?php else: ?>"navi-on"<?php endif; ?>><a href="./template.php"	onMouseOver="naviStyleChange('template', '#a5a5a5')" <?php if ($this->_tpl_vars['tpl_subno'] != 'template'): ?>onMouseOut="naviStyleChange('template', '#636469')"<?php endif; ?> id="template"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">テンプレート設定</span></a></td></tr>
	<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<?php if ($this->_tpl_vars['tpl_subno'] == 'template'): ?>
		<tr><td class=<?php if ($this->_tpl_vars['tpl_subno_template'] != 'top'): ?>"subnavi"<?php else: ?>"subnavi-on"<?php endif; ?>><a href="./template.php?tpl_subno_template=<?php echo $this->_tpl_vars['arrSubnavi']['title']['1']; ?>
"	onMouseOver="naviStyleChange('top', '#b7b7b7')" <?php if ($this->_tpl_vars['tpl_subno_template'] != 'top'): ?>onMouseOut="naviStyleChange('top', '#818287')"<?php endif; ?> id="top"><span class="subnavi_text">TOPページ</span></a></td></tr>
		<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<tr><td class=<?php if ($this->_tpl_vars['tpl_subno_template'] != 'product'): ?>"subnavi"<?php else: ?>"subnavi-on"<?php endif; ?>><a href="./template.php?tpl_subno_template=<?php echo $this->_tpl_vars['arrSubnavi']['title']['2']; ?>
"	onMouseOver="naviStyleChange('product', '#b7b7b7')" <?php if ($this->_tpl_vars['tpl_subno_template'] != 'product'): ?>onMouseOut="naviStyleChange('product', '#818287')"<?php endif; ?> id="product"><span class="subnavi_text">商品一覧ページ</span></a></td></tr>
		<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<tr><td class=<?php if ($this->_tpl_vars['tpl_subno_template'] != 'detail'): ?>"subnavi"<?php else: ?>"subnavi-on"<?php endif; ?>><a href="./template.php?tpl_subno_template=<?php echo $this->_tpl_vars['arrSubnavi']['title']['3']; ?>
"	onMouseOver="naviStyleChange('detail', '#b7b7b7')" <?php if ($this->_tpl_vars['tpl_subno_template'] != 'detail'): ?>onMouseOut="naviStyleChange('detail', '#818287')"<?php endif; ?> id="detail"><span class="subnavi_text">商品詳細ページ</span></a></td></tr>
		<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<tr><td class=<?php if ($this->_tpl_vars['tpl_subno_template'] != 'mypage'): ?>"subnavi"<?php else: ?>"subnavi-on"<?php endif; ?>><a href="./template.php?tpl_subno_template=<?php echo $this->_tpl_vars['arrSubnavi']['title']['4']; ?>
"	onMouseOver="naviStyleChange('mypage', '#b7b7b7')" <?php if ($this->_tpl_vars['tpl_subno_template'] != 'mypage'): ?>onMouseOut="naviStyleChange('mypage', '#818287')"<?php endif; ?> id="mypage"><span class="subnavi_text">MYページ</span></a></td></tr>
		<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<tr><td class=<?php if ($this->_tpl_vars['tpl_subno_template'] != 'upload'): ?>"subnavi"<?php else: ?>"subnavi-on"<?php endif; ?>><a href="./upload.php" onMouseOver="naviStyleChange('upload', '#b7b7b7')" <?php if ($this->_tpl_vars['tpl_subno_template'] != 'upload'): ?>onMouseOut="naviStyleChange('upload', '#818287')"<?php endif; ?> id="upload"><span class="subnavi_text">アップロード</span></a></td></tr>
		<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
	<?php endif; ?>
	
	<!--ナビ-->
</table>