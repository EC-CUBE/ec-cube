<?php /* Smarty version 2.6.13, created on 2007-01-17 13:45:43
         compiled from order/edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'order/edit.tpl', 21, false),array('modifier', 'sfGetErrorColor', 'order/edit.tpl', 71, false),array('modifier', 'sfDispDBDate', 'order/edit.tpl', 79, false),array('modifier', 'default', 'order/edit.tpl', 79, false),array('modifier', 'nl2br', 'order/edit.tpl', 130, false),array('modifier', 'sfPreTax', 'order/edit.tpl', 262, false),array('modifier', 'number_format', 'order/edit.tpl', 262, false),array('modifier', 'sfMultiply', 'order/edit.tpl', 263, false),array('modifier', 'count', 'order/edit.tpl', 361, false),array('function', 'html_options', 'order/edit.tpl', 73, false),)), $this); ?>
<script type="text/javascript">
<!--
	function fnEdit(customer_id) {
		document.form1.action = '/admin/customer/edit.php';
		document.form1.mode.value = "edit"
		document.form1['edit_customer_id'].value = customer_id;
		document.form1.submit();
		return false;
	}
//-->
</script>
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="order_id" value="<?php echo $this->_tpl_vars['tpl_order_id']; ?>
">
<input type="hidden" name="edit_customer_id" value="<?php echo $this->_tpl_vars['tpl_order_id']; ?>
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
		<td class="mainbg" >
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
						
						<!--登録テーブルここから-->
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td background="<?php echo @URL_DIR; ?>
img/contents/contents_title_left_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->受注履歴編集</span></td>
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

						<!--▼お客様情報ここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">対応状況</td>
								<td bgcolor="#ffffff">
									<?php $this->assign('key', 'status'); ?>
									<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
									<select name="<?php echo $this->_tpl_vars['key']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">選択してください</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrORDERSTATUS'],'selected' => $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value']), $this);?>

									</select>
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">発送日</td>
								<td bgcolor="#ffffff"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrDisp']['commit_date'])) ? $this->_run_mod_handler('sfDispDBDate', true, $_tmp) : sfDispDBDate($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, "未発送") : smarty_modifier_default($_tmp, "未発送")); ?>
</td>
							</tr>
						</table>
						
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
						</table>
						
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<?php $_from = $this->_tpl_vars['arrSearchHidden']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
								<input type="hidden" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['item'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
							<?php endforeach; endif; unset($_from); ?>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="717" colspan="4">▼お客様情報</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">受注番号</td>
								<td bgcolor="#ffffff" width="248"><?php echo $this->_tpl_vars['arrDisp']['order_id']; ?>
</td>
								<td bgcolor="#f2f1ec" width="110">顧客ID</td>
								<td bgcolor="#ffffff" width="249">
								<?php if ($this->_tpl_vars['arrDisp']['customer_id'] > 0): ?>
									<?php echo $this->_tpl_vars['arrDisp']['customer_id']; ?>

								<?php else: ?>
									（非会員）
								<?php endif; ?>
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">受注日</td>
								<td bgcolor="#ffffff" width="607" colspan="3"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrDisp']['create_date'])) ? $this->_run_mod_handler('sfDispDBDate', true, $_tmp) : sfDispDBDate($_tmp)); ?>
</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">顧客名</td>
								<td bgcolor="#ffffff" width="248"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrDisp']['order_name01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['arrDisp']['order_name02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
								<td bgcolor="#f2f1ec" width="110">顧客名（カナ）</td>
								<td bgcolor="#ffffff" width="249"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrDisp']['order_kana01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['arrDisp']['order_kana02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">メールアドレス</td>
								<td bgcolor="#ffffff" width="248"><a href="mailto:<?php echo ((is_array($_tmp=$this->_tpl_vars['arrDisp']['order_email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrDisp']['order_email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a></td>
								<td bgcolor="#f2f1ec" width="110">TEL</td>
								<td bgcolor="#ffffff" width="249"><?php echo $this->_tpl_vars['arrDisp']['order_tel01']; ?>
-<?php echo $this->_tpl_vars['arrDisp']['order_tel02']; ?>
-<?php echo $this->_tpl_vars['arrDisp']['order_tel03']; ?>
</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">住所</td>
								<td bgcolor="#ffffff" width="607" colspan="3">〒<?php echo $this->_tpl_vars['arrDisp']['order_zip01']; ?>
-<?php echo $this->_tpl_vars['arrDisp']['order_zip02']; ?>
<br>
								<?php $this->assign('key', $this->_tpl_vars['arrDisp']['order_pref']); ?>
								<?php echo $this->_tpl_vars['arrPref'][$this->_tpl_vars['key']];  echo $this->_tpl_vars['arrDisp']['order_addr01'];  echo $this->_tpl_vars['arrDisp']['order_addr02']; ?>
</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">備考</td>
								<td bgcolor="#ffffff" width="607" colspan="3"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrDisp']['message'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</td>
							</tr>
						</table>
						<!--▲お客様情報ここまで-->
						
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
						</table>
						
						<!--▼配送先情報ここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="717" colspan="4">▼配送先情報</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">お名前</td>
								<td bgcolor="#ffffff" width="248">
								<?php $this->assign('key1', 'deliv_name01'); ?>
								<?php $this->assign('key2', 'deliv_name02'); ?>
								<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key1']];  echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key2']]; ?>
</span>
								<input type="text" name="<?php echo $this->_tpl_vars['key1']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key1']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key1']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key1']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="15" class="box15" />
								<input type="text" name="<?php echo $this->_tpl_vars['key2']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key2']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key2']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key2']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="15" class="box15" />
								</td>
								<td bgcolor="#f2f1ec" width="110">お名前（カナ）</td>
								<td bgcolor="#ffffff" width="249">
								<?php $this->assign('key1', 'deliv_kana01'); ?>
								<?php $this->assign('key2', 'deliv_kana02'); ?>
								<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key1']];  echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key2']]; ?>
</span>
								<input type="text" name="<?php echo $this->_tpl_vars['key1']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key1']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key1']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key1']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="15" class="box15" />
								<input type="text" name="<?php echo $this->_tpl_vars['key2']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key2']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key2']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key2']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="15" class="box15" />
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">郵便番号</td>
								<td bgcolor="#ffffff" width="248">
								<?php $this->assign('key1', 'deliv_zip01'); ?>
								<?php $this->assign('key2', 'deliv_zip02'); ?>
								<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key1']];  echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key2']]; ?>
</span>
								〒
								<input type="text" name="<?php echo $this->_tpl_vars['key1']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key1']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key1']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key1']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"  size="6" class="box6" />
								 - 
								<input type="text"  name="<?php echo $this->_tpl_vars['key2']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key2']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key2']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key2']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"  size="6" class="box6" />
								<input type="button" name="address_input" value="住所入力" onclick="fnCallAddress('<?php echo @URL_INPUT_ZIP; ?>
', 'deliv_zip01', 'deliv_zip02', 'deliv_pref', 'deliv_addr01');" />
								</td>
								<td bgcolor="#f2f1ec" width="110">TEL</td>
								<td bgcolor="#ffffff" width="249">
								<?php $this->assign('key1', 'deliv_tel01'); ?>
								<?php $this->assign('key2', 'deliv_tel02'); ?>
								<?php $this->assign('key3', 'deliv_tel03'); ?>
								<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key1']]; ?>
</span>
								<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key2']]; ?>
</span>
								<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key3']]; ?>
</span>
								<input type="text" name="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key1']]['keyname']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key1']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key1']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key1']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="6" class="box6" /> - 
								<input type="text" name="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key2']]['keyname']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key2']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key2']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key2']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"  size="6" class="box6" /> - 
								<input type="text" name="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key3']]['keyname']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key3']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key3']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key3']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="6" class="box6" />
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec">住所</td>
								<td bgcolor="#ffffff" colspan="3">
								<table border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
									<td>
										<?php $this->assign('key', 'deliv_pref'); ?>
										<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
										<select name="<?php echo $this->_tpl_vars['key']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">							
										<option value="" selected="">都道府県を選択</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrPref'],'selected' => $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value']), $this);?>

										</select>
									</td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr class="fs10n">
										<td>
										<?php $this->assign('key', 'deliv_addr01'); ?>
										<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
										<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="60" class="box60" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" />
										</td>
										</tr>
										<tr><td height="5"></td></tr>
										<tr class="fs10n">
											<td>
											<?php $this->assign('key', 'deliv_addr02'); ?>
											<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
											<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="60" class="box60" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" />
											</td>
										</tr>
								</table>
								</td>
							</tr>
						</table>
						<!--▲配送先情報ここまで-->

						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
						</table>

						<!--▼受注商品情報ここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="717" colspan="7">▼受注商品情報
								<input type="button" name="cheek" value="計算結果の確認" onclick="fnModeSubmit('cheek','','');" />
								<br />
								<span class="red12"><?php echo $this->_tpl_vars['arrErr']['quantity']; ?>
</span>
								<span class="red12"><?php echo $this->_tpl_vars['arrErr']['price']; ?>
</span>
								</td>
							</tr>
							<tr bgcolor="#f2f1ec" align="center" class="fs12n">
								<td width="140">商品コード</td>
								<td width="215">商品名/規格1/規格2</td>
								<td width="84">単価</td>
								<td width="45">数量</td>
								<td width="84">税込み価格</td>
								<td width="94">小計</td>
							</tr>
							<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrForm']['quantity']['value']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
							<?php $this->assign('key', ($this->_sections['cnt']['index'])); ?>
							<tr bgcolor="#ffffff" class="fs12">
								<td width="140"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrDisp']['product_code'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
								<td width="215"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrDisp']['product_name'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
/<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrDisp']['classcategory_name1'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, "(なし)") : smarty_modifier_default($_tmp, "(なし)")); ?>
/<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrDisp']['classcategory_name2'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, "(なし)") : smarty_modifier_default($_tmp, "(なし)")); ?>
</td>
								<input type="hidden" name="product_id[]" value="<?php echo $this->_tpl_vars['arrDisp']['product_id'][$this->_tpl_vars['key']]; ?>
">
								<input type="hidden" name="product_code[]" value="<?php echo $this->_tpl_vars['arrDisp']['product_code'][$this->_tpl_vars['key']]; ?>
">
								<input type="hidden" name="product_name[]" value="<?php echo $this->_tpl_vars['arrDisp']['product_name'][$this->_tpl_vars['key']]; ?>
">
								<input type="hidden" name="point_rate[]" value="<?php echo $this->_tpl_vars['arrDisp']['point_rate'][$this->_tpl_vars['key']]; ?>
">	
								<input type="hidden" name="classcategory_id1[]" value="<?php echo $this->_tpl_vars['arrDisp']['classcategory_id1'][$this->_tpl_vars['key']]; ?>
">	
								<input type="hidden" name="classcategory_id2[]" value="<?php echo $this->_tpl_vars['arrDisp']['classcategory_id2'][$this->_tpl_vars['key']]; ?>
">
								<input type="hidden" name="classcategory_name1[]" value="<?php echo $this->_tpl_vars['arrDisp']['classcategory_name1'][$this->_tpl_vars['key']]; ?>
">	
								<input type="hidden" name="classcategory_name2[]" value="<?php echo $this->_tpl_vars['arrDisp']['classcategory_name2'][$this->_tpl_vars['key']]; ?>
">				
								<td width="84" align="center"><input type="text" name="price[]" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['price']['value'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="6" class="box6" maxlength="<?php echo $this->_tpl_vars['arrForm']['price']['length']; ?>
"/> 円</td>
								<td width="45" align="center"><input type="text" name="quantity[]" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['quantity']['value'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="3" class="box3" maxlength="<?php echo $this->_tpl_vars['arrForm']['quantity']['length']; ?>
"/></td>
								<?php $this->assign('price', ($this->_tpl_vars['arrForm']['price']['value'][$this->_tpl_vars['key']])); ?>
								<?php $this->assign('quantity', ($this->_tpl_vars['arrForm']['quantity']['value'][$this->_tpl_vars['key']])); ?>
								<td width="84" align="right"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['price'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 円</td>
								<td width="94" align="right"><?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['price'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule'])))) ? $this->_run_mod_handler('sfMultiply', true, $_tmp, $this->_tpl_vars['quantity']) : sfMultiply($_tmp, $this->_tpl_vars['quantity'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
円</td>
							</tr>
							<?php endfor; endif; ?>
							<tr bgcolor="#ffffff" class="fs12n">
								<td colspan="5" align="right">小計</td>
								<td align="right"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['subtotal']['value'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
円</td>
							</tr>
							<tr bgcolor="#ffffff" class="fs12n">
								<td colspan="5" align="right">値引</td>
								<td align="right">
							<?php $this->assign('key', 'discount'); ?>
							<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
							<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"  size="5" class="box6" />
							 円</td>
							</tr>
							<tr bgcolor="#ffffff" class="fs12n">
								<td colspan="5" align="right">送料</td>
								<td align="right">
							<?php $this->assign('key', 'deliv_fee'); ?>
							<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
							<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"  size="5" class="box6" />
							 円</td>
							</tr>
							<tr bgcolor="#ffffff" class="fs12n">
								<td colspan="5" align="right">手数料</td>
								<td align="right">
							<?php $this->assign('key', 'charge'); ?>
							<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
							<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"  size="5" class="box6" />
							 円</td>
							</tr>
							<tr bgcolor="#ffffff" class="fs12n">
								<td colspan="5" align="right">合計</td>
								<td align="right">
								<span class="red12"><?php echo $this->_tpl_vars['arrErr']['total']; ?>
</span>
								<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['total']['value'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 円</td>
							</tr>
							<tr bgcolor="#ffffff" class="fs12n">
								<td colspan="5" align="right">お支払い合計</td>
								<td align="right">
								<span class="red12"><?php echo $this->_tpl_vars['arrErr']['payment_total']; ?>
</span>
								<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['payment_total']['value'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

								 円</td>
							</tr>
							<tr bgcolor="#ffffff" class="fs12n">
								<td colspan="5" align="right">使用ポイント</td>
								<td align="right">
								<?php $this->assign('key', 'use_point'); ?>
								<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
								<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"  size="5" class="box6" />
								 pt</td>
							</tr>
							<?php if ($this->_tpl_vars['arrForm']['birth_point']['value'] > 0): ?>
							<tr bgcolor="#ffffff" class="fs12n">
								<td colspan="5" align="right">お誕生日ポイント</td>
								<td align="right">
								<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['birth_point']['value'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

								 pt</td>
							</tr>
							<?php endif; ?>
							<tr bgcolor="#ffffff" class="fs12n">
								<td colspan="5" align="right">加算ポイント</td>
								<td align="right">
								<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrForm']['add_point']['value'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>

								 pt</td>
							</tr>
							<tr bgcolor="#ffffff" class="fs12n">
								<?php if ($this->_tpl_vars['arrDisp']['customer_id'] > 0): ?>
								<td colspan="5" align="right">現在ポイント（ポイントの修正は<a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="return fnEdit('<?php echo $this->_tpl_vars['arrDisp']['customer_id']; ?>
');">顧客編集</a>から手動にてお願い致します。）</td>
								<td align="right">
								<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['point']['value'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

								 pt</td>
								<?php else: ?>
								<td colspan="5" align="right">現在ポイント</td><td align="center">（なし）</td>
								<?php endif; ?>
							</tr>
														<tr class="fs12n">
								<td bgcolor="#f2f1ec" colspan="6">▼お支払方法<span class="red">（お支払方法の変更に伴う手数料の変更は手動にてお願いします。)</span></td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#ffffff" colspan="6">
								<?php $this->assign('key', 'payment_id'); ?>
								<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
								<select name="<?php echo $this->_tpl_vars['key']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
								<option value="" selected="">選択してください</option>
								<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrPayment'],'selected' => $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value']), $this);?>

								</select></td>
							</tr>
							
							<?php if (count($this->_tpl_vars['arrDisp']['payment_info']) > 0): ?>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" colspan="6">▼<?php echo $this->_tpl_vars['arrDisp']['payment_type']; ?>
情報</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#ffffff" colspan="6">
									<?php $_from = $this->_tpl_vars['arrDisp']['payment_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
									<?php if ($this->_tpl_vars['key'] != 'title'):  if ($this->_tpl_vars['item']['name'] != ""):  echo $this->_tpl_vars['item']['name']; ?>
：<?php endif;  echo $this->_tpl_vars['item']['value']; ?>
<br/><?php endif; ?>
									<?php endforeach; endif; unset($_from); ?>
								</td>
							</tr>
							<?php endif; ?>
							
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" colspan="6">▼時間指定</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#ffffff" colspan="6">
								<?php $this->assign('key', 'deliv_time_id'); ?>
								<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
								<select name="<?php echo $this->_tpl_vars['key']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">	
								<option value="" selected="0">指定無し</option>
								<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrDelivTime'],'selected' => $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value']), $this);?>

								</select>
								</td>
							</tr>
							<?php $this->assign('key', 'deliv_date'); ?>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" colspan="6">▼配達日指定</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#ffffff" colspan="6">
								<?php $this->assign('key', 'deliv_date'); ?>
								<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
								<?php echo ((is_array($_tmp=@$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('default', true, $_tmp, "指定なし") : smarty_modifier_default($_tmp, "指定なし")); ?>

								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" colspan="6">▼メモ</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#ffffff" colspan="6">
								<?php $this->assign('key', 'note'); ?>
								<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
								<textarea name="<?php echo $this->_tpl_vars['key']; ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" cols="80" rows="6" class="area80" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" ><?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea></td>
								</td>
							</tr>
						</table>
						<!--▲受注商品情報ここまで-->
				
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
										<td>
											<?php if (count ( $this->_tpl_vars['arrSearchHidden'] ) > 0): ?>		
											<a href="#" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/contents/btn_search_back_on.jpg','back');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/contents/btn_search_back.jpg','back');" onclick="fnChangeAction('<?php echo @URL_SEARCH_ORDER; ?>
'); fnModeSubmit('search','',''); return false;"><img src="<?php echo @URL_DIR; ?>
img/contents/btn_search_back.jpg" width="123" height="24" alt="検索画面に戻る" border="0" name="back"></a>
											<?php endif; ?>
											<input type="image" onMouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_regist.jpg',this)" src="<?php echo @URL_DIR; ?>
img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" onclick="return fnConfirm();">
										</td>
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

						<!--登録テーブルここまで-->
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
		</td>
	</tr>
</form>
</table>
<!--★★メインコンテンツ★★-->		