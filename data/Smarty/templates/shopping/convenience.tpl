<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--��CONTENTS-->
<table width="780" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="18" alt="" /></td>
		<td bgcolor="#ffffff" colspan="3"><img src="../img/_.gif" width="778" height="1" alt="" /></td>

		<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="18" alt="" /></td>		
	</tr>
	<tr>
		<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff"><img src="../img/_.gif" width="10" height="1" alt="" /></td>
		<td><img src="../img/shopping/flow06.gif" width="758" height="78" alt="���㤤ʪ��ή��" /></td>
		<td bgcolor="#ffffff"><img src="../img/_.gif" width="10" height="1" alt="" /></td>
		<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>		
	</tr>
</table>

<table width="780" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc" width="1"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff" width="9"><img src="../img/_.gif" width="39" height="1" alt="" /></td>
		<td bgcolor="#ffffff" align="left"> 
		<!--��MAIN CONTENTS-->
		<table cellspacing="0" cellpadding="0" summary=" " id="containerfull">
			<tr><td height="20"></td></tr>
			<tr valign="top">
				<!--��CONTENTS-->

				<td>
				<div id="maintitle"><img src="../img/shopping/conveni_title.jpg" width="700" height="40" alt="����ӥ˷��" /></div>
				<div class="fs12n" id="comment01">�������顢����ʧ�����륳��ӥˤ����򤯤������ޤ���<br />
				����塢���ֲ��Ρ֤���ʸ��λ�ڡ����ءץܥ���򥯥�å����Ƥ���������</div>
				<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
				<input type="hidden" name="mode" value="complete">
				<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
				<span class="red12st"><!--{$arrErr.convenience}--></span>
				<table cellspacing="1" cellpadding="8" summary=" " id="frame">
					<tr class="fs12n">
						<td id="select">����</td>
						<td id="payment">����ӥˤμ���</td>
					</tr>
					<!--{foreach key=key item=item from=$arrCONVENIENCE}-->
					<tr>
						<td id="select_c"><input type="radio" name="convenience" value="<!--{$key}-->" style="<!--{$arrErr.convenience|sfGetErrorColor}-->"></td>
						<td class="fs12n" id="payment_c"><!--{$item|escape}--></td>
					</tr>
					<!--{/foreach}-->
				</table>
				<div class="red12" id="comment02">���֤���ʸ��λ�ڡ����ءפ򥯥�å��塢��λ�ڡ�����ɽ�������ޤǤ��Ԥ�����������</div>
				<div id="button">
				<!--�����ס���Ͽ��-->
				<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onmouseover="chgImg('/img/button/back03_on.gif','back03')" onmouseout="chgImg('/img/button/back03.gif','back03')" onclick="fnModeSubmit('return', '', ''); return false;" /><img src="/img/button/back03.gif" width="110" height="30" alt="���" border="0" name="back03" id="back03" ></a><img src="../img/_.gif" width="20" height="" alt="" /><input type="image" onmouseover="chgImgImageSubmit('../img/shopping/complete_on.gif',this)" onmouseout="chgImgImageSubmit('../img/shopping/complete.gif',this)" src="../img/shopping/complete.gif" width="170" height="30" alt="����ʸ��λ�ڡ�����" border="0" name="complete" id="complete" />
				</div>
				</form>
				
				<table cellspacing="0" cellpadding="0" summary=" " id="verisign">
					<tr>
						<td><script src=https://seal.verisign.com/getseal?host_name=secure.tokado.jp&size=S&use_flash=YES&use_transparent=NO&lang=ja></script></td>
						<td><img src="../img/_.gif" width="10" height="1" alt="" /></td>
						<td class="fs10">���󥿡��ͥåȥ���åԥ󥰤Ǥϡ��̿��ΰ���������ݤ��륻�����ƥ��⡼�ɤ����ꤷ�Ƥ��ޤ����ְŹ沽(SSL)�פ����򤹤�ȡ�����������ǡ������Ź沽���졢ϳ�̤δ������㤯�ʤ�ޤ����ޤ������ܥ٥ꥵ����Ҥˤ�ä��̿������Ф�ǧ�ڤ���뤿�ᡢ�ʤꤹ�ޤ��ʤɤˤ��ID���ѥ���ɤ����Ѥβ�ǽ�����㸺�Ǥ��ޤ���</td>
					</tr>

				</table>
				</td>
				<!--��ONTENTS-->	
			</tr>
		</table>
		<!--��MAIN CONTENTS-->
		</td>
		<td bgcolor="#ffffff" width="10"><img src="../img/_.gif" width="39" height="1" alt="" /></td>
		<td bgcolor="#cccccc" width="1"><img src="../img/_.gif" width="1" height="10" alt="" />
<!-- EBiS start -->
<script type="text/javascript">
if ( location.protocol == 'http:' ){ 
	strServerName = 'http://daikoku.ebis.ne.jp'; 
} else { 
	strServerName = 'https://secure2.ebis.ne.jp/ver3';
}
cid = 'tqYg3k6U'; pid = 'shopping_card'; m1id=''; a1id=''; o1id=''; o2id=''; o3id=''; o4id=''; o5id='';
document.write("<scr" + "ipt type=\"text\/javascript\" src=\"" + strServerName + "\/ebis_tag.php?cid=" + cid + "&pid=" + pid + "&m1id=" + m1id + "&a1id=" + a1id + "&o1id=" + o1id + "&o2id=" + o2id + "&o3id=" + o3id + "&o4id=" + o4id + "&o5id=" + o5id + "\"><\/scr" + "ipt>");
</script>
<!-- EBiS end -->
		</td>

	</tr>
</table>
<!--��CONTENTS-->