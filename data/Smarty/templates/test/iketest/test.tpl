<table>
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="update_id" value="">
<!--{section name=cnt loop=$arrRet}-->
<tr>
<td><!--{$arrRet[cnt].func_name}--></td>
<td><!--{$arrRet[cnt].func_explain}--></td>
<td><!--{$arrRet[cnt].version}--></td>
<td><!--{$arrRet[cnt].update_date}--></td>
<td><!--{$arrRet[cnt].file_size}--></td>
<!--{if !$smarty.section.cnt.first}-->
<td><!--{if $arrRet[cnt].no_install_module}--><a href="#" onclick="fnModeSubmit('confirm', 'update_id', '<!--{$arrRet[cnt].update_id}-->');"><!--{/if}-->インストール</a></td>
<!--{/if}-->
</tr>
<!--{/section}-->
</table>