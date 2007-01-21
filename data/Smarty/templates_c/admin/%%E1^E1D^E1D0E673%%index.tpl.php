<?php /* Smarty version 2.6.13, created on 2007-01-10 00:09:50
         compiled from products/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'products/index.tpl', 44, false),array('modifier', 'sfGetErrorColor', 'products/index.tpl', 91, false),array('modifier', 'sfRmDupSlash', 'products/index.tpl', 301, false),array('modifier', 'default', 'products/index.tpl', 303, false),array('modifier', 'sfTrimURL', 'products/index.tpl', 314, false),array('modifier', 'number_format', 'products/index.tpl', 324, false),array('modifier', 'sfTrim', 'products/index.tpl', 333, false),array('modifier', 'count', 'products/index.tpl', 348, false),array('function', 'html_options', 'products/index.tpl', 104, false),array('function', 'html_checkboxes', 'products/index.tpl', 109, false),)), $this); ?>

<script type="text/javascript">
// URLの表示非表示切り替え
function lfnDispChange(){
	inner_id = 'switch';

	cnt = form1.item_cnt.value;
	
	if(document.getElementById('disp_url1').style.display == 'none'){
		for (i = 1; i <= cnt; i++) {
			disp_id = 'disp_url'+i;
			document.getElementById(disp_id).style.display="";
	
			disp_id = 'disp_cat'+i;
			document.getElementById(disp_id).style.display="none";
			
			document.getElementById(inner_id).innerHTML = '	URL <a href="#" onClick="lfnDispChange();"> <FONT Color="#FFFF99"> >> カテゴリ表示</FONT></a>';
		}
	}else{
		for (i = 1; i <= cnt; i++) {
			disp_id = 'disp_url'+i;
			document.getElementById(disp_id).style.display="none";
	
			disp_id = 'disp_cat'+i;
			document.getElementById(disp_id).style.display="";
			
			document.getElementById(inner_id).innerHTML = '	カテゴリ <a href="#" onClick="lfnDispChange();"> <FONT Color="#FFFF99"> >> URL表示</FONT></a>';
		}
	}

}

</script>

<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="search_form" id="search_form" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input type="hidden" name="mode" value="search">
<?php $_from = $this->_tpl_vars['arrHidden']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
<?php if ($this->_tpl_vars['key'] == 'campaign_id' || $this->_tpl_vars['key'] == 'search_mode'): ?>
<input type="hidden" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['item'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
	<tr valign="top">
		<td background="<?php echo @URL_DIR; ?>
img/contents/navi_bg.gif" height="402">
			<!-- サブナビ -->
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['tpl_subnavi'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
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
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->検索条件設定</span></td>
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
						<!--検索条件設定テーブルここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<tr class="fs12">
								<td bgcolor="#f2f1ec" width="110">商品ID</td>
								<td bgcolor="#ffffff" width="194"><input type="text" name="search_product_id" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['search_product_id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="30" class="box30" /></td>
								<td bgcolor="#f2f1ec" width="110">規格名称</td>
								<td bgcolor="#ffffff" width="195"><span class="red"><?php echo $this->_tpl_vars['arrErr']['search_product_class_name']; ?>
</span><input type="text" name="search_product_class_name" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['search_product_class_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="30" class="box30"style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_product_class_name'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" /></td>
							</tr>
							<tr class="fs12">
								<td bgcolor="#f2f1ec" width="110">商品コード</td>
								<td bgcolor="#ffffff" width="194"><input type="text" name="search_product_code" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['search_product_code'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="30" class="box30" /></td>
								<td bgcolor="#f2f1ec" width="110">商品名</td>
								<td bgcolor="#ffffff" width="195"><input type="text" name="search_name" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['search_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="30" class="box30" /></td>
							</tr>
							<tr class="fs12">
								<td bgcolor="#f2f1ec" width="110">カテゴリ</td>
								<td bgcolor="#ffffff" width="194">
									<select name="search_category_id" style="<?php if ($this->_tpl_vars['arrErr']['search_category_id'] != ""): ?>background-color: <?php echo @ERR_COLOR;  endif; ?>">
									<option value="">選択してください</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrCatList'],'selected' => $this->_tpl_vars['arrForm']['search_category_id']), $this);?>

									</select>
								</td>
								<td bgcolor="#f2f1ec" width="110">種別</td>
								<td bgcolor="#ffffff" width="195">
									<?php echo smarty_function_html_checkboxes(array('name' => 'search_status','options' => $this->_tpl_vars['arrDISP'],'selected' => $this->_tpl_vars['arrForm']['search_status']), $this);?>

								</td>
							</tr class="fs12">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">登録・更新日</td>
								<td bgcolor="#ffffff" width="499" colspan=3>
									<span class="red"><?php echo $this->_tpl_vars['arrErr']['search_startyear']; ?>
</span>
									<span class="red"><?php echo $this->_tpl_vars['arrErr']['search_endyear']; ?>
</span>		
									<select name="search_startyear" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_startyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">----</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrStartYear'],'selected' => $this->_tpl_vars['arrForm']['search_startyear']), $this);?>

									</select>年
									<select name="search_startmonth" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_startyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">--</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrStartMonth'],'selected' => $this->_tpl_vars['arrForm']['search_startmonth']), $this);?>

									</select>月
									<select name="search_startday" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_startyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">--</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrStartDay'],'selected' => $this->_tpl_vars['arrForm']['search_startday']), $this);?>

									</select>日〜
									<select name="search_endyear" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_endyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">----</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrEndYear'],'selected' => $this->_tpl_vars['arrForm']['search_endyear']), $this);?>

									</select>年
									<select name="search_endmonth" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_endyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">--</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrEndMonth'],'selected' => $this->_tpl_vars['arrForm']['search_endmonth']), $this);?>

									</select>月
									<select name="search_endday" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['search_endyear'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="">--</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrEndDay'],'selected' => $this->_tpl_vars['arrForm']['search_endday']), $this);?>

									</select>日
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">ステータス</td>
								<td bgcolor="#ffffff" width="499" colspan="3">
								<?php echo smarty_function_html_checkboxes(array('name' => 'search_product_flag','options' => $this->_tpl_vars['arrSTATUS'],'selected' => $this->_tpl_vars['arrForm']['search_product_flag']), $this);?>

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
										<td class="fs12n">検索結果表示件数
											<?php $this->assign('key', 'search_page_max'); ?>
											<span class="red12"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
											<select name="<?php echo $this->_tpl_vars['key']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
											<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrPageMax'],'selected' => $this->_tpl_vars['arrForm']['search_page_max']), $this);?>

											</select> 件
										</td>
										<td><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="10" height="1" alt=""></td>
										<td><input type="image" name="subm" onMouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_search_on.jpg',this)" onMouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_search.jpg',this)" src="<?php echo @URL_DIR; ?>
img/contents/btn_search.jpg" width="123" height="24" alt="この条件で検索する" border="0"></td>
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
						<!--検索条件設定テーブルここまで-->
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

<?php if (count ( $this->_tpl_vars['arrErr'] ) == 0 && ( $_POST['mode'] == 'search' || $_POST['mode'] == 'delete' )): ?>

<!--★★検索結果一覧★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input type="hidden" name="mode" value="search">
<input type="hidden" name="product_id" value="">
<input type="hidden" name="category_id" value="">
<?php $_from = $this->_tpl_vars['arrHidden']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
<input type="hidden" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['item'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<?php endforeach; endif; unset($_from); ?>	
	<tr><td colspan="2"><img src="<?php echo @URL_DIR; ?>
img/contents/search_line.jpg" width="878" height="12" alt=""></td></tr>
	<tr bgcolor="cbcbcb">
		<td>
		<table border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/contents/search_left.gif" width="19" height="22" alt=""></td>
				<td>
				<!--検索結果-->
				<table border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td><img src="<?php echo @URL_DIR; ?>
img/contents/reselt_left_top.gif" width="22" height="5" alt=""></td>
						<td background="<?php echo @URL_DIR; ?>
img/contents/reselt_top_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="5" alt=""></td>
						<td><img src="<?php echo @URL_DIR; ?>
img/contents/reselt_right_top.gif" width="22" height="5" alt=""></td>
					</tr>
					<tr>
						<td background="<?php echo @URL_DIR; ?>
img/contents/reselt_left_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/contents/reselt_left_middle.gif" width="22" height="12" alt=""></td>
						<td bgcolor="#393a48" class="white10">検索結果一覧　<span class="reselt"><!--検索結果数--><?php echo $this->_tpl_vars['tpl_linemax']; ?>
件</span>&nbsp;が該当しました。</td>
						<td background="<?php echo @URL_DIR; ?>
img/contents/reselt_right_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="22" height="8" alt=""></td>
					</tr>
					<tr>
						<td><img src="<?php echo @URL_DIR; ?>
img/contents/reselt_left_bottom.gif" width="22" height="5" alt=""></td>
						<td background="<?php echo @URL_DIR; ?>
img/contents/reselt_bottom_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="5" alt=""></td>
						<td><img src="<?php echo @URL_DIR; ?>
img/contents/reselt_right_bottom.gif" width="22" height="5" alt=""></td>
					</tr>
				</table>
				<!--検索結果-->
				<?php if (@ADMIN_MODE == '1'): ?>
				<input type="button" name="subm" value="検索結果をすべて削除" onclick="fnModeSubmit('delete_all','','');" />
				<?php endif; ?>
				</td>
				<td><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="8" height="1" alt=""></td>
				<td><a href="#" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/contents/btn_csv_on.jpg','btn_csv');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/contents/btn_csv.jpg','btn_csv');"  onclick="fnModeSubmit('csv','','');" ><img src="<?php echo @URL_DIR; ?>
img/contents/btn_csv.jpg" width="99" height="22" alt="CSV DOWNLOAD" border="0" name="btn_csv" id="btn_csv"></a></td>
				<td><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="8" height="1" alt=""></td>
				<td><a href="../contents/csv.php?tpl_subno_csv=product"><span class="fs12n"> >> CSV出力項目設定 </span></a></td>
			</tr>
		</table>
		</td>
		<td align="right">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['tpl_pager'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</td>									
	</tr>
	<tr><td bgcolor="cbcbcb" colspan="2"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="5" alt=""></td></tr>
</table>

<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#f0f0f0" align="center">

		<?php if (count ( $this->_tpl_vars['arrProducts'] ) > 0): ?>		
			<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr><td height="12"></td></tr>
				<tr>
					<td bgcolor="#cccccc">
					
					<!--検索結果表示テーブル-->
					<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
						<tr bgcolor="#636469" align="center" class="fs12n">
							<td width="50" rowspan="2"><span class="white">商品ID</span></td>
							<td width="90" rowspan="2"><span class="white">商品画像</span></td>
							<td width="90"><span class="white">商品コード</span></td>
							<td width="350"><span class="white">商品名</span></td>
							<td width="60"><span class="white">在庫</span></td>
							<td width="50" rowspan="2"><span class="white">編集</span></td>
							<td width="50" rowspan="2"><span class="white">確認</span></td>
							<?php if (@OPTION_CLASS_REGIST == 1): ?>
							<td width="50" rowspan="2"><span class="white">規格</span></td>
							<?php endif; ?>
							<td width="50"><span class="white">削除</span></td>
						</tr>
						<tr bgcolor="#636469" align="center" class="fs12n">
							<td width="90"><span class="white">価格(円)</span></td>
							<td width="430">
								<span class="white"  id="switch">
									カテゴリ <a href="#" onClick="lfnDispChange();"> <FONT Color="#FFFF99"> >> URL表示</FONT></a>
								</span>
							</td>
							<td width="60"><span class="white">種別</span></td>
							<td width="50"><span class="white">複製</span></td>
						</tr>

						<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrProducts']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
						<?php $this->assign('status', ($this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['status'])); ?>
						<tr bgcolor="<?php echo $this->_tpl_vars['arrPRODUCTSTATUS_COLOR'][$this->_tpl_vars['status']]; ?>
" class="fs12n">
							<td rowspan="2" align="center"><?php echo $this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['product_id']; ?>
</td>
							<td rowspan="2" align="center">
							<?php if ($this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['main_list_image'] != ""): ?>
								<?php $this->assign('image_path', (@IMAGE_SAVE_DIR)."/".($this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['main_list_image'])); ?>
							<?php else: ?>
								<?php $this->assign('image_path', (@NO_IMAGE_DIR)); ?>
							<?php endif; ?>
							<img src="<?php echo @SITE_URL; ?>
resize_image.php?image=<?php echo ((is_array($_tmp=$this->_tpl_vars['image_path'])) ? $this->_run_mod_handler('sfRmDupSlash', true, $_tmp) : sfRmDupSlash($_tmp)); ?>
&width=65&height=65">
							</td>
							<td><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['product_code'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, "-") : smarty_modifier_default($_tmp, "-")); ?>
</td>
							<td><?php echo ((is_array($_tmp=$this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
							<td align="center">
														<?php if ($this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['stock_unlimited'] == '1'): ?>
							無制限
							<?php else: ?>
							<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['stock'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, "-") : smarty_modifier_default($_tmp, "-")); ?>

							<?php endif; ?>
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="<?php echo @URL_DIR; ?>
" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <?php echo $this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['product_id']; ?>
); return false;" >編集</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<?php echo ((is_array($_tmp=@SITE_URL)) ? $this->_run_mod_handler('sfTrimURL', true, $_tmp) : sfTrimURL($_tmp)); ?>
/products/detail.php?product_id=<?php echo $this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['product_id']; ?>
&admin=on" target="_blank">確認</a></td>
							<?php if (@OPTION_CLASS_REGIST == 1): ?>
							<td align="center" rowspan="2"><span class="icon_class"><a href="<?php echo @URL_DIR; ?>
" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <?php echo $this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['product_id']; ?>
); return false;" >規格</a></td>
							<?php endif; ?>
							<td align="center"><span class="icon_delete"><a href="<?php echo @URL_DIR; ?>
" onclick="fnSetFormValue('category_id', '<?php echo $this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['category_id']; ?>
'); fnModeSubmit('delete', 'product_id', <?php echo $this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['product_id']; ?>
); return false;">削除</a></span></td>
						</tr>
						<tr bgcolor="<?php echo $this->_tpl_vars['arrPRODUCTSTATUS_COLOR'][$this->_tpl_vars['status']]; ?>
" class="fs12n">
							<td align="right">
														<?php if ($this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['price02'] != ""): ?>
							<?php echo ((is_array($_tmp=$this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['price02'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

							<?php else: ?>
							-
							<?php endif; ?>
							</td>
							<td>
														<div id="disp_cat<?php echo $this->_sections['cnt']['iteration']; ?>
" style="display:<?php echo $this->_tpl_vars['cat_flg']; ?>
">
							<?php $this->assign('key', $this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['category_id']); ?>
							<?php echo ((is_array($_tmp=$this->_tpl_vars['arrCatList'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfTrim', true, $_tmp) : sfTrim($_tmp)); ?>

							</div>

														<div id="disp_url<?php echo $this->_sections['cnt']['iteration']; ?>
" style="display:none">
							<?php echo ((is_array($_tmp=@SITE_URL)) ? $this->_run_mod_handler('sfTrimURL', true, $_tmp) : sfTrimURL($_tmp)); ?>
/products/detail.php?product_id=<?php echo $this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['product_id']; ?>

							</div>
							</td>
														<?php $this->assign('key', $this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['status']); ?>
							<td align="center"><?php echo $this->_tpl_vars['arrDISP'][$this->_tpl_vars['key']]; ?>
</td>
							<td align="center"><span class="icon_copy"><a href="<?php echo @URL_DIR; ?>
" onclick="fnChangeAction('./product.php'); fnModeSubmit('copy', 'product_id', <?php echo $this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['product_id']; ?>
); return false;" >複製</a></span></td>
						</tr>
						<!--▲商品<?php echo $this->_sections['cnt']['iteration']; ?>
-->
						<?php endfor; endif; ?>
						<input type="hidden" name="item_cnt" value="<?php echo count($this->_tpl_vars['arrProducts']); ?>
">
					</table>
					<!--検索結果表示テーブル-->
					</td>
				</tr>
			</table>
		<?php endif; ?>

		</td>
	</tr>
</form>
</table>		

<!--★★検索結果一覧★★-->		
<?php endif; ?>