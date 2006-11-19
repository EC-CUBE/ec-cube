<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->">
<meta http-equiv="content-script-type" content="text/javascript">
<meta http-equiv="content-style-type" content="text/css">
<script type="text/javascript" src="/html/js/site.js"></script>
<script type="text/javascript" src="/html/test/uehara/js/tree.js"></script>
<title>Untitled</title>
<script type="text/javascript">
</script>
</head>
<body onload="fnTreeDrow('tree')">

■tree
<div id="tree"></div>

■ファイル
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="text" id="test1" name="test1" value="" onclick="test()" size="300">
	<input type="hidden" name="mode" value="">
	<select name=select_file size="5">
	<!--{section name=cnt loop=$arrFileList}-->
	<option value="<!--{$arrFileList[cnt].file_path}-->"><!--{$arrFileList[cnt].file_name}--><!--{$arrFileList[cnt].file_size}--><!--{$arrFileList[cnt].file_time}--></option>
	<!--{/section}-->
	</select><br/>
	<input type="button" onclick="fnModeSubmit('view','','');" value="表示">
	<input type="button" onclick="fnModeSubmit('download','','');" value="ダウンロード">
	<input type="button" onclick="fnModeSubmit('delete','','');" value="削除">
</form>

</body>
</html>