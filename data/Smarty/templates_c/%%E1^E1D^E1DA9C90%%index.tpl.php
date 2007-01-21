<?php /* Smarty version 2.6.13, created on 2007-01-10 00:23:11
         compiled from forgot/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'forgot/index.tpl', 27, false),array('modifier', 'sfGetErrorColor', 'forgot/index.tpl', 51, false),)), $this); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo @CHAR_CODE; ?>
">
<meta http-equiv="content-script-type" content="text/javascript">
<meta http-equiv="content-style-type" content="text/css">
<link rel="stylesheet" href="<?php echo @URL_DIR; ?>
user_data/css/contents.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo @URL_DIR; ?>
css/common.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo $this->_tpl_vars['tpl_css']; ?>
" type="text/css" media="all" />
<script type="text/javascript" src="<?php echo @URL_DIR; ?>
js/css.js"></script>
<script type="text/javascript" src="<?php echo @URL_DIR; ?>
js/navi.js"></script>
<script type="text/javascript" src="<?php echo @URL_DIR; ?>
js/win_op.js"></script>
<script type="text/javascript" src="<?php echo @URL_DIR; ?>
js/site.js"></script>
<title><?php echo $this->_tpl_vars['arrSiteInfo']['shop_name']; ?>
/パスワードを忘れた方(入力ページ)</title>
</head>

<body bgcolor="#f0f0f0" text="#555555" link="#3a75af" vlink="#3a75af" alink="#3a75af" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="preLoadImg('<?php echo @URL_DIR; ?>
')">
<noscript>
<link rel="stylesheet" href="<?php echo @URL_DIR; ?>
css/common.css" type="text/css">
</noscript>
<div align="center">
	<form action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" method="post" name="form1" />
	<input type="hidden" name="mode" value="mail_check" />
	
	<table width="550" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<tr><td height="15"></td></tr>
		<tr><td bgcolor="#ffa85c"><img src="<?php echo @URL_DIR; ?>
misc/_.gif" width="1" height="5" alt=""></td></tr>
		<tr>
			<td align="center" bgcolor="#ffffff">
			<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr><td height="15"></td></tr>
				<tr>
					<td><img src="<?php echo @URL_DIR; ?>
img/forget/title.jpg" width="500" height="40" alt="パスワードを忘れた方"></td>
				</tr>
				<tr><td height="15"></td></tr>
				<tr>
					<td class="fs12">ご登録時のメールアドレスを入力して「次へ」ボタンをクリックしてください。<br>
					<span class="red">※新しくパスワードを発行いたしますので、お忘れになったパスワードはご利用できなくなります。</span></td>
				</tr>
				<tr><td height="15"></td></tr>
				<tr>
					<td align="center" bgcolor="#cccccc">
					<table width="490" border="0" cellspacing="0" cellpadding="0" summary=" ">
						<tr><td height="5"></td></tr>
						<tr>
							<td align="center" height="120" bgcolor="#ffffff" class="fs12">メールアドレス：　&nbsp;<!--★メールアドレス入力★--><input type="text" name="email" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_login_email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="50" size="40" class="box40" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['errmsg'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
; ime-mode: disabled;" /></td>
						</tr>
						<tr><td height="5"></td></tr>
					</table>
					</td>
				</tr>
				<?php if ($this->_tpl_vars['errmsg']): ?>
				<tr>
					<td class="fs12" align="left"><span class="red"><?php echo $this->_tpl_vars['errmsg']; ?>
</span></td>
				</tr>
				<?php endif; ?>
				<tr><td height="15"></td></tr>
				<tr>
					<td align="center"><input type="image" onmouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/common/b_next_on.gif',this)" onmouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/common/b_next.gif',this)" src="<?php echo @URL_DIR; ?>
img/common/b_next.gif" width="150" height="30" alt="次へ" border="0" name="next" id="next" /></td>
				</tr>
				<tr><td height="30"></td></tr>
			</table>
			</td>
		</tr>
		<tr><td bgcolor="#ffa85c"><img src="<?php echo @URL_DIR; ?>
misc/_.gif" width="1" height="5" alt=""></td></tr>
		<tr><td height="20"></td></tr>
	</table>	
	</form>
</div>

</body>
</html>