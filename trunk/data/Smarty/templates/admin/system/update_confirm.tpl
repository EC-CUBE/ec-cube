<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
FTP����³���������ޤ�����<br/>
�ʲ��Υե�����ˡ����󥹥ȡ���ե��������Ū�˳�Ǽ���ޤ���</br>
<!--{$local_save_dir}--><br/>
�ƥǥ��쥯�ȥ�˽񤭹��߸��¤�Ϳ���Ƥ���������</br>
�ޤ������ξ������դ��ơ����󥹥ȡ��뤷�Ƥ�������</br>

<!--{section name=cnt loop=$arrFile}-->
	<!--{if $arrFile[cnt].main_file != ""}-->
		��<!--{$arrFile[cnt].main_file|escape}-->��񤭹��ߤޤ���</br>�ƥǥ��쥯�ȥ�˽񤭹��߸��¤�Ϳ���Ƥ���������</br>
	<!--{/if}-->
	<!--{if $arrFile[cnt].sql_file != ""}-->
		���ơ��֥�<!--{$arrFile[cnt].sql_file}-->��ƹ��ۤ��ޤ��Τǡ�����դ���������</br>�ǡ����ϼ�ưŪ�˥Хå����åפ���ޤ�</br>
	<!--{/if}-->
<!--{/section}-->

<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="install">
<input type="hidden" name="update_id" value="<!--{$smarty.post.update_id}-->">
<input type="button" name="back" value="���" onclick="location.href='./update.php'";>��<input type="submit" value="���󥹥ȡ���">
</form1>