<!--{*
 * Copyright ��� 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--��CONTENTS-->
<table width="780" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc" width="1"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff" width="9"><img src="../img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#ffffff" align="left"> 
		<!--��MAIN CONTENTS-->
		<!--�ѥ󥯥�-->
		<div id="pan"><span class="fs12n"><a href="../index.php">�ȥåץڡ���</a> �� <span class="redst">���䤤��碌�����ϥڡ�����</span></span></div>
		<!--�ѥ󥯥�-->
		<table cellspacing="0" cellpadding="0" summary=" " id="container">
			<tr><td height="10"></td></tr>
			<tr valign="top">
				<!--��LEFT CONTENTS-->
				<td id="left">
				<!--���Хʡ�--><!--{include file=$tpl_banner}--><!--���Хʡ�-->
				
				<!--�����ʸ���-->
					<!--{include_php file=$tpl_search_products_php}-->
				<!--�����ʸ���-->
								
				<!--�����ʥ�-->
					<!--{include file=$tpl_leftnavi}-->
				<!--�����ʥ�-->
								
				</td>
				<!--��LEFT CONTENTS-->
				
				<!--��RIGHT CONTENTS-->
				<td id="right">
				<div id="maintitle"><img src="../img/right_contact/title.jpg" width="570" height="40" alt="���䤤��碌" /></div>
				<div id="comment" class="fs12">�������ܤˤ����Ϥ�����������<span class="asterisk">��</span>�װ�������ɬ�ܹ��ܤǤ���<br />
				���ϸ塢���ֲ��Ρֳ�ǧ�ڡ����ءץܥ���򥯥�å����Ƥ���������<br />��<br />
				<!--{$name|escape}-->��</span></div>
				
				<form action="<!--{$smarty.server.PHP_SELF}-->" method="post" name="form1">
				<input type="hidden" name="mode" value="confirm">
				<input type="hidden" name="name" value="<!--{$name|escape}-->">
				<input type="hidden" name="kana" value="<!--{$kana|escape}-->">
				<input type="hidden" name="customer_id" value="<!--{$customer_id}-->">
				
				<table cellspacing="1" cellpadding="10" summary=" " id="frame">
					<tr class="fs12n">
						<td id="left"><span class="asterisk">��</span>���䤤��碌�μ���</td>
						<td id="right">
						<!--{assign var=key value="question"}-->
						<span class="red"><!--{$arrErr[$key]}--></span>
							<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
								<option value="" selected>���򤷤Ƥ�������</option>
								<!--{html_options options=$arrContact selected=$arrForm[$key]}-->
							</select>
						</td>
					</tr>
					<tr>
						<td class="fs12" id="left"><span class="asterisk">��</span>���䤤��碌����<br />
						<span class="indent12">������1000ʸ�������</span></td>
						<td id="right" class="fs12n"><!--{assign var=key value="contents"}-->
							<span class="red"><!--{$arrErr[$key]}--></span>
							<textarea name="contents" cols="45" rows="8" wrap="physical" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{$arrForm[$key]|escape}--></textarea>
						</td>
					</tr>
				</table>
				
				<!--{assign var=key value="method"}-->
				<div id="comment02"><span class="red12">����ؤ�������ˡ�����Ӥ��������ޤ���</span>
				<!--{if $arrErr[$key]}--><br /><span class="red12"><!--{$arrErr[$key]}--></span><!--{/if}-->
				</div>			
				<table cellspacing="1" cellpadding="10" summary=" " id="frame">
					<tr>
						<td class="fs12n" id="left"><input type="radio" name="method" value="1" onClick="setInputArea(this.value)" <!--{if $arrForm.method eq "1"}-->checked<!--{/if}--> />�᡼��Ǥβ������˾</td>
						<td id="right">
						<table cellspacing="0" cellpadding="0" summary=" ">
			
							<tr>
								<td class="fs12n">��������᡼�륢�ɥ쥹<br><span class="red"><!--{$arrErr.email}--><!--{$arrErr.email02}--></span></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td><input type="text" name="email" value="<!--{$arrForm.email|escape}-->" style="<!--{$arrErr.email|sfGetErrorColor}-->" size="40" class="box40" /></td>
							</tr>
							<tr><td height="2"></td></tr>
							<tr>
								<td><input type="text" name="email02" value="<!--{$arrForm.email02|escape}-->" style="<!--{$arrErr.email02|sfGetErrorColor}-->" size="40" class="box40" /></td>
							</tr>
							<tr><td height="2"></td></tr>
							<tr>
								<td class="fs10"><span class="red">��ǧ�Τ����2�����Ϥ��Ƥ���������</span><br />
								�������äΥ᡼�륢�ɥ쥹�򤴻��Ѥξ��ϥɥᥤ���忮����ˤ���դ���������<br />
								�����餫��Τ������᡼�뤬�Ϥ��ʤ����Ȥ��������ޤ���</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td class="fs12n" id="left"><input type="radio" name="method" value="2" onClick="setInputArea(this.value)" <!--{if $arrForm.method eq "2"}-->checked<!--{/if}-->>�����äǤβ������˾</td>
						<td id="right">
						<table cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n">�������褪�����ֹ�<br><span class="red"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td class="fs12n">
									<input type="text" name="tel01" value="<!--{$arrForm.tel01|escape}-->" style="<!--{$arrErr.tel01|sfGetErrorColor}-->" size="6" />&nbsp;-&nbsp;
									<input type="text" name="tel02" value="<!--{$arrForm.tel02|escape}-->" style="<!--{$arrErr.tel02|sfGetErrorColor}-->" size="6" />&nbsp;-&nbsp;
									<input type="text" name="tel03" value="<!--{$arrForm.tel03|escape}-->" style="<!--{$arrErr.tel03|sfGetErrorColor}-->" size="6" />
								</td>
							</tr>
							<tr><td height="15" class="fs12n"></td></tr>
							<tr>
								<td class="fs12n">����Ϣ����֤Ϥ��Ĥ����������Ǥ��礦����</td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td class="fs12n">
								<!--{assign var=key value="contact_time"}-->
								<span class="red"><!--{$arrErr[$key]}--></span>
								<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
								<!--{html_options options=$arrContactTime selected=$arrForm[$key]}-->
								</select></td>
							</tr>
							<tr><td height="15"></td></tr>
							<tr>
								<td class="fs12n">���٤������ֻ��꤬�������ޤ����顢����������������<span class="red">(1000ʸ������)</span></td>
							</tr>
							<tr><td height="2"></td></tr>
							<tr>
								<td class="red10">���ҤαĶȻ��֤�9:30��17:00���ڡ������˺����٤ߡˤǤ���<br />
								���ҤαĶȻ�����Ǥ�����򤪴ꤤ�������ޤ���</td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td class="fs12n">
								<!--{assign var=key value="message"}-->
								<span class="red"><!--{$arrErr[$key]}--></span>
								<textarea name="<!--{$key}-->"  cols="46" rows="8" class="area46" wrap="physical" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{$arrForm[$key]|escape}--></textarea>
								</td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				<div id="button">
				<input type="image" onmouseover="chgImgImageSubmit('../img/button/confirm_on.gif',this)" onmouseout="chgImgImageSubmit('../img/button/confirm.gif',this)" src="../img/button/confirm.gif" width="150" height="30" alt="��ǧ�ڡ�����" border="0" name="confirm" id="confirm" /></div>
				</form>
				</td>
				<!--��RIGHT CONTENTS-->
				
			</tr>
		</table>
		<!--��MAIN CONTENTS-->
		</td>
		<td bgcolor="#ffffff" width="10"><img src="../img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#cccccc" width="1"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
	</tr>
</table>
<!--��CONTENTS-->

<script language="JavaScript">
<!--
function setInputArea(val) {
	var fm = document.form1;
	tFlag = true;
	mFlag = true;
	tColor = '<!--{$smarty.const.DISABLED_RGB}-->';
	mColor = '<!--{$smarty.const.DISABLED_RGB}-->';
	errColor = '<!--{$smarty.const.ERR_COLOR}-->';
	
	if ( val == 1 ){
		mColor = '';
		mFlag = false;
	} else if ( val == 2 ){
		tColor = '';
		tFlag = false;
	}
	fm.email.disabled = mFlag;
	fm.email02.disabled = mFlag;
	fm.tel01.disabled = tFlag;
	fm.tel02.disabled = tFlag;
	fm.tel03.disabled = tFlag;
	fm.contact_time.disabled = tFlag;
	fm.message.disabled = tFlag;
	
	if ( fm.email02.style.backgroundColor != errColor ) 		fm.email02.style.backgroundColor = mColor;
	if ( fm.email.style.backgroundColor != errColor ) 			fm.email.style.backgroundColor = mColor;
	if ( fm.tel01.style.backgroundColor != errColor ) 			fm.tel01.style.backgroundColor = tColor;	
	if ( fm.tel02.style.backgroundColor != errColor )			fm.tel02.style.backgroundColor = tColor;
	if ( fm.tel03.style.backgroundColor != errColor )			fm.tel03.style.backgroundColor = tColor;
	if ( fm.contact_time.style.backgroundColor != errColor )	fm.contact_time.style.backgroundColor = tColor;
	if ( fm.message.style.backgroundColor != errColor )			fm.message.style.backgroundColor = tColor;
		
}

//-->
</script>

