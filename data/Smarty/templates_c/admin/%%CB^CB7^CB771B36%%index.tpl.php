<?php /* Smarty version 2.6.13, created on 2007-01-10 18:34:50
         compiled from mail/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'mail/index.tpl', 10, false),array('modifier', 'sfGetErrorColor', 'mail/index.tpl', 54, false),array('modifier', 'default', 'mail/index.tpl', 383, false),array('modifier', 'sfDispDBDate', 'mail/index.tpl', 398, false),array('function', 'html_options', 'mail/index.tpl', 68, false),array('function', 'html_checkboxes', 'mail/index.tpl', 80, false),array('function', 'html_radios', 'mail/index.tpl', 95, false),array('function', 'sfSetErrorStyle', 'mail/index.tpl', 117, false),)), $this); ?>
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form_search" id="form_search" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input type="hidden" name="mode" value="search">
	<tr valign="top">
		<td background="<?php echo @URL_DIR; ?>
img/contents/navi_bg.gif" height="402">
			<!-- サブナビ -->
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['tpl_subnavi'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</td>
		<td class="mainbg">
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
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->配信先検索条件設定</span></td>
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
						
						<!--検索条件設定テーブルここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
				
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">顧客名</td>
								<td bgcolor="#ffffff" width="194">
									<?php if ($this->_tpl_vars['arrErr']['name']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['name']; ?>
</span><br><?php endif; ?>
									<input type="text" name="name" maxlength="<?php echo @STEXT_LEN; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="30" class="box30"  style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['name'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" />
								</td>
								<td bgcolor="#f2f1ec" width="110">顧客名（カナ）</td>
								<td bgcolor="#ffffff" width="195">
									<?php if ($this->_tpl_vars['arrErr']['kana']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['kana']; ?>
</span><br><?php endif; ?>
									<input type="text" name="kana" maxlength="<?php echo @STEXT_LEN; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['kana'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="30" class="box30"  style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['kana'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" />
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">都道府県</td>
								<td bgcolor="#ffffff" width="194">
									<?php if ($this->_tpl_vars['arrErr']['pref']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['pref']; ?>
</span><br><?php endif; ?>
									<select name="pref">
										<option value="" selected="selected"  style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['pref'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">都道府県を選択</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrPref'],'selected' => $this->_tpl_vars['list_data']['pref']), $this);?>

									</select>
								</td>
								<td bgcolor="#f2f1ec" width="110">TEL</td>
								<td bgcolor="#ffffff" width="195">
									<?php if ($this->_tpl_vars['arrErr']['tel']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['tel']; ?>
</span><br><?php endif; ?>
									<input type="text" name="tel" maxlength="<?php echo @TEL_LEN; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['tel'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="30" class="box30" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['tel'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" />
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">性別</td>
								<td bgcolor="#ffffff" width="194">
									<?php echo smarty_function_html_checkboxes(array('name' => 'sex','options' => $this->_tpl_vars['arrSex'],'separator' => "&nbsp;",'selected' => $this->_tpl_vars['list_data']['sex']), $this);?>

								</td>
								<td bgcolor="#f2f1ec" width="110">誕生月</td>
								<td bgcolor="#ffffff" width="195">
									<?php if ($this->_tpl_vars['arrErr']['birth_month']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['birth_month']; ?>
</span><br><?php endif; ?>
									<select name="birth_month" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['birth_month'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" >
										<option value="" selected="selected">--</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getMonth(),'selected' => ((is_array($_tmp=$this->_tpl_vars['list_data']['birth_month'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp))), $this);?>

									</select>月
								</td>				
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">配信形式</td>
								<td bgcolor="#ffffff" width="194">
									<?php if ($this->_tpl_vars['arrErr']['htmlmail']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['htmlmail']; ?>
</span><br /><?php endif; ?>
									<?php echo smarty_function_html_radios(array('name' => 'htmlmail','options' => $this->_tpl_vars['arrHtmlmail'],'separator' => "&nbsp;",'selected' => $this->_tpl_vars['list_data']['htmlmail']), $this);?>

								</td>
								<td bgcolor="#f2f1ec" width="110">購入回数</td>
								<td bgcolor="#ffffff" width="195">
									<?php if ($this->_tpl_vars['arrErr']['buy_times_from'] || $this->_tpl_vars['arrErr']['buy_times_to']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['buy_times_from'];  echo $this->_tpl_vars['arrErr']['buy_times_to']; ?>
</span><br><?php endif; ?>
									<input type="text" name="buy_times_from" maxlength="<?php echo @INT_LEN; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['buy_times_from'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="6" class="box6" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['buy_times_from'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" /> 回 〜 
									<input type="text" name="buy_times_to" maxlength="<?php echo @INT_LEN; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['buy_times_to'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="6" class="box6" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['buy_times_to'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" /> 回
								</td>
							</tr>
				
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">購入商品<br />コード</td>
								<td bgcolor="#ffffff" width="194">
								<?php if ($this->_tpl_vars['arrErr']['buy_product_code']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['buy_product_code']; ?>
</span><?php endif; ?>
								<input type="text" name="buy_product_code" value="<?php echo $this->_tpl_vars['list_data']['buy_product_code']; ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" size="30" class="box30" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['buy_product_code'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" >
								</td>
				
								<td bgcolor="#f2f1ec" width="110">購入金額</td>
								<td bgcolor="#ffffff" width="195">
									<?php if ($this->_tpl_vars['arrErr']['buy_total_from'] || $this->_tpl_vars['arrErr']['buy_total_to']): ?>
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['buy_total_from'];  echo $this->_tpl_vars['arrErr']['buy_total_to']; ?>
</span><br>
									<?php endif; ?>
									<input type="text" name="buy_total_from" maxlength="<?php echo @INT_LEN; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['buy_total_from'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="6" class="box6" <?php if ($this->_tpl_vars['arrErr']['buy_total_from'] || $this->_tpl_vars['arrErr']['buy_total_to']):  echo sfSetErrorStyle(array(), $this); endif; ?> /> 円 〜
									<input type="text" name="buy_total_to" maxlength="<?php echo @INT_LEN; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['buy_total_to'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="6" class="box6" <?php if ($this->_tpl_vars['arrErr']['buy_total_from'] || $this->_tpl_vars['arrErr']['buy_total_to']):  echo sfSetErrorStyle(array(), $this); endif; ?> /> 円
								</td>
							</tr>
														<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">メールアドレス</td>
								<td bgcolor="#ffffff" colspan="3">
									<?php if ($this->_tpl_vars['arrErr']['email']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['email']; ?>
</span><?php endif; ?>
									<span style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['email'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<input type="text" name="email" maxlength="<?php echo @STEXT_LEN; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="60" class="box60"  style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['email'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"/>
									</span>
								</td>
							</tr>
							
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">職業</td>
								<td bgcolor="#ffffff" width="499" colspan="3">
									<?php if ($this->_tpl_vars['arrErr']['job']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['job']; ?>
</span><?php endif; ?>
									<?php echo smarty_function_html_checkboxes(array('name' => 'job','options' => $this->_tpl_vars['arrJob'],'separator' => "&nbsp;",'selected' => $this->_tpl_vars['list_data']['job']), $this);?>

								</td>
							</tr>
				
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">生年月日</td>
								<td bgcolor="#ffffff" colspan="3">
									<?php if ($this->_tpl_vars['arrErr']['b_start_year'] || $this->_tpl_vars['arrErr']['b_end_year']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['b_start_year'];  echo $this->_tpl_vars['arrErr']['b_end_year']; ?>
</span><br><?php endif; ?>
									<select name="b_start_year" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['b_start_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">----</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getYear(@BIRTH_YEAR),'selected' => $this->_tpl_vars['list_data']['b_start_year']), $this);?>

									</select>年
									<select name="b_start_month" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['b_start_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">--</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getMonth(),'selected' => $this->_tpl_vars['list_data']['b_start_month']), $this);?>

									</select>月
									<select name="b_start_day" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['b_start_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">--</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getDay(),'selected' => $this->_tpl_vars['list_data']['b_start_day']), $this);?>

									</select>日&nbsp;〜&nbsp;
									<select name="b_end_year" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['b_end_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">----</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getYear(@BIRTH_YEAR),'selected' => $this->_tpl_vars['list_data']['b_end_year']), $this);?>

									</select>年
									<select name="b_end_month" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['b_end_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">--</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getMonth(),'selected' => $this->_tpl_vars['list_data']['b_end_month']), $this);?>

									</select>月
									<select name="b_end_day" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['b_end_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">--</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getDay(),'selected' => $this->_tpl_vars['list_data']['b_end_day']), $this);?>

									</select>日
								</td>
							</tr>	
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">登録日</td>
								<td bgcolor="#ffffff" colspan="3">
									<?php if ($this->_tpl_vars['arrErr']['start_year'] || $this->_tpl_vars['arrErr']['end_year']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['start_year'];  echo $this->_tpl_vars['arrErr']['end_year']; ?>
</span><br><?php endif; ?>
									<select name="start_year"  style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['start_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">----</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getYear(@RELEASE_YEAR),'selected' => $this->_tpl_vars['list_data']['start_year']), $this);?>

									</select>年
									<select name="start_month" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['start_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">--</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getMonth(),'selected' => $this->_tpl_vars['list_data']['start_month']), $this);?>

									</select>月
									<select name="start_day" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['start_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">--</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getDay(),'selected' => $this->_tpl_vars['list_data']['start_day']), $this);?>

									</select>日&nbsp;〜&nbsp;
									<select name="end_year"  style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['end_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">----</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getYear(@RELEASE_YEAR),'selected' => $this->_tpl_vars['list_data']['end_year']), $this);?>

									</select>年
									<select name="end_month" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['end_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">--</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getMonth(),'selected' => $this->_tpl_vars['list_data']['end_month']), $this);?>

									</select>月
									<select name="end_day" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['end_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">--</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getDay(),'selected' => $this->_tpl_vars['list_data']['end_day']), $this);?>

									</select>日
								</td>
							</tr>			
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">最終購入日</td>
								<td bgcolor="#ffffff" colspan="3" width="499">
									<?php if ($this->_tpl_vars['arrErr']['buy_start_year'] || $this->_tpl_vars['arrErr']['buy_end_year']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['buy_start_year'];  echo $this->_tpl_vars['arrErr']['buy_end_year']; ?>
</span><br><?php endif; ?>
									<select name="buy_start_year" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['buy_start_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">----</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getYear(@RELEASE_YEAR),'selected' => $this->_tpl_vars['list_data']['buy_start_year']), $this);?>

									</select>年
									<select name="buy_start_month" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['buy_start_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">--</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getMonth(),'selected' => $this->_tpl_vars['list_data']['buy_start_month']), $this);?>

									</select>月
									<select name="buy_start_day" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['buy_start_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">--</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getDay(),'selected' => $this->_tpl_vars['list_data']['buy_start_day']), $this);?>

									</select>日&nbsp;〜&nbsp;
									<select name="buy_end_year" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['buy_end_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">----</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getYear(@RELEASE_YEAR),'selected' => $this->_tpl_vars['list_data']['buy_end_year']), $this);?>

									</select>年
									<select name="buy_end_month" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['buy_end_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">--</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getMonth(),'selected' => $this->_tpl_vars['list_data']['buy_end_month']), $this);?>

									</select>月
									<select name="buy_end_day" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['buy_end_year'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="" selected="selected">--</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['objDate']->getDay(),'selected' => $this->_tpl_vars['list_data']['buy_end_day']), $this);?>

									</select>日
								</td>
							</tr>
				
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">購入商品名</td>
								<td bgcolor="#ffffff" width="194">
									<?php if ($this->_tpl_vars['arrErr']['buy_product_name']): ?><span class="red12"><?php echo $this->_tpl_vars['arrErr']['buy_product_name']; ?>
</span><?php endif; ?>
									<span style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['buy_product_name'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<input type="text" name="buy_product_name" maxlength="<?php echo @STEXT_LEN; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['buy_product_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="30" class="box30"  style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['buy_product_name'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"/>
									</span>
								</td>
								<td bgcolor="#f2f1ec" width="110">カテゴリ</td>
								<td bgcolor="#ffffff" width="195">
									<select name="category_id" style="<?php if ($this->_tpl_vars['arrErr']['category_id'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>">
										<option value="">選択してください</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrCatList'],'selected' => $this->_tpl_vars['list_data']['category_id']), $this);?>

									</select>
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">キャンペーン</td>
								<td bgcolor="#ffffff" width="499" colspan="3">
									<select name="campaign_id" style="<?php if ($this->_tpl_vars['arrErr']['campaign_id'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>">
										<option value="">選択してください</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrCampaignList'],'selected' => $this->_tpl_vars['list_data']['campaign_id']), $this);?>

									</select>
								</td>
							</tr>
						</table>
						<!--検索条件設定テーブルここまで-->

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
										<td><input type="image" name="subm" onMouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_search_on.jpg',this)" onMouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_search.jpg',this)" src="<?php echo @URL_DIR; ?>
img/contents/btn_search.jpg" width="123" height="24" alt="この条件で検索する" border="0"></td>
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
		</td>
	</tr>
</form>	
</table>
<!--★★メインコンテンツ★★-->

<?php if (count ( $this->_tpl_vars['arrErr'] ) == 0 && ( $_POST['mode'] == 'search' || $_POST['mode'] == 'delete' || $_POST['mode'] == 'back' )): ?>

<!--★★検索結果一覧★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input type="hidden" name="mode" value="">
<input type="hidden" name="search_pageno" value="<?php echo $this->_tpl_vars['tpl_pageno']; ?>
">
<input type="hidden" name="result_email" value="">
<?php $_from = $this->_tpl_vars['arrHidden']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['val']):
?>	
	<input type="hidden" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['val'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<?php endforeach; endif; unset($_from); ?>
	<tr><td colspan="2"><img src="<?php echo @URL_DIR; ?>
img/contents/search_line.jpg" width="878" height="12" alt=""></td></tr>
	<tr bgcolor="cbcbcb">
		<td>
		<table border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/contents/search_left.gif" width="19" height="22" alt=""></td>
				<td>
				<!--検索結果-->
				<table border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td><img src="<?php echo @URL_DIR; ?>
img/contents/reselt_left_top.gif" width="22" height="5" alt=""></td>
						<td background="<?php echo @URL_DIR; ?>
img/contents/reselt_top_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="5" alt=""></td>
						<td><img src="<?php echo @URL_DIR; ?>
img/contents/reselt_right_top.gif" width="22" height="5" alt=""></td>
					</tr>
					<tr>
						<td background="<?php echo @URL_DIR; ?>
img/contents/reselt_left_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/contents/reselt_left_middle.gif" width="22" height="12" alt=""></td>
						<td bgcolor="#393a48" class="white10">検索結果一覧　<span class="reselt"><!--検索結果数--><?php echo $this->_tpl_vars['tpl_linemax']; ?>
件</span>&nbsp;が該当しました。</td>
						<td background="<?php echo @URL_DIR; ?>
img/contents/reselt_right_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="22" height="8" alt=""></td>
					</tr>
					<tr>
						<td><img src="<?php echo @URL_DIR; ?>
img/contents/reselt_left_bottom.gif" width="22" height="5" alt=""></td>
						<td background="<?php echo @URL_DIR; ?>
img/contents/reselt_bottom_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="5" alt=""></td>
						<td><img src="<?php echo @URL_DIR; ?>
img/contents/reselt_right_bottom.gif" width="22" height="5" alt=""></td>
					</tr>
				</table>
				<!--検索結果-->
				<?php if (@ADMIN_MODE == '1'): ?>
				<input type="button" name="subm" value="検索結果をすべて削除" onclick="fnModeSubmit('delete_all','','');" />
				<?php endif; ?>
				</td>
				<td><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="8" height="1" alt=""></td>
				<td><input type="submit" name="subm" value="配信内容を設定する" onclick="document.form1['mode'].value='input';"/></td>
			</tr>
		</table>
		</td>
		<td align="right">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['tpl_pager'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</td>
	</tr>
	<tr><td bgcolor="cbcbcb" colspan="2"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="5" alt=""></td></tr>
</table>

<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#f0f0f0" align="center">

		<?php if (count ( $this->_tpl_vars['arrResults'] ) > 0): ?>		

			<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr><td height="12"></td></tr>
				<tr>
					<td bgcolor="#cccccc">
					<!--検索結果表示テーブル-->
					<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
						<tr bgcolor="#636469" align="center" class="fs12n">
							<td width="20"><span class="white">#</span></td>
							<td width="80"><span class="white">会員番号</span></td>
							<td width="80"><span class="white">受注番号</span></td>
							<td width="140"><span class="white">名前</span></td>				
							<td width="190"><span class="white">メールアドレス</span></td>	
							<td width="101"><span class="white">希望配信</span></td>
							<td width="100"><span class="white">登録日</span></td>
							<td width="40"><span class="white">削除</span></td>		
						</tr>
						<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['arrResults']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
						<tr bgcolor="#FFFFFF" class="fs12n">
							<td align="center"><?php echo $this->_sections['i']['iteration']; ?>
</td>
							<td align="center"><?php echo ((is_array($_tmp=@$this->_tpl_vars['arrResults'][$this->_sections['i']['index']]['customer_id'])) ? $this->_run_mod_handler('default', true, $_tmp, "非会員") : smarty_modifier_default($_tmp, "非会員")); ?>
</td>
			
							<?php $this->assign('key', ($this->_tpl_vars['arrResults'][$this->_sections['i']['index']]['customer_id'])); ?>
							<td align="center">
							<?php $_from = $this->_tpl_vars['arrCustomerOrderId'][$this->_tpl_vars['key']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['val']):
?>
							<a href="#" onclick="fnOpenWindow('../order/edit.php?order_id=<?php echo $this->_tpl_vars['val']; ?>
','order_disp','800','900'); return false;" ><?php echo $this->_tpl_vars['val']; ?>
</a><br />
							<?php endforeach; else: ?>
							-
							<?php endif; unset($_from); ?>
							</td>
							
							<td><?php echo ((is_array($_tmp=$this->_tpl_vars['arrResults'][$this->_sections['i']['index']]['name01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['arrResults'][$this->_sections['i']['index']]['name02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
							<td><?php echo ((is_array($_tmp=$this->_tpl_vars['arrResults'][$this->_sections['i']['index']]['email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
							<?php $this->assign('key', ($this->_tpl_vars['arrResults'][$this->_sections['i']['index']]['mail_flag'])); ?>
							<td align="center"><?php echo $this->_tpl_vars['arrMAILMAGATYPE'][$this->_tpl_vars['key']]; ?>
</td>
							<td><?php echo ((is_array($_tmp=$this->_tpl_vars['arrResults'][$this->_sections['i']['index']]['create_date'])) ? $this->_run_mod_handler('sfDispDBDate', true, $_tmp) : sfDispDBDate($_tmp)); ?>
</td>
							<?php if ($this->_tpl_vars['arrResults'][$this->_sections['i']['index']]['customer_id'] != ""): ?>
							<td align="center">-</td>
							<?php else: ?>
							<td align="center"><a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="fnFormModeSubmit('form1','delete','result_email','<?php echo ((is_array($_tmp=$this->_tpl_vars['arrResults'][$this->_sections['i']['index']]['email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
'); return false;">削除</a></td>	
							<?php endif; ?>
						</tr>
						<?php endfor; endif; ?>
					</table>
					<!--検索結果表示テーブル-->
					</td>
				</tr>
			</table>
		<?php endif; ?>

		</td>
	</tr>
</form>
</table>		
<!--★★検索結果一覧★★-->		

<?php endif; ?>