<?php /* Smarty version 2.6.13, created on 2007-01-10 17:17:43
         compiled from order/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'order/index.tpl', 10, false),array('modifier', 'sfGetErrorColor', 'order/index.tpl', 55, false),array('modifier', 'sfDispDBDate', 'order/index.tpl', 321, false),array('modifier', 'number_format', 'order/index.tpl', 326, false),array('modifier', 'default', 'order/index.tpl', 327, false),array('function', 'html_options', 'order/index.tpl', 65, false),array('function', 'html_checkboxes', 'order/index.tpl', 133, false),)), $this); ?>
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="search_form" id="search_form" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
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
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->検索条件設定</span></td>
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
								<td bgcolor="#f2f1ec" width="110">受注番号</td>
								<td bgcolor="#ffffff" width="194">
									<?php $this->assign('key1', 'search_order_id1'); ?>
									<?php $this->assign('key2', 'search_order_id2'); ?>
									<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key1']]; ?>
</span>
									<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key2']]; ?>
</span>
									<input type="text" name="<?php echo $this->_tpl_vars['key1']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key1']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key1']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key1']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"  size="6" class="box6" />
									 〜 
									<input type="text" name="<?php echo $this->_tpl_vars['key2']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key2']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key2']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key2']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"  size="6" class="box6" />
								</td>
								<td bgcolor="#f2f1ec" width="110">対応状況</td>
								<td bgcolor="#ffffff" width="195">
									<?php $this->assign('key', 'search_order_status'); ?>
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
								<td bgcolor="#f2f1ec" width="110">顧客名</td>
								<td bgcolor="#ffffff" width="194">
								<?php $this->assign('key', 'search_order_name'); ?>
								<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
								<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="30" class="box30" />				
								</td>
								<td bgcolor="#f2f1ec" width="110">顧客名（カナ）</td>
								<td bgcolor="#ffffff" width="195">
								<?php $this->assign('key', 'search_order_kana'); ?>
								<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
								<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="30" class="box30" />				
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">メールアドレス</td>
								<td bgcolor="#ffffff" width="194">
									<?php $this->assign('key', 'search_order_email'); ?>
									<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
									<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="30" class="box30" />				
								</td>
								<td bgcolor="#f2f1ec" width="110">TEL</td>
								<td bgcolor="#ffffff" width="195">
									<?php $this->assign('key', 'search_order_tel'); ?>
									<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
									<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="30" class="box30" />				
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">生年月日</td>
								<td bgcolor="#ffffff" width="499" colspan="3">
									<span class="red"><?php echo $this->_tpl_vars['arrErr']['search_sbirthyear']; ?>
</span>
									<span class="red"><?php echo $this->_tpl_vars['arrErr']['search_ebirthyear']; ?>
</span>		
									<select name="search_sbirthyear" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_sbirthyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">----</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrBirthYear'],'selected' => $this->_tpl_vars['arrForm']['search_sbirthyear']['value']), $this);?>

									</select>年
									<select name="search_sbirthmonth" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_sbirthyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">--</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrMonth'],'selected' => $this->_tpl_vars['arrForm']['search_sbirthmonth']['value']), $this);?>

									</select>月
									<select name="search_sbirthday" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_sbirthyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">--</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrDay'],'selected' => $this->_tpl_vars['arrForm']['search_sbirthday']['value']), $this);?>

									</select>日〜
									<select name="search_ebirthyear" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_ebirthyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">----</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrBirthYear'],'selected' => $this->_tpl_vars['arrForm']['search_ebirthyear']['value']), $this);?>

									</select>年
									<select name="search_ebirthmonth" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_ebirthyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">--</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrMonth'],'selected' => $this->_tpl_vars['arrForm']['search_ebirthmonth']['value']), $this);?>

									</select>月
									<select name="search_ebirthday" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_ebirthyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">--</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrDay'],'selected' => $this->_tpl_vars['arrForm']['search_ebirthday']['value']), $this);?>

									</select>日
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">性別</td>
								<td bgcolor="#ffffff" width="499" colspan="3">
								<?php $this->assign('key', 'search_order_sex'); ?>
								<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
								<?php echo smarty_function_html_checkboxes(array('name' => ($this->_tpl_vars['key']),'options' => $this->_tpl_vars['arrSex'],'selected' => $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value']), $this);?>

							</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">支払方法</td>
								<td bgcolor="#ffffff" width="499" colspan="3">
								<?php $this->assign('key', 'search_payment_id'); ?>
								<span class="red12"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</span>
								<?php echo smarty_function_html_checkboxes(array('name' => ($this->_tpl_vars['key']),'options' => ((is_array($_tmp=$this->_tpl_vars['arrPayment'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)),'selected' => $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value']), $this);?>

								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">登録・更新日</td>
								<td bgcolor="#ffffff" width="499" colspan="3">
									<span class="red"><?php echo $this->_tpl_vars['arrErr']['search_startyear']; ?>
</span>
									<span class="red"><?php echo $this->_tpl_vars['arrErr']['search_endyear']; ?>
</span>		
									<select name="search_startyear"  style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_startyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">----</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrRegistYear'],'selected' => $this->_tpl_vars['arrForm']['search_startyear']['value']), $this);?>

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
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrRegistYear'],'selected' => $this->_tpl_vars['arrForm']['search_endyear']['value']), $this);?>

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
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">購入金額</td>
								<td bgcolor="#ffffff" width="499" colspan="3">
									<?php $this->assign('key1', 'search_total1'); ?>
									<?php $this->assign('key2', 'search_total2'); ?>
									<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key1']]; ?>
</span>
									<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key2']]; ?>
</span>
									<input type="text" name="<?php echo $this->_tpl_vars['key1']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key1']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key1']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key1']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"  size="6" class="box6" />
									
									円 〜 
									<input type="text" name="<?php echo $this->_tpl_vars['key2']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key2']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key2']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key2']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"  size="6" class="box6" />
									円
								</td>
							</tr>
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
										<td class="fs12n">検索結果表示件数
											<?php $this->assign('key', 'search_page_max'); ?>
											<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
											<select name="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['keyname']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
											<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrPageMax'],'selected' => $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['value']), $this);?>

											</select> 件
										</td>
										<td><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="10" height="1" alt=""></td>
										<td><input type="image" name="subm" onMouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_search_on.jpg',this)" onMouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_search.jpg',this)" src="<?php echo @URL_DIR; ?>
img/contents/btn_search.jpg" width="123" height="24" alt="この条件で検索する" border="0" ></td>
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
</form>	
</table>
<!--★★メインコンテンツ★★-->						

<?php if (count ( $this->_tpl_vars['arrErr'] ) == 0 && ( $_POST['mode'] == 'search' || $_POST['mode'] == 'delete' )): ?>

<!--★★検索結果一覧★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input type="hidden" name="mode" value="search">
<input type="hidden" name="order_id" value="">		
<?php $_from = $this->_tpl_vars['arrHidden']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
<input type="hidden" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['item'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
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
				<td><a href="#" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/contents/btn_csv_on.jpg','btn_csv');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/contents/btn_csv.jpg','btn_csv');"  onclick="fnModeSubmit('csv','','');" ><img src="<?php echo @URL_DIR; ?>
img/contents/btn_csv.jpg" width="99" height="22" alt="CSV DOWNLOAD" border="0" name="btn_csv" id="btn_csv"></a></td>
				<td><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="8" height="1" alt=""></td>
				<td><a href="../contents/csv.php?tpl_subno_csv=order"><span class="fs12n"> >> CSV出力項目設定 </span></a></td>
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
							<td width="130"><span class="white">受注日</span></td>
							<td width="70"><span class="white">受注番号</span></td>
							<td width="120"><span class="white">顧客名</span></td>
							<td width="75"><span class="white">支払方法</span></td>
							<td width="80"><span class="white">購入金額(円)</span></td>
							<td width="130"><span class="white">全商品発送日</span></td>
							<td width="75"><span class="white">対応状況</span></td>
							<td width="50"><span class="white">編集</span></td>
							<td width="50"><span class="white">メール</span></td>
							<td width="50"><span class="white">削除</span></td>
						</tr>
						
						<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrResults']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
						<?php $this->assign('status', ($this->_tpl_vars['arrResults'][$this->_sections['cnt']['index']]['status'])); ?>
						<tr bgcolor="<?php echo $this->_tpl_vars['arrORDERSTATUS_COLOR'][$this->_tpl_vars['status']]; ?>
" class="fs12n">
							<td align="center"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrResults'][$this->_sections['cnt']['index']]['create_date'])) ? $this->_run_mod_handler('sfDispDBDate', true, $_tmp) : sfDispDBDate($_tmp)); ?>
</td>
							<td align="center"><?php echo $this->_tpl_vars['arrResults'][$this->_sections['cnt']['index']]['order_id']; ?>
</td>
							<td><?php echo ((is_array($_tmp=$this->_tpl_vars['arrResults'][$this->_sections['cnt']['index']]['order_name01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['arrResults'][$this->_sections['cnt']['index']]['order_name02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
							<?php $this->assign('payment_id', ($this->_tpl_vars['arrResults'][$this->_sections['cnt']['index']]['payment_id'])); ?>
							<td align="center"><?php echo $this->_tpl_vars['arrPayment'][$this->_tpl_vars['payment_id']]; ?>
</td>
							<td align="right"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrResults'][$this->_sections['cnt']['index']]['total'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
</td>
							<td align="center"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrResults'][$this->_sections['cnt']['index']]['commit_date'])) ? $this->_run_mod_handler('sfDispDBDate', true, $_tmp) : sfDispDBDate($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, "未発送") : smarty_modifier_default($_tmp, "未発送")); ?>
</td>
							<td align="center"><?php echo $this->_tpl_vars['arrORDERSTATUS'][$this->_tpl_vars['status']]; ?>
</td>
							<td align="center"><a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="fnChangeAction('<?php echo @URL_ORDER_EDIT; ?>
'); fnModeSubmit('pre_edit', 'order_id', '<?php echo $this->_tpl_vars['arrResults'][$this->_sections['cnt']['index']]['order_id']; ?>
'); return false;"><span class="icon_edit">編集</span></a></td>
							<td align="center"><a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="fnChangeAction('<?php echo @URL_ORDER_MAIL; ?>
'); fnModeSubmit('pre_edit', 'order_id', '<?php echo $this->_tpl_vars['arrResults'][$this->_sections['cnt']['index']]['order_id']; ?>
'); return false;"><span class="icon_mail">通知</span></a></td>
							<td align="center"><a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="fnModeSubmit('delete', 'order_id', <?php echo $this->_tpl_vars['arrResults'][$this->_sections['cnt']['index']]['order_id']; ?>
); return false;"><span class="icon_delete">削除</span></a></td>
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