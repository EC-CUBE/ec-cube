<?php /* Smarty version 2.6.13, created on 2007-01-18 18:20:20
         compiled from mail/template_input.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'mail/template_input.tpl', 38, false),array('function', 'sfSetErrorStyle', 'mail/template_input.tpl', 81, false),array('function', 'html_radios', 'mail/template_input.tpl', 81, false),)), $this); ?>
<script language="JavaScript">
<!--
function lfnCheckSubmit(){
	
	fm = document.form1;
	var err = '';
	
	if ( ! fm["subject"].value ){
		err += 'Subjectを入力して下さい。';
	}
	if ( ! fm["body"].value ){
		if ( err ) err += '\n';
		err += '本文を入力して下さい。';
	}
	if ( err ){
		alert(err);
		return false;
	} else {
		if(window.confirm('内容を登録しても宜しいですか')){
			return true;
		}else{
			return false;
		}
	}
}
//-->
</script>

<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onSubmit="return lfnCheckSubmit();">
<input type="hidden" name="mode" value="<?php echo $this->_tpl_vars['mode']; ?>
">
<input type="hidden" name="template_id" value="<?php echo $this->_tpl_vars['arrForm']['template_id']; ?>
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->配信内容設定：<?php echo $this->_tpl_vars['title']; ?>
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

								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n">
										<td bgcolor="#f2f1ec">メール形式<span class="red"> *</span></td>
										<td bgcolor="#ffffff"><span <?php if ($this->_tpl_vars['arrErr']['mail_method']):  echo sfSetErrorStyle(array(), $this); endif; ?>><?php echo smarty_function_html_radios(array('name' => 'mail_method','options' => $this->_tpl_vars['arrMagazineType'],'separator' => "&nbsp;",'selected' => $this->_tpl_vars['arrForm']['mail_method']), $this);?>
</span>
										<?php if ($this->_tpl_vars['arrErr']['mail_method']): ?><br><span class="red12"><?php echo $this->_tpl_vars['arrErr']['mail_method']; ?>
</span><?php endif; ?>
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" class="fs12n">Subject<span class="red"> *</span></td>
										<td bgcolor="#ffffff">
										<input type="text" name="subject" size="65" class="box65" <?php if ($this->_tpl_vars['arrErr']['subject']):  echo sfSetErrorStyle(array(), $this); endif; ?> value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['subject'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
										<?php if ($this->_tpl_vars['arrErr']['subject']): ?><br><span class="red12"><?php echo $this->_tpl_vars['arrErr']['subject']; ?>
</span><?php endif; ?>
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" colspan="2" class="fs12n">本文<span class="red"> *</span>（名前差し込み時は {name} といれてください）</td>
									</tr>
									<tr>
										<td bgcolor="#ffffff" colspan="2">
										<textarea name="body" cols="90" rows="40" class="area90" <?php if ($this->_tpl_vars['arrErr']['body']):  echo sfSetErrorStyle(array(), $this); endif; ?>><?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['body'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea>
										<?php if ($this->_tpl_vars['arrErr']['body']): ?><br><span class="red12"><?php echo $this->_tpl_vars['arrErr']['body']; ?>
</span><?php endif; ?>
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
												<td width="30%"></td>
												<td width="40%" align = "center" valign="upper"><input type="image" onMouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_regist.jpg',this)" src="<?php echo @URL_DIR; ?>
img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm"></td>
												<td width="30%" align = "right" valign="upper"><input type="button" width="110" height="30" value="文字数カウント" onclick="fnCharCount('form1','body','cnt_footer');" border="0" name="next" id="next" />
												<br><span class="fs10n">今までに入力したのは<input type="text" name="cnt_footer" size="4" class="box4" readonly = true style="text-align:right">文字です。</span></td>
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