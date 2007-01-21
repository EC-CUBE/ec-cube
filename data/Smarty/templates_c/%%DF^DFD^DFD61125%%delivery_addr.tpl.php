<?php /* Smarty version 2.6.13, created on 2007-01-10 20:18:04
         compiled from /home/web/beta.ec-cube.net/html/user_data/templates/mypage/delivery_addr.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/home/web/beta.ec-cube.net/html/user_data/templates/mypage/delivery_addr.tpl', 19, false),array('modifier', 'sfGetErrorColor', '/home/web/beta.ec-cube.net/html/user_data/templates/mypage/delivery_addr.tpl', 61, false),array('function', 'html_options', '/home/web/beta.ec-cube.net/html/user_data/templates/mypage/delivery_addr.tpl', 101, false),)), $this); ?>
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
/<?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</title>
</head>

<body bgcolor="#ffe9e6" text="#555555" link="#3a75af" vlink="#3a75af" alink="#3a75af" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="preLoadImg('<?php echo @URL_DIR; ?>
'); <?php echo $this->_tpl_vars['tpl_onload']; ?>
">
<noscript>
<link rel="stylesheet" href="<?php echo @URL_DIR; ?>
css/common.css" type="text/css">
</noscript>
<div align="center">
<a name="top" id="top"></a>

<!--▼CONTENTS-->
<table width="550" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td height="15"></td></tr>
	<tr><td bgcolor="#ffa85c"><img src="<?php echo @URL_DIR; ?>
misc/_.gif" width="1" height="5" alt=""></td></tr>
	<tr>
		<td align="center" bgcolor="#ffffff">

		<!--▼入力フォームここから-->
		<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
		<input type="hidden" name="mode" value="edit">
		<input type="hidden" name="other_deliv_id" value="<?php echo $_SESSION['other_deliv_id']; ?>
" >
		<input type="hidden" name="ParentPage" value="<?php echo $this->_tpl_vars['ParentPage']; ?>
" >

			<tr><td height="15"></td></tr>
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/shopping/delivadd_title.jpg" width="500" height="40" alt="新しいお届け先の追加・変更"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">下記項目にご入力ください。「<span class="red">※</span>」印は入力必須項目です。<br>
				入力後、一番下の「確認ページへ」ボタンをクリックしてください。</td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td bgcolor="#cccccc">
				<!--入力フォームここから-->
				<table width="500" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="117" bgcolor="#f0f0f0" class="fs12">お名前<span class="red">※</span></td>
						<td width="340" bgcolor="#ffffff" class="fs12n">
							<span class="red"><?php echo $this->_tpl_vars['arrErr']['name01'];  echo $this->_tpl_vars['arrErr']['name02']; ?>
</span>
							姓&nbsp;<input type="text" name="name01" value="<?php if ($this->_tpl_vars['name01'] == ""):  echo ((is_array($_tmp=$this->_tpl_vars['arrOtherDeliv']['name01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  else:  echo ((is_array($_tmp=$this->_tpl_vars['name01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  endif; ?>" maxlength="<?php echo @STEXT_LEN; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['name01'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size=15 class="box15" />　
							名&nbsp;<input type="text" name="name02" value="<?php if ($this->_tpl_vars['name02'] == ""):  echo ((is_array($_tmp=$this->_tpl_vars['arrOtherDeliv']['name02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  else:  echo ((is_array($_tmp=$this->_tpl_vars['name02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  endif; ?>" maxlength="<?php echo @STEXT_LEN; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['name02'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size=15 class="box15" />
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">お名前（フリガナ）<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><?php echo $this->_tpl_vars['arrErr']['kana01'];  echo $this->_tpl_vars['arrErr']['kana02']; ?>
</span>
							セイ&nbsp;<input type="text" name="kana01" value="<?php if ($this->_tpl_vars['kana01'] == ""):  echo ((is_array($_tmp=$this->_tpl_vars['arrOtherDeliv']['kana01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  else:  echo ((is_array($_tmp=$this->_tpl_vars['kana01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  endif; ?>" maxlength="<?php echo @STEXT_LEN; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['kana01'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size=15 class="box15" />　メイ&nbsp;<input type="text" name="kana02" value="<?php if ($this->_tpl_vars['kana02'] == ""):  echo ((is_array($_tmp=$this->_tpl_vars['arrOtherDeliv']['kana02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  else:  echo ((is_array($_tmp=$this->_tpl_vars['kana02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  endif; ?>" maxlength="<?php echo @STEXT_LEN; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['kana02'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size=15 class="box15" /></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">郵便番号<span class="red">※</span></td>
						<td bgcolor="#ffffff">
							<table cellspacing="0" cellpadding="0" summary=" ">
								<tr>
									<td class="fs12n">
										<?php $this->assign('key1', 'zip01'); ?>
										<?php $this->assign('key2', 'zip02'); ?>
										<span class="red"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key1']];  echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['key2']]; ?>
</span>
										〒&nbsp;<input type="text" name="zip01" value="<?php if ($this->_tpl_vars['zip01'] == ""):  echo ((is_array($_tmp=$this->_tpl_vars['arrOtherDeliv']['zip01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  else:  echo ((is_array($_tmp=$this->_tpl_vars['zip01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  endif; ?>" maxlength="<?php echo @ZIP01_LEN; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key1']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
; ime-mode: disabled;" size=6 class="box6" />&nbsp;-&nbsp;<input type="text" name="zip02" value="<?php if ($this->_tpl_vars['zip02'] == ""):  echo ((is_array($_tmp=$this->_tpl_vars['arrOtherDeliv']['zip02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  else:  echo ((is_array($_tmp=$this->_tpl_vars['zip02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  endif; ?>" maxlength="<?php echo @ZIP02_LEN; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['key2']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
; ime-mode: disabled;" size=6 class="box6" />
									</td>
									<td>
										&nbsp;&nbsp;<a href="../address/index.php" onclick="fnCallAddress('<?php echo @URL_INPUT_ZIP; ?>
', 'zip01', 'zip02', 'pref', 'addr01'); return false;" target="_blank"><img src="<?php echo @URL_DIR; ?>
img/common/address.gif" width="86" height="20" alt="住所自動入力" /></a></td>
									</td>
								</tr>
								<tr><td height="5"></td></tr>
								<tr>
									<td colspan="2" class="fs12">郵便番号がわからない方は→<a href="http://search.post.japanpost.jp/7zip/" target="_blank">こちら</a></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">住所<span class="red">※</span></td>
						<td bgcolor="#ffffff">
							<table cellspacing="0" cellpadding="0" summary=" " id="frame02">
								<tr>
									<td class="fs12n"><span class="red"><?php echo $this->_tpl_vars['arrErr']['pref']; ?>
</span>
									<select name="pref" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['pref'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
									<option value="" selected>選択してください</option>
									<?php if ($this->_tpl_vars['pref'] == ""): ?>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrPref'],'selected' => ((is_array($_tmp=$this->_tpl_vars['arrOtherDeliv']['pref'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp))), $this);?>

									<?php else: ?>
									<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrPref'],'selected' => ((is_array($_tmp=$this->_tpl_vars['pref'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp))), $this);?>

									<?php endif; ?>
									</select></td>
								</tr>
								<tr><td height="5"></td></tr>
								<tr>
									<td class="fs12n"><span class="red"><?php echo $this->_tpl_vars['arrErr']['addr01']; ?>
</span>
									<input type="text" name="addr01" value="<?php if ($this->_tpl_vars['addr01'] == ""):  echo ((is_array($_tmp=$this->_tpl_vars['arrOtherDeliv']['addr01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  else:  echo ((is_array($_tmp=$this->_tpl_vars['addr01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  endif; ?>" maxlength="<?php echo @MTEXT_LEN; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['addr01'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size=40 class="box40" /></td>
								</tr>
								<tr><td height="2"></td></tr>
								<tr>
									<td class="fs10n">ご住所1（神戸市中央区）</td>
								</tr>
								<tr><td height="5"></td></tr>
								<tr>
									<td class="fs12n"><span class="red"><?php echo $this->_tpl_vars['arrErr']['addr02']; ?>
</span>
									<input type="text" name="addr02" value="<?php if ($this->_tpl_vars['addr02'] == ""):  echo ((is_array($_tmp=$this->_tpl_vars['arrOtherDeliv']['addr02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  else:  echo ((is_array($_tmp=$this->_tpl_vars['addr02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  endif; ?>" maxlength="<?php echo @MTEXT_LEN; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['addr02'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size=40 class="box40" /></td>
								</tr>
								<tr><td height="2"></td></tr>
								<tr>
									<td class="fs10"><span class="red">住所は必ず2つに分けてご記入ください。マンション名は必ず記入してください。</span></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">電話番号<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><?php echo $this->_tpl_vars['arrErr']['tel01'];  echo $this->_tpl_vars['arrErr']['tel02'];  echo $this->_tpl_vars['arrErr']['tel03']; ?>
</span>
							<input type="text" name="tel01" value="<?php if ($this->_tpl_vars['tel01'] == ""):  echo ((is_array($_tmp=$this->_tpl_vars['arrOtherDeliv']['tel01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  else:  echo ((is_array($_tmp=$this->_tpl_vars['tel01'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  endif; ?>" maxlength="<?php echo @TEL_ITEM_LEN; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['tel01'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size=6 class="box6" />&nbsp;-&nbsp;<input type="text" name="tel02" value="<?php if ($this->_tpl_vars['tel02'] == ""):  echo ((is_array($_tmp=$this->_tpl_vars['arrOtherDeliv']['tel02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  else:  echo ((is_array($_tmp=$this->_tpl_vars['tel02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  endif; ?>" maxlength="<?php echo @TEL_ITEM_LEN; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['tel02'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size=6 class="box6" />&nbsp;-&nbsp;<input type="text" name="tel03" value="<?php if ($this->_tpl_vars['tel03'] == ""):  echo ((is_array($_tmp=$this->_tpl_vars['arrOtherDeliv']['tel03'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  else:  echo ((is_array($_tmp=$this->_tpl_vars['tel03'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  endif; ?>" maxlength="<?php echo @TEL_ITEM_LEN; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr']['tel03'])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" size=6 class="box6" /></td>
					</tr>
				</table>
				<!--入力フォームここまで-->
				</td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td align="center">
					<input type="image" onmouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/common/b_entry_on.gif',this);" onmouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/common/b_entry.gif',this);" src="<?php echo @URL_DIR; ?>
img/common/b_entry.gif" width="150" height="30" alt="登録する" name="register" id="register" />
				</td>
			</tr>
			<tr><td height="30"></td></tr>
		</form>
		</table>
		</td>
	</tr>
	<tr><td bgcolor="#ffa85c"><img src="<?php echo @URL_DIR; ?>
misc/_.gif" width="1" height="5" alt=""></td></tr>
	<tr><td height="20"></td></tr>
</table>
</div>
</body>
</html>


