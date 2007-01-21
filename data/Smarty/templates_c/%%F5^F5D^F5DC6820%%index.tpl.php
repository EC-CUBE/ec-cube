<?php /* Smarty version 2.6.13, created on 2007-01-10 18:45:04
         compiled from /home/web/beta.ec-cube.net/html/user_data/templates/mypage/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/home/web/beta.ec-cube.net/html/user_data/templates/mypage/index.tpl', 8, false),array('modifier', 'sfDispDBDate', '/home/web/beta.ec-cube.net/html/user_data/templates/mypage/index.tpl', 62, false),array('modifier', 'number_format', '/home/web/beta.ec-cube.net/html/user_data/templates/mypage/index.tpl', 66, false),)), $this); ?>
<!--▼CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input type="hidden" name="order_id" value="" >
<input type="hidden" name="pageno" value="<?php echo $this->_tpl_vars['tpl_pageno']; ?>
">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/mypage/title.jpg" width="700" height="40" alt="MYページ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr valign="top">
				<td>
					<?php if ($this->_tpl_vars['tpl_navi'] != ""): ?>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['tpl_navi'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					<?php else: ?>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => (@USER_PATH)."templates/mypage/navi.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					<?php endif; ?>
				</td>
				<td align="right">
				<table width="515" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td><!--★タイトル--><img src="<?php echo @URL_DIR; ?>
img/mypage/subtitle01.gif" width="515" height="32" alt="購入履歴一覧"></td>
					</tr>
					<tr><td height="15"></td></tr>
					
					<?php if ($this->_tpl_vars['tpl_linemax'] > 0): ?>
					
					<tr>
						<td class="fs12n"><?php echo $this->_tpl_vars['tpl_linemax']; ?>
件の購入履歴があります。</td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr>
						<td class="fs12n" align="center">
							<!--▼ページナビ-->
							<?php echo $this->_tpl_vars['tpl_strnavi']; ?>

							<!--▲ページナビ-->
						</td>
					</tr>
					<tr>
						<td bgcolor="#cccccc" align="center">
						<!--表示エリアここから-->
						<table width="515" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr align="center" bgcolor="#f0f0f0">
								<td width="140" class="fs12n">購入日時</td>
								<td width="70" class="fs12n">注文番号</td>
								<td width="90" class="fs12n">お支払い方法</td>
								<td width="70" class="fs12n">合計金額</td>
								<td width="39" class="fs12n">詳細</td>
							</tr>
							<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrOrder']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['cnt']['show'] = true;
$this->_sections['cnt']['max'] = $this->_sections['cnt']['loop'];
$this->_sections['cnt']['step'] = 1;
$this->_sections['cnt']['start'] = $this->_sections['cnt']['step'] > 0 ? 0 : $this->_sections['cnt']['loop']-1;
if ($this->_sections['cnt']['show']) {
    $this->_sections['cnt']['total'] = $this->_sections['cnt']['loop'];
    if ($this->_sections['cnt']['total'] == 0)
        $this->_sections['cnt']['show'] = false;
} else
    $this->_sections['cnt']['total'] = 0;
if ($this->_sections['cnt']['show']):

            for ($this->_sections['cnt']['index'] = $this->_sections['cnt']['start'], $this->_sections['cnt']['iteration'] = 1;
                 $this->_sections['cnt']['iteration'] <= $this->_sections['cnt']['total'];
                 $this->_sections['cnt']['index'] += $this->_sections['cnt']['step'], $this->_sections['cnt']['iteration']++):
$this->_sections['cnt']['rownum'] = $this->_sections['cnt']['iteration'];
$this->_sections['cnt']['index_prev'] = $this->_sections['cnt']['index'] - $this->_sections['cnt']['step'];
$this->_sections['cnt']['index_next'] = $this->_sections['cnt']['index'] + $this->_sections['cnt']['step'];
$this->_sections['cnt']['first']      = ($this->_sections['cnt']['iteration'] == 1);
$this->_sections['cnt']['last']       = ($this->_sections['cnt']['iteration'] == $this->_sections['cnt']['total']);
?>
							<tr bgcolor="#ffffff">
								<td class="fs12"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrOrder'][$this->_sections['cnt']['index']]['create_date'])) ? $this->_run_mod_handler('sfDispDBDate', true, $_tmp) : sfDispDBDate($_tmp)); ?>
</td>
								<td align="center" class="fs12"><?php echo $this->_tpl_vars['arrOrder'][$this->_sections['cnt']['index']]['order_id']; ?>
</td>
								<?php $this->assign('payment_id', ($this->_tpl_vars['arrOrder'][$this->_sections['cnt']['index']]['payment_id'])); ?>
								<td align="center" class="fs12"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrPayment'][$this->_tpl_vars['payment_id']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
								<td align="right" class="fs12"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrOrder'][$this->_sections['cnt']['index']]['payment_total'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
円</td>
								<td align="center" class="fs12"><a href="#" onclick="fnChangeAction('./history.php'); fnKeySubmit('order_id','<?php echo $this->_tpl_vars['arrOrder'][$this->_sections['cnt']['index']]['order_id']; ?>
');">詳細</a></td>
							</tr>
							<?php endfor; endif; ?>
						</table>
						<!--表示エリアここまで-->
						</td>
					</tr>
					<tr>
						<td class="fs12n" align="center">
							<!--▼ページナビ-->
							<?php echo $this->_tpl_vars['tpl_strnavi']; ?>

							<!--▲ページナビ-->
						</td>
					</tr>
					<?php else: ?>
					<tr>
						<td class="fs12n" align="center">
					<table border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="5"></td></tr>
					<tr class="fs12"><td align="center">購入履歴はありません。</td></tr>
					</table>
						</td>
					</tr>
					<?php endif; ?>
				</table>
				</td>
			</tr>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</form>
</table>
<!--▲CONTENTS-->
