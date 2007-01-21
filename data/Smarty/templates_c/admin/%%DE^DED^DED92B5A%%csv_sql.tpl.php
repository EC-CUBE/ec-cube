<?php /* Smarty version 2.6.13, created on 2007-01-19 12:59:29
         compiled from contents/csv_sql.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'contents/csv_sql.tpl', 36, false),array('function', 'html_options', 'contents/csv_sql.tpl', 208, false),)), $this); ?>
<script type="text/javascript">
<!--
// リストボックスのサイズ変更
function ChangeSize(button, TextArea, Max, Min, row_tmp){
	if(TextArea.rows <= Min){
		TextArea.rows=Max; button.value="小さくする"; row_tmp.value=Max;
	}else{
		TextArea.rows =Min; button.value="大きくする"; row_tmp.value=Min;
	}
}

// SQL確認画面起動
function doPreview(){
	document.form1.mode.value="preview"
	document.form1.target = "_blank";
	document.form1.submit();
}

// formのターゲットを自分に戻す
function fnTargetSelf(){
	document.form1.target = "_self";
}

//-->
</script>

<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="sql_id" value="<?php echo $this->_tpl_vars['sql_id']; ?>
">
<input type="hidden" name="csv_output_id" value="">
<input type="hidden" name="selectTable" value="">
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

									<!-- SQL一覧 ここから -->
									<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
										</tr>
										<tr>
											<td background="<?php echo @URL_DIR; ?>
img/contents/contents_title_left_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
											<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->SQL一覧</span></td>
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

									<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" ">
										<tr class="fs12n">
											<td bgcolor="#f2f1ec" align="center" colspan=3 ><strong>SQL一覧</strong></td>
										</tr>

										<?php $_from = $this->_tpl_vars['arrSqlList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
										<tr class="fs12n" height=20>
											<td align="center" width=600 bgcolor="<?php if ($this->_tpl_vars['item']['sql_id'] == $this->_tpl_vars['sql_id']):  echo @SELECT_RGB;  else: ?>#ffffff<?php endif; ?>">
												<a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
?sql_id=<?php echo $this->_tpl_vars['item']['sql_id']; ?>
" ><?php echo $this->_tpl_vars['item']['sql_name']; ?>
</a>
											</td>
											<td align="center" width=78 bgcolor="<?php if ($this->_tpl_vars['item']['sql_id'] == $this->_tpl_vars['sql_id']):  echo @SELECT_RGB;  else: ?>#ffffff<?php endif; ?>">
												<input type='button' value='CSV出力' name='csv' onclick="fnTargetSelf(); fnFormModeSubmit('form1','csv_output','csv_output_id',<?php echo $this->_tpl_vars['item']['sql_id']; ?>
);"  />
											</td>
											<td align="center" width=78 bgcolor="<?php if ($this->_tpl_vars['item']['sql_id'] == $this->_tpl_vars['sql_id']):  echo @SELECT_RGB;  else: ?>#ffffff<?php endif; ?>">
												<input type='button' value='削除' name='del' onclick="fnTargetSelf(); fnFormModeSubmit('form1','delete','sql_id',<?php echo $this->_tpl_vars['item']['sql_id']; ?>
);"  />
											</td>
										</tr>
										<?php endforeach; endif; unset($_from); ?>
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
													<td><input type='button' value='新規SQL作成' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','new_page','','');"  /></td>
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
									<!-- SQL一覧 ここまで -->

									<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr><td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
									</table>

									<!-- SQL設定 ここから -->
									<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
										</tr>
										<tr>
											<td background="<?php echo @URL_DIR; ?>
img/contents/contents_title_left_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
											<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->CSV出力設定</span></td>
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

									<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" ">
										<tr class="fs12n">
											<td bgcolor="#f2f1ec" align="center" colspan=2><strong>SQL設定</strong></td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f2f1ec" align="center" width="100">名称<span class="red"> *</span></td>
											<td bgcolor="#ffffff" align="left">
												<span class="red12"><?php echo $this->_tpl_vars['arrErr']['sql_name']; ?>
</span>
												<input type="text" name="sql_name" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrSqlData']['sql_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" style="<?php if ($this->_tpl_vars['arrErr']['name'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" size="60" class="box60" /><span class="red"> （上限<?php echo @STEXT_LEN; ?>
文字）</span>
											</td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f2f1ec" align="center" width="100">SQL文<br>（SELECTは記述しないでください。）<span class="red"> *</span></td>
											<td bgcolor="#ffffff" align="left">
												<span class="red12"><?php echo $this->_tpl_vars['arrErr']['csv_sql']; ?>
</span>
												<div>
												<textarea name="csv_sql" cols=75 rows=30 align="left" wrap=off style="<?php if ($this->_tpl_vars['arrErr']['csv_sql'] != ""): ?>background-color: <?php echo @ERR_COLOR; ?>
;<?php endif; ?> width: 547px;"><?php echo $this->_tpl_vars['arrSqlData']['csv_sql']; ?>
</textarea>
												</div>
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
													<td>
														<input type="image" onMouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_regist.jpg',this)" src="<?php echo @URL_DIR; ?>
img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" onClick="mode.value='confirm'; fnTargetSelf();">
														<input type="image" onMouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_confirm_on.jpg',this)" onMouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_confirm.jpg',this)" src="<?php echo @URL_DIR; ?>
img/contents/btn_confirm.jpg" width="123" height="24" alt="確認ページへ" border="0" name="subm" onClick="doPreview(); return false;">
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
									<!-- SQL設定 ここまで -->

									<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr><td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
									</table>

									<!-- DB一覧 ここから -->
									<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td width="50%">
											<table width="100%" border="0" cellspacing="1" cellpadding="5" summary=" ">
												<tr class="fs12n">
													<td bgcolor="#f2f1ec" align="center" colspan=2><strong>テーブル一覧</strong></td>
												</tr>
												<tr class="fs12n">
													<td bgcolor="#ffffff" align="center">
														<select name="arrTableList[]" size="20" style="width:325px; height:300px;" onChange="mode.value=''; selectTable.value=this.value; submit();" onDblClick="csv_sql.value = csv_sql.value +' , ' + this.value;">
														<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrTableList'],'selected' => $this->_tpl_vars['selectTable']), $this);?>

														</select>
													</td>
												</tr>
											</table>
											</td>
											<td width="50%">
												<table width="100%" border="0" cellspacing="1" cellpadding="5" summary=" ">
													<tr class="fs12n">
														<td bgcolor="#f2f1ec" align="center" colspan=2><strong>項目一覧</strong></td>
													</tr>
													<tr class="fs12n">
														<td bgcolor="#ffffff" align="center">
															<select name="arrColList[]" size="20" style="width:325px; height:300px;" onDblClick="csv_sql.value = csv_sql.value +' , ' + this.value;">
															<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrColList']), $this);?>

															</select>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									<!-- DB一覧 ここまで -->
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


</script>