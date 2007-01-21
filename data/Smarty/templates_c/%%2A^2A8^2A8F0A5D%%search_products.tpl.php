<?php /* Smarty version 2.6.13, created on 2007-01-10 13:54:21
         compiled from /home/web/beta.ec-cube.net/html/user_data/include/bloc/search_products.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/search_products.tpl', 27, false),array('modifier', 'escape', '/home/web/beta.ec-cube.net/html/user_data/include/bloc/search_products.tpl', 37, false),)), $this); ?>
<!--▼検索条件ここから-->
<table width="166" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/side/title_search.jpg" width="166" height="35" alt="検索条件"></td>
	</tr>
	<tr>
		<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="10" alt=""></td>
		<td align="center" bgcolor="#ffffff">
		<!--検索フォーム-->
		<table width="146" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<form name="search_form" id="search_form" method="get" action="<?php echo @URL_DIR; ?>
products/list.php">
		<input type="hidden" name="mode" value="search">
			<tr><td height="10"></td></tr>
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/side/search_cat.gif" width="104" height="10" alt="商品カテゴリから選ぶ"></td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td>
					<select name="category_id">
					<option label="すべての商品" value="">全ての商品</option>
					<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['arrCatList'],'selected' => $this->_tpl_vars['category_id']), $this);?>

					</select>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/side/search_name.gif" width="66" height="10" alt="商品名を入力"></td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td><input type="text" name="name" size="18" class="box18" maxlength="50" value="<?php echo ((is_array($_tmp=$_GET['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"/></td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td align="center">
					<input type="image" onMouseover="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/side/button_search_on.gif',this)" onMouseout="chgImgImageSubmit('<?php echo @URL_DIR; ?>
img/side/button_search.gif',this)" src="<?php echo @URL_DIR; ?>
img/side/button_search.gif" width="51" height="22" alt="検索" border="0" name="search">
				</td>
			</tr>
		</form>
		</table>
		<!--検索フォーム-->
		</td>
		<td bgcolor="#cccccc"><img src="<?php echo @URL_DIR; ?>
img/common/_.gif" width="1" height="10" alt=""></td>
	</tr>
	<tr>
		<td colspan="3"><img src="<?php echo @URL_DIR; ?>
img/side/flame_bottom03.gif" width="166" height="15" alt=""></td>
	</tr>
	<tr><td height="10"></td></tr>
</table>
<!--▲検索条件ここまで-->