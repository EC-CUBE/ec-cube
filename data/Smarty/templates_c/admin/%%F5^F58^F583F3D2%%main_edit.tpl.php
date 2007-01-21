<?php /* Smarty version 2.6.13, created on 2007-01-15 19:58:49
         compiled from design/main_edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'design/main_edit.tpl', 36, false),)), $this); ?>
<script type="text/javascript">
function doPreview(){
	document.form_edit.mode.value="preview"
	document.form_edit.target = "_blank";
	document.form_edit.submit();
}

function fnTargetSelf(){
	document.form_edit.target = "_self";
}

</script>

<SCRIPT language="JavaScript">
<!--
browser_type = 0;
if(navigator.userAgent.indexOf("MSIE") >= 0){
    browser_type = 1;
}
else if(navigator.userAgent.indexOf("Mozilla") >= 0){
    browser_type = 2;
}
//-->
</SCRIPT>


<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form_edit" id="form_edit" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" >
<input type="hidden" name="mode" value="">
<input type="hidden" name="page_id" value="<?php echo $this->_tpl_vars['page_id']; ?>
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
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->ページ詳細設定</span></td>
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

						<!--▼編集画面　ここから-->
						<?php if ($this->_tpl_vars['arrErr']['page_id_err'] != ""): ?>
						<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr>
								<td bgcolor="#ffffff" align="center" class="fs14">
									<span class="red"><strong><?php echo $this->_tpl_vars['arrErr']['page_id_err']; ?>
</strong></span>
								</td>
							</tr>
						</table>
						<?php endif; ?>
						
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#ffffff" align="left" colspan=2>
									<?php if ($this->_tpl_vars['arrErr']['page_name'] != ""): ?> <div align="center"> <span class="red12"><?php echo $this->_tpl_vars['arrErr']['page_name']; ?>
</span></div> <?php endif; ?>
									<?php if ($this->_tpl_vars['arrPageData']['edit_flg'] == 2): ?>
										名称：<?php echo ((is_array($_tmp=$this->_tpl_vars['arrPageData']['page_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<input type="hidden" name="page_name" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrPageData']['page_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
									<?php else: ?>
										名称：<input type="text" name="page_name" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrPageData']['page_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" style="<?php if ($this->_tpl_vars['arrErr']['page_name'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" size="60" class="box60" /><span class="red"> （上限<?php echo @STEXT_LEN; ?>
文字）</span>
									<?php endif; ?>
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#ffffff" align="left" colspan=2>
									<?php if ($this->_tpl_vars['arrErr']['url'] != ""): ?> <div align="center"> <span class="red12"><?php echo $this->_tpl_vars['arrErr']['url']; ?>
</span></div> <?php endif; ?>
									URL：<?php if ($this->_tpl_vars['arrPageData']['edit_flg'] == 2): ?>
											<?php echo @SITE_URL;  echo ((is_array($_tmp=$this->_tpl_vars['arrPageData']['url'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

											<input type="hidden" name="url" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrPageData']['filename'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" />
										<?php else: ?>
											<?php echo $this->_tpl_vars['user_URL']; ?>
<input type="text" name="url" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrPageData']['directory'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  echo ((is_array($_tmp=$this->_tpl_vars['arrPageData']['filename'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" style="<?php if ($this->_tpl_vars['arrErr']['url'] != ""): ?>background-color: <?php echo @ERR_COLOR; ?>
;<?php endif; ?> ime-mode: disabled;" size="60" class="box60" />.php<span class="red"> （上限<?php echo @STEXT_LEN; ?>
文字）</span>
										<?php endif; ?>
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#ffffff" align="left">
									<label for="header"><input type="checkbox" name="header_chk" id="header" <?php echo $this->_tpl_vars['arrPageData']['header_chk']; ?>
>共通のヘッダーを使用する</label>
								</td>
								<td bgcolor="#ffffff" align="left">
									<label for="footer"><input type="checkbox" name="footer_chk" id="footer" <?php echo $this->_tpl_vars['arrPageData']['footer_chk']; ?>
>共通のフッターを使用する</label>
								</td>
							</tr>

							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=2>
									<br/>
									<div>
									<textarea name="tpl_data" cols=90 rows=<?php echo $this->_tpl_vars['text_row']; ?>
 align="left" wrap=off style="width: 650px; "><?php echo $this->_tpl_vars['arrPageData']['tpl_data']; ?>
</textarea>
									<input type="hidden" name="html_area_row" value="<?php echo $this->_tpl_vars['text_row']; ?>
">
									</div>
									<div align="right">
										<input type="button" value="大きくする" onClick="ChangeSize(this, tpl_data, 50, 13, html_area_row)">
									</div>
									<br/>
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=2>
									<input type='button' value='登録' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form_edit','confirm','','');"  />
									<input type='button' value='プレビュー' name='preview' onclick="doPreview(); "  />
								</td>
							</tr>
						</table>
						<!--▲編集画面　ここまで-->
						
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
						</table>
						
						<!--▼一覧　ここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=3 ><strong>編集可能画面一覧</strong></td>
							</tr>
							
							<?php $_from = $this->_tpl_vars['arrPageList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
							<?php if ($this->_tpl_vars['item']['tpl_dir'] != ""): ?>
							<tr class="fs12n" height=20>
								<td align="center" width=600 bgcolor="<?php if ($this->_tpl_vars['item']['page_id'] == $this->_tpl_vars['page_id']):  echo @SELECT_RGB;  else: ?>#ffffff<?php endif; ?>">
									<a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
?page_id=<?php echo $this->_tpl_vars['item']['page_id']; ?>
" ><?php echo $this->_tpl_vars['item']['page_name']; ?>
</a>
								</td>
								<td align="center" width=78 bgcolor="<?php if ($this->_tpl_vars['item']['page_id'] == $this->_tpl_vars['page_id']):  echo @SELECT_RGB;  else: ?>#ffffff<?php endif; ?>">
									<input type="button" value="レイアウト" name="layout<?php echo $this->_tpl_vars['item']['page_id']; ?>
" onclick="location.href='./index.php?page_id=<?php echo $this->_tpl_vars['item']['page_id']; ?>
';"  />
									<input type="hidden" value="<?php echo $this->_tpl_vars['item']['page_id']; ?>
" name="del_id<?php echo $this->_tpl_vars['item']['page_id']; ?>
">
								</td>
								<td align="center" width=78 bgcolor="<?php if ($this->_tpl_vars['item']['page_id'] == $this->_tpl_vars['page_id']):  echo @SELECT_RGB;  else: ?>#ffffff<?php endif; ?>">
									<?php if ($this->_tpl_vars['item']['edit_flg'] == 1): ?>
									<input type="button" value="削除" name="del<?php echo $this->_tpl_vars['item']['page_id']; ?>
" onclick="fnTargetSelf(); fnFormModeSubmit('form_edit','delete','page_id',this.name.substr(3));"  />
									<input type="hidden" value="<?php echo $this->_tpl_vars['item']['page_id']; ?>
" name="del_id<?php echo $this->_tpl_vars['item']['page_id']; ?>
">
									<?php endif; ?>
								</td>
							</tr>
							<?php endif; ?>
							<?php endforeach; endif; unset($_from); ?>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=3>
								<input type='button' value='新規ページ作成' name='subm' onclick="location.href='http://<?php echo $_SERVER['HTTP_HOST'];  echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
'">
								</td>
							</tr>
						</table>
						<!--▲一覧　ここまで-->

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

