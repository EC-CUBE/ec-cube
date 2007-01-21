<?php /* Smarty version 2.6.13, created on 2007-01-18 18:22:50
         compiled from mail/input_confirm.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'mail/input_confirm.tpl', 10, false),array('modifier', 'nl2br', 'mail/input_confirm.tpl', 77, false),array('modifier', 'sfGetEnabled', 'mail/input_confirm.tpl', 99, false),)), $this); ?>
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<?php $_from = $this->_tpl_vars['arrHidden']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['val']):
?>	
	<input type="hidden" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['val'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<?php endforeach; endif; unset($_from); ?>
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->配信設定：配信内容設定</span></td>
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
									<?php if (@MELMAGA_BATCH_MODE): ?>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec">配信時間設定<span class="red"> *</span></td>
										<td bgcolor="#ffffff">
										<?php echo $this->_tpl_vars['list_data']['send_year']; ?>
年<?php echo $this->_tpl_vars['list_data']['send_month']; ?>
月<?php echo $this->_tpl_vars['list_data']['send_day']; ?>
日
										<?php echo $this->_tpl_vars['list_data']['send_hour']; ?>
時<?php echo $this->_tpl_vars['list_data']['send_minutes']; ?>
分
										</td>
									</tr>
									<?php endif; ?>
									<!--▼インクルードここから-->
									<?php if ($this->_tpl_vars['list_data']['template_id']): ?>
									<tr>
										<td bgcolor="#f2f1ec" class="fs12n">Subject<span class="red"> *</span></td>
										<td bgcolor="#ffffff" class="fs12n"><?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['subject'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
									</tr>
									<?php if ($this->_tpl_vars['list_data']['mail_method'] != 2): ?>
									<tr>
										<td bgcolor="#ffffff" colspan="2" class="fs10n"><a href="#" onClick="return document.form2.submit();">HTMLで確認</a></td>
									</tr>
									<?php endif; ?>
									<?php if ($_POST['template_mode'] != 'html_template'): ?>
									<tr>
										<td bgcolor="#f2f1ec" colspan="2" class="fs10n">本文<span class="red"> *</span>（名前差し込み時は {name} といれてください）</td>
									</tr>
									<tr>
										<td bgcolor="#ffffff" colspan="2" class="fs10n"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['list_data']['body'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</td>
									</tr>
									<?php endif; ?>
									<?php endif; ?>
									<!--▲インクルードここまで-->
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
												<td>
													<input type="hidden" name="mode" value="template">
													<input type="button" name="subm02" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'regist_back', '' )" value="テンプレート設定画面へ戻る" />
													<?php if (@MELMAGA_BATCH_MODE): ?>
													<input type="button" name="subm03" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'regist_complete', '' )" value="配信を予約する" <?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['template_id'])) ? $this->_run_mod_handler('sfGetEnabled', true, $_tmp) : sfGetEnabled($_tmp)); ?>
/>
													<?php else: ?>
													<input type="button" name="subm03" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'regist_complete', '' )" value="配信する" <?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['template_id'])) ? $this->_run_mod_handler('sfGetEnabled', true, $_tmp) : sfGetEnabled($_tmp)); ?>
/>
													<?php endif; ?>
													</form>
													<form name="form2" id="form2" method="post" action="./preview.php" target="_blank">
													<input type="hidden" name="subject" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['subject'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
													<input type="hidden" name="body" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['body'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
													</form>
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
</table>
<!--★★メインコンテンツ★★-->