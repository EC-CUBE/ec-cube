<?php /* Smarty version 2.6.13, created on 2007-01-17 13:46:09
         compiled from system/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'system/index.tpl', 75, false),)), $this); ?>
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="">
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->メンバー管理</span></td>
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
								<tr bgcolor="#ffffff" class="fs12n"><td>
										<table width="650" border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr bgcolor="#ffffff" class="fs12n">
												<td align="center">
												<!--▼ページ送り-->
												<?php echo $this->_tpl_vars['tpl_strnavi']; ?>

												<!--▲ページ送り-->
												</td>
											</tr>
											<tr><td height="10"></td></tr>
										</table>
								
										<!--▼メンバー一覧ここから-->
										<table width="650" bgcolor="#cccccc" border="0" cellspacing="1" cellpadding="5" summary=" ">
											<tr bgcolor="#f2f1ec" align="center" class="fs12n">
												<td width="65">権限</td>
												<td width="155">名前</td>
												<td width="155">所属</td>
												<td width="30">稼動</td>
												<td width="60">非稼動</td>
												<td width="50">編集</td>
												<td width="50">削除</td>
												<td width="80">移動</td>
											</tr>
											<?php unset($this->_sections['data']);
$this->_sections['data']['name'] = 'data';
$this->_sections['data']['loop'] = is_array($_loop=$this->_tpl_vars['list_data']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['data']['show'] = true;
$this->_sections['data']['max'] = $this->_sections['data']['loop'];
$this->_sections['data']['step'] = 1;
$this->_sections['data']['start'] = $this->_sections['data']['step'] > 0 ? 0 : $this->_sections['data']['loop']-1;
if ($this->_sections['data']['show']) {
    $this->_sections['data']['total'] = $this->_sections['data']['loop'];
    if ($this->_sections['data']['total'] == 0)
        $this->_sections['data']['show'] = false;
} else
    $this->_sections['data']['total'] = 0;
if ($this->_sections['data']['show']):

            for ($this->_sections['data']['index'] = $this->_sections['data']['start'], $this->_sections['data']['iteration'] = 1;
                 $this->_sections['data']['iteration'] <= $this->_sections['data']['total'];
                 $this->_sections['data']['index'] += $this->_sections['data']['step'], $this->_sections['data']['iteration']++):
$this->_sections['data']['rownum'] = $this->_sections['data']['iteration'];
$this->_sections['data']['index_prev'] = $this->_sections['data']['index'] - $this->_sections['data']['step'];
$this->_sections['data']['index_next'] = $this->_sections['data']['index'] + $this->_sections['data']['step'];
$this->_sections['data']['first']      = ($this->_sections['data']['iteration'] == 1);
$this->_sections['data']['last']       = ($this->_sections['data']['iteration'] == $this->_sections['data']['total']);
?><!--▼メンバー<?php echo $this->_sections['data']['iteration']; ?>
-->
											<tr bgcolor="#ffffff" class="fs12">
												<?php $this->assign('auth', $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['authority']); ?><td width="65" align="center"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrAUTHORITY'][$this->_tpl_vars['auth']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
												<td width=""><?php echo ((is_array($_tmp=$this->_tpl_vars['list_data'][$this->_sections['data']['index']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
												<td width=""><?php echo ((is_array($_tmp=$this->_tpl_vars['list_data'][$this->_sections['data']['index']]['department'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
												<td width="" align="center"><?php if ($this->_tpl_vars['list_data'][$this->_sections['data']['index']]['work'] == 1): ?><input type="radio" name="radio<?php echo $this->_sections['data']['iteration']; ?>
" value="稼動" onclick="fnChangeRadio(this.name, 1, <?php echo $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['member_id']; ?>
, <?php echo $this->_tpl_vars['tpl_disppage']; ?>
);" checked /><?php else: ?><input type="radio" name="radio<?php echo $this->_sections['data']['iteration']; ?>
" value="稼動" onclick="fnChangeRadio(this.name, 1, <?php echo $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['member_id']; ?>
, <?php echo $this->_tpl_vars['tpl_disppage']; ?>
);"/><?php endif; ?></td>
												<td width="" align="center"><?php if ($this->_tpl_vars['list_data'][$this->_sections['data']['index']]['work'] == 0): ?><input type="radio" name="radio<?php echo $this->_sections['data']['iteration']; ?>
" value="非稼動"  onclick="fnChangeRadio(this.name, 0, <?php echo $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['member_id']; ?>
, <?php echo $this->_tpl_vars['tpl_disppage']; ?>
);" checked /><?php else: ?><input type="radio" name="radio<?php echo $this->_sections['data']['iteration']; ?>
" value="非稼動" onclick="fnChangeRadio(this.name, 0, <?php echo $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['member_id']; ?>
, <?php echo $this->_tpl_vars['tpl_disppage']; ?>
);" <?php if ($this->_tpl_vars['workmax'] <= 1): ?>disabled<?php endif; ?>  /><?php endif; ?></td>
												<td width="" align="center"><a href="./" onClick="win01('./input.php?id=<?php echo $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['member_id']; ?>
&pageno=<?php echo $this->_tpl_vars['tpl_disppage']; ?>
','member_edit','500','420'); return false;">編集</a></td>
												<td width="" align="center"><?php if ($this->_tpl_vars['workmax'] > 1): ?><a href="./" onClick="fnDeleteMember(<?php echo $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['member_id']; ?>
,<?php echo $this->_tpl_vars['tpl_disppage']; ?>
); return false;">削除</a><?php else: ?>-<?php endif; ?></td>
												<td width="" align="center">
												<?php echo $this->_tpl_vars['tpl_nomove']; ?>

												<?php if (! ( $this->_sections['data']['first'] && $this->_tpl_vars['tpl_disppage'] == 1 )): ?><a href="./rank.php?id=<?php echo $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['member_id']; ?>
&move=up&pageno=<?php echo $this->_tpl_vars['tpl_disppage']; ?>
">上へ</a><?php endif; ?>
												<?php if (! ( $this->_sections['data']['last'] && $this->_tpl_vars['tpl_disppage'] == $this->_tpl_vars['tpl_pagemax'] )): ?><a href="./rank.php?id=<?php echo $this->_tpl_vars['list_data'][$this->_sections['data']['index']]['member_id']; ?>
&move=down&pageno=<?php echo $this->_tpl_vars['tpl_disppage']; ?>
">下へ</a><?php endif; ?>
												</td>
											</tr>
											<!--▲メンバー<?php echo $this->_sections['data']['iteration']; ?>
-->
											<?php endfor; endif; ?>
										</table>
										<table width="650" border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr><td height="10"></td></tr>
											<tr bgcolor="#ffffff" class="fs12n">
												<td align="center">
												<!--▼ページ送り-->
												<?php echo $this->_tpl_vars['tpl_strnavi']; ?>

												<!--▲ページ送り-->
								
												</td>
											</tr>
										</table>
									</td></tr>

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
												<td><input type="button" name="new" value="メンバー新規登録" onclick="win01('./input.php','input','500','420');" /></td>
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