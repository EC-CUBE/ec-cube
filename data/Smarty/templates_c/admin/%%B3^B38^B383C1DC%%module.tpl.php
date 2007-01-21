<?php /* Smarty version 2.6.13, created on 2007-01-19 13:01:10
         compiled from system/module.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'system/module.tpl', 66, false),array('modifier', 'sfDispDBDate', 'system/module.tpl', 68, false),)), $this); ?>
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="module_id" value="">
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->モジュール機能一覧</span></td>
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
								
																<?php if (count ( $this->_tpl_vars['arrUpdate'] ) > 0): ?>
								<table width="678" border="0" cellspacing="1" cellpadding="4" summary=" ">
									<tr bgcolor="#f2f1ec" align="center" class="fs12n">
										<td width="170">機能名</td>
										<td width="300">説明</td>
										<td width="50">現状</td>										
										<td width="50">最新</td>										
										<td width="80">リリース日</td>
										<td width="50">設定</td>
										<td width="50">実行</td>
									</tr>
									<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrUpdate']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
										<td ><?php echo $this->_tpl_vars['arrUpdate'][$this->_sections['cnt']['index']]['module_name']; ?>
</td>
										<td ><?php echo $this->_tpl_vars['arrUpdate'][$this->_sections['cnt']['index']]['module_explain']; ?>
(<?php echo $this->_tpl_vars['arrUpdate'][$this->_sections['cnt']['index']]['eccube_version']; ?>
以降に対応)</td>
										<td align="center"><?php echo ((is_array($_tmp=@$this->_tpl_vars['arrUpdate'][$this->_sections['cnt']['index']]['now_version'])) ? $this->_run_mod_handler('default', true, $_tmp, "-") : smarty_modifier_default($_tmp, "-")); ?>
</td>
										<td align="center"><?php echo $this->_tpl_vars['arrUpdate'][$this->_sections['cnt']['index']]['latest_version']; ?>
</td>		
										<td align="center"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrUpdate'][$this->_sections['cnt']['index']]['release_date'])) ? $this->_run_mod_handler('sfDispDBDate', true, $_tmp, false) : sfDispDBDate($_tmp, false)); ?>
</td>
										<td align="center">
										<?php if ($this->_tpl_vars['arrUpdate'][$this->_sections['cnt']['index']]['now_version'] == "" || $this->_tpl_vars['arrUpdate'][$this->_sections['cnt']['index']]['now_version'] < $this->_tpl_vars['arrUpdate'][$this->_sections['cnt']['index']]['latest_version']): ?>
											<?php if ($this->_tpl_vars['arrUpdate'][$this->_sections['cnt']['index']]['eccube_version'] <= @ECCUBE_VERSION): ?>
											<span class="icon_edit"><a href="#" onclick="fnModeSubmit('install','module_id','<?php echo $this->_tpl_vars['arrUpdate'][$this->_sections['cnt']['index']]['module_id']; ?>
');">適用</a></span>
											<?php else: ?>
											-
											<?php endif; ?>
										<?php else: ?>
											<span class="icon_delete"><a href="#" onclick="fnModeSubmit('uninstall','module_id','<?php echo $this->_tpl_vars['arrUpdate'][$this->_sections['cnt']['index']]['module_id']; ?>
');">削除</a></span>
										<?php endif; ?>									
										</td>
										<td align="center">
										<?php if ($this->_tpl_vars['arrUpdate'][$this->_sections['cnt']['index']]['now_version'] != ""): ?>
											<span class="icon_confirm"><a href="#" onclick="win01('<?php echo @URL_DIR; ?>
load_module.php?module_id=<?php echo $this->_tpl_vars['arrUpdate'][$this->_sections['cnt']['index']]['module_id']; ?>
','module<?php echo $this->_tpl_vars['arrUpdate'][$this->_sections['cnt']['index']]['module_id']; ?>
','<?php echo $this->_tpl_vars['arrUpdate'][$this->_sections['cnt']['index']]['module_x']; ?>
','<?php echo $this->_tpl_vars['arrUpdate'][$this->_sections['cnt']['index']]['module_y']; ?>
'); return false;">実行</a></span>
										<?php else: ?>
											-
										<?php endif; ?>
										</td>
									</tr>
									<?php endfor; endif; ?>
								</table>
								<?php else: ?>
								<table width="678" border="0" cellspacing="1" cellpadding="4" summary=" ">
									<tr bgcolor="#ffffff" align="center" class="fs12n">
										<td>現在、モジュール情報はございません。</td>
									</tr>
								</table>
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">	
									<tr>
										<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
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
												<td><input type="submit" name="subm" value="最新のアップデート情報を取得する"/></td>
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
								
								<?php if ($this->_tpl_vars['update_mess'] != ""): ?>
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
											<?php echo $this->_tpl_vars['update_mess']; ?>

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