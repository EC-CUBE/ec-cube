<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->">
<meta http-equiv="content-script-type" content="text/javascript">
<meta http-equiv="content-style-type" content="text/css">
<script type="text/javascript" src="/html/test/uehara/js/tree.js"></script>
<title>Untitled</title>
<script type="text/javascript">
</script>
</head>
<body onload="fnTreeDrow('tree')">
<div id="tree"></div>

<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="text" id="test1" name="test1" value="" onclick="test()" size="300">
	<select name=select_file size="5">
		<!--{html_options options=$arrFileList}-->
	</select>
	<input type="button" onclick="fnModeSubmit('view','','');" value="ɽ��">
	<input type="button" onclick="fnModeSubmit('download','','');" value="���������">
	<input type="button" onclick="fnModeSubmit('delete','','');" value="���">
</form>

arrFileList



</body>
</html>