<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td class="mainbg">
		<table width="588" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<!--�ᥤ�󥨥ꥢ-->
			<tr>
				<td align="center">
				<table width="562" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<form name="form1" method="post" action="#">
					<tr><td height="14"></td></tr>
					<tr>
						<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/home_top.jpg" width="562" height="14" alt=""></td>
					</tr>
					<tr>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
						<td bgcolor="#cccccc">
						<!--�����ƥ���󤳤�����-->
						<table width="534" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/homettl_system.gif" width="534" height="26" alt="�����ƥ����"></td>
							</tr>
							<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/home_bar.jpg" width="534" height="10" alt=""></td></tr>
						</table>
						<table width="534" border="0" cellspacing="1" cellpadding="4" summary=" ">
							<tr>
								<td bgcolor="#f2f1ec" width="178" class="fs12">EC-CUBE�С������</td>
								<td bgcolor="#ffffff" width="337" class="fs12" align="right"><!--{$smarty.const.ECCUBE_VERSION}--></td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">PHP�С������</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><!--{$php_version}--></td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">DB�С������</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><!--{$db_version}--></td>
							</tr>							
						</table>
						
						<!--����åפξ�����������-->
						<table width="534" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/home_bar02.jpg" width="534" height="20" alt=""></td></tr>
							<tr>
								<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/homettl_shop.gif" width="534" height="26" alt="����åפξ���"></td>
							</tr>
							<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/home_bar.jpg" width="534" height="10" alt=""></td></tr>
						</table>
						<table width="534" border="0" cellspacing="1" cellpadding="4" summary=" ">
							<tr>
								<td bgcolor="#f2f1ec" width="178" class="fs12">���ߤβ����</td>
								<td bgcolor="#ffffff" width="337" class="fs12" align="right"><!--{$customer_cnt|default:"0"|number_format}-->̾</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">����������</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><!--{$order_yesterday_amount|default:"0"|number_format}-->��</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">�����������</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><!--{$order_yesterday_cnt|default:"0"|number_format}-->��</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec"><span class="fs12">���������</span><span class="fs10">(�����ޤ�) </span></td>
								<td bgcolor="#ffffff" class="fs12" align="right"><!--{$order_month_amount|default:"0"|number_format}-->��</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec"><span class="fs12">���������� </span><span class="fs10">(�����ޤ�) </span></td>
								<td bgcolor="#ffffff" class="fs12" align="right"><!--{$order_month_cnt|default:"0"|number_format}-->��</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">�����Υ�ӥ塼�񤭹��߿�</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><!--{$review_yesterday_cnt|default:"0"}-->��</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">�ܵҤ��ݻ��ݥ���ȹ��</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><!--{$customer_point|default:"0"}-->pt</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">��ӥ塼�񤭹�����ɽ����</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><!--{$review_nondisp_cnt|default:"0"}-->��</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">���ڤ쾦��</td>
								<td bgcolor="#ffffff" class="fs12">
								<!--{section name=i loop=$arrSoldout}-->
								<!--{$arrSoldout[i].product_id}-->:<!--{$arrSoldout[i].name|escape}--><br>
								<!--{/section}-->			
								</td>
							</tr>
						</table>
						<!--����åפξ��������ޤ�-->
						<table width="534" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/home_bar02.jpg" width="534" height="20" alt=""></td></tr>
							<tr>
								<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/homettl_list.gif" width="534" height="26" alt="�������հ���"></td>
							</tr>
							<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/home_bar.jpg" width="534" height="10" alt=""></td></tr>
						</table>
						<!--�������հ�����������-->
						<table width="534" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr bgcolor="#636469" align="center" class="fs10n">
								<td width="100"><span class="white">������</span></td>
								<td width="90"><span class="white">�ܵ�̾</span></td>
								<td width="159"><span class="white">��������</span></td>
								<td width="70"><span class="white">��ʧ��ˡ</span></td>
								<td width="70"><span class="white">�������(��)</span></td>
							</tr>
							<!--{section name=i loop=$arrNewOrder}-->
							<tr bgcolor="#ffffff" class="fs10">
								<td><!--{$arrNewOrder[i].create_date}--></td>
								<td><!--{$arrNewOrder[i].name01|escape}--> <!--{$arrNewOrder[i].name02|escape}--></td>
								<td><!--{$arrNewOrder[i].product_name|escape}--></td>
								<td><!--{$arrNewOrder[i].payment_method|escape}--></td>
								<td align="right"><!--{$arrNewOrder[i].total|number_format}-->��</td>
							</tr>
							<!--{/section}-->
						</table>
						<!--�������հ��������ޤ�-->
						</td>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
					</tr>
					<tr>
						<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/home_bottom.jpg" width="562" height="14" alt=""></td>
					</tr>
					<tr><td height="30"></td></tr>
				</form>
				</table>
				</td>
			</tr>
			<!--�ᥤ�󥨥ꥢ-->
		</table>
		</td>
		<td bgcolor="#a8a8a8"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
		<td class="infobg" bgcolor="#e3e3e3">
		<table width="288" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td align="center">
				<table width="266" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<!--���Τ餻��������-->
					<!--{section name=i loop=$arrInfo}-->
					<tr><td height="15"></td></tr>
					<tr>
						<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_top_left.jpg" width="12" height="5" alt="" border="0"></td>
						<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_top.jpg" width="249" height="5" alt="" border="0"></td>
						<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_top_right.jpg" width="5" height="5" alt="" border="0"></td>
					</tr>
					<tr>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_day_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_day_left.jpg" width="12" height="10" alt="" border="0"></td>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_bg01.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt=""><span class="infodate"><!--{$arrInfo[i][0]}--></span></td>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_day_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_day_right.jpg" width="5" height="10" alt="" border="0"></td>
					</tr>
					<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_middle.jpg" width="266" height="8" alt="" border="0"></td></tr>
					<tr>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_bottom_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="12" height="1" alt=""></td>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_bg02.jpg" class="infottl"><!--{$arrInfo[i][1]}--></td>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_bottom_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="5" height="1" alt=""></td>
					</tr>
					<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_bottom.jpg" width="266" height="7" alt="" border="0"></td></tr>
					<tr><td height="5"></td></tr>
					<tr>
						<td colspan="3" class="fs10"><span class="info"><!--{$arrInfo[i][2]}--></span></td>
					</tr>
					<!--{/section}-->
					<!--���Τ餻�����ޤ�-->
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<!--�����ᥤ�󥳥�ƥ�ġ���-->		
<!--��CONTENTS-->