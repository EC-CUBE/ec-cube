<?php /* Smarty version 2.6.13, created on 2007-01-10 00:17:16
         compiled from shopping/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'shopping/index.tpl', 48, false),array('modifier', 'sfGetErrorColor', 'shopping/index.tpl', 48, false),array('modifier', 'sfGetChecked', 'shopping/index.tpl', 53, false),)), $this); ?>
<!--▼CONTENTS-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/login/title.jpg" width="700" height="40" alt="ログイン"></td>
			</tr>
			<tr><td height="20"></td></tr>
		</table>
		<!--▼会員登録がお済みのお客様-->
		<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td align="center" bgcolor="#cccccc">
				<table width="630" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="5"></td></tr>
					<tr>
						<td align="center" bgcolor="#ffffff">
						<table width="604" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="13"></td></tr>
							<tr>
								<td><img src="<?php echo @URL_DIR; ?>
img/login/member.gif" width="202" height="16" alt="会員登録がお済みのお客様"></td>
							</tr>
							<tr><td height="20"></td></tr>
						</table>
						<!--ログインここから-->
						<table width="530" border="0" cellspacing="0" cellpadding="0" summary=" ">
						<form name="member_form" id="member_form" method="post" action="./deliv.php" onsubmit="return fnCheckLogin('member_form')">
						<input type="hidden" name="mode" value="login">
							<tr>
								<td class="fs12">会員の方は、登録時に入力されたメールアドレスとパスワードでログインしてください。</td>
							</tr>
							<tr><td height="10"></td></tr>
							<tr>
								<td align="center" bgcolor="#f0f0f0">
								<table width="490" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="20"></td></tr>
									<tr>
										<td><img src="<?php echo @URL_DIR; ?>
img/login/mailadress.gif" width="92" height="13" alt="メールアドレス"></td>
										<td class="fs12">
											<?php $this->assign('key', 'login_email'); ?><span class="red"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
											<input type="text" name="<?php echo $this->_tpl_vars['key']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_login_email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
; ime-mode: disabled;" size="40" class="box40" />
										</td>
									</tr>
									<tr>
										<td align="right"></td>
										<td class="fs10n"><input type="checkbox" name="login_memory" value="1" <?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_login_memory'])) ? $this->_run_mod_handler('sfGetChecked', true, $_tmp, 1) : sfGetChecked($_tmp, 1)); ?>
/><label for="memory">会員メールアドレスをコンピューターに記憶させる</label></td>
									</tr>
									<tr><td height="10"></td></tr>
									<tr>
										<td><img src="<?php echo @URL_DIR; ?>
img/login/password.gif" width="92" height="13" alt="パスワード"></td>
										<td class="fs12">
											<?php $this->assign('key', 'login_pass'); ?><span class="red"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key']]; ?>
</span>
											<input type="password" name="<?php echo $this->_tpl_vars['key']; ?>
" maxlength="<?php echo $this->_tpl_vars['arrForm'][$this->_tpl_vars['key']]['length']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size="40" class="box40" />
										</td>
									</tr>
									<tr><td height="20"></td></tr>
								</table>
								</td>
							</tr>
							<tr><td height="20"></td></tr>
							<tr>
								<td align="center">
									<input type="image" onmouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/login/b_login_on.gif',this)" onmouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/login/b_login.gif',this)" src="<?php echo @URL_DIR; ?>
img/login/b_login.gif" width="140" height="30" alt="ログイン" name="log" id="log" />
								</td>
							</tr>
							<tr><td height="15"></td></tr>
							<tr>
								<td class="fs10">パスワードを忘れた方は<a href="<?php echo @URL_DIR; ?>
forgot/index.php" onclick="win01('/forgot/index.php','forget','600','400'); return false;" target="_blank">こちら</a>からパスワードの再発行を行ってください。<br>
								メールアドレスを忘れた方は、お手数ですが、<a href="<?php echo @URL_DIR; ?>
contact/index.php">お問い合わせページ</a>からお問い合わせください。</td>
							</tr>
							<tr><td height="20"></td></tr>
						</form>
						</table>
						<!--ログインここまで-->
						</td>
					</tr>
					<tr><td height="5"></td></tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
		</table>
		<!--▲会員登録がお済みのお客様-->
		<!--▼まだ会員登録されていないお客様-->
		<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td align="center" bgcolor="#cccccc">
				<table width="630" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="5"></td></tr>
					<tr>
						<td align="center" bgcolor="#ffffff">
						<table width="604" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="13"></td></tr>
							<tr>
								<td><img src="<?php echo @URL_DIR; ?>
img/login/guest.gif" width="247" height="16" alt="まだ会員登録されていないお客様"></td>
							</tr>
							<tr><td height="20"></td></tr>
						</table>
						
						<table width="530" border="0" cellspacing="0" cellpadding="0" summary=" ">
						<form name="member_form2" id="member_form2" method="post" action="./index.php">
						<input type="hidden" name="mode" value="nonmember">
							<tr>
								<td class="fs12">会員登録をすると便利なMyページをご利用いただけます。<br>
								また、ログインするだけで、毎回お名前や住所などを入力することなくスムーズにお買い物をお楽しみいただけます。</td>
							</tr>
							<tr><td height="10"></td></tr>
							<tr>
								<td align="center" bgcolor="#f0f0f0">
								<table width="490" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="20"></td></tr>
									<tr>
										<td align="center">
											<a href="<?php echo @URL_DIR; ?>
entry/kiyaku.php" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/login/b_gotoentry_on.gif','b_gotoentry');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/login/b_gotoentry.gif','b_gotoentry');"><img src="<?php echo @URL_DIR; ?>
img/login/b_gotoentry.gif" width="130" height="30" alt="会員登録をする" border="0" name="b_gotoentry"></a>　
											<input type="image" onmouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/login/b_buystep_on.gif',this)" onmouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/login/b_buystep.gif',this)" src="<?php echo @URL_DIR; ?>
img/login/b_buystep.gif" width="130" height="30" alt="購入手続きへ" name="buystep" id="buystep" />
										</td>
									</tr>
									<tr><td height="20"></td></tr>
								</table>
								</td>
							</tr>
							<tr><td height="20"></td></tr>
						</form>
						</table>
						</td>
					</tr>
					<tr><td height="5"></td></tr>
				</table>
				</td>
			</tr>
		</table>
		<!--▲まだ会員登録されていないお客様-->
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->