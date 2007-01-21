<?php /* Smarty version 2.6.13, created on 2007-01-10 00:03:26
         compiled from error.tpl */ ?>
 <!--▼CONTENTS-->
<table width=760 border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="20"></td></tr>
			<tr>
				<td align="center" bgcolor="#cccccc">
				<table width="690" border="0" cellspacing="0" cellpadding="3" summary=" ">
					<tr><td height="5"></td></tr>
					<tr>
						<td align="center" height="250" bgcolor="#ffffff" class="fs12"><!--★エラーメッセージ--><?php echo $this->_tpl_vars['tpl_error']; ?>
</td>
					</tr>
					<tr><td height="5"></td></tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr align="center">
				<td>
					<div id="button">
						<?php if ($this->_tpl_vars['return_top']): ?>
							<a href="<?php echo @URL_DIR; ?>
index.php" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/common/b_toppage.gif','b_toppage');"><img src="<?php echo @URL_DIR; ?>
img/common/b_toppage.gif" width="150" height="30" alt="トップページへ" border="0" name="b_toppage"></a>
						<?php else: ?>
							<a href="javascript:history.back()" onmouseOver="chgImg('<?php echo @URL_DIR; ?>
img/common/b_back_on.gif','b_back');" onmouseOut="chgImg('<?php echo @URL_DIR; ?>
img/common/b_back.gif','b_back');"><img src="<?php echo @URL_DIR; ?>
img/common/b_back.gif" width="150" height="30" alt="戻る" name="b_back" id="b_back" /></a>
						<?php endif; ?>
					</div>
				</td>
			</tr>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->