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

■ツリー
<div id="tree"></div><br/>

■ファイル
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->"  enctype="multipart/form-data">
	<input type="text" id="test1" name="test1" value="" onclick="test('test1')" size="300">
	<input type="hidden" name="mode" value="">
	<input type="hidden" name="now_file" value="<!--{$tpl_now_file}-->">
	<input type="hidden" name="tree_select_file" value="">
	<input type="hidden" name="tree_status" value="">
	<select name=select_file size="5">
	<!--{section name=cnt loop=$arrFileList}-->
	<option value="<!--{$arrFileList[cnt].file_path|escape}-->" <!--{if $arrFileList[cnt].file_path eq $arrParam.select_file}-->selected<!--{/if}-->><!--{$arrFileList[cnt].file_name|escape}-->　<!--{$arrFileList[cnt].file_size|escape}-->　<!--{$arrFileList[cnt].file_time|escape}--></option>
	<!--{/section}-->
	</select><br/>
	<input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('view','',''); return false;" value="表示">
	<input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('download','',''); return false;" value="ダウンロード">
	<input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('delete','',''); return false;" value="削除">
	<br/>

■フォルダ作成

	<input type="file" name="upload_file"><input type="button" onclick="setTreeStatus('tree_status');fnFormModeSubmit('form1', 'upload','',''); return false;" value="アップロード"><br/>
	<input type="text" name="create_file" value=""><input type="button" onclick="setTreeStatus('tree_status');fnFormModeSubmit('form1', 'create','',''); return false;" value="作成">
</form>




<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="">
<input type="hidden" name="news_id" value="<!--{$news_id|escape}-->">
<input type="hidden" name="term" value="">

	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/navi_bg.gif" height="402">
			<!--▼SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--▲SUB NAVI-->
		</td>
		<td class="mainbg">
			<!--▼登録テーブルここから-->
			<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--メインエリア-->
				<tr>
					<td align="center">
						<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">
						
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
		
								</td>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr><td height="30"></td></tr>
						</table>
					</td>
				</tr>
				<!--メインエリア-->
			</table>
			<!--▲登録テーブルここまで-->
		</td>
	</tr>
</table>



</body>
</html>

