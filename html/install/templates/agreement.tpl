<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<script type="text/javascript">
<!--
// �饸���ܥ���ˤ��ɽ������ɽ��
function fnChangeVisible(check_id, mod_id){
	
    if (document.getElementById(check_id).checked){
		document.getElementById(mod_id).onclick = false;
		document.getElementById(mod_id).src = '../img/install/next.jpg';		
    } else {
		document.getElementById(mod_id).disabled = true;		
		document.getElementById(mod_id).src = '../img/install/next_off.jpg';
    }
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
<tr><td align="left" class="fs12st">�����ѵ���������Ʊ��</td></tr>
<tr><td align="left" class="fs12">
	�ʲ��λ��ѵ����������ɤߤ���������<br/>
	���󥹥ȡ����³�Ԥ���ˤϤ��η�����Ʊ�դ���ɬ�פ�����ޤ���
</td></tr>
<tr><td height="10"></td></tr>
<tr>
	<td bgcolor="#cccccc" class="fs12">
	<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
		<tr>
			<td bgcolor="#ffffff" class="fs12">
			<div id="agreement">
				===���եȥ��������ѵ�����ˤ�Ʊ�ղ�����===<br/>
				<br/>
				������ҥ�å�����ʰʲ������ҡפȤ����ˤǤϡ������ͤ��ܥ��եȥ����������Ѥˤʤ뤿��ˤϡ������֥��եȥ��������ѷ����פ����Ƥ�������ĺ�����Ȥ����ˤʤä�<br/>
				����ޤ����ܥ��եȥ������򥤥󥹥ȡ���ޤ��ϥ��ԡ��������Ѥˤʤä������ǲ����֥��եȥ��������ѵ�����פˤ�Ʊ�դ�����������ΤȤߤʤ��ޤ���<br/><br/>
				--------------------- ���եȥ��������ѵ����� ---------------------<br/><br/>
				1.�饤����<br/><br/>
				EC-CUBE�Ǥ����ʤλ��Ѥˤ����äơ�̵����GPL�饤���󥹤�ͭ���ξ��ѥ饤���󥹤Τɤ��餫�����򤹤뤳�Ȥ��Ǥ���֥ǥ奢��饤���������פ���Ѥ��Ƥ���ޤ����ƥ饤���󥹤μ����ħ�ϰʲ����̤�Ǥ���<br/><br/>
				1-1.GPL�饤����<br/><br/>
				̵����EC-CUBE����Ѥ��뤳�Ȥ��Ǥ���ʣ�������ѡ����ۤ�Ԥ����Ȥ��Ǥ��뤬��EC-CUBE����Ѥ������ץꥱ�����������ۤ�����ˤϡ����Υ��ץꥱ�������Υ����������ɤ�����������Ѳ�ǽ�ʾ��֤ˤ��ʤ��ƤϤʤ�ʤ���<br/><br/>
				�� ���ѡʥ������ޥ����ˤ���ݤϡ��ץ����ե������PHP�ե��������ˤΥإå�����ʬ�˵��ܤ��Ƥ���ޤ����ɽ���ʳ��βս�����Ʋ��Ѥ��������ޤ���<br/><br/>
				�� GPL�饤���󥹡�GNU ���̸������ѵ��������)�������ʾ��ˤĤ��Ƥϡ�http://www.fsf.org/licenses/ �����ܸ���http://www.opensource.jp/gpl/gpl.ja.html�ˤ򻲾Ȥ��Ʋ�������<br/><br/>
				1-2.���ѥ饤����<br/><br/>
				EC-CUBE���ѥ饤���󥹤ϡ�GPL�饤���󥹤˽�򤷤����ʤ��������Υ饤���󥹤Ǥ���<br/>
				EC-CUBE���ѥ饤���󥹤�������������ޤ��ȡ����ѥ饤���󥹤��ϰϤǡ������ȤΥ��ץꥱ�������򥪡��ץ󥽡����ˤ���ɬ�פϤ���ޤ���<br/><br/>
				�� GPL�饤���󥹤˽�򤷤ʤ����ƤΤ����Ѥˤ����ơ����ѥ饤���󥹤�ɬ�פȤʤ�ޤ���<br/><br/>
				�� ���ѥ饤���󥹤ξܺ٤˴ؤ��Ƥϡ�http://www.ec-cube.net/license/business.php�򻲾Ȥ��Ʋ�������<br/><br/>
				2.����<br/><br/>
				2-1.���ѼԤϡ��ܥ��եȥ������λ��Ѥ˴�Ť���ȯ���������ڤ�ľ�ܡ����ܤ�»���ʥǡ����Ǽ��������С������󡢶�̳���ڡ��軰�Ԥ���Υ��졼�����ˤ���Ӵ��Ϥ��٤����ѼԤΤߤ��餦���Ȥ򤳤��˳�ǧ����Ʊ�դ����ΤȤ��ޤ���<br/>
				2-2.�����ʤ���Ǥ��äƤ⡢��ˡ�԰١����󤽤�¾�����ʤ�ˡŪ����ˤ����Ǥ⡢�ܥ��եȥ������ζ���ԡ�������ȼԡ�����ӳƾ��󥳥�ƥ�Ĥ��󶡲�Ҥϡ������ͤ���¾���軰�Ԥ��Ф����ĶȲ��ͤ��Ӽ�����̳����ߡ�����ԥ塼���θξ�ˤ��»��������¾�����뾦��Ū»����»������ޤ���ڤ�ľ��Ū������Ū���ü�Ū���տ�Ū�ޤ��Ϸ��Ū»����»���ˤĤ�����Ǥ���餤�ޤ��󡣤���ˡ����Ҥϡ��軰�ԤΤ����ʤ륯�졼����Ф��Ƥ���Ǥ���餤�ޤ���<br/><br/>
				3.�����Ⱦ���μ���<br/><br/>
				3-1 EC-CEBE�򥤥󥹥ȡ��뤹��ݤϥ�����URL��Ź��̾��EC-CUBE�С������PHP����DB�������ξ�������ҤˤƼ����������פ����Ȥ򤳤��˳�ǧ����Ʊ�դ����ΤȤ��롣<br/>		
			</div>
			</td>
		</tr>
	</table>
	</td>
</tr>
<tr><td height="10"></td></tr>
<!--{assign var=key value="agreement"}-->
<tr><td align="left" class="fs12"><input type="radio" id="agreement_yes" name="<!--{$key}-->" value=true onclick="fnChangeVisible('agreement_yes', 'next');" <!--{if $arrHidden[$key]}-->checked<!--{/if}-->><label for="agreement_yes">Ʊ�դ���</label>��<input type="radio" id="agreement_no" name="<!--{$key}-->" value=false onclick="fnChangeVisible('agreement_yes', 'next');" <!--{if !$arrHidden[$key]|escape}-->checked<!--{/if}-->><label for="agreement_no">Ʊ�դ��ʤ�</label></td></tr>
</table>

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
	<tr><td height="20"></td></tr>
	<tr>
		<td align="center">
		<a href="#" onmouseover="chgImg('../img/install/back_on.jpg','back')" onmouseout="chgImg('../img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_welcome';document.form1.submit();" /><img  width="105" src="../img/install/back.jpg"  height="24" alt="�������" border="0" name="back"></a>
		<a href="#" onclick="document.form1.submit();"><input type='image' onMouseover="chgImgImageSubmit('../img/install/next_on.jpg',this)" onMouseout="chgImgImageSubmit('../img/install/next.jpg',this)" src="../img/install/next.jpg" width="105" height="24" alt="���ؿʤ�" border="0" name="next" id="next"></a>
		</td>
	</tr>
	<tr><td height="30"></td></tr>
</from>
</table>								
