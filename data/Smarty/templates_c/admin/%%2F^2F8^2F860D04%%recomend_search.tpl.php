<?php /* Smarty version 2.6.13, created on 2007-01-19 12:53:29
         compiled from contents/recomend_search.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'contents/recomend_search.tpl', 50, false),array('modifier', 'default', 'contents/recomend_search.tpl', 107, false),array('function', 'html_options', 'contents/recomend_search.tpl', 58, false),)), $this); ?>
<!--　-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=<?php echo @CHAR_CODE; ?>
" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
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
<script type="text/javascript" src="<?php echo @URL_DIR; ?>
js/css.js"></script>
<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();

function func_submit( id ){
	var fm = window.opener.document.form<?php echo $_GET['rank']; ?>
;
	fm.product_id.value = id;
	fm.mode.value = 'set_item';
	fm.rank.value = '<?php echo $_GET['rank']; ?>
';
	fm.submit();
	window.close();
	return false;
}
//-->
</script>
<title>ECサイト管理者ページ</title>
</head>


<body bgcolor="#ffffff" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<noscript>
<link rel="stylesheet" href="<?php echo @URL_ADMIN_CSS; ?>
common.css" type="text/css" />
</noscript>

<!--▼CONTENTS-->
<div align="center">
　
<!--▼検索フォーム-->
<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['REQUEST_URI'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input name="mode" type="hidden" value="search">
<input name="search_pageno" type="hidden" value="">
<table bgcolor="#cccccc" width="420" border="0" cellspacing="1" cellpadding="5" summary=" ">
	<tr class="fs12n">
		<td bgcolor="#f0f0f0" width="100">カテゴリ</td>
		<td bgcolor="#ffffff" width="287"><select name="search_category_id">
		<option value="" selected="selected">選択してください</option>
		<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrCatList'],'selected' => $this->_tpl_vars['arrForm']['search_category_id']), $this);?>

		</select>
		</td>
	</tr>
	<tr class="fs12n">
		<td bgcolor="#f0f0f0">商品名</td>
		<td bgcolor="#ffffff"><input type="text" name="search_name" value="<?php echo $this->_tpl_vars['arrForm']['search_name']; ?>
" size="35" class="box35" /></td>
	</tr>
</table>
<br />
<input type="submit" name="subm" value="検索を開始" />
<br />
<br />

	<!--▼検索結果表示-->
	<?php if ($this->_tpl_vars['tpl_linemax']): ?>
	<table width="420" border="0" cellspacing="0" cellpadding="0" summary=" " bgcolor="#FFFFFF">
		<tr class="fs12">
			<td align="left"><?php echo $this->_tpl_vars['tpl_linemax']; ?>
件が該当しました。	</td>
		</tr>
		<tr class="fs12">
			<td align="center">
			<!--▼ページナビ-->
			<?php echo $this->_tpl_vars['tpl_strnavi']; ?>

			<!--▲ページナビ-->
			</td>
		</tr>
		<tr><td height="10"></td></tr>
	</table>
		
	<!--▼検索後表示部分-->
	<table width="420" border="0" cellspacing="1" cellpadding="5" bgcolor="#cccccc">
		<tr bgcolor="#f0f0f0" align="center" class="fs12">
			<td>商品画像</td>
			<td>商品番号</td>
			<td>商品名</td>
			<td>決定</td>
		</tr>
		<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrProducts']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['cnt']['show'] = true;
$this->_sections['cnt']['max'] = $this->_sections['cnt']['loop'];
$this->_sections['cnt']['step'] = 1;
$this->_sections['cnt']['start'] = $this->_sections['cnt']['step'] > 0 ? 0 : $this->_sections['cnt']['loop']-1;
if ($this->_sections['cnt']['show']) {
    $this->_sections['cnt']['total'] = $this->_sections['cnt']['loop'];
    if ($this->_sections['cnt']['total'] == 0)
        $this->_sections['cnt']['show'] = false;
} else
    $this->_sections['cnt']['total'] = 0;
if ($this->_sections['cnt']['show']):

            for ($this->_sections['cnt']['index'] = $this->_sections['cnt']['start'], $this->_sections['cnt']['iteration'] = 1;
                 $this->_sections['cnt']['iteration'] <= $this->_sections['cnt']['total'];
                 $this->_sections['cnt']['index'] += $this->_sections['cnt']['step'], $this->_sections['cnt']['iteration']++):
$this->_sections['cnt']['rownum'] = $this->_sections['cnt']['iteration'];
$this->_sections['cnt']['index_prev'] = $this->_sections['cnt']['index'] - $this->_sections['cnt']['step'];
$this->_sections['cnt']['index_next'] = $this->_sections['cnt']['index'] + $this->_sections['cnt']['step'];
$this->_sections['cnt']['first']      = ($this->_sections['cnt']['iteration'] == 1);
$this->_sections['cnt']['last']       = ($this->_sections['cnt']['iteration'] == $this->_sections['cnt']['total']);
?>
		<!--▼商品<?php echo $this->_sections['cnt']['iteration']; ?>
-->
		<tr bgcolor="#FFFFFF" class="fs12n">
			<td width="90" align="center">
			<?php if ($this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['main_list_image'] != ""): ?>
				<?php $this->assign('image_path', (@IMAGE_SAVE_DIR)."/".($this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['main_list_image'])); ?>
			<?php else: ?>
				<?php $this->assign('image_path', (@NO_IMAGE_DIR)); ?>
			<?php endif; ?>
			<img src="<?php echo @SITE_URL; ?>
resize_image.php?image=<?php echo $this->_tpl_vars['image_path']; ?>
&width=65&height=65" alt="">
			</td>
			<td><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['product_code'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, "-") : smarty_modifier_default($_tmp, "-")); ?>
</td>
			<td><?php echo ((is_array($_tmp=$this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
			<td align="center"><a href="" onClick="return func_submit(<?php echo $this->_tpl_vars['arrProducts'][$this->_sections['cnt']['index']]['product_id']; ?>
)">決定</a></td>
		</tr>
		<!--▲商品<?php echo $this->_sections['cnt']['iteration']; ?>
-->
		<?php endfor; else: ?>
		<tr bgcolor="#FFFFFF" class="fs10n">
			<td colspan="4">商品が登録されていません</td>
		</tr>	
		<?php endif; ?>
	</table>
	<?php endif; ?>
	<!--▲検索結果表示-->

</form>

</div>
<!--▲CONTENTS-->
</body>
</html>