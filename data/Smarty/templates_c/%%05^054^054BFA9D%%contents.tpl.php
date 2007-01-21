<?php /* Smarty version 2.6.13, created on 2007-01-10 16:13:32
         compiled from /home/web/beta.ec-cube.net/html/user_data/include/campaign/testtest/active//contents.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/home/web/beta.ec-cube.net/html/user_data/include/campaign/testtest/active//contents.tpl', 60, false),)), $this); ?>
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
						<td>キャンペーン開催中</td>
					</tr>
					<tr><td height="20"></td></tr>
				</table>
				
				<table width="530" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td class="fs12">キャンペーン内容</td>
					</tr>
					<tr><td height="10"></td></tr>
				</table>
				</td>
			</tr>
			<tr><td height="5"></td></tr>
		</table>
		</td>
	</tr>
</table>
<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
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
						<td><img src="/img/login/member.gif" width="202" height="16" alt="会員登録がお済みのお客様"></td>
					</tr>
					<tr><td height="20"></td></tr>
				</table>
				<!--ログインここから-->
				<table width="530" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<form name="member_form" id="member_form" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onsubmit="return fnCheckLogin('member_form')">
				<input type="hidden" name="mode" value="login">
					<tr>
						<td class="fs12">会員の方は、登録時に入力されたメールアドレスとパスワードでログインしてお申込ください。</td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr>
						<td align="center" bgcolor="#f0f0f0">
						<table width="490" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="20"></td></tr>
							<tr>
								<td><img src="/img/login/mailadress.gif" width="92" height="13" alt="メールアドレス"></td>
								<td class="fs12">
									<span class="red"></span>
									<input type="text" name="login_email" value="" maxlength="" style="; ime-mode: disabled;" size="40" class="box40" />
								</td>
							</tr>
							<tr>
								<td align="right"></td>
								<td class="fs10n"><input type="checkbox" name="login_memory" value="1" /><label for="memory">会員メールアドレスをコンピューターに記憶させる</label></td>
							</tr>
							<tr><td height="10"></td></tr>
							<tr>
								<td><img src="/img/login/password.gif" width="92" height="13" alt="パスワード"></td>
								<td class="fs12">
									<span class="red"></span>
									<input type="password" name="login_pass" maxlength="" style="" size="40" class="box40" />
								</td>
							</tr>
							<tr><td height="20"></td></tr>
						</table>
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td align="center">
							<input type="image" onmouseover="chgImgImageSubmit('/img/login/b_login_on.gif',this)" onmouseout="chgImgImageSubmit('/img/login/b_login.gif',this)" src="/img/login/b_login.gif" width="140" height="30" alt="ログイン" name="log" id="log" />
						</td>
					</tr>
					<tr><td height="15"></td></tr>
					<tr>
						<td class="fs10">パスワードを忘れた方は<a href="/forgot/index.php" onclick="win01('/forgot/index.php','forget','600','400'); return false;" target="_blank">こちら</a>からパスワードの再発行を行ってください。<br>
						メールアドレスを忘れた方は、お手数ですが、<a href="/contact/index.php">お問い合わせページ</a>からお問い合わせください。</td>
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
					<tr>
						<td class="fs12">お申込を行う為には、会員登録が必要です。<br>
						会員登録をするボタンをクリックして会員登録を行ってください。</td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr>
						<td align="center" bgcolor="#f0f0f0">
						<table width="490" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="20"></td></tr>
							<tr>
								<td align="center">
									<a href="<?php echo @CAMPAIGN_URL;  echo $this->_tpl_vars['dir_name']; ?>
/entry.php" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/login/b_gotoentry_on.gif','b_gotoentry');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/login/b_gotoentry.gif','b_gotoentry');"><img src="<?php echo @URL_DIR; ?>
img/login/b_gotoentry.gif" width="130" height="30" alt="会員登録をする" border="0" name="b_gotoentry"></a>　
								</td>
							</tr>
							<tr><td height="20"></td></tr>
						</table>
						</td>
					</tr>
					<tr><td height="20"></td></tr>
				</table>
				</td>
			</tr>
			<tr><td height="5"></td></tr>
		</table>
		</td>
	</tr>
</table>
<!--▲まだ会員登録されていないお客様-->