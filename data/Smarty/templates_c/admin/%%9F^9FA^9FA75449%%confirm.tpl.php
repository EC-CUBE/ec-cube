<?php /* Smarty version 2.6.13, created on 2007-01-10 00:13:46
         compiled from products/confirm.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'products/confirm.tpl', 25, false),array('modifier', 'strip', 'products/confirm.tpl', 63, false),array('modifier', 'sfTrim', 'products/confirm.tpl', 63, false),array('modifier', 'count_characters', 'products/confirm.tpl', 75, false),array('modifier', 'sfPutBR', 'products/confirm.tpl', 136, false),array('modifier', 'nl2br', 'products/confirm.tpl', 156, false),array('modifier', 'sfRmDupSlash', 'products/confirm.tpl', 240, false),)), $this); ?>
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
			<!--▼CONTENTS-->
			<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--メインエリア-->
				<tr>
					<td align="center">
					<!--▼MAIN CONTENTS-->
						<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">
						<!--▼登録テーブルここから-->
						<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" enctype="multipart/form-data">
						<?php $_from = $this->_tpl_vars['arrForm']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
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
									<td bgcolor="#f2f1ec" width="160" class="fs12n">商品名</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">商品カテゴリ</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php $this->assign('key', $this->_tpl_vars['arrForm']['category_id']); ?>
									<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrCatList'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('strip', true, $_tmp) : smarty_modifier_strip($_tmp)))) ? $this->_run_mod_handler('sfTrim', true, $_tmp) : sfTrim($_tmp)); ?>

									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">公開・非公開</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php echo $this->_tpl_vars['arrDISP'][$this->_tpl_vars['arrForm']['status']]; ?>

									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">商品ステータス</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=((is_array($_tmp=$this->_tpl_vars['arrForm']['product_flag'])) ? $this->_run_mod_handler('count_characters', true, $_tmp) : smarty_modifier_count_characters($_tmp))) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
										<?php if ($this->_tpl_vars['arrForm']['product_flag'][$this->_sections['cnt']['index']] == '1'):  $this->assign('key', ($this->_sections['cnt']['iteration'])); ?><img src="<?php echo $this->_tpl_vars['arrSTATUS_IMAGE'][$this->_tpl_vars['key']]; ?>
"><?php endif; ?>
									<?php endfor; endif; ?>
									</td>
								</tr>
								
								<?php if ($this->_tpl_vars['tpl_nonclass'] == true): ?>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">商品コード</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['product_code'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">参考市場価格</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['price01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

									円</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">商品価格</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['price02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

									円</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">在庫数</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php if ($this->_tpl_vars['arrForm']['stock_unlimited'] == 1): ?>
									無制限
									<?php else: ?>
									<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['stock'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

									個<?php endif; ?>
									</td>
								</tr>
								<?php endif; ?>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">ポイント付与率</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['point_rate'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

									％</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">発送日目安</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php echo ((is_array($_tmp=$this->_tpl_vars['arrDELIVERYDATE'][$this->_tpl_vars['arrForm']['deliv_date_id']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

									</td>
								</tr>			
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">購入制限</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php if ($this->_tpl_vars['arrForm']['sale_unlimited'] == 1): ?>
									無制限
									<?php else: ?>
									<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['sale_limit'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

									個<?php endif; ?>
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">メーカーURL</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrForm']['comment1'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('sfPutBR', true, $_tmp, @LINE_LIMIT_SIZE) : sfPutBR($_tmp, @LINE_LIMIT_SIZE)); ?>

									</td>
								</tr>
																<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">検索ワード</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['comment3'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">一覧-メインコメント</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrForm']['main_list_comment'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>

									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-メインコメント</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['main_comment'])) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>

									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">一覧-メイン画像</td>
									<td bgcolor="#ffffff" width="557">
									<?php $this->assign('key', 'main_list_image'); ?>
									<?php if ($this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath'] != ""): ?>
									<img src="<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath']; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /><br />
									<?php endif; ?>
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-メイン画像</td>
									<td bgcolor="#ffffff" width="557">
									<?php $this->assign('key', 'main_image'); ?>
									<?php if ($this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath'] != ""): ?>
									<img src="<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath']; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /><br />
									<?php endif; ?>
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-メイン拡大画像</td>
									<td bgcolor="#ffffff" width="557">
									<?php $this->assign('key', 'main_large_image'); ?>
									<?php if ($this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath'] != ""): ?>
									<img src="<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath']; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /><br />
									<?php endif; ?>
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
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php $this->assign('key', "sub_title".($this->_sections['cnt']['iteration'])); ?>
									<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-サブコメント（<?php echo $this->_sections['cnt']['iteration']; ?>
）</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php $this->assign('key', "sub_comment".($this->_sections['cnt']['iteration'])); ?>
									<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>

									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-サブ画像（<?php echo $this->_sections['cnt']['iteration']; ?>
）</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php $this->assign('key', "sub_image".($this->_sections['cnt']['iteration'])); ?>
									<?php if ($this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath'] != ""): ?>
									<img src="<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath']; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /><br />
									<?php endif; ?>
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-サブ拡大画像（<?php echo $this->_sections['cnt']['iteration']; ?>
）</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php $this->assign('key', "sub_large_image".($this->_sections['cnt']['iteration'])); ?>
									<?php if ($this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath'] != ""): ?>
									<img src="<?php echo $this->_tpl_vars['arrFile'][$this->_tpl_vars['key']]['filepath']; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /><br />
									<?php endif; ?>
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
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<?php if ($this->_tpl_vars['arrRecommend'][$this->_tpl_vars['recommend_no']]['name'] != ""): ?>
									商品コード:<?php echo $this->_tpl_vars['arrRecommend'][$this->_tpl_vars['recommend_no']]['product_code_min']; ?>
<br>
									商品名:<?php echo ((is_array($_tmp=$this->_tpl_vars['arrRecommend'][$this->_tpl_vars['recommend_no']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<br>
									コメント:<br>
									<?php echo ((is_array($_tmp=$this->_tpl_vars['arrRecommend'][$this->_tpl_vars['recommend_no']]['comment'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

									<?php endif; ?>
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
												<a href="#" onMouseover="chgImg('<?php echo @URL_DIR; ?>
img/contents/btn_back_on.jpg','back')" onMouseout="chgImg('<?php echo @URL_DIR; ?>
img/contents/btn_back.jpg','back');" onclick="fnModeSubmit('confirm_return','',''); return false;"><img src="<?php echo @URL_DIR; ?>
img/contents/btn_back.jpg" width="123" height="24" alt="前のページに戻る" border="0" name="back"></a>
												<input type="image" onMouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_regist.jpg',this)" src="<?php echo @URL_DIR; ?>
img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" >
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
						
					<!--▲登録テーブルここまで-->
					</form>
					<!--▲MAIN CONTENTS-->
					</td>
				</tr>
			</table>
			<!--▲CONTENTS-->
		</td>
	</tr>
</table>
<!--★★メインコンテンツ★★-->