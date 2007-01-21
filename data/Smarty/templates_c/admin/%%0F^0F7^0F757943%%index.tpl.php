<?php /* Smarty version 2.6.13, created on 2007-01-17 13:46:11
         compiled from total/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'total/index.tpl', 46, false),array('modifier', 'sfGetErrorColor', 'total/index.tpl', 56, false),array('function', 'html_options', 'total/index.tpl', 57, false),)), $this); ?>
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td background="<?php echo @URL_DIR; ?>
img/contents/navi_bg.gif" height="200">
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
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル--><?php echo $this->_tpl_vars['tpl_subtitle']; ?>
</span></td>
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
							<form name="search_form1" id="search_form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
							<input type="hidden" name="mode" value="search">
							<input type="hidden" name="form" value="1">
							<input type="hidden" name="page" value="<?php echo $this->_tpl_vars['arrForm']['page']['value']; ?>
">
							<input type="hidden" name="type" value="<?php echo $_POST['type']; ?>
">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">月度集計</td>
								<td bgcolor="#ffffff" width="607" colspan="3">
									<span class="red"><?php echo $this->_tpl_vars['arrErr']['search_startyear_m']; ?>
</span>
									<span class="red"><?php echo $this->_tpl_vars['arrErr']['search_endyear_m']; ?>
</span>		
									<select name="search_startyear_m"  style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_startyear_m'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrYear'],'selected' => $this->_tpl_vars['arrForm']['search_startyear_m']['value']), $this);?>

									</select>年
									<select name="search_startmonth_m" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_startyear_m'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrMonth'],'selected' => $this->_tpl_vars['arrForm']['search_startmonth_m']['value']), $this);?>

									</select>月度 （<?php if (@CLOSE_DAY == 31): ?>末<?php else:  echo @CLOSE_DAY;  endif; ?>日締め）
									　<input type="submit" name="subm" value="月度で集計する" />
								</td>
							</tr>
							</form>
							<form name="search_form2" id="search_form2" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
							<input type="hidden" name="mode" value="search">
							<input type="hidden" name="form" value="2">
							<input type="hidden" name="page" value="<?php echo $this->_tpl_vars['arrForm']['page']['value']; ?>
">
							<input type="hidden" name="type" value="<?php echo $_POST['type']; ?>
">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">期間集計</td>
								<td bgcolor="#ffffff" width="607" colspan="3">
									<span class="red"><?php echo $this->_tpl_vars['arrErr']['search_startyear']; ?>
</span>
									<span class="red"><?php echo $this->_tpl_vars['arrErr']['search_endyear']; ?>
</span>		
									<select name="search_startyear"  style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_startyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">----</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrYear'],'selected' => $this->_tpl_vars['arrForm']['search_startyear']['value']), $this);?>

									</select>年
									<select name="search_startmonth" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_startyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">--</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrMonth'],'selected' => $this->_tpl_vars['arrForm']['search_startmonth']['value']), $this);?>

									</select>月
									<select name="search_startday" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_startyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">--</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrDay'],'selected' => $this->_tpl_vars['arrForm']['search_startday']['value']), $this);?>

									</select>日〜
									<select name="search_endyear" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_endyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">----</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrYear'],'selected' => $this->_tpl_vars['arrForm']['search_endyear']['value']), $this);?>

									</select>年
									<select name="search_endmonth" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_endyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">--</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrMonth'],'selected' => $this->_tpl_vars['arrForm']['search_endmonth']['value']), $this);?>

									</select>月
									<select name="search_endday" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_endyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">--</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrDay'],'selected' => $this->_tpl_vars['arrForm']['search_endday']['value']), $this);?>

									</select>日
									　<input type="submit" name="subm" value="期間で集計する" />
								</td>
							</tr>
							</form>

						</table>
						<!--検索条件設定テーブルここまで-->
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
</table>
<!--★★メインコンテンツ★★-->


<?php if (count ( $this->_tpl_vars['arrResults'] ) > 0): ?>
	<!--★★検索結果一覧★★-->
	<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="type" value="<?php echo $this->_tpl_vars['arrForm']['type']['value']; ?>
">
	<input type="hidden" name="page" value="<?php echo $this->_tpl_vars['arrForm']['page']['value']; ?>
">
	<?php $_from = $this->_tpl_vars['arrHidden']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
	<input type="hidden" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['item'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
	<?php endforeach; endif; unset($_from); ?>	
		<tr><td colspan="2"><img src="<?php echo @URL_DIR; ?>
img/contents/search_line.jpg" width="878" height="12" alt=""></td></tr>
	</table>
	
	<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<tr>
			<td bgcolor="#f0f0f0" align="center">
	
				<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td bgcolor="#f0f0f0">
						<!--検索結果表示テーブル-->
						<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr><td align="center">
																<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td><hr noshade size="1" color="#f0f0f0" /></td></tr>
									<tr><td height="5"></td></tr>
									<tr>
										<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['tpl_graphsubtitle'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
									</tr>
								</table>
						
								<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="5"></td></tr>
									<tr>
										<td align = center>
										<input type="button" name="subm" value="検索結果をCSVダウンロード" onclick="fnModeSubmit('csv','','');" />
																				</td>
									</tr>
								</table>

										
								<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="15"></td></tr>
									<tr>
										<td align="center">
																						<img src="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
?draw_image=true&mode=search&page=<?php echo $_POST['page']; ?>
&search_startyear_m=<?php echo $_POST['search_startyear_m']; ?>
&search_startmonth_m=<?php echo $_POST['search_startmonth_m']; ?>
&search_startyear=<?php echo $_POST['search_startyear']; ?>
&search_startmonth=<?php echo $_POST['search_startmonth']; ?>
&search_startday=<?php echo $_POST['search_startday']; ?>
&search_endyear=<?php echo $_POST['search_endyear']; ?>
&search_endmonth=<?php echo $_POST['search_endmonth']; ?>
&search_endday=<?php echo $_POST['search_endday']; ?>
" alt="グラフ">
										</td>
									</tr>
									<tr><td height="15"></td></tr>
								</table>
										
								<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td bgcolor="#cccccc">
										<!--▼検索結果テーブルここから-->
										<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['tpl_page_type'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
										<!--▲検索結果テーブルここまで-->
										</td>
									</tr>
								</table>
								<!--▲MAIN CONTENTS-->
								</td>
							</tr>
						</table>
						<!--検索結果表示テーブル-->
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</form>
	</table>
<!--▲検索結果表示エリアここまで-->
<?php else: ?>
	<?php if ($_POST['mode'] == 'search'): ?>
	<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="type" value="<?php echo $this->_tpl_vars['arrForm']['type']['value']; ?>
">
	<input type="hidden" name="page" value="<?php echo $this->_tpl_vars['arrForm']['page']['value']; ?>
">
	<?php $_from = $this->_tpl_vars['arrHidden']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
	<input type="hidden" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['item'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
	<?php endforeach; endif; unset($_from); ?>	
		<tr><td colspan="2"><img src="<?php echo @URL_DIR; ?>
img/contents/search_line.jpg" width="878" height="12" alt=""></td></tr>
	</table>	
	<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<tr>
			<td bgcolor="#f0f0f0" align="center">
				<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td bgcolor="#f0f0f0">
						<!--検索結果表示テーブル-->
						<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr><td align="center">	
																<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td><hr noshade size="1" color="#f0f0f0" /></td></tr>
									<tr><td height="5"></td></tr>
									<tr>
											<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['tpl_graphsubtitle'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
									</tr>
								</table>
								<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="10"></td></tr>
									<tr class="fs12"><td align="center" height="200">該当するデータはありません。</td></tr>
								</table>
								</td>
							</tr>
						</table>
						<!--検索結果表示テーブル-->
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</form>
	</table>
	<?php endif; ?>
<?php endif; ?>

<!--★★検索結果一覧★★-->		