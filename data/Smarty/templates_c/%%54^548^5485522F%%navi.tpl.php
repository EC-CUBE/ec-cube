<?php /* Smarty version 2.6.13, created on 2007-01-10 18:45:04
         compiled from /home/web/beta.ec-cube.net/html/user_data/templates/mypage/navi.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/home/web/beta.ec-cube.net/html/user_data/templates/mypage/navi.tpl', 49, false),array('modifier', 'number_format', '/home/web/beta.ec-cube.net/html/user_data/templates/mypage/navi.tpl', 50, false),array('modifier', 'default', '/home/web/beta.ec-cube.net/html/user_data/templates/mypage/navi.tpl', 50, false),)), $this); ?>
<!--▼NAVI-->
<table width="170" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<?php if ($this->_tpl_vars['tpl_mypageno'] == 'index'): ?>
			<td><a href="./index.php"><img src="<?php echo @URL_DIR; ?>
img/mypage/navi01_on.jpg" width="170" height="30" alt="購入履歴一覧" border="0" name="m_navi01"></a></td>
		<?php else: ?>
			<td><a href="./index.php" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/mypage/navi01_on.jpg','m_navi01');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/mypage/navi01.jpg','m_navi01');"><img src="<?php echo @URL_DIR; ?>
img/mypage/navi01.jpg" width="170" height="30" alt="購入履歴一覧" border="0" name="m_navi01"></a></td>
		<?php endif; ?>
	</tr>
	<tr>
		<?php if ($this->_tpl_vars['tpl_mypageno'] == 'change'): ?>
			<td><a href="./change.php"><img src="<?php echo @URL_DIR; ?>
img/mypage/navi02_on.jpg" width="170" height="30" alt="会員登録内容変更" border="0" name="m_navi02"></a></td>
		<?php else: ?>
			<td><a href="./change.php" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/mypage/navi02_on.jpg','m_navi02');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/mypage/navi02.jpg','m_navi02');"><img src="<?php echo @URL_DIR; ?>
img/mypage/navi02.jpg" width="170" height="30" alt="会員登録内容変更" border="0" name="m_navi02"></a></td>
		<?php endif; ?>
	</tr>
	<tr>
		<?php if ($this->_tpl_vars['tpl_mypageno'] == 'delivery'): ?>
			<td><a href="./delivery.php"><img src="<?php echo @URL_DIR; ?>
img/mypage/navi03_on.jpg" width="170" height="30" alt="お届け先追加・変更" border="0" name="m_navi03"></a></td>
		<?php else: ?>
			<td><a href="./delivery.php" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/mypage/navi03_on.jpg','m_navi03');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/mypage/navi03.jpg','m_navi03');"><img src="<?php echo @URL_DIR; ?>
img/mypage/navi03.jpg" width="170" height="30" alt="お届け先追加・変更" border="0" name="m_navi03"></a></td>
		<?php endif; ?>
	</tr>
	<tr>
		<?php if ($this->_tpl_vars['tpl_mypageno'] == 'refusal'): ?>
			<td><a href="./refusal.php"><img src="<?php echo @URL_DIR; ?>
img/mypage/navi04_on.jpg" width="170" height="30" alt="退会手続き" border="0" name="m_navi04"></a></td>
		<?php else: ?>
			<td><a href="./refusal.php" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/mypage/navi04_on.jpg','m_navi04');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/mypage/navi04.jpg','m_navi04');"><img src="<?php echo @URL_DIR; ?>
img/mypage/navi04.jpg" width="170" height="30" alt="退会手続き" border="0" name="m_navi04"></a></td>
		<?php endif; ?>
	</tr>
</table>

<table><tr><td height="15"></td></tr></table>

<!-- 現在のポイント ここから -->
<?php if ($this->_tpl_vars['point_disp'] !== false): ?>
<table width="170" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr align="center">
		<td class="fs12" bgcolor="#f0d0a0">
		<table width="170" border="0" cellspacing="3" cellpadding="10" summary=" ">
			<tr align="center">
				<td class="fs12" bgcolor="#ffffff">
				ようこそ <br/>
				<?php echo ((is_array($_tmp=$this->_tpl_vars['CustomerName1'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['CustomerName2'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
様<br>
				現在の所持ポイントは<span class="redst"><?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['CustomerPoint'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
pt</span>です。
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<?php endif; ?>
<!-- 現在のポイント ここまで -->

<!--▲NAVI-->