<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->

<!--��CONTENTS-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#ffffff" align="center" valign="top" height="400">
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<!--��SUB NAVI-->
				<td class="fs12n"><!--{include file=$tpl_subnavi}--></td>
				<!--��SUB NAVI-->
			</tr><tr><td height="25"></td></tr>
		</table>
		
		<!--��MAIN CONTENTS-->
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td class="fs14n"><strong>����ӥ塼��Ͽ</strong></td>
			</tr>
			<tr><td height="10"></td></tr>
		</table>
		
		<!--����Ͽ�ơ��֥뤳������-->
		<form name="form1" id="form1" method="post" action="">
		<table width="740" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">�����</td>
				<td bgcolor="#ffffff" width="607">
				<select name="year_from">
				<option value="" selected="selected">------</option>
				</select>ǯ
				<select name="month_from">
				<option value="" selected="selected">----</option>
				</select>��
				<select name="day_from">
				<option value="" selected="selected">----</option>
				</select>��</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">��Ƽ�̾</td>
				<td bgcolor="#ffffff" width="607"><input type="text" name="name" size="30" class="box30" /></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">����</td>
				<td bgcolor="#ffffff" width="607"><input type="checkbox" name="sex01" value="����" />������<input type="checkbox" name="sex02" value="����" />����</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">�᡼�륢�ɥ쥹</td>
				<td bgcolor="#ffffff" width="607"><input type="text" name="mail" size="30" class="box30" /></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">����̾</td>
				<td bgcolor="#ffffff" width="607"><input type="text" name="product_mname" size="30" class="box30" /></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">���������٥�</td>
				<td bgcolor="#ffffff" width="607"><select name="recomend">
				<option value="" selected="selected">���򤷤Ƥ�������</option>
				<option value="����������">����������</option>
				<option value="��������">��������</option>
				<option value="������">������</option>
				<option value="����">����</option>
				<option value="��">��</option>
				</select></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">�����ȥ�</td>
				<td bgcolor="#ffffff" width="607"><input type="text" name="title" size="30" class="box30" /></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">������</td>
				<td bgcolor="#ffffff" width="607"><textarea name="textfield01" cols="60" rows="8" class="area60"></textarea></td>
			</tr>
		</table>
		<!--����Ͽ�ơ��֥뤳���ޤ�-->
		
		<br />
		<input type="button" name="subm" value="�������Ƥ���Ͽ����" />
		</form>
		
		<!--��MAIN CONTENTS-->
		</td>
	</tr>
</table>
<!--��CONTENTS-->
