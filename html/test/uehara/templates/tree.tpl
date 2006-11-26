<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->">
<meta http-equiv="content-script-type" content="text/javascript">
<meta http-equiv="content-style-type" content="text/css">
<link rel="stylesheet" href="/html/test/uehara/css/tree.css" type="text/css" media="all" />
<script type="text/javascript" src="/html/js/site.js"></script>
<script type="text/javascript" src="/html/js/win_op.js"></script>
<script type="text/javascript" src="/html/test/uehara/js/tree.js"></script>
<title>Untitled</title>
<script language="JavaScript">
<!--
<!--{$tpl_javascript}-->

arrTree = new Array();
<!--{section name=cnt loop=$arrTree}-->
	arrTree[<!--{$arrTree[cnt].count}-->] = new Array("<!--{$arrTree[cnt].count}-->", "<!--{$arrTree[cnt].type}-->", "<!--{$arrTree[cnt].path}-->", <!--{$arrTree[cnt].rank}-->, <!--{if $arrTree[cnt].open}-->true<!--{else}-->false<!--{/if}-->);
<!--{/section}-->

//-->
</script>
</head>
<body onload="fnTreeView('tree', arrTree);<!--{$tpl_onload}-->">

■エラー
<!--{foreach key=key item=item from=$arrErr}-->
	<!--{$key}-->：<!--{$item}--><br/>
<!--{/foreach}--><br/><br/>


<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->"  enctype="multipart/form-data">
<input type="text" id="test1" name="test1" value="" onclick="test('test1')" size="300">
<input type="hidden" name="mode" value="">
<input type="hidden" name="now_file" value="<!--{$tpl_now_file}-->">
<input type="hidden" name="tree_select_file" value="">
<input type="hidden" name="tree_status" value="">
<table>
	<tr>
		<td valign="top">
		■ツリー
		<div id="tree"></div>
		</td>
		<td valign="top">
		■ファイル
		<div>
			<select name=select_file size="5" id="file_view">
			<!--{section name=cnt loop=$arrFileList}-->
			<option value="<!--{$arrFileList[cnt].file_path|escape}-->" <!--{if $arrFileList[cnt].file_path eq $arrParam.select_file}-->selected<!--{/if}-->><!--{$arrFileList[cnt].file_name|escape}-->　<!--{$arrFileList[cnt].file_size|escape}-->　<!--{$arrFileList[cnt].file_time|escape}--></option>
			<!--{/section}-->
			</select><br/>
			<input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('view','',''); return false;" value="表示">
			<input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('download','',''); return false;" value="ダウンロード">
			<input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('delete','',''); return false;" value="削除">
		</div>
		</td>
	</tr>
</table>
■フォルダ作成

	<input type="file" name="upload_file"><input type="button" onclick="setTreeStatus('tree_status');fnFormModeSubmit('form1', 'upload','',''); return false;" value="アップロード"><br/>
	<input type="text" name="create_file" value=""><input type="button" onclick="setTreeStatus('tree_status');fnFormModeSubmit('form1', 'create','',''); return false;" value="作成">
</form>


</body>
</html>

