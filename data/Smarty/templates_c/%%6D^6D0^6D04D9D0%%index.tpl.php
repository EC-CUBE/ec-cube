<?php /* Smarty version 2.6.13, created on 2007-01-10 00:01:41
         compiled from /home/web/beta.ec-cube.net/html/../data/Smarty/templates/campaign/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/home/web/beta.ec-cube.net/html/../data/Smarty/templates/campaign/index.tpl', 72, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['tpl_dir_name'])."/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<script type="text/javascript" src="<?php echo @URL_DIR; ?>
js/site.js"></script>
<script type="text/javascript" src="<?php echo @URL_DIR; ?>
js/navi.js"></script>
<script type="text/javascript">
<!--
<?php echo $this->_tpl_vars['tpl_javascript']; ?>


// セレクトボックスに項目を割り当てる。
function lnSetSelect(name1, name2, id, val) {
	sele1 = document.form1[name1];
	sele2 = document.form1[name2];
	lists = eval('lists' + id);
	vals = eval('vals' + id);
	
	if(sele1 && sele2) {
		index = sele1.selectedIndex;
		
		// セレクトボックスのクリア
		count = sele2.options.length;
		for(i = count; i >= 0; i--) {
			sele2.options[i] = null;
		}
		
		// セレクトボックスに値を割り当てる
		len = lists[index].length;
		for(i = 0; i < len; i++) {
			sele2.options[i] = new Option(lists[index][i], vals[index][i]);
			if(val != "" && vals[index][i] == val) {
				sele2.options[i].selected = true;
			}
		}
	}
}

// 全商品IDを取得する
function fnGetIds() {
	var change_tag = document.getElementsByTagName("div");
	var ids = "";
	var count = 0;

	for (var i = 0; i < change_tag.length; i++) {
    	str = change_tag.item(i).id;
    	if (str.match('cart_tag_*')) {
    		var nama_id = change_tag.item(i).id;
    		arrIds =  nama_id.split("_");

    		if (count > 0) ids += '-';    		
			ids += arrIds[2];
			count ++;
		}
	}
	
	return ids;
}

// 読込後に実行する(on_load)
function init() {
	if(<?php echo $this->_tpl_vars['tpl_init']; ?>
) {
		var ids = fnGetIds();
		location.href = './index.php?init=1&ids=' + ids;
	} else {
		<?php echo $this->_tpl_vars['tpl_onload']; ?>

	}
}

window.onload = init;

//-->
</script>
<form name="form1" id="form1" method="post" action="<?php echo ((is_array($_tmp=$_SERVER['REQUEST_URI'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input type="hidden" name="mode" value="cart">
<input type="hidden" name="product_id" value="">
<input type="hidden" name="cp" value="true">

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['tpl_dir_name'])."/contents.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

</form>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['tpl_dir_name'])."/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>