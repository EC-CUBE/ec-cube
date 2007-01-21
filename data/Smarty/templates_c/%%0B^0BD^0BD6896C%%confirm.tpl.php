<?php /* Smarty version 2.6.13, created on 2007-01-10 00:22:13
         compiled from contact/confirm.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'contact/confirm.tpl', 12, false),array('modifier', 'nl2br', 'contact/confirm.tpl', 59, false),)), $this); ?>
<!--▼CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td align="right" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
		<input type="hidden" name="mode" value="complete">
		<?php $_from = $this->_tpl_vars['arrForm']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
		<?php if ($this->_tpl_vars['key'] != 'mode'): ?>
		<input type="hidden" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['item'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
		<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/contact/title.jpg" width="580" height="40" alt="お問い合わせ"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">下記入力内容で送信してもよろしいでしょうか？<br>
				よろしければ、一番下の「送信」ボタンをクリックしてください。</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td bgcolor="#cccccc" align="center">
				<!--入力フォームここから-->
				<table width="" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="135" bgcolor="#f0f0f0" class="fs12">お名前<span class="red">※</span></td>
						<td width="402" bgcolor="#ffffff" class="fs12"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['name01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
　<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['name02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">お名前（フリガナ）<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['kana01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
　<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['kana02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">郵便番号</td>
						<td bgcolor="#ffffff" class="fs12"><?php if (strlen ( $this->_tpl_vars['arrForm']['zip01'] ) > 0 && strlen ( $this->_tpl_vars['arrForm']['zip02'] ) > 0): ?>〒<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['zip01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
-<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['zip02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  endif; ?></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">住所</td>
						<td bgcolor="#ffffff" class="fs12"><?php echo $this->_tpl_vars['arrPref'][$this->_tpl_vars['arrForm']['pref']];  echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['addr01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['addr02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">電話番号</td>
						<td bgcolor="#ffffff" class="fs12"><?php if (strlen ( $this->_tpl_vars['arrForm']['tel01'] ) > 0 && strlen ( $this->_tpl_vars['arrForm']['tel02'] ) > 0 && strlen ( $this->_tpl_vars['arrForm']['tel03'] ) > 0):  echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['tel01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
-<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['tel02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
-<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['tel03'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  endif; ?></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">メールアドレス<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12n"><a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">お問い合わせ内容<span class="red">※</span><br>
						<span class="mini">（全角1000字以下）</span></td>
						<td bgcolor="#ffffff" class="fs12"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrForm']['contents'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</td>
					</tr>
				</table>
				<!--入力フォームここまで-->
				</td>
			</tr>
			<tr><td height="25"></td></tr>
			<tr align="center">
				<td>
					<a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="fnModeSubmit('return', '', ''); return false;" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/common/b_back_on.gif','back02');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/common/b_back.gif','back02');"><img src="<?php echo @URL_DIR; ?>
img/common/b_back.gif" width="150" height="30" alt="戻る" name="back02" id="back02" /></a><img src="<?php echo @URL_DIR; ?>
img/_.gif" width="20" height="" alt="" />
					<input type="image" onmouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/common/b_complete_on.gif',this)" onmouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/common/b_complete.gif',this)" src="<?php echo @URL_DIR; ?>
img/common/b_complete.gif" width="150" height="30" alt="完了ページへ" border="0" name="send" id="send" />	
				</td>
			</tr>
		</form>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->




