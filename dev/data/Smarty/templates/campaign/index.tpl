<!--{*ヘッダー読込*}-->
<!--{include file=`$tpl_dir_name`/header.tpl}-->

<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript">
<!--
<!--{$tpl_javascript}-->

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
	if(<!--{$tpl_init}-->) {
		var ids = fnGetIds();
		location.href = './index.php?init=1&ids=' + ids;
	} else {
		<!--{$tpl_onload}-->
	}
}

window.onload = init;

//-->
</script>
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="cart">
<input type="hidden" name="product_id" value="">
<input type="hidden" name="cp" value="true">

<!--{*コンテンツ読込*}-->
<!--{include file=`$tpl_dir_name`/contents.tpl}-->

</form>

<!--{*フッター読込*}-->
<!--{include file=`$tpl_dir_name`/footer.tpl}-->