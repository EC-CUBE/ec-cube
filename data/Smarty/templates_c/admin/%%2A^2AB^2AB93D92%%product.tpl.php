<?php /* Smarty version 2.6.13, created on 2007-01-10 00:09:57
         compiled from products/product.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'products/product.tpl', 27, false),array('modifier', 'default', 'products/product.tpl', 138, false),array('modifier', 'sfGetErrorColor', 'products/product.tpl', 144, false),array('modifier', 'sfRmDupSlash', 'products/product.tpl', 314, false),array('function', 'html_options', 'products/product.tpl', 81, false),array('function', 'html_checkboxes', 'products/product.tpl', 91, false),)), $this); ?>
<script type="text/javascript">
<!--
//-->
</script>
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
						<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" enctype="multipart/form-data">
						<?php $_from = $this->_tpl_vars['arrSearchHidden']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
							<input type="hidden" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['item'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
						<?php endforeach; endif; unset($_from); ?>
						<input type="hidden" name="mode" value="edit">
						<input type="hidden" name="image_key" value="">
						<input type="hidden" name="product_id" value="<?php echo $this->_tpl_vars['arrForm']['product_id']; ?>
" >
						<input type="hidden" name="copy_product_id" value="<?php echo $this->_tpl_vars['arrForm']['copy_product_id']; ?>
" >
						<input type="hidden" name="anchor_key" value="">
						<?php $_from = $this->_tpl_vars['arrHidden']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
							<input type="hidden" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['item'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
						<?php endforeach; endif; unset($_from); ?>
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->商品登録</span></td>
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
										<td bgcolor="#f2f1ec" width="160" class="fs12n">商品ID</td>
										<td bgcolor="#ffffff" width="557" class="fs10n"><?php echo $this->_tpl_vars['arrForm']['product_id']; ?>
</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">商品名<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['name']; ?>
</span>
										<input type="text" name="name" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" style="<?php if ($this->_tpl_vars['arrErr']['name'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" size="60" class="box60" /><span class="red"> （上限<?php echo @STEXT_LEN; ?>
文字）</span>
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">商品カテゴリ<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['category_id']; ?>
</span>
										<select name="category_id" style="<?php if ($this->_tpl_vars['arrErr']['category_id'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" onchange="">
										<option value="">選択してください</option>
										<?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['arrCatVal'],'output' => $this->_tpl_vars['arrCatOut'],'selected' => $this->_tpl_vars['arrForm']['category_id']), $this);?>

										</select></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">公開・非公開<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557" class="fs12n"><input type="radio" name="status" value="1" <?php if ($this->_tpl_vars['arrForm']['status'] == '1'): ?>checked<?php endif; ?>/>公開　<input type="radio" name="status" value="2" <?php if ($this->_tpl_vars['arrForm']['status'] == '2'): ?>checked<?php endif; ?> />非公開</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">商品ステータス</td>
										<td bgcolor="#ffffff" width="557">
										<?php echo smarty_function_html_checkboxes(array('name' => 'product_flag','options' => $this->_tpl_vars['arrSTATUS'],'selected' => $this->_tpl_vars['arrForm']['product_flag']), $this);?>

										</td>
									</tr>
									
									<?php if ($this->_tpl_vars['tpl_nonclass'] == true): ?>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">商品コード<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['product_code']; ?>
</span>
										<input type="text" name="product_code" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['product_code'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" style="<?php if ($this->_tpl_vars['arrErr']['product_code'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" size="60" class="box60" /><span class="red"> （上限<?php echo @STEXT_LEN; ?>
文字）</span></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">参考市場価格</td>
										<td bgcolor="#ffffff" width="557">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['price01']; ?>
</span>
										<input type="text" name="price01" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['price01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="6" class="box6" maxlength="<?php echo @PRICE_LEN; ?>
" style="<?php if ($this->_tpl_vars['arrErr']['price01'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>"/>円<span class="red10"> （半角数字で入力）</span></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">商品価格<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['price02']; ?>
</span>
										<input type="text" name="price02" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['price02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="6" class="box6" maxlength="<?php echo @PRICE_LEN; ?>
" style="<?php if ($this->_tpl_vars['arrErr']['price02'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>"/>円<span class="red10"> （半角数字で入力）</span></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">在庫数<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['stock']; ?>
</span>
										<input type="text" name="stock" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['stock'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="6" class="box6" maxlength="<?php echo @AMOUNT_LEN; ?>
" style="<?php if ($this->_tpl_vars['arrErr']['stock'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>"/>個
										<input type="checkbox" name="stock_unlimited" value="1" <?php if ($this->_tpl_vars['arrForm']['stock_unlimited'] == '1'): ?>checked<?php endif; ?> onclick="fnCheckStockLimit('<?php echo @DISABLED_RGB; ?>
');"/>無制限</td>
										</td>
									</tr>
									<?php endif; ?>
									
																		
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">ポイント付与率<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['point_rate']; ?>
</span>
										<input type="text" name="point_rate" value="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrForm']['point_rate'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['arrInfo']['point_rate']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['arrInfo']['point_rate'])); ?>
" size="6" class="box6" maxlength="<?php echo @PERCENTAGE_LEN; ?>
" style="<?php if ($this->_tpl_vars['arrErr']['point_rate'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>"/>％<span class="red10"> （半角数字で入力）</span></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">発送日目安</td>
										<td bgcolor="#ffffff" width="557">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['deliv_date_id']; ?>
</span>
										<select name="deliv_date_id" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['deliv_date_id'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="">選択してください</option>
										<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrDELIVERYDATE'],'selected' => $this->_tpl_vars['arrForm']['deliv_date_id']), $this);?>

										</select>
										</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">購入制限<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['sale_limit']; ?>
</span>
										<input type="text" name="sale_limit" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['sale_limit'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="6" class="box6" maxlength="<?php echo @AMOUNT_LEN; ?>
" style="<?php if ($this->_tpl_vars['arrErr']['sale_limit'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>"/>個
										<input type="checkbox" name="sale_unlimited" value="1" <?php if ($this->_tpl_vars['arrForm']['sale_unlimited'] == '1'): ?>checked<?php endif; ?> onclick="fnCheckSaleLimit('<?php echo @DISABLED_RGB; ?>
');"/>無制限</td>
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">メーカーURL</td>
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['comment1']; ?>
</span>
										<input type="text" name="comment1" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['comment1'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @URL_LEN; ?>
" size="60" class="box60" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['comment1'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" /><span class="red"> （上限<?php echo @URL_LEN; ?>
文字）</span></td>
									</tr>
																		<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">検索ワード<br />※複数の場合は、カンマ( , )区切りで入力して下さい</td>
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['comment3']; ?>
</span>
										<textarea name="comment3" cols="60" rows="8" class="area60" maxlength="<?php echo @LLTEXT_LEN; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['comment3'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['comment3'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea><br /><span class="red"> （上限<?php echo @LLTEXT_LEN; ?>
文字）</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">一覧-メインコメント<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['main_list_comment']; ?>
</span>
										<textarea name="main_list_comment" maxlength="<?php echo @MTEXT_LEN; ?>
" style="<?php if ($this->_tpl_vars['arrErr']['main_list_comment'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>" cols="60" rows="8" class="area60"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['main_list_comment'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea><br /><span class="red"> （上限<?php echo @MTEXT_LEN; ?>
文字）</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-メインコメント<span class="red">(タグ許可)*</span></td>
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr']['main_comment']; ?>
</span>
										<textarea name="main_comment" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['main_comment'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @LLTEXT_LEN; ?>
" style="<?php if ($this->_tpl_vars['arrErr']['main_comment'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>"  cols="60" rows="8" class="area60"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['main_comment'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea><br /><span class="red"> （上限<?php echo @LLTEXT_LEN; ?>
文字）</span></td>
									</tr>
									<tr>
										<?php $this->assign('key', 'main_list_image'); ?>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">一覧-メイン画像<span class="red"> *</span><br />[<?php echo @SMALL_IMAGE_HEIGHT; ?>
×<?php echo @SMALL_IMAGE_WIDTH; ?>
]</td>
										<td bgcolor="#ffffff" width="557" class="fs12n">
										<a name="<?php echo $this->_tpl_vars['key']; ?>
"></a>
										<a name="main_image"></a>
										<a name="main_large_image"></a>
										<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
										<?php if ($this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath'] != ""): ?>
										<img src="<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath']; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />　<a href="" onclick="fnModeSubmit('delete_image', 'image_key', '<?php echo $this->_tpl_vars['key']; ?>
'); return false;">[画像の取り消し]</a><br>
										<?php endif; ?>
										<input type="file" name="main_list_image" size="50" class="box50" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" />
										<input type="button" name="btn" onclick="fnModeSubmit('upload_image', 'image_key', '<?php echo $this->_tpl_vars['key']; ?>
')" value="アップロード">
										</td>
									</tr>
									<tr>
										<?php $this->assign('key', 'main_image'); ?>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-メイン画像<span class="red"> *</span><br />[<?php echo @NORMAL_IMAGE_HEIGHT; ?>
×<?php echo @NORMAL_IMAGE_WIDTH; ?>
]</td>
										<td bgcolor="#ffffff" width="557" class="fs12n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
										<?php if ($this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath'] != ""): ?>
										<img src="<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath']; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />　<a href="" onclick="fnModeSubmit('delete_image', 'image_key', '<?php echo $this->_tpl_vars['key']; ?>
'); return false;">[画像の取り消し]</a><br>
										<?php endif; ?>
										<input type="file" name="main_image" size="50" class="box50" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" />
										<input type="button" name="btn" onclick="fnModeSubmit('upload_image', 'image_key', '<?php echo $this->_tpl_vars['key']; ?>
')" value="アップロード">
										</td>
									</tr>
									<tr>
										<?php $this->assign('key', 'main_large_image'); ?>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-メイン拡大画像<br />[<?php echo @LARGE_IMAGE_HEIGHT; ?>
×<?php echo @LARGE_IMAGE_WIDTH; ?>
]</td>
										<td bgcolor="#ffffff" width="557" class="fs12n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
										<?php if ($this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath'] != ""): ?>
										<img src="<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath']; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />　<a href="" onclick="fnModeSubmit('delete_image', 'image_key', '<?php echo $this->_tpl_vars['key']; ?>
'); return false;">[画像の取り消し]</a><br>
										<?php endif; ?>
										<input type="file" name="<?php echo $this->_tpl_vars['key']; ?>
" size="50" class="box50" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" />
										<input type="button" name="btn" onclick="fnModeSubmit('upload_image', 'image_key', '<?php echo $this->_tpl_vars['key']; ?>
')" value="アップロード">
										</td>
									</tr>
																		<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=@PRODUCTSUB_MAX) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
									<!--▼商品<?php echo $this->_sections['cnt']['iteration']; ?>
-->
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-サブタイトル（<?php echo $this->_sections['cnt']['iteration']; ?>
）</td>
										<?php $this->assign('key', "sub_title".($this->_sections['cnt']['iteration'])); ?>
										<td bgcolor="#ffffff" width="557" class="fs12n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
										<input type="text" name="sub_title<?php echo $this->_sections['cnt']['iteration']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="60" class="box60" maxlength="<?php echo @STEXT_LEN; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"/><span class="red10"> （上限<?php echo @STEXT_LEN; ?>
文字）</span>
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-サブコメント（<?php echo $this->_sections['cnt']['iteration']; ?>
）<span class="red">(タグ許可)</span></td>
										<?php $this->assign('key', "sub_comment".($this->_sections['cnt']['iteration'])); ?>
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
										<textarea name="sub_comment<?php echo $this->_sections['cnt']['iteration']; ?>
" cols="60" rows="8" class="area60" maxlength="<?php echo @LLTEXT_LEN; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea><br /><span class="red10"> （上限<?php echo @LLTEXT_LEN; ?>
文字）</span></td>
									</tr>
									<tr>
										<?php $this->assign('key', "sub_image".($this->_sections['cnt']['iteration'])); ?>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-サブ画像（<?php echo $this->_sections['cnt']['iteration']; ?>
）<br />[<?php echo @NORMAL_SUBIMAGE_HEIGHT; ?>
×<?php echo @NORMAL_SUBIMAGE_WIDTH; ?>
]</td>
										<td bgcolor="#ffffff" width="557" class="fs12n">
										<a name="<?php echo $this->_tpl_vars['key']; ?>
"></a>
										<?php $this->assign('largekey', "sub_large_image".($this->_sections['cnt']['iteration'])); ?>
										<a name="<?php echo $this->_tpl_vars['largekey']; ?>
"></a>
										<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
										<?php if ($this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath'] != ""): ?>
										<img src="<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath']; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />　<a href="" onclick="fnModeSubmit('delete_image', 'image_key', '<?php echo $this->_tpl_vars['key']; ?>
'); return false;">[画像の取り消し]</a><br>
										<?php endif; ?>
										<input type="file" name="<?php echo $this->_tpl_vars['key']; ?>
" size="50" class="box50" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"/>
										<input type="button" name="btn" onclick="fnModeSubmit('upload_image', 'image_key', '<?php echo $this->_tpl_vars['key']; ?>
')" value="アップロード">
										</td>
									</tr>
									<tr>
										<?php $this->assign('key', "sub_large_image".($this->_sections['cnt']['iteration'])); ?>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-サブ拡大画像（<?php echo $this->_sections['cnt']['iteration']; ?>
）<br />[<?php echo @LARGE_SUBIMAGE_HEIGHT; ?>
×<?php echo @LARGE_SUBIMAGE_WIDTH; ?>
]</td>
										<td bgcolor="#ffffff" width="557" class="fs12n">
										<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
										<?php if ($this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath'] != ""): ?>
										<img src="<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath']; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />　<a href="" onclick="fnModeSubmit('delete_image', 'image_key', '<?php echo $this->_tpl_vars['key']; ?>
'); return false;">[画像の取り消し]</a><br>
										<?php endif; ?>
										<input type="file" name="<?php echo $this->_tpl_vars['key']; ?>
" size="50" class="box50" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
"/>
										<input type="button" name="btn" onclick="fnModeSubmit('upload_image', 'image_key', '<?php echo $this->_tpl_vars['key']; ?>
')" value="アップロード">
										</td>
									</tr>
									<!--▲商品<?php echo $this->_sections['cnt']['iteration']; ?>
-->
									<?php endfor; endif; ?>
									
									<?php if (@OPTION_RECOMMEND == 1): ?>			
									<!--▼関連商品-->
									<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=@RECOMMEND_PRODUCT_MAX) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
									<?php $this->assign('recommend_no', ($this->_sections['cnt']['iteration'])); ?>
									<tr>
										<?php $this->assign('key', "recommend_id".($this->_sections['cnt']['iteration'])); ?>
										<?php $this->assign('anckey', "recommend_no".($this->_sections['cnt']['iteration'])); ?>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">関連商品(<?php echo $this->_sections['cnt']['iteration']; ?>
)<br>
										<?php if ($this->_tpl_vars['arrRecommend'][$this->_tpl_vars['recommend_no']]['main_list_image'] != ""): ?>
											<?php $this->assign('image_path', (@IMAGE_SAVE_DIR)."/".($this->_tpl_vars['arrRecommend'][$this->_tpl_vars['recommend_no']]['main_list_image'])); ?>
										<?php else: ?>
											<?php $this->assign('image_path', (@NO_IMAGE_DIR)); ?>
										<?php endif; ?>
										<img src="<?php echo @SITE_URL; ?>
resize_image.php?image=<?php echo ((is_array($_tmp=$this->_tpl_vars['image_path'])) ? $this->_run_mod_handler('sfRmDupSlash', true, $_tmp) : sfRmDupSlash($_tmp)); ?>
&width=65&height=65" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrRecommend'][$this->_tpl_vars['recommend_no']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
										</td>
										<td bgcolor="#ffffff" width="557" class="fs12">
										<a name="<?php echo $this->_tpl_vars['anckey']; ?>
"></a>
										<input type="hidden" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrRecommend'][$this->_tpl_vars['recommend_no']]['product_id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
										<input type="button" name="change" value="変更" onclick="win03('./product_select.php?no=<?php echo $this->_sections['cnt']['iteration']; ?>
', 'search', '500', '500'); " >
										<?php $this->assign('key', "recommend_delete".($this->_sections['cnt']['iteration'])); ?>
										<input type="checkbox" name="<?php echo $this->_tpl_vars['key']; ?>
" value="1">削除<br>
										商品コード:<?php echo $this->_tpl_vars['arrRecommend'][$this->_tpl_vars['recommend_no']]['product_code_min']; ?>
<br>
										商品名:<?php echo ((is_array($_tmp=$this->_tpl_vars['arrRecommend'][$this->_tpl_vars['recommend_no']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<br>
										<?php $this->assign('key', "recommend_comment".($this->_sections['cnt']['iteration'])); ?>
										<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
										<textarea name="<?php echo $this->_tpl_vars['key']; ?>
" cols="60" rows="8" class="area60" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" ><?php echo ((is_array($_tmp=$this->_tpl_vars['arrRecommend'][$this->_tpl_vars['recommend_no']]['comment'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea><br /><span class="red10"> （上限<?php echo @LTEXT_LEN; ?>
文字）</span></td>
										</td>
									</tr>
									<?php endfor; endif; ?>
									<!--▲関連商品-->
									<?php endif; ?>
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
												<?php if (count ( $this->_tpl_vars['arrSearchHidden'] ) > 0): ?>
												<!--▼検索結果へ戻る-->
													<a href="#" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/contents/btn_search_back_on.jpg','back');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/contents/btn_search_back.jpg','back');" onClick="fnChangeAction('<?php echo @URL_SEARCH_TOP; ?>
'); fnModeSubmit('search','',''); return false;"><img src="<?php echo @URL_DIR; ?>
img/contents/btn_search_back.jpg" width="123" height="24" alt="検索画面に戻る" border="0" name="back"></a>
												<!--▲検索結果へ戻る-->
												<?php endif; ?>
												<input type="image" onMouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_confirm_on.jpg',this)" onMouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_confirm.jpg',this)" src="<?php echo @URL_DIR; ?>
img/contents/btn_confirm.jpg" width="123" height="24" alt="確認ページへ" border="0" name="subm" >
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