<?php /* Smarty version 2.6.13, created on 2007-01-19 13:01:28
         compiled from system/bkup.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'system/bkup.tpl', 54, false),array('modifier', 'sfCutString', 'system/bkup.tpl', 125, false),)), $this); ?>
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="list_name" value="">
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->バックアップ作成</span></td>
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
									<tr align="center" class="fs12n">
										<td bgcolor="#f2f1ec" width="130">バックアップ名<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="548" align=left>
											<span class="red12"><?php echo $this->_tpl_vars['arrErr']['bkup_name']; ?>
</span>
											<input type="text" name="bkup_name" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['bkup_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" size="60" class="box60" style="<?php if ($this->_tpl_vars['arrErr']['bkup_name'] != ""): ?>background-color: <?php echo @ERR_COLOR; ?>
;<?php endif; ?> ime-mode: disabled;" /><span class="red"> （上限<?php echo @STEXT_LEN; ?>
文字）</span>
										</td>
									</tr>
									<tr align="center" class="fs12n">
										<td bgcolor="#f2f1ec" width="130">バックアップメモ</td>
										<td bgcolor="#ffffff" width="548" align=left>
											<span class="red12"><?php echo $this->_tpl_vars['arrErr']['bkup_memo']; ?>
</span>
											<textarea name="bkup_memo" maxlength="<?php echo @MTEXT_LEN; ?>
" cols="60" rows="5" class="area60" style="<?php if ($this->_tpl_vars['arrErr']['bkup_memo'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" ><?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['bkup_memo'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea>
											<span class="red"> （上限<?php echo @MTEXT_LEN; ?>
文字）</span>
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
												<td><input type="button" name="cre_bkup" value="バックアップデータを作成する" onClick="document.body.style.cursor = 'wait'; form1.mode.value='bkup'; submit();" /></td>
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

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
								</table>
								
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<?php echo @URL_DIR; ?>
img/contents/contents_title_left_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->バックアップ一覧</span></td>
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
								
																<?php if (count ( $this->_tpl_vars['arrBkupList'] ) > 0): ?>
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr bgcolor="#f2f1ec" align="center" class="fs12n">
										<td width="150" align="center">バックアップ名</td>
										<td width="338">バックアップメモ</td>
										<td width="170">作成日</td>
										<td width="50">リストア</td>
										<td width="90">ダウンロード</td>
										<td width="50" align="center">削除</td>
									</tr>
									<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrBkupList']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
									<tr bgcolor="#ffffff" class="fs12">
										<td ><?php echo $this->_tpl_vars['arrBkupList'][$this->_sections['cnt']['index']]['bkup_name']; ?>
</td>
										<td ><?php echo $this->_tpl_vars['arrBkupList'][$this->_sections['cnt']['index']]['bkup_memo']; ?>
</td>			
										<td align="center"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrBkupList'][$this->_sections['cnt']['index']]['create_date'])) ? $this->_run_mod_handler('sfCutString', true, $_tmp, 19, true, false) : sfCutString($_tmp, 19, true, false)); ?>
</td>
										<td align="center"><a href="#" onclick="document.body.style.cursor = 'wait'; fnModeSubmit('restore','list_name','<?php echo $this->_tpl_vars['arrBkupList'][$this->_sections['cnt']['index']]['bkup_name']; ?>
');">restore</a></td>
										<td align="center"><a href="#" onclick="fnModeSubmit('download','list_name','<?php echo $this->_tpl_vars['arrBkupList'][$this->_sections['cnt']['index']]['bkup_name']; ?>
');">download</a></td>
										<td align="center">
											<a href="#" onclick="fnModeSubmit('delete','list_name','<?php echo $this->_tpl_vars['arrBkupList'][$this->_sections['cnt']['index']]['bkup_name']; ?>
');">delete</a>
										</td>	
									</tr>
									<?php endfor; endif; ?>
								</table>
								<?php endif; ?>

								
								<?php if ($this->_tpl_vars['restore_msg'] != ""): ?>
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">								
									<tr>
										<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>
													
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr bgcolor="#ffffff" class="fs12">
										<td>
											▼実行結果<br>
											<?php if ($this->_tpl_vars['restore_err'] == false): ?><input type="button" name="restore_config" value="テーブル構成を無視してリストアする" onClick="document.body.style.cursor = 'wait'; form1.mode.value='restore_config'; form1.list_name.value='<?php echo $this->_tpl_vars['restore_name']; ?>
'; submit();" /><br><?php endif; ?>
											<?php echo $this->_tpl_vars['restore_msg']; ?>

										</td>
									</tr>								
								</table>
								<?php endif; ?>
								
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