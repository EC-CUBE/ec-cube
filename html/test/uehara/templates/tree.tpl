<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->">
<meta http-equiv="content-script-type" content="text/javascript">
<meta http-equiv="content-style-type" content="text/css">
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->user_data/css/contents.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->css/common.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$tpl_css}-->" type="text/css" media="all" />
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
<body onload="fnTreeView('tree', arrTree, '<!--{$tpl_now_file}-->');<!--{$tpl_onload}-->">

■エラー
<!--{foreach key=key item=item from=$arrErr}-->
	<!--{$key}-->：<!--{$item}--><br/>
<!--{/foreach}--><br/><br/>


<table>
	<tr>
		<td valign="top">
		■ツリー
		<div id="tree"></div>
		</td>
		<td valign="top">
		<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->"  enctype="multipart/form-data">
		<input type="hidden" name="mode" value="">
		<input type="hidden" name="now_file" value="<!--{$tpl_now_file}-->">
		<input type="hidden" name="tree_select_file" value="">
		<input type="hidden" name="tree_status" value="">
		<input type="hidden" name="select_file" value="">		
		■ファイル
		<div id="file_view">
			<table>
				<!--{section name=cnt loop=$arrFileList}-->
				<!--{assign var="id" value="select_file`$smarty.section.cnt.index`"}-->
				<tr id="<!--{$id}-->" onclick="fnSetFormVal('form1', 'select_file', '<!--{$arrFileList[cnt].file_path|escape}-->');fnSelectFile('<!--{$id}-->', '#3333FF');" style="" onMouseOver="fnChangeBgColor('<!--{$id}-->', '#3333FF');" onMouseOut="fnChangeBgColor('<!--{$id}-->', '');">
					<td><!--{$arrFileList[cnt].file_name|escape}--></td>
					<td><!--{$arrFileList[cnt].file_size|escape}--></td>
					<td><!--{$arrFileList[cnt].file_time|escape}--></td>
				</tr>
				<!--{/section}-->
			</table>
		</div>
		<input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('view','',''); return false;" value="表示">
		<input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('download','',''); return false;" value="ダウンロード">
		<input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('delete','',''); return false;" value="削除">
		</td>
	</tr>
</table>
■フォルダ作成<br />

	<input type="file" name="upload_file"><input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('upload','',''); return false;" value="アップロード"><br/>
	<input type="text" name="create_file" value=""><input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('create','',''); return false;" value="作成">
</form>


</body>
</html>

