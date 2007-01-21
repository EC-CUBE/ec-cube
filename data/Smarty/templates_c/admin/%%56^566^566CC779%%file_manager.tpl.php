<?php /* Smarty version 2.6.13, created on 2007-01-10 13:54:44
         compiled from contents/file_manager.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'contents/file_manager.tpl', 10, false),array('modifier', 'number_format', 'contents/file_manager.tpl', 94, false),array('modifier', 'sfTrimURL', 'contents/file_manager.tpl', 112, false),)), $this); ?>
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"  enctype="multipart/form-data">
<input type="hidden" name="mode" value="">
<input type="hidden" name="now_file" value="<?php echo $this->_tpl_vars['tpl_now_dir']; ?>
">
<input type="hidden" name="tree_select_file" value="">
<input type="hidden" name="tree_status" value="">
<input type="hidden" name="select_file" value="">
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
							<tr valign="top">
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->ファイル管理</span></td>
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
								<!--▼ファイル管理テーブルここから-->
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n" bgcolor="#f2f1ec">
										<td>ディレクトリ</td>
										<td>
											<div id="now_dir">
											<img src="<?php echo @URL_DIR; ?>
img/admin/contents/folder_open.gif" alt="フォルダ">
											&nbsp;<?php echo $this->_tpl_vars['tpl_now_file']; ?>

											</div>
										</td>
									</tr>
									<tr class="fs12n" bgcolor="#ffffff">
										<td>
											<div id="tree"></div>
										</td>
										<td><span class="red"><?php echo $this->_tpl_vars['arrErr']['select_file']; ?>
</span>
											<div id="file_view">
												<table border="0" cellspacing="0" cellpadding="2" summary=" ">
													<tr class="fs12n" bgcolor="#f2f1ec">
														<td>ファイル名</td>
														<td align="right">サイズ</td>
														<td>更新日付</td>
													</tr>
													<?php if (! $this->_tpl_vars['tpl_is_top_dir']): ?>
													<tr class="fs12n" id="parent_dir" onclick="fnSetFormVal('form1', 'select_file', '<?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_parent_dir'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
');fnSelectFile('parent_dir', '#808080');" onDblClick="setTreeStatus('tree_status');fnDbClick(arrTree, '<?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_parent_dir'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
', true, '<?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_now_dir'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
', true)" style="" onMouseOver="fnChangeBgColor('parent_dir', '#808080');" onMouseOut="fnChangeBgColor('parent_dir', '');">
														<td>
															<img src="<?php echo @URL_DIR; ?>
img/admin/contents/folder_parent.gif" alt="フォルダ">&nbsp;..
														</td>
														<td align="right"></td>
														<td></td>
													</tr>										
													<?php endif; ?>
													<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrFileList']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
													<?php $this->assign('id', "select_file".($this->_sections['cnt']['index'])); ?>
													<tr class="fs12n" id="<?php echo $this->_tpl_vars['id']; ?>
" onclick="fnSetFormVal('form1', 'select_file', '<?php echo ((is_array($_tmp=$this->_tpl_vars['arrFileList'][$this->_sections['cnt']['index']]['file_path'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
');fnSelectFile('<?php echo $this->_tpl_vars['id']; ?>
', '#808080');" onDblClick="setTreeStatus('tree_status');fnDbClick(arrTree, '<?php echo ((is_array($_tmp=$this->_tpl_vars['arrFileList'][$this->_sections['cnt']['index']]['file_path'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
', <?php if (((is_array($_tmp=$this->_tpl_vars['arrFileList'][$this->_sections['cnt']['index']]['is_dir'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp))): ?>true<?php else: ?>false<?php endif; ?>, '<?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_now_dir'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
', false)" style="" onMouseOver="fnChangeBgColor('<?php echo $this->_tpl_vars['id']; ?>
', '#808080');" onMouseOut="fnChangeBgColor('<?php echo $this->_tpl_vars['id']; ?>
', '');">
														<td>
															<?php if ($this->_tpl_vars['arrFileList'][$this->_sections['cnt']['index']]['is_dir']): ?>
															<img src="<?php echo @URL_DIR; ?>
img/admin/contents/folder_close.gif" alt="フォルダ">
															<?php else: ?>
															<img src="<?php echo @URL_DIR; ?>
img/admin/contents/file.gif">
															<?php endif; ?>
															<?php echo ((is_array($_tmp=$this->_tpl_vars['arrFileList'][$this->_sections['cnt']['index']]['file_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

														</td>
														<td align="right"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrFileList'][$this->_sections['cnt']['index']]['file_size'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
</td>
														<td><?php echo ((is_array($_tmp=$this->_tpl_vars['arrFileList'][$this->_sections['cnt']['index']]['file_time'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
													</tr>
													<?php endfor; endif; ?>
												</table>
											</div>
											<table border="0" cellspacing="0" cellpadding="5" summary=" ">
												<tr>
													<td><input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('view','',''); return false;" value="表示"></td>
													<td><input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('download','',''); return false;" value="ダウンロード"></td>
													<td><input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('delete','',''); return false;" value="削除"></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
								<table width="678" border="1" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n">
										<td>現在のディレクトリ&nbsp;：&nbsp;<?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_now_dir'])) ? $this->_run_mod_handler('sfTrimURL', true, $_tmp) : sfTrimURL($_tmp)); ?>
/</td>
									</tr>
								</table>
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n">
										<td bgcolor="#f2f1ec">ファイルのアップロード</td>
										<td bgcolor="#ffffff"><span class="red"><?php echo $this->_tpl_vars['arrErr']['upload_file']; ?>
</span><input type="file" name="upload_file" size="64" class="box54" <?php if ($this->_tpl_vars['arrErr']['upload_file']): ?>style="background-color:<?php echo ((is_array($_tmp=@ERR_COLOR)) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?>><input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('upload','',''); return false;" value="アップロード"></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec">フォルダ作成</td>
										<td bgcolor="#ffffff"><span class="red"><?php echo $this->_tpl_vars['arrErr']['create_file']; ?>
</span><input type="text" name="create_file" value="" style="width:336px;<?php if ($this->_tpl_vars['arrErr']['create_file']): ?> background-color:<?php echo ((is_array($_tmp=@ERR_COLOR)) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  endif; ?>"><input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('create','',''); return false;" value="作成"></td>
									</tr>
									<thead>
								</table>
								<!--▲ファイル管理テーブルここまで-->
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
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