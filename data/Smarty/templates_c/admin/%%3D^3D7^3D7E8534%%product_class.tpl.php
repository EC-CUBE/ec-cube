<?php /* Smarty version 2.6.13, created on 2007-01-10 00:10:05
         compiled from products/product_class.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'products/product_class.tpl', 25, false),array('modifier', 'default', 'products/product_class.tpl', 118, false),array('function', 'html_options', 'products/product_class.tpl', 65, false),array('function', 'sfSetErrorStyle', 'products/product_class.tpl', 142, false),)), $this); ?>
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
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
							<form name="form1" id="form1" method="post" action="">
							<?php $_from = $this->_tpl_vars['arrSearchHidden']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
								<input type="hidden" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['item'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
							<?php endforeach; endif; unset($_from); ?>
							<input type="hidden" name="mode" value="edit">
							<input type="hidden" name="product_id" value="<?php echo $this->_tpl_vars['tpl_product_id']; ?>
">
							<input type="hidden" name="pageno" value="<?php echo $this->_tpl_vars['tpl_pageno']; ?>
">
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->規格登録</span></td>
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
										<td bgcolor="#f2f1ec" width="160" class="fs12n">商品名</td>
										<td bgcolor="#ffffff" width="557" class="fs12n"><?php echo $this->_tpl_vars['arrForm']['product_name']; ?>
</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">規格1<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557" class="fs12n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['select_class_id1']; ?>
</span>
										<select name="select_class_id1">
											<option value="">選択してください</option>
											<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrClass'],'selected' => $this->_tpl_vars['arrForm']['select_class_id1']), $this);?>

										</select>
										</td>
									</tr>
									<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">規格2</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<span class="red12"><?php echo $this->_tpl_vars['arrErr']['select_class_id2']; ?>
</span>
									<select name="select_class_id2">
										<option value="">選択してください</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrClass'],'selected' => $this->_tpl_vars['arrForm']['select_class_id2']), $this);?>

									</select>
									</td>
									</tr>
									<tr>
										<td align="center" bgcolor="#f2f1ec" colspan=2>
											<input type="button" value="検索結果へ戻る" onclick="fnChangeAction('<?php echo @URL_SEARCH_TOP; ?>
'); fnModeSubmit('search','',''); return false;" >
											<input type="button" name="btn" value="表示する" onclick="fnModeSubmit('disp','','')">
											<?php if (count ( $this->_tpl_vars['arrClassCat'] ) > 0): ?>
											<input type="button" name="btn" value="削除する" onclick="fnModeSubmit('delete','','');">
											<?php endif; ?>
										</td>
									</tr>
								</table>

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
								</table>

								<?php if (count ( $this->_tpl_vars['arrClassCat'] ) > 0): ?>
						
									<?php $_from = $this->_tpl_vars['arrClassCat']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['i'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['i']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['item']):
        $this->_foreach['i']['iteration']++;
?>
									<?php if (($this->_foreach['i']['iteration'] <= 1)): ?>
									<?php $this->assign('cnt', $this->_foreach['i']['total']); ?>	
									<?php endif; ?>
									<?php endforeach; endif; unset($_from); ?>
						
									<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
										<tr bgcolor="#f2f1ec">
											<td class="fs12n" align="left">
												<a href="<?php echo @URL_DIR; ?>
" onclick="fnAllCheck(); return false;">全選択</a>　
												<a href="<?php echo @URL_DIR; ?>
" onclick="fnAllUnCheck(); return false;">全解除</a>　
												<a href="<?php echo @URL_DIR; ?>
" onclick="fnCopyValue('<?php echo $this->_tpl_vars['cnt']; ?>
', '<?php echo @DISABLED_RGB; ?>
'); return false;">一行目のデータをコピーする</a></td>
										</tr>
									</table>

									<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
										<?php $this->assign('class_id1', $this->_tpl_vars['arrForm']['class_id1']); ?>
										<?php $this->assign('class_id2', $this->_tpl_vars['arrForm']['class_id2']); ?>
										<input type="hidden" name="class_id1" value="<?php echo $this->_tpl_vars['class_id1']; ?>
">
										<input type="hidden" name="class_id2" value="<?php echo $this->_tpl_vars['class_id2']; ?>
">
										<tr bgcolor="#f2f1ec" align="center" class="fs12n">
											<td width="30">登録</td>
											<td width="100">規格1(<?php echo ((is_array($_tmp=@$this->_tpl_vars['arrClass'][$this->_tpl_vars['class_id1']])) ? $this->_run_mod_handler('default', true, $_tmp, "未選択") : smarty_modifier_default($_tmp, "未選択")); ?>
)</td>
											<td width="100">規格2(<?php echo ((is_array($_tmp=@$this->_tpl_vars['arrClass'][$this->_tpl_vars['class_id2']])) ? $this->_run_mod_handler('default', true, $_tmp, "未選択") : smarty_modifier_default($_tmp, "未選択")); ?>
)</td>
											<td width="80">商品コード</td>
											<td width="160">在庫(個)</td>
											<td width="100">参考市場価格(円)</td>
											<td width="100">価格(円)</td>
										</tr>
										<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrClassCat']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
										<?php $this->assign('key', "error:".($this->_sections['cnt']['iteration'])); ?>
										<?php if ($this->_tpl_vars['arrErr'][$this->_tpl_vars['key']] != ""): ?>
										<tr bgcolor="#ffffff" class="fs12">
											<td bgcolor="#ffffff" class="fs12" colspan="8"><span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span></td>
										</tr>
										<?php endif; ?>
										<tr  bgcolor="#ffffff" class="fs10n">
											<input type="hidden" name="classcategory_id1:<?php echo $this->_sections['cnt']['iteration']; ?>
" value="<?php echo $this->_tpl_vars['arrClassCat'][$this->_sections['cnt']['index']]['classcategory_id1']; ?>
">
											<input type="hidden" name="classcategory_id2:<?php echo $this->_sections['cnt']['iteration']; ?>
" value="<?php echo $this->_tpl_vars['arrClassCat'][$this->_sections['cnt']['index']]['classcategory_id2']; ?>
">
											<input type="hidden" name="name1:<?php echo $this->_sections['cnt']['iteration']; ?>
" value="<?php echo $this->_tpl_vars['arrClassCat'][$this->_sections['cnt']['index']]['name1']; ?>
">
											<input type="hidden" name="name2:<?php echo $this->_sections['cnt']['iteration']; ?>
" value="<?php echo $this->_tpl_vars['arrClassCat'][$this->_sections['cnt']['index']]['name2']; ?>
">
											<?php $this->assign('key', "check:".($this->_sections['cnt']['iteration'])); ?>
											<td align="center"><input type="checkbox" name="check:<?php echo $this->_sections['cnt']['iteration']; ?>
" value="1" <?php if ($this->_tpl_vars['arrForm'][$this->_tpl_vars['key']] == 1): ?>checked="checked"<?php endif; ?>></td>
											<td><?php echo $this->_tpl_vars['arrClassCat'][$this->_sections['cnt']['index']]['name1']; ?>
</td>
											<td><?php echo $this->_tpl_vars['arrClassCat'][$this->_sections['cnt']['index']]['name2']; ?>
</td>
											<?php $this->assign('key', "product_code:".($this->_sections['cnt']['iteration'])); ?>
											<td align="center"><input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]; ?>
" size="6" class="box6" maxlength="<?php echo @STEXT_LEN; ?>
" <?php if ($this->_tpl_vars['arrErr'][$this->_tpl_vars['key']] != ""):  echo sfSetErrorStyle(array(), $this); endif; ?>></td>
											<?php $this->assign('key', "stock:".($this->_sections['cnt']['iteration'])); ?>
											<?php $this->assign('chkkey', "stock_unlimited:".($this->_sections['cnt']['iteration'])); ?>
											<td align="center">
											<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]; ?>
" size="6" class="box6" maxlength="<?php echo @AMOUNT_LEN; ?>
" <?php if ($this->_tpl_vars['arrErr'][$this->_tpl_vars['key']] != ""):  echo sfSetErrorStyle(array(), $this); endif; ?>>
											<?php $this->assign('key', "stock_unlimited:".($this->_sections['cnt']['iteration'])); ?>
											<input type="checkbox" name="<?php echo $this->_tpl_vars['key']; ?>
" value="1" <?php if ($this->_tpl_vars['arrForm'][$this->_tpl_vars['key']] == '1'): ?>checked<?php endif; ?> onClick="fnCheckStockNoLimit('<?php echo $this->_sections['cnt']['iteration']; ?>
','<?php echo @DISABLED_RGB; ?>
');"/>無制限</td>
											</td>
											<?php $this->assign('key', "price01:".($this->_sections['cnt']['iteration'])); ?>
											<td align="center"><input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]; ?>
" size="6" class="box6" maxlength="<?php echo @PRICE_LEN; ?>
" <?php if ($this->_tpl_vars['arrErr'][$this->_tpl_vars['key']] != ""):  echo sfSetErrorStyle(array(), $this); endif; ?>></td>
											<?php $this->assign('key', "price02:".($this->_sections['cnt']['iteration'])); ?>
											<td align="center"><input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]; ?>
" size="6" class="box6" maxlength="<?php echo @PRICE_LEN; ?>
" <?php if ($this->_tpl_vars['arrErr'][$this->_tpl_vars['key']] != ""):  echo sfSetErrorStyle(array(), $this); endif; ?>></td>
										</tr>
										<?php endfor; endif; ?>
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