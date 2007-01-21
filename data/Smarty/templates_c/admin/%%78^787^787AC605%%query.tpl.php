<?php /* Smarty version 2.6.13, created on 2007-01-18 18:21:10
         compiled from mail/query.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'mail/query.tpl', 39, false),array('modifier', 'default', 'mail/query.tpl', 76, false),)), $this); ?>
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
<title>ECサイト管理者ページ</title>
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
<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input type="hidden" name="mode" value="search">
	<tr valign="top">
		<td class="mainbg">
			<!--▼登録テーブルここから-->
			<table width="680" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--メインエリア-->
				<tr>
					<td align="center">
						<table width="660" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_top.jpg" width="668" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<?php echo @URL_DIR; ?>
img/contents/main_left.jpg"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
									<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_top.gif" width="640" height="7" alt=""></td>
										</tr>
										<tr>
											<td background="<?php echo @URL_DIR; ?>
img/contents/contents_title_left_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
											<td bgcolor="#636469" width="600" class="fs14n"><span class="white"><!--コンテンツタイトル-->メンバー登録/編集</span></td>
											<td background="<?php echo @URL_DIR; ?>
img/contents/contents_title_right_bg.gif"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="18" height="1" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/contents_title_bottom.gif" width="640" height="7" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bar.jpg" width="640" height="10" alt=""></td>
										</tr>
									</table>
									
									<table width="640" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">顧客名</td>
											<td bgcolor="#ffffff" width="198"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['list_data']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, "（未指定）") : smarty_modifier_default($_tmp, "（未指定）")); ?>
</td>
											<td bgcolor="#f0f0f0" width="110">顧客名（カナ）</td>
											<td bgcolor="#ffffff" width="249"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['list_data']['kana'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, "（未指定）") : smarty_modifier_default($_tmp, "（未指定）")); ?>
</td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">都道府県</td>
											<td bgcolor="#ffffff" width="198"><?php echo ((is_array($_tmp=@$this->_tpl_vars['list_data']['pref_disp'])) ? $this->_run_mod_handler('default', true, $_tmp, "（未指定）") : smarty_modifier_default($_tmp, "（未指定）")); ?>
</td>
											<td bgcolor="#f0f0f0" width="110">TEL</td>
											<td bgcolor="#ffffff" width="249"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['list_data']['tel'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, "（未指定）") : smarty_modifier_default($_tmp, "（未指定）")); ?>
</td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">性別</td>
											<td bgcolor="#ffffff" width="198"><?php echo ((is_array($_tmp=@$this->_tpl_vars['list_data']['sex_disp'])) ? $this->_run_mod_handler('default', true, $_tmp, "（未指定）") : smarty_modifier_default($_tmp, "（未指定）")); ?>
</td>
											<td bgcolor="#f0f0f0" width="110">誕生月</td>
											<td bgcolor="#ffffff" width="249"><?php if ($this->_tpl_vars['list_data']['birth_month']):  echo ((is_array($_tmp=$this->_tpl_vars['list_data']['birth_month'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
月<?php else: ?>（未指定）<?php endif; ?></td>				
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">配信形式</td>
											<td bgcolor="#ffffff" width="198"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['list_data']['htmlmail_disp'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, "（未指定）") : smarty_modifier_default($_tmp, "（未指定）")); ?>
</td>
											<td bgcolor="#f0f0f0" width="110">購入回数</td>
											<td bgcolor="#ffffff" width="199"><?php if ($this->_tpl_vars['list_data']['buy_times_from']):  echo ((is_array($_tmp=$this->_tpl_vars['list_data']['buy_times_from'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
回 〜 <?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['buy_times_to'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
回<?php else: ?>（未指定）<?php endif; ?></td>
										</tr>
										<tr class="fs12n">
																					<td bgcolor="#f0f0f0" width="110">購入商品コード</td>
											<td bgcolor="#ffffff" width="198"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['list_data']['buy_product_code'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, "（未指定）") : smarty_modifier_default($_tmp, "（未指定）")); ?>
</td>
											<td bgcolor="#f0f0f0" width="110">購入金額</td>
											<td bgcolor="#ffffff" width="199"><?php if ($this->_tpl_vars['list_data']['buy_total_from']):  echo ((is_array($_tmp=$this->_tpl_vars['list_data']['buy_total_from'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
円 〜 <?php echo ((is_array($_tmp=$this->_tpl_vars['list_data']['buy_total_to'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
円<?php else: ?>（未指定）<?php endif; ?></td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">メールアドレス</td>
											<td bgcolor="#ffffff" width="507" colspan="3"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['list_data']['email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, "（未指定）") : smarty_modifier_default($_tmp, "（未指定）")); ?>
</td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">職業</td>
											<td bgcolor="#ffffff" width="507" colspan="3"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['list_data']['job_disp'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, "（未指定）") : smarty_modifier_default($_tmp, "（未指定）")); ?>
</td>
										</tr>
							
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">生年月日</td>
											<td bgcolor="#ffffff" width="507" colspan="3">
											<?php if ($this->_tpl_vars['list_data']['b_start_year']): ?>
												<?php echo $this->_tpl_vars['list_data']['b_start_year']; ?>
年<?php echo $this->_tpl_vars['list_data']['b_start_month']; ?>
月<?php echo $this->_tpl_vars['list_data']['b_start_day']; ?>
日&nbsp;〜&nbsp;<?php echo $this->_tpl_vars['list_data']['b_end_year']; ?>
年<?php echo $this->_tpl_vars['list_data']['b_end_month']; ?>
月<?php echo $this->_tpl_vars['list_data']['b_end_day']; ?>
日
											<?php else: ?>（未指定）<?php endif; ?>
											</td>
										</tr>	
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">登録日</td>
											<td bgcolor="#ffffff" width="507" colspan="3">
											<?php if ($this->_tpl_vars['list_data']['start_year']): ?>
												<?php echo $this->_tpl_vars['list_data']['start_year']; ?>
年<?php echo $this->_tpl_vars['list_data']['start_month']; ?>
月<?php echo $this->_tpl_vars['list_data']['start_day']; ?>
日&nbsp;〜&nbsp;<?php echo $this->_tpl_vars['list_data']['end_year']; ?>
年<?php echo $this->_tpl_vars['list_data']['end_month']; ?>
月<?php echo $this->_tpl_vars['list_data']['end_day']; ?>
日
											<?php else: ?>（未指定）<?php endif; ?>
											</td>
										</tr>			
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">最終購入日</td>
											<td bgcolor="#ffffff" width="507" colspan="3">
											<?php if ($this->_tpl_vars['list_data']['buy_start_year']): ?>
												<?php echo $this->_tpl_vars['list_data']['buy_start_year']; ?>
年<?php echo $this->_tpl_vars['list_data']['buy_start_month']; ?>
月<?php echo $this->_tpl_vars['list_data']['buy_start_day']; ?>
日&nbsp;〜&nbsp;<?php echo $this->_tpl_vars['list_data']['buy_end_year']; ?>
年<?php echo $this->_tpl_vars['list_data']['buy_end_month']; ?>
月<?php echo $this->_tpl_vars['list_data']['buy_end_day']; ?>
日
											<?php else: ?>（未指定）<?php endif; ?>	
											</td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">購入商品名</td>
											<td bgcolor="#ffffff" width="198"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['list_data']['buy_product_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, "（未指定）") : smarty_modifier_default($_tmp, "（未指定）")); ?>
</td>
											<td bgcolor="#f0f0f0" width="110">カテゴリ</td>
											<td bgcolor="#ffffff" width="199"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['list_data']['category_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, "（未指定）") : smarty_modifier_default($_tmp, "（未指定）")); ?>
</td>
										</tr>
									</table>
	
									<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="5" alt=""></td>
											<td><img src="<?php echo @URL_DIR; ?>
img/contents/tbl_top.gif" width="638" height="7" alt=""></td>
											<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="5" alt=""></td>
										</tr>
										<tr>
											<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="10" alt=""></td>
											<td bgcolor="#e9e7de" align="center">
											<table border="0" cellspacing="0" cellpadding="0" summary=" ">
												<tr>
													<td><input type="button" name="close" value="ウインドウを閉じる" onclick="window.close();" /></td>
												</tr>
											</table>
											</td>
											<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="10" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/tbl_bottom.gif" width="640" height="8" alt=""></td>
										</tr>
									</table>
								</td>
								<td background="<?php echo @URL_DIR; ?>
img/contents/main_right.jpg"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/contents/main_bottom.jpg" width="668" height="14" alt=""></td>
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