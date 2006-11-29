<script language="JavaScript">
<!--
arrTree = new Array();
<!--{section name=cnt loop=$arrTree}-->
	arrTree[<!--{$arrTree[cnt].count}-->] = new Array("<!--{$arrTree[cnt].count}-->", "<!--{$arrTree[cnt].type}-->", "<!--{$arrTree[cnt].path}-->", <!--{$arrTree[cnt].rank}-->, <!--{if $arrTree[cnt].open}-->true<!--{else}-->false<!--{/if}-->);
<!--{/section}-->

//-->
</script>

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

