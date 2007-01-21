<?php /* Smarty version 2.6.13, created on 2007-01-10 15:56:08
         compiled from /home/web/beta.ec-cube.net/html/user_data/include/campaign/dir5/active/preview.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/home/web/beta.ec-cube.net/html/user_data/include/campaign/dir5/active/preview.tpl', 126, false),)), $this); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="content-script-type" content="text/javascript">
<meta http-equiv="content-style-type" content="text/css">
<link rel="stylesheet" href="/user_data/css/contents.css" type="text/css" media="all" />
<link rel="stylesheet" href="/css/common.css" type="text/css" media="all" />
<link rel="stylesheet" href="/css/layout/index.css" type="text/css" media="all" />
<title>テストの店/TOPページ</title>
<meta name="author" content="">
<meta name="description" content="">
<meta name="keywords" content="">

<script type="text/javascript">
<!--
//-->
</script>
</head>

<body bgcolor="#ffe9e6" text="#555555" link="#3a75af" vlink="#3a75af" alink="#3a75af" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="preLoadImg('<?php echo @URL_DIR; ?>
');">
<noscript>
<link rel="stylesheet" href="<?php echo @URL_DIR; ?>
css/common.css" type="text/css" />
</noscript>

<div align="center">
<a name="top" id="top"></a>

<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffa85c">
		<table width="762" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs10n"><span class="white"></span></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		</td>
	</tr>
	<tr><td bgcolor="#ff6600"><img src="/img/common/_.gif" width="778" height="1" alt=""></td></tr>
</table>
<!--▲HEADER-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc" width="1"><img src="/img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff" width="1"><img src="/img/_.gif" width="5" height="1" alt="" /></td>
		<td bgcolor="#ffffff" align="left" height="5" width=100%></td>
		<td bgcolor="#ffffff"><img src="./img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#cccccc"><img src="./img/_.gif" width="1" height="10" alt="" /></td>
		</td>
	</tr>
</table>
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr align="center">
		<td bgcolor="#cccccc" width="1"><img src="/img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff" width="1"><img src="/img/_.gif" width="5" height="1" alt="" /></td>
		<td bgcolor="#ffffff" align="left" height="5" width=100% align="center">
			<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td align="center" bgcolor="#ffffff">
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
<!--▲まだ会員登録されていないお客様-->				</td>
				</tr>
			</table>
		</td>
		<td bgcolor="#ffffff"><img src="./img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#cccccc"><img src="./img/_.gif" width="1" height="10" alt="" /></td>
		</td>
	</tr>
</table>

<!--▼FOTTER-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td rowspan="3" bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
		<td align="center" bgcolor="#ffffff">
		<table width="762" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="30"></td></tr>
			<tr>
				<td align="right" class="fs10n"><a href="#top"><img src="/img/common/pagetop.gif" width="100" height="10" alt="このページのTOPへ" border="0"></a></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		</td>
		<td rowspan="3" bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
	</tr>
	<tr><td bgcolor="#ff6600"><img src="/img/common/_.gif" width="778" height="1" alt=""></td></tr>
	<tr>
		<td align="center" bgcolor="#ffa85c">
		<table width="762" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs10n"><span class="white"></span></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		</td>
	</tr>
</table>
<!--▲FOTTER-->
</div>
</body>
</html>