<?php /* Smarty version 2.6.13, created on 2007-01-10 00:05:52
         compiled from shopping/deliv.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'shopping/deliv.tpl', 23, false),)), $this); ?>
<!--▼CONTENTS-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		
		<!--購入手続きの流れ-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="15"></td></tr>
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/shopping/flow01.gif" width="700" height="36" alt="購入手続きの流れ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<!--購入手続きの流れ-->

		<!--▼MAIN ONTENTS-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
		<input type="hidden" name="mode" value="customer_addr">
		<input type="hidden" name="uniqid" value="<?php echo $this->_tpl_vars['tpl_uniqid']; ?>
">
		<input type="hidden" name="other_deliv_id" value="">
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/shopping/deliv_title.jpg" width="700" height="40" alt="お届け先の指定"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">下記一覧よりお届け先住所を選択して、「選択したお届け先に送る」ボタンをクリックしてください。
				一覧にご希望の住所が無い場合は、「新しいお届け先を追加する」より追加登録してください。<br>
				※最大20件まで登録できます。</td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td>
					<a href="../mypage/delivery_addr.php" onclick="win02('../mypage/delivery_addr.php?page=<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
','new_deiv','600','640'); return false;" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/common/newadress_on.gif','addition');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/common/newadress.gif','addition');"><img src="<?php echo @URL_DIR; ?>
img/common/newadress.gif" width="160" height="22" alt="新しいお届け先を追加する" name="addition" id="addition" /></a>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td bgcolor="#cccccc">
				<!--表示エリアここから-->
				
				<?php if ($this->_tpl_vars['arrErr']['deli'] != ""): ?>
				<table width="700" border="0" cellspacing="2" cellpadding="10" summary=" " bgcolor="#ff7e56">
					<tr>
						<td align="center" class="fs14" bgcolor="#ffffff">
							<span class="red"><strong><?php echo $this->_tpl_vars['arrErr']['deli']; ?>
</strong></span>
						</td>
					</tr>
				</table>
				</td></tr><tr><td height=15></td></tr><tr><td bgcolor="#cccccc">
				<?php endif; ?>
				
				<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr align="center" bgcolor="#f0f0f0">
						<td width="40" class="fs12">選択</td>
						<td width="100" class="fs12">住所種類</td>
						<td width="374" class="fs12">お届け先</td>
						<td width="40" class="fs12">変更</td>
						<td width="40" class="fs12">削除</td>
					</tr>

					<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrAddr']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
						<tr class="fs12" bgcolor="#ffffff">
							<td align="center">
								<?php if ($this->_sections['cnt']['first']): ?>
								<input type="radio" name="deli" id="chk_id_<?php echo $this->_sections['cnt']['iteration']; ?>
" value="<?php echo $this->_sections['cnt']['iteration']; ?>
" onclick="mode.value='customer_addr';">
								<?php else: ?>
								<input type="radio" name="deli" id="chk_id_<?php echo $this->_sections['cnt']['iteration']; ?>
" value="<?php echo $this->_sections['cnt']['iteration']; ?>
" onclick="mode.value='other_addr'; other_deliv_id.value=<?php echo $this->_tpl_vars['arrAddr'][$this->_sections['cnt']['index']]['other_deliv_id']; ?>
;">
								<?php endif; ?>
							</td>
							<td>
								<label for="chk_id_<?php echo $this->_sections['cnt']['iteration']; ?>
"><?php if ($this->_sections['cnt']['first']): ?>会員登録住所<?php else: ?>追加登録住所<?php endif; ?></label>
							</td>
							<td>
								<?php $this->assign('key', $this->_tpl_vars['arrAddr'][$this->_sections['cnt']['index']]['pref']);  echo $this->_tpl_vars['arrPref'][$this->_tpl_vars['key']];  echo ((is_array($_tmp=$this->_tpl_vars['arrAddr'][$this->_sections['cnt']['index']]['addr01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  echo ((is_array($_tmp=$this->_tpl_vars['arrAddr'][$this->_sections['cnt']['index']]['addr02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<br/>
								<?php echo ((is_array($_tmp=$this->_tpl_vars['arrAddr'][$this->_sections['cnt']['index']]['name01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['arrAddr'][$this->_sections['cnt']['index']]['name02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

							</td>
							<td align="center">
								<?php if (! $this->_sections['cnt']['first']): ?><a href="<?php echo @URL_DIR; ?>
mypage/delivery_addr.php" onclick="win02('/mypage/delivery_addr.php?page=<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&other_deliv_id=<?php echo $this->_tpl_vars['arrAddr'][$this->_sections['cnt']['index']]['other_deliv_id']; ?>
','new_deiv','600','640'); return false;">変更</a><?php endif; ?>
							</td>
							<td align="center">
								<?php if (! $this->_sections['cnt']['first']): ?><a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="fnModeSubmit('delete', 'other_deliv_id', '<?php echo $this->_tpl_vars['arrAddr'][$this->_sections['cnt']['index']]['other_deliv_id']; ?>
'); return false">削除</a><?php endif; ?>
							</td>
						</tr>
					<?php endfor; endif; ?>

				</table>
				<!--表示エリアここまで-->
				</td>
			</tr>
			<tr><td height="25"></td></tr>
			<tr align="center">
				<td>
					<input type="image" onmouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/shopping/b_select_on.gif',this)" onmouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/shopping/b_select.gif',this)" src="<?php echo @URL_DIR; ?>
img/shopping/b_select.gif" width="190" height="30" alt="選択したお届け先に送る" border="0" name="send_button" id="send_button" />
				</td>
			</tr>
		</form>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->