<?php /* Smarty version 2.6.13, created on 2007-01-18 18:22:32
         compiled from mail/input.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'mail/input.tpl', 10, false),array('modifier', 'sfGetErrorColor', 'mail/input.tpl', 56, false),array('function', 'html_options', 'mail/input.tpl', 58, false),array('function', 'sfSetErrorStyle', 'mail/input.tpl', 98, false),)), $this); ?>
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
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="200">テンプレート選択<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="507">
										<?php if ($this->_tpl_vars['arrErr']['template_id']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['template_id']; ?>
</span><?php endif; ?>
										<select name="template_id" onchange="return fnInsertValAndSubmit( document.form1, 'mode', 'template', '' ) " style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['template_id'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">選択してください</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrTemplate'],'selected' => $this->_tpl_vars['list_data']['template_id']), $this);?>

										</select>
										</td>
									
																		<?php if (@MELMAGA_BATCH_MODE): ?>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec">配信時間設定<span class="red"> *</span></td>
										<td bgcolor="#ffffff">
										<?php if ($this->_tpl_vars['arrErr']['send_year'] || $this->_tpl_vars['arrErr']['send_month'] || $this->_tpl_vars['arrErr']['send_day'] || $this->_tpl_vars['arrErr']['send_hour'] || $this->_tpl_vars['arrErr']['send_minutes']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['send_year'];  echo $this->_tpl_vars['arrErr']['send_month'];  echo $this->_tpl_vars['arrErr']['send_day'];  echo $this->_tpl_vars['arrErr']['send_hour'];  echo $this->_tpl_vars['arrErr']['send_minutes']; ?>
</span><br><?php endif; ?>
										<select name="send_year" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['send_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrYear'],'selected' => $this->_tpl_vars['arrNowDate']['year']), $this);?>

										</select>年
										<select name="send_month" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['send_month'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getMonth(),'selected' => $this->_tpl_vars['arrNowDate']['month']), $this);?>

										</select>月
										<select name="send_day" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['send_day'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getDay(),'selected' => $this->_tpl_vars['arrNowDate']['day']), $this);?>

										</select>日
										<select name="send_hour" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['send_hour'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getHour(),'selected' => $this->_tpl_vars['arrNowDate']['hour']), $this);?>

										</select>時
										<select name="send_minutes" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['send_minutes'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getMinutesInterval(),'selected' => $this->_tpl_vars['arrNowDate']['minutes']), $this);?>

										</select>分</td>
									</tr>
									<?php endif; ?>
								</table>

								<?php if ($this->_tpl_vars['list_data']['template_id']): ?>
									<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr><td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
									</table>
	
	
									<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
										<tr>
											<td bgcolor="#f2f1ec" class="fs12n">Subject<span class="red"> *</span></td>
											<td bgcolor="#ffffff" class="fs12n">
											<?php if ($this->_tpl_vars['arrErr']['subject']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['subject']; ?>
</span><?php endif; ?>
											<input type="text" name="subject" size="65" class="box65" <?php if ($this->_tpl_vars['arrErr']['subject']):  echo sfSetErrorStyle(array(), $this); endif; ?> value="<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['subject'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
											</td>
										</tr>
										<tr>
											<td bgcolor="#f2f1ec" colspan="2" class="fs12n">本文<span class="red"> *</span>（名前差し込み時は {name} といれてください）</td>
										</tr>
										<tr>
											<td bgcolor="#ffffff" colspan="2" class="fs12n">
											<?php if ($this->_tpl_vars['arrErr']['body']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['body']; ?>
</span><?php endif; ?>
											<textarea name="body" cols="90" rows="40" class="area90" <?php if ($this->_tpl_vars['arrErr']['body']):  echo sfSetErrorStyle(array(), $this); endif; ?>><?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['body'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea>
											</td>
										</tr>
									</table>
								<?php endif; ?>

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
													<input type="hidden" name="mail_method" value="<?php echo $this->_tpl_vars['list_data']['mail_method']; ?>
">

													<input type="image" onMouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_search_back_on.jpg',this)" onMouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_search_back.jpg',this)" src="<?php echo @URL_DIR; ?>
img/contents/btn_search_back.jpg" width="123" height="24" alt="検索画面に戻る" border="0" name="subm02" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'back', '' )">
													<input type="image" onMouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_confirm_on.jpg',this)" onMouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_confirm.jpg',this)" src="<?php echo @URL_DIR; ?>
img/contents/btn_confirm.jpg" width="123" height="24" alt="確認ページへ" border="0" name="subm03" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'regist_confirm', '' )" >
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
</form>
</table>
<!--★★メインコンテンツ★★-->