<?php /* Smarty version 2.6.13, created on 2007-01-19 12:58:00
         compiled from /home/web/beta.ec-cube.net/html/user_data/include/campaign/test15/active/preview.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/home/web/beta.ec-cube.net/html/user_data/include/campaign/test15/active/preview.tpl', 126, false),array('modifier', 'sfGetErrorColor', '/home/web/beta.ec-cube.net/html/user_data/include/campaign/test15/active/preview.tpl', 128, false),array('modifier', 'default', '/home/web/beta.ec-cube.net/html/user_data/include/campaign/test15/active/preview.tpl', 151, false),array('function', 'html_options', '/home/web/beta.ec-cube.net/html/user_data/include/campaign/test15/active/preview.tpl', 130, false),)), $this); ?>
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

<div id="cart_tag_27669">
<?php $this->assign('id', $this->_tpl_vars['arrProducts'][27669]['product_id']); ?>
<!--▼買い物かご-->
<table width="420" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td height=5></td></tr>
	<tr valign="top" align="right" id="price">
		<td id="right" colspan=2>
			<table cellspacing="0" cellpadding="0" summary=" " id="price">
				<tr>
					<td align="center">
					<table width="285" cellspacing="0" cellpadding="0" summary=" ">
						<?php if ($this->_tpl_vars['tpl_classcat_find1'][$this->_tpl_vars['id']]): ?>
						<?php $this->assign('class1', "classcategory_id".($this->_tpl_vars['id'])."_1"); ?>
						<?php $this->assign('class2', "classcategory_id".($this->_tpl_vars['id'])."_2"); ?>
						<tr><td colspan="2" height="10" align="center" class="fs12"><span class="redst"><?php if ($this->_tpl_vars['arrErr'][$this->_tpl_vars['class1']] != ""): ?>※ <?php echo $this->_tpl_vars['tpl_class_name1'][$this->_tpl_vars['id']]; ?>
を入力して下さい。<?php endif; ?></span></td></tr>
						<tr>
							<td align="right" class="fs12st"><?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_class_name1'][$this->_tpl_vars['id']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
： </td>
							<td>
								<select name="<?php echo $this->_tpl_vars['class1']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['class1']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" onchange="lnSetSelect('<?php echo $this->_tpl_vars['class1']; ?>
', '<?php echo $this->_tpl_vars['class2']; ?>
', '<?php echo $this->_tpl_vars['id']; ?>
','');">
								<option value="">選択してください</option>
								<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrClassCat1'][$this->_tpl_vars['id']],'selected' => $this->_tpl_vars['arrForm'][$this->_tpl_vars['class1']]), $this);?>

								</select>
							</td>
						</tr>
						<?php endif; ?>
						<?php if ($this->_tpl_vars['tpl_classcat_find2'][$this->_tpl_vars['id']]): ?>
						<tr><td colspan="2" height="5" align="center" class="fs12"><span class="redst"><?php if ($this->_tpl_vars['arrErr'][$this->_tpl_vars['class2']] != ""): ?>※ <?php echo $this->_tpl_vars['tpl_class_name2'][$this->_tpl_vars['id']]; ?>
を入力して下さい。<?php endif; ?></span></td></tr>
						<tr>
							<td align="right" class="fs12st"><?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_class_name2'][$this->_tpl_vars['id']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
： </td>
							<td>
								<select name="<?php echo $this->_tpl_vars['class2']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['class2']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
								<option value="">選択してください</option>
								</select>
							</td>
						</tr>
						<?php endif; ?>
						<?php $this->assign('quantity', "quantity".($this->_tpl_vars['id'])); ?>		
						<tr><td colspan="2" height="10" align="center" class="fs12"><span class="redst"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['quantity']]; ?>
</span></td></tr>
						<tr>
							<td align="right" width="115" class="fs12st">個数： 
								<?php if ($this->_tpl_vars['arrErr']['quantity'] != ""): ?><br/><span class="redst"><?php echo $this->_tpl_vars['arrErr']['quantity']; ?>
</span><?php endif; ?>
								<input type="text" name="<?php echo $this->_tpl_vars['quantity']; ?>
" size="3" class="box3" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['arrForm'][$this->_tpl_vars['quantity']])) ? $this->_run_mod_handler('default', true, $_tmp, 1) : smarty_modifier_default($_tmp, 1)); ?>
" maxlength=<?php echo @INT_LEN; ?>
 style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['quantity']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" >
							</td>
							<td width="170" align="center">
								<a href="" onclick="fnChangeAction('<?php echo ((is_array($_tmp=$_SERVER['REQUEST_URI'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
#product<?php echo $this->_tpl_vars['id']; ?>
'); fnModeSubmit('cart','product_id','<?php echo $this->_tpl_vars['id']; ?>
'); return false;" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/products/b_cartin_on.gif','cart<?php echo $this->_tpl_vars['id']; ?>
');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/products/b_cartin.gif','cart<?php echo $this->_tpl_vars['id']; ?>
');"><img src="<?php echo @URL_DIR; ?>
img/products/b_cartin.gif" width="115" height="25" alt="カゴに入れる" name="cart<?php echo $this->_tpl_vars['id']; ?>
" id="cart<?php echo $this->_tpl_vars['id']; ?>
" /></a>
							</td>
						</tr>
						<tr><td height="10"></td></tr>
					</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<div id="cart_tag_30050">
<?php $this->assign('id', $this->_tpl_vars['arrProducts'][30050]['product_id']); ?>
<!--▼買い物かご-->
<table width="420" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td height=5></td></tr>
	<tr valign="top" align="right" id="price">
		<td id="right" colspan=2>
			<table cellspacing="0" cellpadding="0" summary=" " id="price">
				<tr>
					<td align="center">
					<table width="285" cellspacing="0" cellpadding="0" summary=" ">
						<?php if ($this->_tpl_vars['tpl_classcat_find1'][$this->_tpl_vars['id']]): ?>
						<?php $this->assign('class1', "classcategory_id".($this->_tpl_vars['id'])."_1"); ?>
						<?php $this->assign('class2', "classcategory_id".($this->_tpl_vars['id'])."_2"); ?>
						<tr><td colspan="2" height="10" align="center" class="fs12"><span class="redst"><?php if ($this->_tpl_vars['arrErr'][$this->_tpl_vars['class1']] != ""): ?>※ <?php echo $this->_tpl_vars['tpl_class_name1'][$this->_tpl_vars['id']]; ?>
を入力して下さい。<?php endif; ?></span></td></tr>
						<tr>
							<td align="right" class="fs12st"><?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_class_name1'][$this->_tpl_vars['id']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
： </td>
							<td>
								<select name="<?php echo $this->_tpl_vars['class1']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['class1']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" onchange="lnSetSelect('<?php echo $this->_tpl_vars['class1']; ?>
', '<?php echo $this->_tpl_vars['class2']; ?>
', '<?php echo $this->_tpl_vars['id']; ?>
','');">
								<option value="">選択してください</option>
								<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrClassCat1'][$this->_tpl_vars['id']],'selected' => $this->_tpl_vars['arrForm'][$this->_tpl_vars['class1']]), $this);?>

								</select>
							</td>
						</tr>
						<?php endif; ?>
						<?php if ($this->_tpl_vars['tpl_classcat_find2'][$this->_tpl_vars['id']]): ?>
						<tr><td colspan="2" height="5" align="center" class="fs12"><span class="redst"><?php if ($this->_tpl_vars['arrErr'][$this->_tpl_vars['class2']] != ""): ?>※ <?php echo $this->_tpl_vars['tpl_class_name2'][$this->_tpl_vars['id']]; ?>
を入力して下さい。<?php endif; ?></span></td></tr>
						<tr>
							<td align="right" class="fs12st"><?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_class_name2'][$this->_tpl_vars['id']])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
： </td>
							<td>
								<select name="<?php echo $this->_tpl_vars['class2']; ?>
" style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['class2']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
">
								<option value="">選択してください</option>
								</select>
							</td>
						</tr>
						<?php endif; ?>
						<?php $this->assign('quantity', "quantity".($this->_tpl_vars['id'])); ?>		
						<tr><td colspan="2" height="10" align="center" class="fs12"><span class="redst"><?php echo $this->_tpl_vars['arrErr'][$this->_tpl_vars['quantity']]; ?>
</span></td></tr>
						<tr>
							<td align="right" width="115" class="fs12st">個数： 
								<?php if ($this->_tpl_vars['arrErr']['quantity'] != ""): ?><br/><span class="redst"><?php echo $this->_tpl_vars['arrErr']['quantity']; ?>
</span><?php endif; ?>
								<input type="text" name="<?php echo $this->_tpl_vars['quantity']; ?>
" size="3" class="box3" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['arrForm'][$this->_tpl_vars['quantity']])) ? $this->_run_mod_handler('default', true, $_tmp, 1) : smarty_modifier_default($_tmp, 1)); ?>
" maxlength=<?php echo @INT_LEN; ?>
 style="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrErr'][$this->_tpl_vars['quantity']])) ? $this->_run_mod_handler('sfGetErrorColor', true, $_tmp) : sfGetErrorColor($_tmp)); ?>
" >
							</td>
							<td width="170" align="center">
								<a href="" onclick="fnChangeAction('<?php echo ((is_array($_tmp=$_SERVER['REQUEST_URI'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
#product<?php echo $this->_tpl_vars['id']; ?>
'); fnModeSubmit('cart','product_id','<?php echo $this->_tpl_vars['id']; ?>
'); return false;" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/products/b_cartin_on.gif','cart<?php echo $this->_tpl_vars['id']; ?>
');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/products/b_cartin.gif','cart<?php echo $this->_tpl_vars['id']; ?>
');"><img src="<?php echo @URL_DIR; ?>
img/products/b_cartin.gif" width="115" height="25" alt="カゴに入れる" name="cart<?php echo $this->_tpl_vars['id']; ?>
" id="cart<?php echo $this->_tpl_vars['id']; ?>
" /></a>
							</td>
						</tr>
						<tr><td height="10"></td></tr>
					</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>				</td>
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