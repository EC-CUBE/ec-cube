<?php /* Smarty version 2.6.13, created on 2007-01-15 20:16:41
         compiled from /home/web/beta.ec-cube.net/html/user_data/templates/detail.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/home/web/beta.ec-cube.net/html/user_data/templates/detail.tpl', 67, false),array('modifier', 'nl2br', '/home/web/beta.ec-cube.net/html/user_data/templates/detail.tpl', 81, false),array('modifier', 'count_characters', '/home/web/beta.ec-cube.net/html/user_data/templates/detail.tpl', 112, false),array('modifier', 'count', '/home/web/beta.ec-cube.net/html/user_data/templates/detail.tpl', 131, false),array('modifier', 'sfPreTax', '/home/web/beta.ec-cube.net/html/user_data/templates/detail.tpl', 142, false),array('modifier', 'number_format', '/home/web/beta.ec-cube.net/html/user_data/templates/detail.tpl', 142, false),array('modifier', 'sfPrePoint', '/home/web/beta.ec-cube.net/html/user_data/templates/detail.tpl', 161, false),array('modifier', 'sfGetErrorColor', '/home/web/beta.ec-cube.net/html/user_data/templates/detail.tpl', 184, false),array('modifier', 'default', '/home/web/beta.ec-cube.net/html/user_data/templates/detail.tpl', 210, false),array('modifier', 'sfDispDBDate', '/home/web/beta.ec-cube.net/html/user_data/templates/detail.tpl', 302, false),array('modifier', 'sfRmDupSlash', '/home/web/beta.ec-cube.net/html/user_data/templates/detail.tpl', 352, false),array('function', 'html_options', '/home/web/beta.ec-cube.net/html/user_data/templates/detail.tpl', 186, false),)), $this); ?>

<script type="text/javascript" src="lightbox.js"></script>
<link rel="stylesheet" href="lightbox.css" type="text/css" />


<script type="text/javascript">
<!--
// セレクトボックスに項目を割り当てる。
function lnSetSelect(form, name1, name2, val) {
	
	sele11 = document[form][name1];
	sele12 = document[form][name2];
	
	if(sele11 && sele12) {
		index = sele11.selectedIndex;
		
		// セレクトボックスのクリア
		count = sele12.options.length;
		for(i = count; i >= 0; i--) {
			sele12.options[i] = null;
		}
		
		// セレクトボックスに値を割り当てる
		len = lists[index].length;
		for(i = 0; i < len; i++) {
			sele12.options[i] = new Option(lists[index][i], vals[index][i]);
			if(val != "" && vals[index][i] == val) {
				sele12.options[i].selected = true;
			}
		}
	}
}

//-->
</script>

<style type="text/css">

#sample2{
	_display:block;
}#sample2 a{
	display:block;
}
</style>

<!--▼CONTENTS-->
<table width="" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#ffffff" valign="top" align="left">
		<!--▼MAIN CONTENTS-->
		<table cellspacing="0" cellpadding="0" summary=" ">
			<tr valign="top">
				<td align="right" bgcolor="#ffffff">
				<!--タイトルここから-->
				<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/products/title_top.gif" width="580" height="8" alt=""></td></tr>
					<tr bgcolor="#ffebca">
						<td><img src="<?php echo @URL_DIR; ?>
img/products/title_icon.gif" width="29" height="24" alt=""></td>
						<td>
						<table width="546" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr valign="top">
								<td class="fs18"><span class="blackst"><!--★タイトル★--><?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_subtitle'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</span></td>
							</tr>
						</table>
						</td>
						<td><img src="<?php echo @URL_DIR; ?>
img/products/title_left.gif" width="5" height="24" alt=""></td>
					</tr>
					<tr><td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/products/title_under.gif" width="580" height="8" alt=""></td></tr>
					<tr><td height="10"></td></tr>
				</table>
				<!--タイトルここまで-->

				<!--詳細ここから-->
				<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td colspan="2" class="fs12"><!--★詳細メインコメント★--><?php echo ((is_array($_tmp=$this->_tpl_vars['arrProduct']['main_comment'])) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</td>
					</tr>
					<tr><td height="15"></td></tr>
					<tr valign="top">
						<td>
						<table width="290" border="0" cellspacing="0" cellpadding="0" summary=" ">	
							<tr>
								<td align="center" valign="middle" width="<?php echo @NORMAL_IMAGE_WIDTH; ?>
" height="<?php echo @NORMAL_IMAGE_HEIGHT; ?>
">
								<?php if ($this->_tpl_vars['arrProduct']['main_large_image'] != ""): ?>
									<!--メイン画像--><?php $this->assign('key', 'main_image'); ?><a href="<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath']; ?>
" rel="lightbox" onclick="win01('./detail_image.php?product_id=<?php echo $this->_tpl_vars['arrProduct']['product_id']; ?>
&image=main_large_image<?php if ($_GET['admin'] == 'on'): ?>&admin=on<?php endif; ?>','detail_image','<?php echo $this->_tpl_vars['arrFile']['main_large_image']['width']+60; ?>
', '<?php echo $this->_tpl_vars['arrFile']['main_large_image']['height']+80; ?>
'); return false;" target="_blank"><img src="<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath']; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrProduct']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /></a>
								<?php else: ?>
									<div id="picture"><!--メイン画像--><?php $this->assign('key', 'main_image'); ?><a href="<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath']; ?>
" rel="lightbox"><img src="<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath']; ?>
" " alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrProduct']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /></a></div>
								<?php endif; ?>
								</td>
							</tr>
							<tr><td height="8"></td></tr>
							<tr>
								<td>
								<?php if ($this->_tpl_vars['arrProduct']['main_large_image'] != ""): ?>
									<!--★拡大する★--><a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="win01('./detail_image.php?product_id=<?php echo $this->_tpl_vars['arrProduct']['product_id']; ?>
&image=main_large_image<?php if ($_GET['admin'] == 'on'): ?>&admin=on<?php endif; ?>','detail_image', '<?php echo $this->_tpl_vars['arrFile']['main_large_image']['width']+60; ?>
', '<?php echo $this->_tpl_vars['arrFile']['main_large_image']['height']+80; ?>
'); return false;" target="_blank"><img src="<?php echo @URL_DIR; ?>
img/products/b_expansion.gif" width="85" height="13" alt="画像を拡大する" /></a>
								<?php endif; ?>
								</td>
							</tr>
						</table>		
						</td>
						<td align="right">
						<table width="280" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<!--アイコン-->
							<tr>
								<td>
									<?php $this->assign('sts_cnt', 0); ?>
									<?php unset($this->_sections['flg']);
$this->_sections['flg']['name'] = 'flg';
$this->_sections['flg']['loop'] = is_array($_loop=((is_array($_tmp=$this->_tpl_vars['arrProduct']['product_flag'])) ? $this->_run_mod_handler('count_characters', true, $_tmp) : smarty_modifier_count_characters($_tmp))) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['flg']['show'] = true;
$this->_sections['flg']['max'] = $this->_sections['flg']['loop'];
$this->_sections['flg']['step'] = 1;
$this->_sections['flg']['start'] = $this->_sections['flg']['step'] > 0 ? 0 : $this->_sections['flg']['loop']-1;
if ($this->_sections['flg']['show']) {
    $this->_sections['flg']['total'] = $this->_sections['flg']['loop'];
    if ($this->_sections['flg']['total'] == 0)
        $this->_sections['flg']['show'] = false;
} else
    $this->_sections['flg']['total'] = 0;
if ($this->_sections['flg']['show']):

            for ($this->_sections['flg']['index'] = $this->_sections['flg']['start'], $this->_sections['flg']['iteration'] = 1;
                 $this->_sections['flg']['iteration'] <= $this->_sections['flg']['total'];
                 $this->_sections['flg']['index'] += $this->_sections['flg']['step'], $this->_sections['flg']['iteration']++):
$this->_sections['flg']['rownum'] = $this->_sections['flg']['iteration'];
$this->_sections['flg']['index_prev'] = $this->_sections['flg']['index'] - $this->_sections['flg']['step'];
$this->_sections['flg']['index_next'] = $this->_sections['flg']['index'] + $this->_sections['flg']['step'];
$this->_sections['flg']['first']      = ($this->_sections['flg']['iteration'] == 1);
$this->_sections['flg']['last']       = ($this->_sections['flg']['iteration'] == $this->_sections['flg']['total']);
?>
										<?php if ($this->_tpl_vars['arrProduct']['product_flag'][$this->_sections['flg']['index']] == '1'): ?>
											<?php $this->assign('key', ($this->_sections['flg']['iteration'])); ?>
											<img src="<?php echo $this->_tpl_vars['arrSTATUS_IMAGE'][$this->_tpl_vars['key']]; ?>
" width="65" height="17" alt="<?php echo $this->_tpl_vars['arrSTATUS'][$this->_tpl_vars['key']]; ?>
" id="icon" />
											<?php $this->assign('sts_cnt', $this->_tpl_vars['sts_cnt']+1); ?>
										<?php endif; ?>
									<?php endfor; endif; ?>
								</td>
							</tr>
							<!--アイコン-->
							<?php if ($this->_tpl_vars['sts_cnt'] > 0): ?><tr><td height="5"></td></tr><?php endif; ?>
							<tr>
								<td class="fs18"><span class="orangest"><!--★商品名★--><?php echo ((is_array($_tmp=$this->_tpl_vars['arrProduct']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</span></td>
							</tr>
							<tr><td height="3"></td></tr>
							<tr>
								<td><span class="red">
								<!--★商品コード★-->
									<span class="fs12">商品コード</span><span class="fs10"></span></span><span class="redst"><span class="fs12">：
									<?php $this->assign('codecnt', count($this->_tpl_vars['arrProductCode'])); ?>
									<?php $this->assign('codemax', ($this->_tpl_vars['codecnt']-1)); ?>
									<?php if ($this->_tpl_vars['codecnt'] > 1): ?>
										<?php echo $this->_tpl_vars['arrProductCode']['0']; ?>
~<?php echo $this->_tpl_vars['arrProductCode'][$this->_tpl_vars['codemax']]; ?>

									<?php else: ?>
										<?php echo $this->_tpl_vars['arrProductCode']['0']; ?>

									<?php endif; ?>
									</span></span><br/>
								<!--★価格★-->
									<span class="red"><span class="fs12">価格</span><span class="fs10">(税込)</span></span><span class="redst"><span class="fs12">：
									<?php if ($this->_tpl_vars['arrProduct']['price02_min'] == $this->_tpl_vars['arrProduct']['price02_max']): ?>				
										<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrProduct']['price02_min'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

									<?php else: ?>
										<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrProduct']['price02_min'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
~<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrProduct']['price02_max'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

									<?php endif; ?>
									円</span></span><br/>
									
									<?php if ($this->_tpl_vars['arrProduct']['price01_max'] > 0): ?>
										<span class="fs12"><span class="red">参考市場価格：</span><span class="redst">
										<?php if ($this->_tpl_vars['arrProduct']['price01_min'] == $this->_tpl_vars['arrProduct']['price01_max']): ?>				
											<?php echo ((is_array($_tmp=$this->_tpl_vars['arrProduct']['price01_min'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

										<?php else: ?>
											<?php echo ((is_array($_tmp=$this->_tpl_vars['arrProduct']['price01_min'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
~<?php echo ((is_array($_tmp=$this->_tpl_vars['arrProduct']['price01_max'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

										<?php endif; ?>
										円
										</span></span><br/>
									<?php endif; ?>
								<!--★ポイント★-->
									<span class="red"><span class="fs12"> ポイント</span></span><span class="redst"><span class="fs12">：
								<?php if ($this->_tpl_vars['arrProduct']['price02_min'] == $this->_tpl_vars['arrProduct']['price02_max']): ?>
									<?php echo ((is_array($_tmp=$this->_tpl_vars['arrProduct']['price02_min'])) ? $this->_run_mod_handler('sfPrePoint', true, $_tmp, $this->_tpl_vars['arrProduct']['point_rate'], @POINT_RULE, $this->_tpl_vars['arrProduct']['product_id']) : sfPrePoint($_tmp, $this->_tpl_vars['arrProduct']['point_rate'], @POINT_RULE, $this->_tpl_vars['arrProduct']['product_id'])); ?>

								<?php else: ?>
									<?php if (((is_array($_tmp=$this->_tpl_vars['arrProduct']['price02_min'])) ? $this->_run_mod_handler('sfPrePoint', true, $_tmp, $this->_tpl_vars['arrProduct']['point_rate'], @POINT_RULE, $this->_tpl_vars['arrProduct']['product_id']) : sfPrePoint($_tmp, $this->_tpl_vars['arrProduct']['point_rate'], @POINT_RULE, $this->_tpl_vars['arrProduct']['product_id'])) == ((is_array($_tmp=$this->_tpl_vars['arrProduct']['price02_max'])) ? $this->_run_mod_handler('sfPrePoint', true, $_tmp, $this->_tpl_vars['arrProduct']['point_rate'], @POINT_RULE, $this->_tpl_vars['arrProduct']['product_id']) : sfPrePoint($_tmp, $this->_tpl_vars['arrProduct']['point_rate'], @POINT_RULE, $this->_tpl_vars['arrProduct']['product_id']))): ?>
										<?php echo ((is_array($_tmp=$this->_tpl_vars['arrProduct']['price02_min'])) ? $this->_run_mod_handler('sfPrePoint', true, $_tmp, $this->_tpl_vars['arrProduct']['point_rate'], @POINT_RULE, $this->_tpl_vars['arrProduct']['product_id']) : sfPrePoint($_tmp, $this->_tpl_vars['arrProduct']['point_rate'], @POINT_RULE, $this->_tpl_vars['arrProduct']['product_id'])); ?>

									<?php else: ?>
										<?php echo ((is_array($_tmp=$this->_tpl_vars['arrProduct']['price02_min'])) ? $this->_run_mod_handler('sfPrePoint', true, $_tmp, $this->_tpl_vars['arrProduct']['point_rate'], @POINT_RULE, $this->_tpl_vars['arrProduct']['product_id']) : sfPrePoint($_tmp, $this->_tpl_vars['arrProduct']['point_rate'], @POINT_RULE, $this->_tpl_vars['arrProduct']['product_id'])); ?>
~<?php echo ((is_array($_tmp=$this->_tpl_vars['arrProduct']['price02_max'])) ? $this->_run_mod_handler('sfPrePoint', true, $_tmp, $this->_tpl_vars['arrProduct']['point_rate'], @POINT_RULE, $this->_tpl_vars['arrProduct']['product_id']) : sfPrePoint($_tmp, $this->_tpl_vars['arrProduct']['point_rate'], @POINT_RULE, $this->_tpl_vars['arrProduct']['product_id'])); ?>

									<?php endif; ?>
								<?php endif; ?>
								Pt</span></span>
							</tr>
							<tr><td height="15"></td></tr>
							<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['REQUEST_URI'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
							<input type="hidden" name="mode" value="cart">
							<input type="hidden" name="product_id" value="<?php echo $this->_tpl_vars['tpl_product_id']; ?>
">
							
							<?php if ($this->_tpl_vars['tpl_classcat_find1']): ?>
							<tr><td height="5" colspan="2" align="left" class="fs12"><span class="redst"><?php if ($this->_tpl_vars['arrErr']['classcategory_id1'] != ""): ?>※ <?php echo $this->_tpl_vars['tpl_class_name1']; ?>
を入力して下さい。<?php endif; ?></span></td></tr>
							<tr>
								<td class="fs12"><img src="<?php echo @URL_DIR; ?>
img/common/arrow_gray.gif" width="15" height="10" alt=""><strong><?php echo $this->_tpl_vars['tpl_class_name1']; ?>
</strong></td>
							</tr>
							<tr><td height="3"></td></tr>
							<tr>
								<td>
									<select name="classcategory_id1" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['classcategory_id1'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" onchange="lnSetSelect('form1', 'classcategory_id1', 'classcategory_id2', ''); ">
									<option value="">選択してください</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrClassCat1'],'selected' => $this->_tpl_vars['arrForm']['classcategory_id1']['value']), $this);?>

									</select>
								</td>
							</tr>
							<tr><td height="10"></td></tr>
							<?php endif; ?>
							<?php if ($this->_tpl_vars['tpl_stock_find']): ?>
								<?php if ($this->_tpl_vars['tpl_classcat_find2']): ?>
								<tr><td height="5" colspan="2" align="left" class="fs12"><span class="redst"><?php if ($this->_tpl_vars['arrErr']['classcategory_id2'] != ""): ?>※ <?php echo $this->_tpl_vars['tpl_class_name2']; ?>
を入力して下さい。<?php endif; ?></span></td></tr>
								<tr>
									<td class="fs12"><img src="<?php echo @URL_DIR; ?>
img/common/arrow_gray.gif" width="15" height="10" alt=""><strong><?php echo $this->_tpl_vars['tpl_class_name2']; ?>
</strong></td>
								</tr>
								<tr><td height="3"></td></tr>
								<tr>
									<td>
										<select name="classcategory_id2" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['classcategory_id2'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
										<option value="">選択してください</option>
										</select>
									</td>
								</tr>
								<tr><td height="10"></td></tr>
								<?php endif; ?>
								<tr>
									<td class="fs12"><?php if ($this->_tpl_vars['arrErr']['quantity'] != ""): ?><span class="redst"><?php echo $this->_tpl_vars['arrErr']['quantity']; ?>
</span><br/><?php endif; ?><img src="<?php echo @URL_DIR; ?>
img/common/arrow_gray.gif" width="15" height="10" alt=""><strong>個　数</strong>
										<input type="text" name="quantity" size="3" class="box3" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['arrForm']['quantity']['value'])) ? $this->_run_mod_handler('default', true, $_tmp, 1) : smarty_modifier_default($_tmp, 1)); ?>
" maxlength=<?php echo @INT_LEN; ?>
 style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['quantity'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" >
									</td>
								</tr>
							<?php endif; ?>
							<tr><td height="20"><img src="<?php echo @URL_DIR; ?>
img/common/line_280.gif" width="280" height="1" alt=""></td></tr>
							<tr>
								<td align="center">
									<?php if ($this->_tpl_vars['tpl_stock_find']): ?>
										<!--★カゴに入れる★--><a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="document.form1.submit(); return false;" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/products/b_cartin_on.gif','cart');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/products/b_cartin.gif','cart');"><img src="<?php echo @URL_DIR; ?>
img/products/b_cartin.gif" width="115" height="25" alt="カゴに入れる" name="cart" id="cart" /></a>
									<?php else: ?>
										<table width="285" cellspacing="0" cellpadding="0" summary=" ">
										<tr><td height="10"></td></tr>
										<tr>
											<td align="center" class="fs12">
											<span class="red">申し訳ございませんが、只今品切れ中です。</span>
											</td>
										</tr>
										<tr><td height="10"></td></tr>
										</table>
									<?php endif; ?>
								</td>
							</tr>
							</form>
						</table>
						</td>
					</tr>
					<tr><td height="35"></td></tr>
				</table>
				<!--詳細ここまで-->
				
				<!--▼サブコメントここから-->		
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
				<?php $this->assign('key', "sub_title".($this->_sections['cnt']['iteration'])); ?>
				<?php if ($this->_tpl_vars['arrProduct'][$this->_tpl_vars['key']] != ""): ?>
					<table width="580" border="0" cellspacing="0" cellpadding="7" summary=" ">
						<tr>
							<td bgcolor="#e4e4e4" class="fs12"><span class="blackst"><!--★サブタイトル★--><?php echo ((is_array($_tmp=$this->_tpl_vars['arrProduct'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</span></td>
						</tr>
					</table>
					
					<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
						<tr><td height="10"></td></tr>
						<tr valign="top">
							<?php $this->assign('key', "sub_comment".($this->_sections['cnt']['iteration'])); ?>
							<td class="fs12" align="left"><!--★サブテキスト★--><?php echo ((is_array($_tmp=$this->_tpl_vars['arrProduct'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</td>
							<!--拡大写真がある場合ここから-->
							<?php $this->assign('key', "sub_image".($this->_sections['cnt']['iteration'])); ?>
							<?php $this->assign('lkey', "sub_large_image".($this->_sections['cnt']['iteration'])); ?>
							<?php if ($this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath'] != ""): ?>
							<td align="right">
							<table width="215" border="0" cellspacing="0" cellpadding="0" summary=" ">	
								<tr>
									<?php if ($this->_tpl_vars['arrFile'][$this->_tpl_vars['lkey']]['filepath'] != ""): ?>
										<td align="center" valign="middle"><div id="picture"><a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="win01('./detail_image.php?product_id=<?php echo $this->_tpl_vars['arrProduct']['product_id']; ?>
&image=<?php echo $this->_tpl_vars['lkey'];  if ($_GET['admin'] == 'on'): ?>&admin=on<?php endif; ?>','detail_image','<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['lkey']]['width']+60; ?>
','<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['lkey']]['height']+80; ?>
'); return false;" target="_blank"><!--サブ画像--><img src="<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath']; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrProduct']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /></a></div>
									<?php else: ?>
										<td align="center" valign="middle"><img src="<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath']; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrProduct']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /></td>
									<?php endif; ?>
								</tr>
								<tr><td height="8"></td></tr>
								<tr>
									<?php if ($this->_tpl_vars['arrFile'][$this->_tpl_vars['lkey']]['filepath'] != ""): ?>
										<td align="center"><div id="more"><a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="win01('./detail_image.php?product_id=<?php echo $this->_tpl_vars['arrProduct']['product_id']; ?>
&image=<?php echo $this->_tpl_vars['lkey'];  if ($_GET['admin'] == 'on'): ?>&admin=on<?php endif; ?>','detail_image','<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['lkey']]['width']+60; ?>
','<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['lkey']]['height']+80; ?>
'); return false;" onmouseover="chgImg('../img/products/b_expansion_on.gif','expansion02');" onmouseout="chgImg('../img/products/b_expansion.gif','expansion02');" target="_blank"><img src="<?php echo @URL_DIR; ?>
img/products/b_expansion.gif" width="85" height="13" alt="画像を拡大する" /></a></div></td>
									<?php endif; ?>
								</tr>
							</table>
							</td>
							<?php endif; ?>
							<!--拡大写真がある場合ここまで-->
						</tr>
						<tr><td height="30"></td></tr>
					</table>
				<?php endif; ?>
				<?php endfor; endif; ?>
				<!--▲サブコメントここまで-->
				
				<!--お客様の声ここから-->
				<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td><img src="<?php echo @URL_DIR; ?>
img/products/title_voice.jpg" width="580" height="30" alt="この商品に対するお客様の声"></td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr>
						<td>
						<?php if (count ( $this->_tpl_vars['arrReview'] ) < @REVIEW_REGIST_MAX): ?>
							<!--★新規コメントを書き込む★--><a href="./review.php" onClick="win02('./review.php?product_id=<?php echo $this->_tpl_vars['arrProduct']['product_id']; ?>
','review','580','580'); return false;" onMouseOver="chgImg('../img/products/b_comment_on.gif','review');" onMouseOut="chgImg('../img/products/b_comment.gif','review');" target="_blank"><img src="<?php echo @URL_DIR; ?>
img/products/b_comment.gif" width="150" height="22" alt="新規コメントを書き込む" name="review" id="review" /></a>
						<?php endif; ?>
						</td>
					</tr>
					<tr><td height="10"></td></tr>
		
					<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrReview']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
					<tr>
						<td class="fs12"><strong><?php echo ((is_array($_tmp=$this->_tpl_vars['arrReview'][$this->_sections['cnt']['index']]['create_date'])) ? $this->_run_mod_handler('sfDispDBDate', true, $_tmp, false) : sfDispDBDate($_tmp, false)); ?>
</strong>　投稿者：<?php if ($this->_tpl_vars['arrReview'][$this->_sections['cnt']['index']]['reviewer_url']): ?><a href="<?php echo $this->_tpl_vars['arrReview'][$this->_sections['cnt']['index']]['reviewer_url']; ?>
" target="_blank"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrReview'][$this->_sections['cnt']['index']]['reviewer_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a><?php else:  echo ((is_array($_tmp=$this->_tpl_vars['arrReview'][$this->_sections['cnt']['index']]['reviewer_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  endif; ?>　おすすめレベル：<span class="red"><?php $this->assign('level', $this->_tpl_vars['arrReview'][$this->_sections['cnt']['index']]['recommend_level']);  echo ((is_array($_tmp=$this->_tpl_vars['arrRECOMMEND'][$this->_tpl_vars['level']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</span></td>
					</tr>
					<tr><td height="5"></td></tr>
					<tr>
						<td class="fs14"><strong><?php echo ((is_array($_tmp=$this->_tpl_vars['arrReview'][$this->_sections['cnt']['index']]['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</strong></td>
					</tr>
					<tr><td height="5"></td></tr>
					<tr>
						<td class="fs12"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrReview'][$this->_sections['cnt']['index']]['comment'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</td>
					</tr>
			
					<?php if (! $this->_sections['cnt']['last']): ?>
					<tr><td height="20"><img src="<?php echo @URL_DIR; ?>
img/common/line_580.gif" width="580" height="1" alt=""></td></tr>
					<?php endif; ?>
					
					<?php endfor; endif; ?>
					
					<tr><td height="30"></td></tr>
				</table>
				<!--お客様の声ここまで-->

				<?php if ($this->_tpl_vars['arrRecommend']): ?>
				<!--▼オススメ商品ここから-->
				<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td colspan=3><img src="<?php echo @URL_DIR; ?>
img/products/title_recommend.jpg" width="580" height="30" alt="オススメ商品" /></td>
					</tr>
					<tr><td colspan=3 height="10"></td></tr>
					<tr>

					<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrRecommend']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['cnt']['step'] = ((int)2) == 0 ? 1 : (int)2;
$this->_sections['cnt']['show'] = true;
$this->_sections['cnt']['max'] = $this->_sections['cnt']['loop'];
$this->_sections['cnt']['start'] = $this->_sections['cnt']['step'] > 0 ? 0 : $this->_sections['cnt']['loop']-1;
if ($this->_sections['cnt']['show']) {
    $this->_sections['cnt']['total'] = min(ceil(($this->_sections['cnt']['step'] > 0 ? $this->_sections['cnt']['loop'] - $this->_sections['cnt']['start'] : $this->_sections['cnt']['start']+1)/abs($this->_sections['cnt']['step'])), $this->_sections['cnt']['max']);
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
					<?php if ($this->_sections['cnt']['index'] >= 2): ?>
					<tr>
						<td height="25"><img src="<?php echo @URL_DIR; ?>
img/common/line_280.gif" width="280" height="1" alt="" /></td>
						<td></td>
						<td align="left"><img src="<?php echo @URL_DIR; ?>
img/common/line_280.gif" width="280" height="1" alt="" /></td>
					</tr>
					<?php endif; ?>
					
					<tr valign="top">
						<td>
							<!-- 左列 -->
							<table width="220" border="0" cellspacing="0" cellpadding="0" summary=" ">
								<tr valign="top">
									<td align="center" valign="middle"><a href="<?php echo @DETAIL_P_HTML;  echo $this->_tpl_vars['arrRecommend'][$this->_sections['cnt']['index']]['product_id']; ?>
">
									<?php if ($this->_tpl_vars['arrRecommend'][$this->_sections['cnt']['index']]['main_list_image'] != ""): ?>
										<?php $this->assign('image_path', (@IMAGE_SAVE_DIR)."/".($this->_tpl_vars['arrRecommend'][$this->_sections['cnt']['index']]['main_list_image'])); ?>
									<?php else: ?>
										<?php $this->assign('image_path', (@NO_IMAGE_DIR)); ?>
									<?php endif; ?>
									<img src="<?php echo @SITE_URL; ?>
resize_image.php?image=<?php echo ((is_array($_tmp=$this->_tpl_vars['image_path'])) ? $this->_run_mod_handler('sfRmDupSlash', true, $_tmp) : sfRmDupSlash($_tmp)); ?>
&width=65&height=65" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrRecommend'][$this->_sections['cnt']['index']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"></a></td>
									<td align="right">
									<table width="145" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<?php $this->assign('price02_min', ($this->_tpl_vars['arrRecommend'][$this->_sections['cnt']['index']]['price02_min'])); ?>
											<?php $this->assign('price02_max', ($this->_tpl_vars['arrRecommend'][$this->_sections['cnt']['index']]['price02_max'])); ?>
											<td><span class="fs12"><a href="<?php echo @DETAIL_P_HTML;  echo $this->_tpl_vars['arrRecommend'][$this->_sections['cnt']['index']]['product_id']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrRecommend'][$this->_sections['cnt']['index']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a></span><br>
											<span class="red"><span class="fs12">価格</span><span class="fs10">(税込)</span></span><span class="redst"><span class="fs12">：
											<?php if ($this->_tpl_vars['price02_min'] == $this->_tpl_vars['price02_max']): ?>
												<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['price02_min'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

											<?php else: ?>
												<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['price02_min'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
〜<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['price02_max'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

											<?php endif; ?>
											円</span></span></td>
										</tr>
										<tr><td height="5"></td></tr>
										<tr>
											<td class="fs10"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrRecommend'][$this->_sections['cnt']['index']]['comment'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</td>
										</tr>
									</table>
									</td>
								</tr>
							</table>
							<!-- 左列 -->
						</td>
						<?php $this->assign('nextCnt', $this->_sections['cnt']['index']+1); ?>
						
						<td id="spacer"></td>
						
						<td>
						<?php if ($this->_tpl_vars['arrRecommend'][$this->_tpl_vars['nextCnt']]['product_id']): ?>
						
							<!-- 右列 -->
							<table width="220" border="0" cellspacing="0" cellpadding="0" summary=" ">
								<tr valign="top">
									<td align="center" valign="middle"><a href="<?php echo @DETAIL_P_HTML;  echo $this->_tpl_vars['arrRecommend'][$this->_tpl_vars['nextCnt']]['product_id']; ?>
">
									<?php if ($this->_tpl_vars['arrRecommend'][$this->_tpl_vars['nextCnt']]['main_list_image'] != ""): ?>
										<?php $this->assign('image_path', (@IMAGE_SAVE_DIR)."/".($this->_tpl_vars['arrRecommend'][$this->_tpl_vars['nextCnt']]['main_list_image'])); ?>
									<?php else: ?>
										<?php $this->assign('image_path', (@NO_IMAGE_DIR)); ?>
									<?php endif; ?>
									<img src="<?php echo @SITE_URL; ?>
resize_image.php?image=<?php echo ((is_array($_tmp=$this->_tpl_vars['image_path'])) ? $this->_run_mod_handler('sfRmDupSlash', true, $_tmp) : sfRmDupSlash($_tmp)); ?>
&width=65&height=65" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrRecommend'][$this->_tpl_vars['nextCnt']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"></a></td>
									<td align="right">
									<table width="145" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<?php $this->assign('price02_min', ($this->_tpl_vars['arrRecommend'][$this->_tpl_vars['nextCnt']]['price02_min'])); ?>
											<?php $this->assign('price02_max', ($this->_tpl_vars['arrRecommend'][$this->_tpl_vars['nextCnt']]['price02_max'])); ?>
											<td><span class="fs12"><a href="<?php echo @DETAIL_P_HTML;  echo $this->_tpl_vars['arrRecommend'][$this->_tpl_vars['nextCnt']]['product_id']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrRecommend'][$this->_tpl_vars['nextCnt']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a></span><br>
											<span class="red"><span class="fs12">価格</span><span class="fs10">(税込)</span></span><span class="redst"><span class="fs12">：
											<?php if ($this->_tpl_vars['price02_min'] == $this->_tpl_vars['price02_max']): ?>
												<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['price02_min'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

											<?php else: ?>
												<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['price02_min'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
〜<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['price02_max'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrSiteInfo']['tax'], $this->_tpl_vars['arrSiteInfo']['tax_rule'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>

											<?php endif; ?>
											円</span></span></td>
										</tr>
										<tr><td height="5"></td></tr>
										<tr>
											<td class="fs10"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrRecommend'][$this->_tpl_vars['nextCnt']]['comment'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</td>
										</tr>
									</table>
									</td>
								</tr>
							</table>
							<!-- 右列 -->
						<?php endif; ?>
						</td>
					</tr>
					<?php endfor; endif; ?>
					<tr><td colspan=3 height="25"></td></tr>
				</table>
				<?php endif; ?>
				<!--▲オススメ商品ここまで-->
				
				</td>
				<!--▲RIGHT CONTENTS-->
			</tr>
		</table>
		<!--▲MAIN CONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->