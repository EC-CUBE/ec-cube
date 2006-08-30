<!--▼CONTENTS-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="/img/login/title.jpg" width="700" height="40" alt="ログイン"></td>
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
								<td><img src="/img/login/member.gif" width="202" height="16" alt="会員登録がお済みのお客様"></td>
							</tr>
							<tr><td height="20"></td></tr>
						</table>
						<!--ログインここから-->
						<table width="530" border="0" cellspacing="0" cellpadding="0" summary=" ">
						<form name="login_mypage" id="login_mypage" method="post" action="./login_check.php" onsubmit="return fnCheckLogin('login_mypage')">
						<input type="hidden" name="mode" value="login" >
							<tr>
								<td class="fs12">会員の方は、登録時に入力されたメールアドレスとパスワードでログインしてください。</td>
							</tr>
							<tr><td height="10"></td></tr>
							<tr>
								<td align="center" bgcolor="#f0f0f0">
								<table width="490" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="20"></td></tr>
									<tr>
										<td><img src="/img/login/mailadress.gif" width="92" height="13" alt="メールアドレス"></td>
										<td>
											<!--{assign var=key value="mypage_login_email"}-->
											<input type="text" name="<!--{$key}-->" value="<!--{$tpl_login_email}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
										</td>
									</tr>
									<tr>
										<td align="right"></td>
										<!--{assign var=key value="mypage_login_memory"}-->
										<td class="fs10n"><input type="checkbox" name="<!--{$key}-->" value="1" <!--{$tpl_login_memory|sfGetChecked:1}--> /><label for="memory">会員メールアドレスをコンピューターに記憶させる</label></td>
									</tr>
									<tr><td height="10"></td></tr>
									<tr>
										<td><img src="/img/login/password.gif" width="92" height="13" alt="パスワード"></td>
										<td>
											<!--{assign var=key value="mypage_login_pass"}-->
											<input type="password" name="<!--{$key}-->" maxlength="<!--{$smarty.const.PASSWORD_LEN2}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
										</td>
									</tr>
									<tr><td height="20"></td></tr>
								</table>
								</td>
							</tr>
							<tr><td height="20"></td></tr>
							<tr>
								<td align="center">
									<input type="image" onmouseover="chgImgImageSubmit('/img/login/b_login_on.gif',this)" onmouseout="chgImgImageSubmit('/img/login/b_login.gif',this)" src="/img/login/b_login.gif" width="140" height="30" alt="ログイン" name="log" id="log" /></a>
								</td>
							</tr>
							<tr><td height="15"></td></tr>
							<tr>
								<td class="fs10">パスワードを忘れた方は<a href="/forget/index.php" onclick="win01('/forgot/index.php','forget','600','400'); return false;" target="_blank">こちら</a>からパスワードの再発行を行ってください。<br>
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
								<td><img src="/img/login/guest.gif" width="247" height="16" alt="まだ会員登録されていないお客様"></td>
							</tr>
							<tr><td height="20"></td></tr>
						</table>
						
						<table width="530" border="0" cellspacing="0" cellpadding="0" summary=" ">
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
											<a href="/entry/kiyaku.php" onmouseover="chgImg('/img/login/b_gotoentry_on.gif','b_gotoentry');" onmouseout="chgImg('/img/login/b_gotoentry.gif','b_gotoentry');"><img src="/img/login/b_gotoentry.gif" width="130" height="30" alt="会員登録をする" border="0" name="b_gotoentry"></a>　
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
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->

