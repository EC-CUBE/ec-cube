<?php /* Smarty version 2.6.13, created on 2007-01-10 13:54:21
         compiled from /home/web/beta.ec-cube.net/html/user_data/include/bloc/guide.tpl */ ?>
<!--▼リンクここから-->
<table width="166" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<?php if ($this->_tpl_vars['tpl_page_category'] != 'abouts'): ?>
			<td><a href="<?php echo @URL_DIR; ?>
abouts/index.php" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/side/about_on.jpg','about');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/side/about.jpg','about');"><img src="<?php echo @URL_DIR; ?>
img/side/about.jpg" width="166" height="30" alt="当サイトについて" border="0" name="about"></a></td>
		<?php else: ?>
			<td><a href="<?php echo @URL_DIR; ?>
abouts/index.php"><img src="<?php echo @URL_DIR; ?>
img/side/about_on.jpg" width="166" height="30" alt="当サイトについて" border="0" name="about"></a></td>
		<?php endif; ?>
	</tr>
	<tr>
		<?php if ($this->_tpl_vars['tpl_page_category'] != 'contact'): ?>
			<td><a href="<?php echo @URL_DIR; ?>
contact/index.php" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/side/contact_on.jpg','contact');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/side/contact.jpg','contact');"><img src="<?php echo @URL_DIR; ?>
img/side/contact.jpg" width="166" height="30" alt="お問い合わせ" border="0" name="contact"></a></td>
		<?php else: ?>
			<td><a href="<?php echo @URL_DIR; ?>
contact/index.php"><img src="<?php echo @URL_DIR; ?>
img/side/contact_on.jpg" width="166" height="30" alt="お問い合わせ" border="0" name="contact"></a><td>
		<?php endif; ?>
	</tr>
	<tr>
		<?php if ($this->_tpl_vars['tpl_page_category'] != 'order'): ?>
			<td><a href="<?php echo @URL_DIR; ?>
order/index.php" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/side/low_on.jpg','low');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/side/low.jpg','low');"><img src="<?php echo @URL_DIR; ?>
img/side/low.jpg" width="166" height="30" alt="特定商取引に関する法律" border="0" name="low"></a></td>
		<?php else: ?>
			<td><a href="<?php echo @URL_DIR; ?>
order/index.php"><img src="<?php echo @URL_DIR; ?>
img/side/low_on.jpg" width="166" height="30" alt="特定商取引に関する法律" border="0" name="low"></a></td>
		<?php endif; ?>
	</tr>
	<tr><td height="10"></td></tr>
</table>
<!--▲リンクここまで-->