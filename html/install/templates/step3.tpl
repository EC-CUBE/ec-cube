<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<script type="text/javascript">
<!--
	// �⡼�ɤȥ�������ꤷ��SUBMIT��Ԥ���
	function fnModeSubmit(mode) {
		switch(mode) {
		case 'drop':
			if(!window.confirm('���ٺ�������ǡ����ϡ������᤻�ޤ���\n������Ƥ⵹�����Ǥ�����')){
				return;
			}
			break;
		default:
			break;
		}
		document.form1['mode'].value = mode;
		document.form1.submit();	
	}
//-->
</script>

<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->">
<input type="hidden" name="step" value="0">

<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->

<tr><td height="30"></td></tr>
<tr><td align="left" class="fs12st">���ǡ����١����ν����</td></tr>
<tr><td align="left" class="fs12"><!--{if $tpl_db_version != ""}-->��³����<!--{$tpl_db_version}--><!--{/if}--></td></tr>
<tr><td align="left" class="fs12">�ǡ����١����ν�����򳫻Ϥ��ޤ�</td></tr>
<tr><td align="left" class="fs12">�����Ǥ˥ơ��֥�������������Ƥ���������Ǥ���ޤ�</td></tr>
<!--{if $tpl_mode != 'complete'}-->
<tr><td align="left" class="fs12"><input type="checkbox" id="skip" name="db_skip" <!--{if $tpl_db_skip == "on"}-->checked<!--{/if}-->> <label for="skip">�ǡ����١����ν����������Ԥ�ʤ�</label></td></tr>
<!--{/if}-->

<!--{if count($arrErr) > 0 || $tpl_message != ""}-->
<tr>
	<td bgcolor="#cccccc" class="fs12">
	<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
		<tr>
			<td bgcolor="#ffffff" class="fs12" height="50">
			<!--{$tpl_message}--><br>
			<span class="red"><!--{$arrErr.all}--></span>
			<!--{if $arrErr.all != ""}-->
			<input type="button" onclick="fnModeSubmit('drop');" value="��¸�ǡ����򤹤٤ƺ������">
			<!--{/if}-->
			</td>
		</tr>
	</table>
	</td>
</tr>
<!--{/if}-->
</table>

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
	<tr><td height="20"></td></tr>
	<tr>
		<td align="center">
		<a href="#" onmouseover="chgImg('../img/install/back_on.jpg','back')" onmouseout="chgImg('../img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_step2';document.form1.submit();return false;" /><img  width="105" src="../img/install/back.jpg"  height="24" alt="�������" border="0" name="back"></a>
		<input type="image" onMouseover="chgImgImageSubmit('../img/install/next_on.jpg',this)" onMouseout="chgImgImageSubmit('../img/install/next.jpg',this)" src="../img/install/next.jpg" width="105" height="24" alt="���ؿʤ�" border="0" name="next" onClick="document.body.style.cursor = 'wait';">
		</td>
	</tr>
	<tr><td height="30"></td></tr>
</from>
</table>								
