<?php /* Smarty version 2.6.13, created on 2007-01-09 23:49:37
         compiled from site_frame.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'site_frame.tpl', 19, false),)), $this); ?>
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
<meta name="author" content="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrPageLayout']['author'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<meta name="description" content="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrPageLayout']['description'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<meta name="keywords" content="<?php echo ((is_array($_tmp=$this->_tpl_vars['arrPageLayout']['keyword'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">

<script type="text/javascript">
<!--
	<?php echo $this->_tpl_vars['tpl_javascript']; ?>

//-->
</script>
</head>

<!-- ¢§ £Â£Ï£Ä£ÙÉô ¥¹¥¿¡¼¥È -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => './site_main.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<!-- ¢¥ £Â£Ï£Ä£ÙÉô ¥¨¥ó¥É -->

</html>