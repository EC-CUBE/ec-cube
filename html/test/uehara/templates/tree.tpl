<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->">
<meta http-equiv="content-script-type" content="text/javascript">
<meta http-equiv="content-style-type" content="text/css">
<script type="text/javascript" src="/html/js/site.js"></script>
<script type="text/javascript" src="/html/js/win_op.js"></script>
<script type="text/javascript" src="/html/test/uehara/js/tree.js"></script>
<title>Untitled</title>
<script language="JavaScript">
<!--
<!--{$tpl_javascript}-->
arrTest[0] = new Array("0", "_parent", "", 0);
fnTreeView('tree', arrTest);
//-->
</script>
</head>
<body onload="<!--{$tpl_onload}-->">

<!--{foreach key=key item=item from=$arrErr}-->
	<!--{$key}-->：<!--{$item}--><br/>
<!--{/foreach}-->

■tree
<div id="tree"></div><br/>

■ファイル
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="text" id="test1" name="test1" value="" onclick="test()" size="300">
	<input type="hidden" name="mode" value="">
	<input type="hidden" name="now_file" value="<!--{$tpl_now_file}-->">
	<select name=select_file size="5">
	<!--{section name=cnt loop=$arrFileList}-->
	<option value="<!--{$arrFileList[cnt].file_path|escape}-->" <!--{if $arrFileList[cnt].file_path eq $arrParam.select_file}-->selected<!--{/if}-->><!--{$arrFileList[cnt].file_name|escape}-->　<!--{$arrFileList[cnt].file_size|escape}-->　<!--{$arrFileList[cnt].file_time|escape}--></option>
	<!--{/section}-->
	</select><br/>
	<input type="button" onclick="fnModeSubmit('view','',''); return false;" value="表示">
	<input type="button" onclick="fnModeSubmit('download','',''); return false;" value="ダウンロード">
	<input type="button" onclick="fnModeSubmit('delete','',''); return false;" value="削除">
</form><br/>

■フォルダ作成
<form name="form2" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" enctype="multipart/form-data">
	<input type="hidden" name="mode" value="">
	<input type="hidden" name="now_file" value="<!--{$tpl_now_file}-->">
	<input type="file" name="upload_file"><input type="button" onclick="fnFormModeSubmit('form2', 'upload','',''); return false;" value="アップロード"><br/>
	<input type="text" name="create_file" value=""><input type="button" onclick="fnFormModeSubmit('form2', 'create','',''); return false;" value="作成">
</form>

</body>
</html>