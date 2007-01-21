<?php /* Smarty version 2.6.13, created on 2007-01-10 16:10:22
         compiled from basis/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'basis/index.tpl', 57, false),array('modifier', 'sfGetErrorColor', 'basis/index.tpl', 167, false),array('function', 'html_options', 'basis/index.tpl', 95, false),array('function', 'html_radios', 'basis/index.tpl', 189, false),)), $this); ?>
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="./index.php">
<input type="hidden" name="mode" value="<?php echo $this->_tpl_vars['tpl_mode']; ?>
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->SHOPマスタ登録</span></td>
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
									<tr>
										<td bgcolor="#f2f1ec" colspan="2" class="fs12n">▼基本情報</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="180" class="fs12n">会社名</td>
										<td bgcolor="#ffffff" width="537" class="fs10n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['company_name']; ?>
</span>
										<input type="text" name="company_name" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['company_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" size="60" class="box60" style="<?php if ($this->_tpl_vars['arrErr']['company_name'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" /><span class="red"> （上限<?php echo @STEXT_LEN; ?>
文字）</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="180" class="fs12n">会社名（カナ）</td>
										<td bgcolor="#ffffff" width="537" class="fs10n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['company_kana']; ?>
</span>
										<input type="text" name="company_kana" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['company_kana'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" size="60" class="box60" style="<?php if ($this->_tpl_vars['arrErr']['company_kana'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" /><span class="red"> （上限<?php echo @STEXT_LEN; ?>
文字）</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="180" class="fs12n">店名<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="537" class="fs10n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['shop_name']; ?>
</span>
										<input type="text" name="shop_name" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['shop_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" size="60" class="box60" style="<?php if ($this->_tpl_vars['arrErr']['shop_name'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" /><span class="red"> （上限<?php echo @STEXT_LEN; ?>
文字）</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="180" class="fs12n">店名（カナ）</td>
										<td bgcolor="#ffffff" width="537" class="fs10n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['shop_kana']; ?>
</span>
										<input type="text" name="shop_kana" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['shop_kana'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" size="60" class="box60" style="<?php if ($this->_tpl_vars['arrErr']['shop_kana'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" /><span class="red"> （上限<?php echo @STEXT_LEN; ?>
文字）</span></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">郵便番号<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="537">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['zip01']; ?>
</span>
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['zip02']; ?>
</span>
										〒 <input type="text" name="zip01" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['zip01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="3" size="6" class="box6" style="<?php if ($this->_tpl_vars['arrErr']['zip01'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" /> - <input type="text" name="zip02" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['zip02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="4"  size="6" class="box6" style="<?php if ($this->_tpl_vars['arrErr']['zip02'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" />
										<input type="button" name="address_input" value="住所入力" onclick="fnCallAddress('<?php echo @URL_INPUT_ZIP; ?>
', 'zip01', 'zip02', 'pref', 'addr01');" />
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="180" class="fs12">SHOP住所<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="537">
										<table width="537" border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td>
													<span class="red12"><?php echo $this->_tpl_vars['arrErr']['pref']; ?>
</span>
													<select name="pref" style="<?php if ($this->_tpl_vars['arrErr']['pref'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" >							
													<option value="" selected="selected">都道府県を選択</option>
													<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrPref'],'selected' => $this->_tpl_vars['arrForm']['pref']), $this);?>

													</select>
												</td>
											</tr>
											<tr><td height="5"></td></tr>
											<tr class="fs10n">
												<td>
												<span class="red12"><?php echo $this->_tpl_vars['arrErr']['addr01']; ?>
</span>
												<input type="text" name="addr01" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['addr01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" size="60" class="box60" style="<?php if ($this->_tpl_vars['arrErr']['addr01'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" /><span class="red"> （上限<?php echo @STEXT_LEN; ?>
文字）</span><br />
												※市区町村を入力 （例：大阪市北区堂島）</td>
											</tr>
											<tr><td height="5"></td></tr>
											<tr class="fs10n">
												<td>
												<span class="red12"><?php echo $this->_tpl_vars['arrErr']['addr02']; ?>
</span>
												<input type="text" name="addr02" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['addr02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"  maxlength="<?php echo @STEXT_LEN; ?>
" size="60" class="box60" style="<?php if ($this->_tpl_vars['arrErr']['addr02'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" /><span class="red"> （上限<?php echo @STEXT_LEN; ?>
文字）</span><br />
												※番地、建物、マンション名などを入力 （例：2丁目1-31 ORIX堂島ビル5階）</td>
											</tr>
										</table>
										</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">TEL</td>
										<td bgcolor="#ffffff" width="537">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['tel01']; ?>
</span>
										<input type="text" name="tel01" value="<?php echo $this->_tpl_vars['arrForm']['tel01']; ?>
" maxlength="6" size="6" class="box6" style="<?php if ($this->_tpl_vars['arrErr']['tel01'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" /> - 
										<input type="text" name="tel02" value="<?php echo $this->_tpl_vars['arrForm']['tel02']; ?>
" maxlength="6" size="6" class="box6" style="<?php if ($this->_tpl_vars['arrErr']['tel01'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" /> - 
										<input type="text" name="tel03" value="<?php echo $this->_tpl_vars['arrForm']['tel03']; ?>
" maxlength="6" size="6" class="box6" style="<?php if ($this->_tpl_vars['arrErr']['tel01'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" /></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">FAX</td>
										<td bgcolor="#ffffff" width="537">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['fax01']; ?>
</span>
										<input type="text" name="fax01" value="<?php echo $this->_tpl_vars['arrForm']['fax01']; ?>
" maxlength="6" size="6" class="box6" style="<?php if ($this->_tpl_vars['arrErr']['fax01'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" /> - 
										<input type="text" name="fax02" value="<?php echo $this->_tpl_vars['arrForm']['fax02']; ?>
" maxlength="6" size="6" class="box6" style="<?php if ($this->_tpl_vars['arrErr']['fax02'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" /> - 
										<input type="text" name="fax03" value="<?php echo $this->_tpl_vars['arrForm']['fax03']; ?>
" maxlength="6" size="6" class="box6" style="<?php if ($this->_tpl_vars['arrErr']['fax03'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>"/></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="180" class="fs12n">店舗営業時間</td>
										<td bgcolor="#ffffff" width="537" class="fs10n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['business_hour']; ?>
</span>
										<input type="text" name="business_hour" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['business_hour'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" size="60" class="box60" style="<?php if ($this->_tpl_vars['arrErr']['business_hour'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" /><span class="red"> （上限<?php echo @STEXT_LEN; ?>
文字）</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="180" class="fs12n">商品注文受付<br>メールアドレス<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="537" class="fs10n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['email01']; ?>
</span>
										<input type="text" name="email01" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['email01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" size="60" class="box60" style="<?php if ($this->_tpl_vars['arrErr']['email01'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" /><span class="red"> （上限<?php echo @STEXT_LEN; ?>
文字）</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="180" class="fs12n">問い合わせ受付<br>メールアドレス<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="537" class="fs10n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['email02']; ?>
</span>
										<input type="text" name="email02" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['email02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" size="60" class="box60" style="<?php if ($this->_tpl_vars['arrErr']['email02'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>"/><span class="red"> （上限<?php echo @STEXT_LEN; ?>
文字）</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="180" class="fs12n">メール送信元<br>メールアドレス<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="537" class="fs10n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['email03']; ?>
</span>
										<input type="text" name="email03" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['email03'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" size="60" class="box60" style="<?php if ($this->_tpl_vars['arrErr']['email03'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>"/><span class="red"> （上限<?php echo @STEXT_LEN; ?>
文字）</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="180" class="fs12n">送信エラー受付<br>メールアドレス<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="537" class="fs10n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['email04']; ?>
</span>
										<input type="text" name="email04" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['email04'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" size="60" class="box60" style="<?php if ($this->_tpl_vars['arrErr']['email04'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>"/><span class="red"> （上限<?php echo @STEXT_LEN; ?>
文字）</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="180" class="fs12n">取扱商品</td>
										<td bgcolor="#ffffff" width="537" class="fs10n">
										<?php $this->assign('key', 'good_traded'); ?>
										<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
										<textarea name="<?php echo $this->_tpl_vars['key']; ?>
" maxlength="<?php echo @LLTEXT_LEN; ?>
" cols="60" rows="8" class="area60" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" ><?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea><span class="red"> （上限<?php echo @LLTEXT_LEN; ?>
文字）</span>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="180" class="fs12n">メッセージ</td>
										<td bgcolor="#ffffff" width="537" class="fs10n">
										<?php $this->assign('key', 'message'); ?>
										<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
										<textarea name="<?php echo $this->_tpl_vars['key']; ?>
" maxlength="<?php echo @LLTEXT_LEN; ?>
" cols="60" rows="8" class="area60" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" ><?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea><span class="red"> （上限<?php echo @LLTEXT_LEN; ?>
文字）</span>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" colspan="2">▼SHOP機能</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="180">消費税率<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="537">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['tax']; ?>
</span>
										<input type="text" name="tax" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['tax'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @PERCENTAGE_LEN; ?>
" size="6" class="box6" style="<?php if ($this->_tpl_vars['arrErr']['tax'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" /> ％</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="180">課税規則<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="537">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['tax_rule']; ?>
</span>
										<?php echo smarty_function_html_radios(array('name' => 'tax_rule','options' => $this->_tpl_vars['arrTAXRULE'],'selected' => $this->_tpl_vars['arrForm']['tax_rule']), $this);?>

										</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="180">送料無料条件</td>
										<td bgcolor="#ffffff" width="537">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['free_rule']; ?>
</span>
										<input type="text" name="free_rule" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['free_rule'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @PRICE_LEN; ?>
" size="6" class="box6" style="<?php if ($this->_tpl_vars['arrErr']['free_rule'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" /> 円以上購入時無料</td>
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
												<td><input type="image" onMouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_regist.jpg',this)" src="<?php echo @URL_DIR; ?>
img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" ></td>
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