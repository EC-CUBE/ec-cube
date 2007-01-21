<?php /* Smarty version 2.6.13, created on 2007-01-19 13:00:21
         compiled from system/input.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'system/input.tpl', 102, false),)), $this); ?>
<!--　-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=<?php echo @CHAR_CODE; ?>
" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="<?php echo @URL_DIR; ?>
admin/css/contents.css" type="text/css" media="all" />
<script type="text/javascript" src="<?php echo @URL_DIR; ?>
js/css.js"></script>
<script type="text/javascript" src="<?php echo @URL_DIR; ?>
js/navi.js"></script>
<script type="text/javascript" src="<?php echo @URL_DIR; ?>
js/win_op.js"></script>
<script type="text/javascript" src="<?php echo @URL_DIR; ?>
js/site.js"></script>
<script type="text/javascript" src="<?php echo @URL_DIR; ?>
js/admin.js"></script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'css/contents.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<title>メンバー登録・編集</title>
<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();
//-->
</script>
</head>

<body bgcolor="#ffffff" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="<?php echo $this->_tpl_vars['tpl_onload']; ?>
">
<noscript>
<link rel="stylesheet" href="<?php echo @URL_ADMIN_CSS; ?>
common.css" type="text/css" />
</noscript>

<div align="center">
<!--★★メインコンテンツ★★-->
<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<?php echo $this->_tpl_vars['tpl_recv']; ?>
" onsubmit="return fnRegistMember();">
<input type="hidden" name="mode" value="<?php echo $this->_tpl_vars['tpl_mode']; ?>
">
<input type="hidden" name="member_id" value="<?php echo $this->_tpl_vars['tpl_member_id']; ?>
">
<input type="hidden" name="pageno" value="<?php echo $this->_tpl_vars['tpl_pageno']; ?>
">
<input type="hidden" name="old_login_id" value="<?php echo $this->_tpl_vars['tpl_old_login_id']; ?>
">
	<tr valign="top">
		<td class="mainbg">
			<!--▼登録テーブルここから-->
			<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--メインエリア-->
				<tr>
					<td align="center">
						<table width="470" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_top.jpg" width="470" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<?php echo @URL_DIR; ?>
img/contents/main_left.jpg"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
									
									<table width="440" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_top.gif" width="440" height="7" alt=""></td>
										</tr>
										<tr>
											<td background="<?php echo @URL_DIR; ?>
img/contents/contents_title_left_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
											<td bgcolor="#636469" width="400" class="fs14n"><span class="white"><!--コンテンツタイトル-->メンバー登録/編集</span></td>
											<td background="<?php echo @URL_DIR; ?>
img/contents/contents_title_right_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="18" height="1" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_bottom.gif" width="440" height="7" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="440" height="10" alt=""></td>
										</tr>
									</table>
									
									<table width="440" border="0" cellspacing="1" cellpadding="8" summary=" ">
										<tr class="fs12n">
											<td width="90" bgcolor="#f3f3f3">名前</td>
											<td width="337" bgcolor="#ffffff"><?php if ($this->_tpl_vars['arrErr']['name']): ?><span class="red"><?php echo $this->_tpl_vars['arrErr']['name']; ?>
</span><?php endif; ?><input type="text" name="name" size="30" class="box30" value="<?php echo $this->_tpl_vars['arrForm']['name']; ?>
" maxlength="<?php echo @STEXT_LEN; ?>
"/>　<span class="red">※必須入力</span>
											</td>
										</tr>
										<tr class="fs12n">
											<td width="90" bgcolor="#f3f3f3">所属</td>
											<td width="337" bgcolor="#ffffff"><?php if ($this->_tpl_vars['arrErr']['department']): ?><span class="red"><?php echo $this->_tpl_vars['arrErr']['department']; ?>
</span><?php endif; ?><input type="text" name="department" size="30" class="box30" value="<?php echo $this->_tpl_vars['arrForm']['department']; ?>
" maxlength="<?php echo @STEXT_LEN; ?>
"/>
											</td>
										</tr>
										<tr class="fs12">
											<td width="90" bgcolor="#f3f3f3">ログインＩＤ</td>
											<td width="337" bgcolor="#ffffff"><?php if ($this->_tpl_vars['arrErr']['login_id']): ?><span class="red"><?php echo $this->_tpl_vars['arrErr']['login_id']; ?>
</span><?php endif; ?><input type="text" name="login_id" size="20" class="box20"  value="<?php echo $this->_tpl_vars['arrForm']['login_id']; ?>
" maxlength="<?php echo @STEXT_LEN; ?>
"/>　<span class="red">※必須入力</span><br />
											※半角英数字・15文字以内</td>
										</tr>
										<tr class="fs12">
											<td width="90" bgcolor="#f3f3f3">パスワード</td>
											<td width="337" bgcolor="#ffffff"><?php if ($this->_tpl_vars['arrErr']['password']): ?><span class="red"><?php echo $this->_tpl_vars['arrErr']['password']; ?>
</span><?php endif; ?><input type="password" name="password" size="20" class="box20" value="<?php echo $this->_tpl_vars['arrForm']['password']; ?>
" onfocus="<?php echo $this->_tpl_vars['tpl_onfocus']; ?>
" maxlength="<?php echo @STEXT_LEN; ?>
"/>　<span class="red">※必須入力</span><br />
											※半角英数字・15文字以内</td>
										</tr>
										<tr class="fs12n">
											<td width="90" bgcolor="#f3f3f3">管理権限</td>
											<td width="337" bgcolor="#ffffff"><?php if ($this->_tpl_vars['arrErr']['authority']): ?><span class="red"><?php echo $this->_tpl_vars['arrErr']['authority']; ?>
</span><?php endif; ?><select name="authority">
											<option value="" >選択してください</option>
											<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrAUTHORITY'],'selected' => $this->_tpl_vars['arrForm']['authority']), $this);?>

											</select>　<span class="red">※必須入力</span></td>
										</tr>
									</table>

									<table width="440" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="5" alt=""></td>
											<td><img src="<?php echo @URL_DIR; ?>
img/contents/tbl_top.gif" width="438" height="7" alt=""></td>
											<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="5" alt=""></td>
										</tr>
										<tr>
											<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="10" alt=""></td>
											<td bgcolor="#e9e7de" align="center">
											<table border="0" cellspacing="0" cellpadding="0" summary=" ">
												<tr>
													<td><input type="image" onMouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/contents/btn_regist.jpg',this)" src="<?php echo @URL_DIR; ?>
img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" ></td>
												</tr>
											</table>
											</td>
											<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="10" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/tbl_bottom.gif" width="440" height="8" alt=""></td>
										</tr>
									</table>
								</td>
								<td background="<?php echo @URL_DIR; ?>
img/contents/main_right.jpg"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bottom.jpg" width="470" height="14" alt=""></td>
							</tr>
							<tr><td height="30"></td></tr>
						</table>
					</td>
				</tr>
				<!--メインエリア-->
			</table>
			<!--▲登録テーブルここまで-->
		</td>
	</tr>
</form>
</table>
<!--★★メインコンテンツ★★-->
</div>

</body>
</html>

