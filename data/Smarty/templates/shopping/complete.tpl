<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--��CONTENTS-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--��MAIN ONTENTS-->
		<!--������³����ή��-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/flow04.gif" width="700" height="36" alt="������³����ή��"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<!--������³����ή��-->
			
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/complete_title.jpg" width="700" height="40" alt="����ʸ��λ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		
		<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td align="center" bgcolor="#cccccc">
				<table width="630" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="5"></td></tr>
					<tr>
						<td align="center" bgcolor="#ffffff">
						
							<!-- ������Ӥ˷�Ѥξ��ˤ�ɽ�� -->
							<!--{if count($arrConv) > 0}-->
							<table cellspacing="0" cellpadding="0" summary=" " id="ichi">
								<tr>
									<td>
									<table cellspacing="0" cellpadding="0" summary=" " id="comp">
										<tr><td height="20"></td></tr>
										<tr>
											<td class="fs12">������ӥ˷�Ѿ���<br />
											����ӥˤμ��ࡧ<!--{$arrCONVENIENCE[$arrConv.cv_type]|escape}--><br />
											<!--{if $arrConv.cv_payment_url != ""}-->����ɼURL(PC)��<!--{$arrConv.cv_payment_url}--><br /><!--{/if}-->
											<!--{if $arrConv.cv_payment_mobile_url != ""}-->����ɼURL(��Х���)��<!--{$arrConv.cv_payment_mobile_url}--><br /><!--{/if}-->
											<!--{if $arrConv.cv_receipt_no != ""}-->����ɼ�ֹ桧<!--{$arrConv.cv_receipt_no}--><br /><!--{/if}-->
											<!--{if $arrConv.cv_company_code != ""}-->��ȥ����ɡ�<!--{$arrConv.cv_company_code}--><br /><!--{/if}-->
											<!--{if $arrConv.cv_order_no != ""}-->�����ֹ桧<!--{$arrConv.cv_order_no}--><br /><!--{/if}-->
											��ʧ����:<!--{$arrConv.cv_payment_limit}--><br />
											<!--{$arrCONVENIMESSAGE[$arrConv.cv_type]}-->
										</tr>
										<tr><td height="20"></td></tr>
									</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td align="center" bgcolor="#ffffff">
							<!--{/if}-->						
							<!-- ������Ӥ˷�Ѥξ��ˤ�ɽ�� -->
						
							<!--����ʸ��λ��ʸ�Ϥ�������-->
							<table width="590" border="0" cellspacing="0" cellpadding="0" summary=" ">
								<tr><td height="25"></td></tr>
								<tr>
									<td class="fs12"><span class="redst"><!--{$arrInfo.company_name|escape}-->�ξ��ʤ򤴹����������������꤬�Ȥ��������ޤ�����</span></td>
								</tr>
								<tr><td height="20"></td></tr>
								<tr>
									<td class="fs12">�������ޡ�����ʸ�γ�ǧ�᡼������ꤵ���Ƥ��������ޤ����� <br>
									���졢����ǧ�᡼�뤬�Ϥ��ʤ����ϡ��ȥ�֥�β�ǽ���⤢��ޤ��Τ����Ѥ�����ǤϤ������ޤ����⤦���٤��䤤��碌�����������������äˤƤ��䤤��碌���������ޤ��� </td>
								</tr>
								<tr><td height="15"></td></tr>
								<tr>
									<td class="fs12">����Ȥ⤴���ܻ��ޤ��褦��������ꤤ�����夲�ޤ���</td>
								</tr>
								<tr><td height="20"></td></tr>
								<tr>
									<td class="fs12"><!--{$arrInfo.company_name|escape}--><br>
									TEL��<!--{$arrInfo.tel01}-->-<!--{$arrInfo.tel02}-->-<!--{$arrInfo.tel03}--> <!--{if $arrInfo.business_hour != ""}-->�ʼ��ջ���/<!--{$arrInfo.business_hour}-->��<!--{/if}--><br>
									E-mail��<a href="mailto:<!--{$arrInfo.email02|escape}-->"><!--{$arrInfo.email02|escape}--></a></td>
								</tr>
								<tr><td height="25"></td></tr>
							</table>
							<!--����ʸ��λ��ʸ�Ϥ����ޤ�-->
						</td>
					</tr>
					<tr><td height="5"></td></tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr align="center">
				<td><a href="<!--{$smarty.const.URL_DIR}-->index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_toppage.gif','b_toppage');"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_toppage.gif" width="150" height="30" alt="�ȥåץڡ�����" border="0" name="b_toppage"></a></td>
			</tr>
		</table>
		<!--��MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--��CONTENTS-->
