<?php /* Smarty version 2.6.13, created on 2007-01-09 23:50:41
         compiled from home.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'home.tpl', 58, false),array('modifier', 'number_format', 'home.tpl', 58, false),array('modifier', 'escape', 'home.tpl', 92, false),)), $this); ?>
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td class="mainbg">
		<table width="588" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<!--メインエリア-->
			<tr>
				<td align="center">
				<table width="562" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<form name="form1" method="post" action="#">
					<tr><td height="14"></td></tr>
					<tr>
						<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/home_top.jpg" width="562" height="14" alt=""></td>
					</tr>
					<tr>
						<td background="<?php echo @URL_DIR; ?>
img/contents/main_left.jpg"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="14" height="1" alt=""></td>
						<td bgcolor="#cccccc">
						<!--システム情報ここから-->
						<table width="534" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td><img src="<?php echo @URL_DIR; ?>
img/contents/homettl_system.gif" width="534" height="26" alt="システム情報"></td>
							</tr>
							<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/home_bar.jpg" width="534" height="10" alt=""></td></tr>
						</table>
						<table width="534" border="0" cellspacing="1" cellpadding="4" summary=" ">
							<tr>
								<td bgcolor="#f2f1ec" width="178" class="fs12">EC-CUBEバージョン</td>
								<td bgcolor="#ffffff" width="337" class="fs12" align="right"><?php echo @ECCUBE_VERSION; ?>
</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">PHPバージョン</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><?php echo $this->_tpl_vars['php_version']; ?>
</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">DBバージョン</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><?php echo $this->_tpl_vars['db_version']; ?>
</td>
							</tr>							
						</table>
						
						<!--ショップの状況ここから-->
						<table width="534" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/home_bar02.jpg" width="534" height="20" alt=""></td></tr>
							<tr>
								<td><img src="<?php echo @URL_DIR; ?>
img/contents/homettl_shop.gif" width="534" height="26" alt="ショップの状況"></td>
							</tr>
							<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/home_bar.jpg" width="534" height="10" alt=""></td></tr>
						</table>
						<table width="534" border="0" cellspacing="1" cellpadding="4" summary=" ">
							<tr>
								<td bgcolor="#f2f1ec" width="178" class="fs12">現在の会員数</td>
								<td bgcolor="#ffffff" width="337" class="fs12" align="right"><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['customer_cnt'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
名</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">昨日の売上高</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['order_yesterday_amount'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
円</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">昨日の売上件数</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['order_yesterday_cnt'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
件</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec"><span class="fs12">今月の売上高</span><span class="fs10">(昨日まで) </span></td>
								<td bgcolor="#ffffff" class="fs12" align="right"><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['order_month_amount'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
円</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec"><span class="fs12">今月の売上件数 </span><span class="fs10">(昨日まで) </span></td>
								<td bgcolor="#ffffff" class="fs12" align="right"><?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['order_month_cnt'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
件</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">昨日のレビュー書き込み数</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><?php echo ((is_array($_tmp=@$this->_tpl_vars['review_yesterday_cnt'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
件</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">顧客の保持ポイント合計</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><?php echo ((is_array($_tmp=@$this->_tpl_vars['customer_point'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
pt</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">レビュー書き込み非表示数</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><?php echo ((is_array($_tmp=@$this->_tpl_vars['review_nondisp_cnt'])) ? $this->_run_mod_handler('default', true, $_tmp, '0') : smarty_modifier_default($_tmp, '0')); ?>
件</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">品切れ商品</td>
								<td bgcolor="#ffffff" class="fs12">
								<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['arrSoldout']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
								<?php echo $this->_tpl_vars['arrSoldout'][$this->_sections['i']['index']]['product_id']; ?>
:<?php echo ((is_array($_tmp=$this->_tpl_vars['arrSoldout'][$this->_sections['i']['index']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<br>
								<?php endfor; endif; ?>			
								</td>
							</tr>
						</table>
						<!--ショップの状況ここまで-->
						<table width="534" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/home_bar02.jpg" width="534" height="20" alt=""></td></tr>
							<tr>
								<td><img src="<?php echo @URL_DIR; ?>
img/contents/homettl_list.gif" width="534" height="26" alt="新規受付一覧"></td>
							</tr>
							<tr><td><img src="<?php echo @URL_DIR; ?>
img/contents/home_bar.jpg" width="534" height="10" alt=""></td></tr>
						</table>
						<!--新規受付一覧ここから-->
						<table width="534" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr bgcolor="#636469" align="center" class="fs10n">
								<td width="100"><span class="white">受注日</span></td>
								<td width="90"><span class="white">顧客名</span></td>
								<td width="159"><span class="white">購入商品</span></td>
								<td width="70"><span class="white">支払方法</span></td>
								<td width="70"><span class="white">購入金額(円)</span></td>
							</tr>
							<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['arrNewOrder']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
							<tr bgcolor="#ffffff" class="fs10">
								<td><?php echo $this->_tpl_vars['arrNewOrder'][$this->_sections['i']['index']]['create_date']; ?>
</td>
								<td><?php echo ((is_array($_tmp=$this->_tpl_vars['arrNewOrder'][$this->_sections['i']['index']]['name01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['arrNewOrder'][$this->_sections['i']['index']]['name02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
								<td><?php echo ((is_array($_tmp=$this->_tpl_vars['arrNewOrder'][$this->_sections['i']['index']]['product_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
								<td><?php echo ((is_array($_tmp=$this->_tpl_vars['arrNewOrder'][$this->_sections['i']['index']]['payment_method'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
								<td align="right"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrNewOrder'][$this->_sections['i']['index']]['total'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
円</td>
							</tr>
							<?php endfor; endif; ?>
						</table>
						<!--新規受付一覧ここまで-->
						</td>
						<td background="<?php echo @URL_DIR; ?>
img/contents/main_right.jpg"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="14" height="1" alt=""></td>
					</tr>
					<tr>
						<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/home_bottom.jpg" width="562" height="14" alt=""></td>
					</tr>
					<tr><td height="30"></td></tr>
				</form>
				</table>
				</td>
			</tr>
			<!--メインエリア-->
		</table>
		</td>
		<td bgcolor="#a8a8a8"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="10" alt=""></td>
		<td class="infobg" bgcolor="#e3e3e3">
		<table width="288" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td align="center">
				<table width="266" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<!--お知らせここから-->
					<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['arrInfo']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
					<tr><td height="15"></td></tr>
					<tr>
						<td><img src="<?php echo @URL_DIR; ?>
img/contents/infottl_top_left.jpg" width="12" height="5" alt="" border="0"></td>
						<td><img src="<?php echo @URL_DIR; ?>
img/contents/infottl_top.jpg" width="249" height="5" alt="" border="0"></td>
						<td><img src="<?php echo @URL_DIR; ?>
img/contents/infottl_top_right.jpg" width="5" height="5" alt="" border="0"></td>
					</tr>
					<tr>
						<td background="<?php echo @URL_DIR; ?>
img/contents/infottl_day_left.jpg"><img src="<?php echo @URL_DIR; ?>
img/contents/infottl_day_left.jpg" width="12" height="10" alt="" border="0"></td>
						<td background="<?php echo @URL_DIR; ?>
img/contents/infottl_bg01.jpg"><img src="<?php echo @URL_DIR; ?>
img/contents/icon.jpg" width="15" height="9" alt=""><span class="infodate"><?php echo $this->_tpl_vars['arrInfo'][$this->_sections['i']['index']][0]; ?>
</span></td>
						<td background="<?php echo @URL_DIR; ?>
img/contents/infottl_day_right.jpg"><img src="<?php echo @URL_DIR; ?>
img/contents/infottl_day_right.jpg" width="5" height="10" alt="" border="0"></td>
					</tr>
					<tr><td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/infottl_middle.jpg" width="266" height="8" alt="" border="0"></td></tr>
					<tr>
						<td background="<?php echo @URL_DIR; ?>
img/contents/infottl_bottom_left.jpg"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="12" height="1" alt=""></td>
						<td background="<?php echo @URL_DIR; ?>
img/contents/infottl_bg02.jpg" class="infottl"><?php echo $this->_tpl_vars['arrInfo'][$this->_sections['i']['index']][1]; ?>
</td>
						<td background="<?php echo @URL_DIR; ?>
img/contents/infottl_bottom_right.jpg"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="5" height="1" alt=""></td>
					</tr>
					<tr><td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/infottl_bottom.jpg" width="266" height="7" alt="" border="0"></td></tr>
					<tr><td height="5"></td></tr>
					<tr>
						<td colspan="3" class="fs10"><span class="info"><?php echo $this->_tpl_vars['arrInfo'][$this->_sections['i']['index']][2]; ?>
</span></td>
					</tr>
					<?php endfor; endif; ?>
					<!--お知らせここまで-->
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<!--★★メインコンテンツ★★-->		
<!--▲CONTENTS-->