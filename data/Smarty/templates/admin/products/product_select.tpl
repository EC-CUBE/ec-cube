<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--��-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/admin.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();

function func_submit( id ){
	var fm = window.opener.document.form1;
	fm.recommend_id<!--{$smarty.get.no}-->.value = id;
	fm.mode.value = 'recommend_select';
	fm.anchor_key.value = 'recommend_no<!--{$smarty.get.no}-->';
	fm.submit();
	window.close();
	return false;
}
//-->
</script>
<title>EC�����ȴ����ԥڡ���</title>
</head>


<body bgcolor="#ffffff" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_ADMIN_CSS}-->common.css" type="text/css" />
</noscript>

<!--��CONTENTS-->
<div align="center">
��
<!--�������ե�����-->
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
<input name="mode" type="hidden" value="search">
<input name="anchor_key" type="hidden" value="">
<input name="search_pageno" type="hidden" value="">
<table bgcolor="#cccccc" width="420" border="0" cellspacing="1" cellpadding="5" summary=" ">
	<tr class="fs12n">
		<td bgcolor="#f0f0f0" width="100">���ƥ���</td>
		<td bgcolor="#ffffff" width="287"><select name="search_category_id">
		<option value="" selected="selected">���򤷤Ƥ�������</option>
		<!--{html_options options=$arrCatList selected=$arrForm.search_category_id}-->
		</select>
		</td>
	</tr>
	<tr class="fs12n">
		<td bgcolor="#f0f0f0">����̾</td>
		<td bgcolor="#ffffff"><input type="text" name="search_name" value="<!--{$arrForm.search_name}-->" size="35" class="box35" /></td>
	</tr>
</table>
<br />
<input type="submit" name="subm" value="�����򳫻�" />
<br />
<br />

	<!--���������ɽ��-->
	<!--{if $tpl_linemax}-->
	<table width="420" border="0" cellspacing="0" cellpadding="0" summary=" " bgcolor="#FFFFFF">
		<tr class="fs12">
			<td align="left"><!--{$tpl_linemax}-->�郎�������ޤ�����	</td>
		</tr>
		<tr class="fs12">
			<td align="center">
			<!--���ڡ����ʥ�-->
			<!--{$tpl_strnavi}-->
			<!--���ڡ����ʥ�-->
			</td>
		</tr>
		<tr><td height="10"></td></tr>
	</table>
		
	<!--��������ɽ����ʬ-->
	<table width="420" border="0" cellspacing="1" cellpadding="5" bgcolor="#cccccc">
		<tr bgcolor="#f0f0f0" align="center" class="fs12">
			<td>���ʲ���</td>
			<td>�����ֹ�</td>
			<td>����̾</td>
			<td>����</td>
		</tr>
		<!--{section name=cnt loop=$arrProducts}-->
		<!--������<!--{$smarty.section.cnt.iteration}-->-->
		<tr bgcolor="#FFFFFF" class="fs12n">
			<td width="90" align="center">
			<!--{if $arrProducts[cnt].main_list_image != ""}-->
				<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_DIR`/`$arrProducts[cnt].main_list_image`"}-->
			<!--{else}-->
				<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_DIR`"}-->
			<!--{/if}-->
			<img src="<!--{$smarty.const.SITE_URL}-->resize_image.php?image=<!--{$image_path}-->&width=65&height=65" alt="<!--{$arrRecommend[$recommend_no].name|escape}-->">
			</td>	
			<td><!--{$arrProducts[cnt].product_code|escape|default:"-"}--></td>
			<td><!--{$arrProducts[cnt].name|escape}--></td>
			<td align="center"><a href="" onClick="return func_submit(<!--{$arrProducts[cnt].product_id}-->)">����</a></td>
		</tr>
		<!--������<!--{$smarty.section.cnt.iteration}-->-->
		<!--{sectionelse}-->
		<tr bgcolor="#FFFFFF" class="fs10n">
			<td colspan="4">���ʤ���Ͽ����Ƥ��ޤ���</td>
		</tr>	
		<!--{/section}-->
	</table>
	<!--{/if}-->
	<!--���������ɽ��-->

</form>

</div>
<!--��CONTENTS-->
</body>
</html>