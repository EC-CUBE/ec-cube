<?php /* Smarty version 2.6.13, created on 2007-01-19 12:55:32
         compiled from inquiry/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'inquiry/index.tpl', 15, false),array('modifier', 'nl2br', 'inquiry/index.tpl', 43, false),array('function', 'sfSetErrorStyle', 'inquiry/index.tpl', 69, false),array('function', 'html_options', 'inquiry/index.tpl', 105, false),)), $this); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<link rel="stylesheet" href="<?php echo @URL_DIR; ?>
css/contents.css" type="text/css">
<link rel="stylesheet" href="<?php echo @URL_DIR; ?>
css/common.css" type="text/css">
<script type="text/javascript" src="<?php echo @URL_DIR; ?>
js/css.js"></script>
<script type="text/javascript" src="<?php echo @URL_DIR; ?>
js/navi.js"></script>
<script type="text/javascript" src="<?php echo @URL_DIR; ?>
js/site.js"></script>
<script type="text/javascript" src="<?php echo @URL_DIR; ?>
js/win_op.js"></script>
<title><?php echo $this->_tpl_vars['arrSiteInfo']['shop_name']; ?>
/アンケート　<?php echo ((is_array($_tmp=$this->_tpl_vars['QUESTION']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</title>
</head>
<body bgcolor="#ffffff" text="#555555" link="#0099cc" vlink="#CC0000" alink="#993399" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">


<!--▲TITLE-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td height="40" bgcolor="#f6f6f6" align="center">
		<table width="710" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td height="30" bgcolor="ff0000"><img src="../misc/_.gif" width="7" height="1" alt=""></td>
				<td height="30"><img src="../misc/_.gif" width="8" height="1" alt=""></td>
				<td height="30"width="695" class="red"><strong><span class="fs18n"><?php echo ((is_array($_tmp=$this->_tpl_vars['QUESTION']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</span></strong></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#e2e2e2"><img src="../misc/_.gif" width="10" height="1" alt=""></td>
	</tr>
	<tr><td height="10"></td></tr>
	<tr>
		<td align="center" valign="top">
		<table width="600" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
			<input type="hidden" name="question_id" value="<?php echo $this->_tpl_vars['question_id']; ?>
">
			<tr>
				<td class="fs12"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['QUESTION']['contents'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>

				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<?php if ($this->_tpl_vars['errmsg']): ?><tr><td class="fs12n"><span class="red"><br>入力エラーが発生致しました。各項目のエラーメッセージをご確認の上、正しく入力してください。</span></td></tr><?php endif; ?>	
			<tr>
				<td bgcolor="#cccccc">
				<table width="600" border="0" cellspacing="1" cellpadding="10" summary=" ">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inquiry/inquiry.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				</table>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td class="fs12n"><span class="red">※</span>印は入力必須項目です。</td>
			</tr>
			<tr><td height="5"></td></tr>
			<input type="hidden" name="mode" value="confirm">
			<tr>
				<td bgcolor="#cccccc">
				<table width="600" border="0" cellspacing="1" cellpadding="10">
					
					<tr>
						<td class="fs12n" bgcolor="#ebf9ff" width="150"><span class="red">※</span>お名前</td>
						<td class="fs12n"bgcolor="#ffffff" width="407">
							<span class="red"><?php echo $this->_tpl_vars['arrErr']['name01'];  echo $this->_tpl_vars['arrErr']['name02']; ?>
</span>
										<input type="text" name="name01" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['name01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" size="20" class="box20" <?php if ($this->_tpl_vars['arrErr']['name01']):  echo sfSetErrorStyle(array(), $this); endif; ?> />
							&nbsp;&nbsp;<input type="text" name="name02" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['name02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" size="20" class="box20" <?php if ($this->_tpl_vars['arrErr']['name02']):  echo sfSetErrorStyle(array(), $this); endif; ?> />
						</td>
					</tr>
					<tr>
						<td class="fs12n" bgcolor="#ebf9ff" width="150"><span class="red">※</span>フリガナ</td>
						<td class="fs12n" bgcolor="#ffffff" width="407">
							<span class="red"><?php echo $this->_tpl_vars['arrErr']['kana01'];  echo $this->_tpl_vars['arrErr']['kana02']; ?>
</span>
										<input type="text" name="kana01" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['kana01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" size="20" class="box20" <?php if ($this->_tpl_vars['arrErr']['kana01']):  echo sfSetErrorStyle(array(), $this); endif; ?> />
							&nbsp;&nbsp;<input type="text" name="kana02" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['kana02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @STEXT_LEN; ?>
" size="20" class="box20" <?php if ($this->_tpl_vars['arrErr']['kana02']):  echo sfSetErrorStyle(array(), $this); endif; ?> />
						</td>
					</tr>
					<tr>
						<td class="fs12n" bgcolor="#ebf9ff" width="150"><span class="red">※</span>郵便番号</td>
						<td bgcolor="#ffffff" width="407">
						<table width="407" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n" width="267">
									<span class="red"><?php echo $this->_tpl_vars['arrErr']['zip01'];  echo $this->_tpl_vars['arrErr']['zip02']; ?>
</span>
									〒&nbsp;<input type="text" name="zip01" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['zip01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @ZIP01_LEN; ?>
" size="6" class="box6" maxlength="3"  <?php if ($this->_tpl_vars['arrErr']['zip01']):  echo sfSetErrorStyle(array(), $this); endif; ?> />
										&nbsp;-&nbsp;
										<input type="text" name="zip02" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['zip02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @ZIP02_LEN; ?>
" size="6" class="box6" maxlength="4"  <?php if ($this->_tpl_vars['arrErr']['zip02']):  echo sfSetErrorStyle(array(), $this); endif; ?> />&nbsp;<input type="button" name="address_input" value="住所入力" onclick="fnCallAddress('<?php echo @URL_INPUT_ZIP; ?>
', 'zip01', 'zip02', 'pref', 'addr01'); return false;" />
								</td>								
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td class="fs12n" bgcolor="#ebf9ff" width="150"><span class="red">※</span>ご住所</td>
						<td bgcolor="#ffffff">
						<table width="407" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n" colspan="2">
								<span class="red"><?php echo $this->_tpl_vars['arrErr']['pref'];  echo $this->_tpl_vars['arrErr']['addr01'];  echo $this->_tpl_vars['arrErr']['addr02']; ?>
</span>
								<select name="pref" <?php if ($this->_tpl_vars['arrErr']['pref']):  echo sfSetErrorStyle(array(), $this); endif; ?>>
									<option value="" selected>選択してください</option>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrPref'],'selected' => $this->_tpl_vars['arrForm']['pref']), $this);?>

								</select>
								</td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td width="207">
									<input type="text" name="addr01" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['addr01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="35" class="box35" <?php if ($this->_tpl_vars['arrErr']['addr01']):  echo sfSetErrorStyle(array(), $this); endif; ?> />
								</td>
								<td width="200"><span class="fs10n">ご住所1（市区町村名）</span></td>
							</tr>
							<tr><td height="3"></td></tr>
							<tr>
								<td class="fs12n">
									<input type="text" name="addr02" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['addr02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="35" class="box35" <?php if ($this->_tpl_vars['arrErr']['addr02']):  echo sfSetErrorStyle(array(), $this); endif; ?> />
								</td>
								<td><span class="fs10n">ご住所2（番地、建物、マンション名）</span><br></td>
							</tr>
							<tr>
								<td><span class="fs10n"><span class="red">住所は必ず2つに分けて入力してください。マンション名は必ず入力してください。</span></span></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td class="fs12n" bgcolor="#ebf9ff" width="150"><span class="red">※</span>お電話番号</td>
						<td class="fs12n" bgcolor="#ffffff" width="407">
							<span class="red"><?php echo $this->_tpl_vars['arrErr']['tel01'];  echo $this->_tpl_vars['arrErr']['tel02'];  echo $this->_tpl_vars['arrErr']['tel03']; ?>
</span>
							<input type="text" name="tel01" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['tel01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @TEL_ITEM_LEN; ?>
" size="6" class="box6" <?php if ($this->_tpl_vars['arrErr']['tel01']):  echo sfSetErrorStyle(array(), $this); endif; ?> />&nbsp;-&nbsp;
							<input type="text" name="tel02" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['tel02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @TEL_ITEM_LEN; ?>
" size="6" class="box6" <?php if ($this->_tpl_vars['arrErr']['tel02']):  echo sfSetErrorStyle(array(), $this); endif; ?> />&nbsp;-&nbsp;
							<input type="text" name="tel03" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['tel03'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="<?php echo @TEL_ITEM_LEN; ?>
" size="6" class="box6" <?php if ($this->_tpl_vars['arrErr']['tel03']):  echo sfSetErrorStyle(array(), $this); endif; ?> />
						</td>
					</tr>
					<tr>
						<td class="fs12n" bgcolor="#ebf9ff" width="150"><span class="red">※</span>メールアドレス</td>
						<td bgcolor="#ffffff">
						<table width="407" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td class="fs12n" colspan="2">
									<span class="red"><?php echo $this->_tpl_vars['arrErr']['email']; ?>
</span>
									<input type="text" name="email" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="35" class="box35" <?php if ($this->_tpl_vars['arrErr']['email']):  echo sfSetErrorStyle(array(), $this); endif; ?> />
								</td>
							</tr>
							<tr><td height="3"></td></tr>
							<tr>
								<td class="fs12n" width="227">
									<span class="red"><?php echo $this->_tpl_vars['arrErr']['email02']; ?>
</span>
									<input type="text" name="email02" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrForm']['email02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="35" class="box35" <?php if ($this->_tpl_vars['arrErr']['email02']):  echo sfSetErrorStyle(array(), $this); endif; ?> />
								</td>
								<td width="180" class="fs10"><span class="red">確認のため2度入力してください。</span></td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td align="center"><input type="submit" name="subm1" value="確認ページへ"></td>
			</tr>
			</form>
		</table>
		<br>			

		</td>
	</tr>
</table>

</body>
</html>