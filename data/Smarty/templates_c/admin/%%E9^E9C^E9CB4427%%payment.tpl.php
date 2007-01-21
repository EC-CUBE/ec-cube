<?php /* Smarty version 2.6.13, created on 2007-01-18 09:23:37
         compiled from basis/payment.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'basis/payment.tpl', 10, false),array('modifier', 'number_format', 'basis/payment.tpl', 66, false),array('modifier', 'default', 'basis/payment.tpl', 76, false),)), $this); ?>
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="payment_id" value="<?php echo $this->_tpl_vars['tpl_payment_id']; ?>
">
	<tr valign="top">
		<td background="<?php echo @URL_DIR; ?>
img/contents/navi_bg.gif" height="402">
			<!--▼SUB NAVI-->
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['tpl_subnavi'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<!--▲SUB NAVI-->
		</td>
		<td class="mainbg">
			<!--▼登録テーブルここから-->
			<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--メインエリア-->
				<tr>
					<td align="center">
						<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_top.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<?php echo @URL_DIR; ?>
img/contents/main_left.jpg"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<?php echo @URL_DIR; ?>
img/contents/contents_title_left_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->支払方法設定</span></td>
										<td background="<?php echo @URL_DIR; ?>
img/contents/contents_title_right_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr align="center" bgcolor="#f2f1ec" class="fs12n">
										<td width="134">支払方法</td>
										<td width="69">手数料（円）</td>
										<td width="124">利用条件</td>
										<td width="84">配送サービス</td>
										<td width="44">編集</td>
										<td width="44">削除</td>
										<td width="69">移動</td>
									</tr>
									<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrPaymentListFree']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
									<tr bgcolor="#ffffff" class="fs12n">
										<td><?php echo ((is_array($_tmp=$this->_tpl_vars['arrPaymentListFree'][$this->_sections['cnt']['index']]['payment_method'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
										<?php if ($this->_tpl_vars['arrPaymentListFree'][$this->_sections['cnt']['index']]['charge_flg'] == 2): ?>
											<td align="center">-</td>
										<?php else: ?>
											<td align="right"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrPaymentListFree'][$this->_sections['cnt']['index']]['charge'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
</td>
										<?php endif; ?>
										<td align="center">
											<table border="0" cellspacing="0" cellpadding="0" summary=" ">
												<tr class="fs12">
													<td align="center" width="80"><?php if ($this->_tpl_vars['arrPaymentListFree'][$this->_sections['cnt']['index']]['rule'] > 0):  echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrPaymentListFree'][$this->_sections['cnt']['index']]['rule'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp));  else: ?>0<?php endif; ?>円</td>
													<td align="center"> 〜 </td>
													<td align="center" width="80"><?php if ($this->_tpl_vars['arrPaymentListFree'][$this->_sections['cnt']['index']]['upper_rule'] > 0):  echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrPaymentListFree'][$this->_sections['cnt']['index']]['upper_rule'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
円<?php else: ?>無制限<?php endif; ?></td>
												</tr>
											</table>
										<td><?php $this->assign('key', ($this->_tpl_vars['arrPaymentListFree'][$this->_sections['cnt']['index']]['deliv_id']));  echo ((is_array($_tmp=@$this->_tpl_vars['arrDelivList'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('default', true, $_tmp, "未登録") : smarty_modifier_default($_tmp, "未登録")); ?>
</td>
										<td align="center"><?php if ($this->_tpl_vars['arrPaymentListFree'][$this->_sections['cnt']['index']]['fix'] != 1): ?><a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="win03('./payment_input.php?mode=pre_edit&payment_id=<?php echo $this->_tpl_vars['arrPaymentListFree'][$this->_sections['cnt']['index']]['payment_id']; ?>
','payment_input','530','400'); return false;">編集</a><?php else: ?>-<?php endif; ?></td>
										<td align="center"><?php if ($this->_tpl_vars['arrPaymentListFree'][$this->_sections['cnt']['index']]['fix'] != 1): ?><a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="fnModeSubmit('delete', 'payment_id', <?php echo $this->_tpl_vars['arrPaymentListFree'][$this->_sections['cnt']['index']]['payment_id']; ?>
); return false;">削除</a><?php else: ?>-<?php endif; ?></td>
										<td align="center">
										<?php if ($this->_sections['cnt']['iteration'] != 1): ?>
										<a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="fnModeSubmit('up','payment_id', <?php echo $this->_tpl_vars['arrPaymentListFree'][$this->_sections['cnt']['index']]['payment_id']; ?>
); return false;">上へ</a>
										<?php endif; ?>
										<?php if ($this->_sections['cnt']['iteration'] != $this->_sections['cnt']['last']): ?>
										<a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="fnModeSubmit('down','payment_id', <?php echo $this->_tpl_vars['arrPaymentListFree'][$this->_sections['cnt']['index']]['payment_id']; ?>
); return false;">下へ</a>
										<?php endif; ?>
										</td>
									</tr>
									<?php endfor; endif; ?>
								</table>

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="5" alt=""></td>
										<td><img src="<?php echo @URL_DIR; ?>
img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
										<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="5" alt=""></td>
									</tr>
									<tr>
										<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="10" alt=""></td>
										<td bgcolor="#e9e7de" align="center">
										<table border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td><input type="button" name="subm2" value="支払方法を追加" onclick="win03('./payment_input.php','payment_input','550','400');" /></td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>
								</td>
								<td background="<?php echo @URL_DIR; ?>
img/contents/main_right.jpg"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr><td height="30"></td></tr>
						</table>
					</td>
				</tr>
				<!--メインエリア-->
			</table>
			<!--▲登録テーブルここまで-->
		</td>
	</tr>
</form>
</table>
<!--★★メインコンテンツ★★-->