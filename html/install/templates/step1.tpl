<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->">
<input type="hidden" name="step" value="0">

<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->

<tr><td height="30"></td></tr>
<tr><td align="left" class="fs12st">��EC�����Ȥ�����</td></tr>
<tr>
	<td bgcolor="#cccccc">
	<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">Ź̾<span class="red">��</span></td>
			<td bgcolor="#ffffff" width="332">
			<!--{assign var=key value="shop_name"}-->
			<span class="red"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
			<br><span class="fs10">�����ʤ���Ź̾�򤴵�������������</span>
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">�����ԡ��᡼�륢�ɥ쥹<span class="red">��</span></td>
			<td bgcolor="#ffffff" width="332">
			<!--{assign var=key value="admin_mail"}-->
			<span class="red"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
			<br><span class="fs10">������᡼��ʤɤΰ���ˤʤ�ޤ�����(��)example@ec-cube.net</span>
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150"><span class="fs12n">�����ԡ�������ID<span class="red">��</span></span><br/><span class="fs10">Ⱦ�ѱѿ�����15ʸ������</span></td>
			<td bgcolor="#ffffff" width="332">
			<!--{assign var=key value="login_id"}-->
			<span class="red"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
			<br><span class="fs10">�������Բ��̤˥����󤹤뤿���ID�Ǥ���</span>
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150"><span class="fs12n">�����ԡ��ѥ����<span class="red">��</span></span><br/><span class="fs10">Ⱦ�ѱѿ�����15ʸ������</span></td>
			<td bgcolor="#ffffff" width="332">
			<!--{assign var=key value="login_pass"}-->
			<span class="red"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
			<input type="password" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->;" size="40" class="box40" />
			<br><span class="fs10">�������Բ��̤˥����󤹤뤿��Υѥ���ɤǤ���</span>
			</td>
		</tr>
	</table>
	</td>
</tr>
<tr><td height="20"></td></tr>
<tr><td align="left" class="fs12st">��WEB�����Ф�����</td></tr>
<tr>
	<td bgcolor="#cccccc">
	<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">		
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">HTML�ѥ�<span class="red">��</span></td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<!--{assign var=key value="install_dir"}-->
			<span class="red"><!--{$arrErr[$key]}--></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">URL(�̾�)<span class="red">��</span></td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<!--{assign var=key value="normal_url"}-->
			<span class="red"><!--{$arrErr[$key]}--></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">URL(�����奢)<span class="red">��</span></td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<!--{assign var=key value="secure_url"}-->
			<span class="red"><!--{$arrErr[$key]}--></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">���̥ɥᥤ��</td>
			<td bgcolor="#ffffff" width="332">	
			<!--{assign var=key value="domain"}-->
			<span class="red"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
			<br><span class="fs10">���̾�URL�ȥ����奢URL�ǥ��֥ɥᥤ�󤬰ۤʤ���˻��ꤷ�ޤ���</span>
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
		<a href="#" onmouseover="chgImg('../img/install/back_on.jpg','back')" onmouseout="chgImg('../img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_step0';document.form1.submit();return false;" /><img  width="105" src="../img/install/back.jpg"  height="24" alt="�������" border="0" name="back"></a>
		<input type="image" onMouseover="chgImgImageSubmit('../img/install/next_on.jpg',this)" onMouseout="chgImgImageSubmit('../img/install/next.jpg',this)" src="../img/install/next.jpg" width="105" height="24" alt="���ؿʤ�" border="0" name="next">
		</td>
	</tr>
	<tr><td height="30"></td></tr>
</from>
</table>
