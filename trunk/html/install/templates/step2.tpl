<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<script type="text/javascript">
function lfnChangePort(db_type) {

	type = db_type.value;
	
	if (type == 'pgsql') {
		form1.db_port.value = '<!--{$arrDB_PORT.pgsql}-->';
	}
	
	if (type == 'mysql') {
		form1.db_port.value = '<!--{$arrDB_PORT.mysql}-->';
	}
}
</script>
<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->">
<input type="hidden" name="step" value="0">
<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->

<tr><td height="30"></td></tr>
<tr><td align="left" class="fs12st">���ǡ����١���������</td></tr>
<tr><td align="left" class="fs12">�����󥹥ȡ�������˿�����DB��������Ƥ���ɬ�פ�����ޤ���</td></tr>
<tr><td align="left" class="red12"><!--{$arrErr.all}--></td></tr>
<tr>
	<td bgcolor="#cccccc">
	<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">DB�μ���<span class="red">��</span></td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<!--{assign var=key value="db_type"}-->
			<span class="red"><!--{$arrErr[$key]}--></span>
			<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" onChange="lfnChangePort(this)">
			<!--{html_options options=$arrDB_TYPE selected=$arrForm[$key].value}-->
			</select>
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">DB������<span class="red">��</span></td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<!--{assign var=key value="db_server"}-->
			<span class="red"><!--{$arrErr[$key]}--></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">�ݡ���</td>
			<td bgcolor="#ffffff" width="332">
			<!--{assign var=key value="db_port"}-->
			<span class="red"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="6" class="box6" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">DB̾<span class="red">��</span></td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<!--{assign var=key value="db_name"}-->
			<span class="red"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">DB�桼��<span class="red">��</span></td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<!--{assign var=key value="db_user"}-->
			<span class="red"><!--{$arrErr[$key]}--></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">DB�ѥ����<span class="red">��</span></td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<!--{assign var=key value="db_password"}-->
			<span class="red"><!--{$arrErr[$key]}--></span>
			<input type="password" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
			</td>
		</tr>		
	</table>
	</td>
</tr>
</table>

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
	<tr><td height="20"></td></tr>
	<tr>
		<td align="center">
		<a href="#" onmouseover="chgImg('../img/install/back_on.jpg','back')" onmouseout="chgImg('../img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_step1';document.form1.submit();return false;" /><img  width="105" src="../img/install/back.jpg"  height="24" alt="�������" border="0" name="back"></a>
		<input type="image" onMouseover="chgImgImageSubmit('../img/install/next_on.jpg',this)" onMouseout="chgImgImageSubmit('../img/install/next.jpg',this)" src="../img/install/next.jpg" width="105" height="24" alt="���ؿʤ�" border="0" name="next">
		</td>
	</tr>
	<tr><td height="30"></td></tr>
</from>
</table>								
