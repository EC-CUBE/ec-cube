<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<p>���åץǡ��Ȳ�ǽ�ʥե���������Ǥ���</p>

<table>
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="filename" value="">
<input type="hidden" name="mode" value="">
<tr>
<td>�ե�����̾</td>
<td>��������</td>
<td>�ե����륵����</td>
</tr>
<!--{section name=cnt loop=$arrFile}-->
<tr>
<td><a href="#" onclick="fnModeSubmit('download', 'filename', '<!--{$arrFile[cnt].filename}-->');"><!--{$arrFile[cnt].filename|escape}--></a></td>
<td><!--{$arrFile[cnt].date|escape}--></td>
<td><!--{$arrFile[cnt].filesize|escape}--></td>
</tr>
<!--{/section}-->
</form>
</table>
