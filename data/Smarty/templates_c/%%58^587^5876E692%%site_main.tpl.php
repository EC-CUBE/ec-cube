<?php /* Smarty version 2.6.13, created on 2007-01-09 23:49:37
         compiled from ./site_main.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', './site_main.tpl', 36, false),array('modifier', 'sfPrintEbisTag', './site_main.tpl', 144, false),array('modifier', 'sfPrintAffTag', './site_main.tpl', 146, false),)), $this); ?>
<body bgcolor="#ffe9e6" text="#555555" link="#3a75af" vlink="#3a75af" alink="#3a75af" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="preLoadImg('<?php echo @URL_DIR; ?>
'); <?php echo $this->_tpl_vars['tpl_onload']; ?>
">
<noscript>
<link rel="stylesheet" href="<?php echo @URL_DIR; ?>
css/common.css" type="text/css" />
</noscript>

<div align="center">
<a name="top" id="top"></a>

<!--▼HEADER-->
<?php if ($this->_tpl_vars['arrPageLayout']['header_chk'] != 2): ?> 
<?php $this->assign('header_dir', (@HTML_PATH)."user_data/include/header.tpl"); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['header_dir'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<!--▲HEADER-->

<!--▼MAIN-->
<div id="base">
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc" width="1"><img src="<?php echo @URL_DIR; ?>
img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff" width="1"><img src="<?php echo @URL_DIR; ?>
img/_.gif" width="5" height="1" alt="" /></td>
		<td bgcolor="#ffffff" align="left" width=100%> 

		
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr valign="top">
				<!--▼左ナビ-->
				<?php if (count($this->_tpl_vars['arrPageLayout']['LeftNavi']) > 0): ?>
			        <td align="left">
			        <table border="0" cellspacing="0" cellpadding="0" summary=" ">
			        	<?php $_from = $this->_tpl_vars['arrPageLayout']['LeftNavi']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['LeftNaviKey'] => $this->_tpl_vars['LeftNaviItem']):
?>
				        <tr><td align="center">
				        <!-- ▼<?php echo $this->_tpl_vars['LeftNaviItem']['bloc_name']; ?>
 ここから-->
			        	<?php if ($this->_tpl_vars['LeftNaviItem']['php_path'] != ""): ?>
							<?php require_once(SMARTY_CORE_DIR . 'core.smarty_include_php.php');
smarty_core_smarty_include_php(array('smarty_file' => $this->_tpl_vars['LeftNaviItem']['php_path'], 'smarty_assign' => '', 'smarty_once' => false, 'smarty_include_vars' => array()), $this); ?>

						<?php else: ?>
							<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['LeftNaviItem']['tpl_path'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<?php endif; ?>
				        <!-- ▲<?php echo $this->_tpl_vars['LeftNaviItem']['bloc_name']; ?>
 ここまで-->
				        </td></tr>
				    <?php endforeach; endif; unset($_from); ?>
					</table>
					</td>
					<td bgcolor="#ffffff" width="5"><img src="<?php echo @URL_DIR; ?>
img/_.gif" width="5" height="1" alt="" /></td>
				<?php endif; ?>
				<!--▲左ナビ-->
			
				<td align="center" width=100%>
			        <table border="0" cellspacing="0" cellpadding="0" summary=" ">
			        
					<!--▼メイン上部-->
					<?php if (count($this->_tpl_vars['arrPageLayout']['MainHead']) > 0): ?>
					<tr><td align="center">
				        <table border="0" cellspacing="0" cellpadding="0" summary=" ">
				        <?php $_from = $this->_tpl_vars['arrPageLayout']['MainHead']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['MainHeadKey'] => $this->_tpl_vars['MainHeadItem']):
?>
					        <tr><td height=3><td></tr>
					        <tr><td align="center">
					        <!-- ▼<?php echo $this->_tpl_vars['MainHeadItem']['bloc_name']; ?>
 ここから-->
				        	<?php if ($this->_tpl_vars['MainHeadItem']['php_path'] != ""): ?>
								<?php require_once(SMARTY_CORE_DIR . 'core.smarty_include_php.php');
smarty_core_smarty_include_php(array('smarty_file' => $this->_tpl_vars['MainHeadItem']['php_path'], 'smarty_assign' => '', 'smarty_once' => false, 'smarty_include_vars' => array()), $this); ?>

							<?php else: ?>
								<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['MainHeadItem']['tpl_path'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
							<?php endif; ?>
					        <!-- ▲<?php echo $this->_tpl_vars['MainHeadItem']['bloc_name']; ?>
 ここまで-->
					        </td></tr>
						<?php endforeach; endif; unset($_from); ?>
						</table>
					</td><tr>
					<?php endif; ?>
					<!--▲メイン上部-->
					
					<tr><td align="center"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['tpl_mainpage'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td></tr>
					
					<!--▼メイン下部-->
					<tr><td align="center">
					<?php if (count($this->_tpl_vars['arrPageLayout']['MainFoot']) > 0): ?>
			        <table border="0" cellspacing="0" cellpadding="0" summary=" ">
				        <?php $_from = $this->_tpl_vars['arrPageLayout']['MainFoot']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['MainFootKey'] => $this->_tpl_vars['MainFootItem']):
?>
					        <tr><td height=3><td></tr>
					        <tr><td align="center">
					        <!-- ▼<?php echo $this->_tpl_vars['MainFootItem']['bloc_name']; ?>
 ここから-->
				        	<?php if ($this->_tpl_vars['MainFootItem']['php_path'] != ""): ?>
								<?php require_once(SMARTY_CORE_DIR . 'core.smarty_include_php.php');
smarty_core_smarty_include_php(array('smarty_file' => $this->_tpl_vars['MainFootItem']['php_path'], 'smarty_assign' => '', 'smarty_once' => false, 'smarty_include_vars' => array()), $this); ?>

							<?php else: ?>
								<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['MainFootItem']['tpl_path'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
							<?php endif; ?>
					        <!-- ▲<?php echo $this->_tpl_vars['MainFootItem']['bloc_name']; ?>
 ここまで-->
					        </td></tr>
						<?php endforeach; endif; unset($_from); ?>
						</table>
					<?php endif; ?>
					</td><tr>
					<!--▲メイン下部-->					
	
					</table>
				</td>

				<!--▼右ナビ-->
				<?php if (count($this->_tpl_vars['arrPageLayout']['RightNavi']) > 0): ?>
					<td bgcolor="#ffffff" width="5"><img src="<?php echo @URL_DIR; ?>
img/_.gif" width="5" height="1" alt="" /></td>
					<td align="right" bgcolor="#ffffff">
				        <table border="0" cellspacing="0" cellpadding="0" summary=" ">
				        <?php $_from = $this->_tpl_vars['arrPageLayout']['RightNavi']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['RightNaviKey'] => $this->_tpl_vars['RightNaviItem']):
?>
					        <tr><td align="center">
					        <!-- ▼<?php echo $this->_tpl_vars['RightNaviItem']['bloc_name']; ?>
 ここから-->
				        	<?php if ($this->_tpl_vars['RightNaviItem']['php_path'] != ""): ?>
								<?php require_once(SMARTY_CORE_DIR . 'core.smarty_include_php.php');
smarty_core_smarty_include_php(array('smarty_file' => $this->_tpl_vars['RightNaviItem']['php_path'], 'smarty_assign' => '', 'smarty_once' => false, 'smarty_include_vars' => array()), $this); ?>

							<?php else: ?>
								<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['RightNaviItem']['tpl_path'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
							<?php endif; ?>
					        <!-- ▲<?php echo $this->_tpl_vars['RightNaviItem']['bloc_name']; ?>
 ここまで-->
					        </td></tr>
						<?php endforeach; endif; unset($_from); ?>
						</table>
					</td>
				<?php endif; ?>
				<!--▲右ナビ-->
			</tr>
		</table>
		<td bgcolor="#ffffff"><img src="./img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#cccccc"><img src="./img/_.gif" width="1" height="10" alt="" /></td>
		</td>
	</tr>
</table>

</div>
<!--▲MAIN-->

<!--▼FOTTER-->
<?php if ($this->_tpl_vars['arrPageLayout']['footer_chk'] != 2): ?> 
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => (@HTML_PATH)."user_data/include/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<!--▲FOTTER-->
</div>
<?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_mainpage'])) ? $this->_run_mod_handler('sfPrintEbisTag', true, $_tmp) : sfPrintEbisTag($_tmp)); ?>

<?php echo ((is_array($_tmp=$this->_tpl_vars['tpl_conv_page'])) ? $this->_run_mod_handler('sfPrintAffTag', true, $_tmp, $this->_tpl_vars['tpl_aff_option']) : sfPrintAffTag($_tmp, $this->_tpl_vars['tpl_aff_option'])); ?>

</body>