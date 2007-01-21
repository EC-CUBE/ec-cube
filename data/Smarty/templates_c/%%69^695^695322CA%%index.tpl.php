<?php /* Smarty version 2.6.13, created on 2007-01-10 00:05:30
         compiled from cart/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'cart/index.tpl', 25, false),array('modifier', 'number_format', 'cart/index.tpl', 25, false),array('modifier', 'default', 'cart/index.tpl', 25, false),array('modifier', 'sfPreTax', 'cart/index.tpl', 93, false),)), $this); ?>
<!--▼CONTENTS-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/cart/title.jpg" width="700" height="40" alt="現在のカゴの中"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/cart/flame_top.gif" width="700" height="15" alt=""></td>
			</tr>
			<tr>
				<td align="center" background="<?php echo @URL_DIR; ?>
img/cart/flame_bg.gif">
				<table width="680" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td align="center" class="fs14">
							<?php if ($this->_tpl_vars['tpl_login']): ?>
							<!--メインコメント--><?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 様の、現在の所持ポイントは「<span class="redst"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['tpl_user_point'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
 pt</span>」です。<br />
							<?php else: ?>
							<!--メインコメント-->ポイント制度をご利用になられる場合は、会員登録後ログインしていだだきますようお願い致します。<br />
							<?php endif; ?>							
							ポイントは商品購入時に1pt＝<?php echo @POINT_VALUE; ?>
円として使用することができます。<br/>

							<!-- カゴの中に商品がある場合にのみ表示 -->
							<?php if (count ( $this->_tpl_vars['arrProductsClass'] ) > 0): ?>
								お買い上げ商品の合計金額は「<span class="redst"><?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_total_pretax'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
円</span>」です。
								<?php if ($this->_tpl_vars['arrInfo']['free_rule'] > 0): ?>
								<?php if (((is_array($_tmp=$this->_tpl_vars['arrData']['deliv_fee'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)) > 0): ?>
									あと「<span class="redst"><?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_deliv_free'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
円</span>」で送料無料です！！
								<?php else: ?>
									現在、「<span class="redst">送料無料</span>」です！！
								<?php endif; ?>
								<?php endif; ?>
							<?php endif; ?>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/cart/flame_bottom.gif" width="700" height="15" alt=""></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td bgcolor="#cccccc" align="center">
					<?php if ($this->_tpl_vars['tpl_message'] != ""): ?>
					<table cellspacing="0" cellpadding="0" summary=" " bgcolor="#ffffff" width=100%>
						<tr>
							<td class="fs12"><span class="redst"><?php echo $this->_tpl_vars['tpl_message']; ?>
</span></td>
						</tr>
					</table>
					<?php endif; ?>
					<?php if (count ( $this->_tpl_vars['arrProductsClass'] ) > 0): ?>
					<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
					<input type="hidden" name="mode" value="confirm">
					<input type="hidden" name="cart_no" value="">
	
					<!--ご注文内容ここから-->
					
						<tr align="center" bgcolor="#f0f0f0">
							<td width="50" class="fs12">削除</td>
							<td width="85" class="fs12">商品写真</td>
							<td width="305" class="fs12">商品名</td>
							<td width="60" class="fs12">単価</td>
							<td width="50" class="fs12">個数</td>
							<td width="150" class="fs12">小計</td>
						</tr>
					
						<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrProductsClass']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
						<tr bgcolor="#ffffff" class="fs12n">
							<td align="center"><a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="fnChangeAction('<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
'); fnModeSubmit('delete', 'cart_no', '<?php echo $this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['cart_no']; ?>
'); return false;">削除</a></td>
							<td ><a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="win01('../products/detail_image.php?product_id=<?php echo $this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['product_id']; ?>
&image=main_image','detail_image','<?php echo $this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['tpl_image_width']; ?>
','<?php echo $this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['tpl_image_height']; ?>
'); return false;" target="_blank">
								<img src="<?php echo @SITE_URL; ?>
resize_image.php?image=<?php echo @IMAGE_SAVE_DIR; ?>
/<?php echo $this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['main_list_image']; ?>
&width=65&height=65" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
							</a></td>
							<td ><strong><?php echo ((is_array($_tmp=$this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</storng><br />
							<?php if ($this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['classcategory_name1'] != ""): ?>
								<?php echo $this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['class_name1']; ?>
：<?php echo $this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['classcategory_name1']; ?>
<br />
							<?php endif; ?>
							<?php if ($this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['classcategory_name2'] != ""): ?>
								<?php echo $this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['class_name2']; ?>
：<?php echo $this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['classcategory_name2']; ?>

							<?php endif; ?>
							</td>
							<td align="right">
							<?php if ($this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['price02'] != ""): ?>
								<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['price02'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
円
							<?php else: ?>
								<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['price01'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
円
							<?php endif; ?>						
							</td>
							<td align="center" >
							<table cellspacing="0" cellpadding="0" summary=" " id="form">
								<tr>
									<td colspan="3" align="center" class="fs12n"><?php echo $this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['quantity']; ?>
</td>
								</tr>
								<tr><td height="5"></td></tr>
								<tr>
									<td><a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="fnChangeAction('<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
'); fnModeSubmit('up','cart_no','<?php echo $this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['cart_no']; ?>
'); return false"><img src="<?php echo @URL_DIR; ?>
img/button/plus.gif" width="16" height="16" alt="＋" /></a></td>
									<td><img src="<?php echo @URL_DIR; ?>
img/_.gif" width="10" height="1" alt="" /></td>
									<td><a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="fnChangeAction('<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
'); fnModeSubmit('down','cart_no','<?php echo $this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['cart_no']; ?>
'); return false"><img src="<?php echo @URL_DIR; ?>
img/button/minus.gif" width="16" height="16" alt="-" /></a></td>
								</tr>
							</table>
							</td>
							<td id="price_c" align="right"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrProductsClass'][$this->_sections['cnt']['index']]['total_pretax'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
円</td>
						</tr>
						<?php endfor; endif; ?>
						
						<tr align="right">
							<td colspan="5" class="fs12n" bgcolor="#f0f0f0">小計</td>
							<td class="fs12n" bgcolor="#ffffff"><?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_total_pretax'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
円</td>
						</tr>
						<tr align="right">
							<td colspan="5" class="fs12n" bgcolor="#f0f0f0">合計</td>
							<td class="fs12st" bgcolor="#ffffff"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrData']['total']-$this->_tpl_vars['arrData']['deliv_fee'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
円</td>
						</tr>
						<?php if ($this->_tpl_vars['arrData']['birth_point'] > 0): ?>
						<tr align="right">
							<td colspan="5" class="fs12n" bgcolor="#f0f0f0">お誕生月ポイント</td>
							<td class="fs12st" bgcolor="#ffffff"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrData']['birth_point'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
pt</td>
						</tr>
						<?php endif; ?>
						<tr align="right">
							<td colspan="5" class="fs12n" bgcolor="#f0f0f0">今回加算ポイント</td>
							<td class="fs12st" bgcolor="#ffffff"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrData']['add_point'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
pt</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td height="10"></td></tr>

			<tr>
				<td class="fs10">
					※商品写真は参考用写真です。ご注文のカラーと異なる写真が表示されている場合でも、商品番号に記載されているカラー表示で間違いございませんのでご安心ください。<br>
					※上記料金に別途送料手数料が発生します。ご注意ください。
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td align="center"><img src="<?php echo @URL_DIR; ?>
img/cart/text.gif" width="390" height="13" alt="上記内容でよろしければ「レジへ行く」ボタンをクリックしてください。"></td>
			</tr>
			<tr><td height="20"></td></tr>

			<tr>
				<td align="center">
					<?php if ($this->_tpl_vars['tpl_prev_url'] != ""): ?>
					<a href="<?php echo $this->_tpl_vars['tpl_prev_url']; ?>
" onmouseOver="chgImg('<?php echo @URL_DIR; ?>
img/cart/b_pageback_on.gif','back');" onmouseOut="chgImg('<?php echo @URL_DIR; ?>
img/cart/b_pageback.gif','back');"><img src="<?php echo @URL_DIR; ?>
img/cart/b_pageback.gif" width="150" height="30" alt="前のページへ戻る" name="back" id="back" /></a>　
					<?php endif; ?>
					<input type="image" onMouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/cart/b_buystep_on.gif',this)" onMouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/cart/b_buystep.gif',this)" src="<?php echo @URL_DIR; ?>
img/cart/b_buystep.gif" width="150" height="30" alt="購入手続きへ" name="confirm" />
				</td>
			</tr>
			</form>
					<?php else: ?>
						<table width=100% cellspacing="0" cellpadding="10" summary=" ">
							<tr bgcolor="#ffffff" align="center">
								<td class="fs12"><span class="redst">※ 現在カート内に商品はございません。</span><br />
							</tr>
						</table>
					<?php endif; ?>
				</td>
				<!--▲CONTENTS-->	
		</table>
		<!--▲MAIN CONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->