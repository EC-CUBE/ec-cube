<?php /* Smarty version 2.6.13, created on 2007-01-10 00:02:46
         compiled from entry/confirm.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'entry/confirm.tpl', 12, false),array('modifier', 'default', 'entry/confirm.tpl', 64, false),)), $this); ?>
<!--▼CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td align="right" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				<input type="hidden" name="mode" value="complete">
			<?php $_from = $this->_tpl_vars['list_data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
				<input type="hidden" name="<?php echo ((is_array($_tmp=$this->_tpl_vars['key'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['item'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
			<?php endforeach; endif; unset($_from); ?>
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/entry/title.jpg" width="580" height="40" alt="会員登録"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">下記の内容で送信してもよろしいでしょうか？<br>
				よろしければ、一番下の「会員登録完了へ」ボタンをクリックしてください。</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td bgcolor="#cccccc" align="center">
				<!--入力フォームここから-->
				<table width="580" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="135" bgcolor="#f0f0f0" class="fs12">お名前<span class="red">※</span></td>
						<td width="402" bgcolor="#ffffff" class="fs12"><?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['name01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
　<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['name02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">お名前（フリガナ）<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12"><?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['kana01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
　<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['kana02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">郵便番号<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12">〒<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['zip01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 - <?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['zip02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">住所<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrPref'][$this->_tpl_vars['list_data']['pref']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  echo ((is_array($_tmp=$this->_tpl_vars['list_data']['addr01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  echo ((is_array($_tmp=$this->_tpl_vars['list_data']['addr02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">電話番号<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12"><?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['tel01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 - <?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['tel02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 - <?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['tel03'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">FAX</td>
						<td bgcolor="#ffffff" class="fs12"><?php if (strlen ( $this->_tpl_vars['list_data']['fax01'] ) > 0 && strlen ( $this->_tpl_vars['list_data']['fax02'] ) > 0 && strlen ( $this->_tpl_vars['list_data']['fax03'] ) > 0):  echo ((is_array($_tmp=$this->_tpl_vars['list_data']['fax01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 - <?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['fax02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 - <?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['fax03'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  else: ?>未登録<?php endif; ?></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">メールアドレス<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12n"><a href="mailto:<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">性別<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12n"><?php if ($this->_tpl_vars['list_data']['sex'] == 1): ?>男性<?php else: ?>女性<?php endif; ?></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0"  class="fs12n">職業</td>
						<td bgcolor="#ffffff" class="fs12n"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrJob'][$this->_tpl_vars['list_data']['job']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, "未登録") : smarty_modifier_default($_tmp, "未登録")); ?>
</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">生年月日</td>
						<td bgcolor="#ffffff" class="fs12n"><?php if (strlen ( $this->_tpl_vars['list_data']['year'] ) > 0 && strlen ( $this->_tpl_vars['list_data']['month'] ) > 0 && strlen ( $this->_tpl_vars['list_data']['day'] ) > 0):  echo ((is_array($_tmp=$this->_tpl_vars['list_data']['year'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
年<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['month'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
月<?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['day'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
日<?php else: ?>未登録<?php endif; ?></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" ><span class="fs12">希望するパスワード<span class="red">※</span></span><br>
						<span class="fs10">パスワードは購入時に必要です</span></td>
						<td bgcolor="#ffffff" class="fs12"><?php echo $this->_tpl_vars['passlen']; ?>
</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">パスワードを忘れた時のヒント<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12">
						<table cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n">質問：</td>
								<td class="fs12n"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrReminder'][$this->_tpl_vars['list_data']['reminder']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
							</tr>
							<tr>
								<td class="fs12n">答え：</td>
								<td class="fs12n"><?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['reminder_answer'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">メールマガジン送付について<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12"><?php if ($this->_tpl_vars['list_data']['mail_flag'] == 1): ?>HTMLメール＋テキストメールを受け取る<?php elseif ($this->_tpl_vars['list_data']['mail_flag'] == 2): ?>テキストメールを受け取る<?php else: ?>受け取らない<?php endif; ?></td>
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
img/common/b_back_on.gif','back')" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/common/b_back.gif','back')"><img src="<?php echo @URL_DIR; ?>
img/common/b_back.gif" width="150" height="30" alt="戻る" border="0" name="back" id="back" /></a>
					<img src="<?php echo @URL_DIR; ?>
img/_.gif" width="20" height="" alt="" />
					<input type="image" onmouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/entry/b_entrycomp_on.gif',this)" onmouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/entry/b_entrycomp.gif',this)" src="<?php echo @URL_DIR; ?>
img/entry/b_entrycomp.gif" width="150" height="30" alt="送信" border="0" name="send" id="send" />
				</td>
			</tr>
		</form>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->