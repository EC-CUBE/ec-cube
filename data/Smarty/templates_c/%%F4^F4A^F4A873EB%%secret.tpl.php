<?php /* Smarty version 2.6.13, created on 2007-01-10 00:23:17
         compiled from forgot/secret.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'forgot/secret.tpl', 29, false),array('modifier', 'sfGetErrorColor', 'forgot/secret.tpl', 51, false),)), $this); ?>
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
/パスワードを忘れた方(確認ページ)</title>
</head>

<body bgcolor="#f0f0f0" text="#555555" link="#3a75af" vlink="#3a75af" alink="#3a75af" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="preLoadImg('<?php echo @URL_DIR; ?>
')">
<noscript>
<link rel="stylesheet" href="../css/common.css" type="text/css" />
</noscript>

<div align="center">
<table width="550" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" method="post" name="form1">
<input type="hidden" name="mode" value="secret_check">
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
					<td class="fs12">ご登録時に入力した下記質問の答えを入力して「次へ」ボタンをクリックしてください。<br>
					※下記質問の答えをお忘れになられた場合は、<a href="mailto:<?php echo ((is_array($_tmp=$this->_tpl_vars['arrSiteInfo']['email02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrSiteInfo']['email02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a>までご連絡ください。</td>
				</tr>
				<tr><td height="15"></td></tr>
				<tr>
					<td align="center" bgcolor="#cccccc">
					<table width="490" border="0" cellspacing="0" cellpadding="0" summary=" ">
						<tr><td height="5"></td></tr>
						<tr>
							<td align="center" height="120" bgcolor="#ffffff" class="fs12"><?php echo $this->_tpl_vars['Reminder']; ?>
：　&nbsp;<!--★答え入力★--><input type="text" name="input_reminder" value="" size="40" class="box40" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['errmsg'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" /></td>
						</tr>
						<tr><td height="5"></td></tr>
					</table>
					</td>
				</tr>
				<?php if ($this->_tpl_vars['errmsg']): ?>
				<tr>
					<td class="fs12"><span class="red"><?php echo $this->_tpl_vars['errmsg']; ?>
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
</form>	
</table>
</div>

</body>
</html>
