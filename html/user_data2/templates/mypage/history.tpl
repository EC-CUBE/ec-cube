<!--{*
 * Copyright (c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--��CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--��MAIN ONTENTS-->
		<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/title.jpg" width="700" height="40" alt="MY�ڡ���"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr valign="top">
				<td>
				<!--��NAVI-->
				<!--{include file=$tpl_navi}-->
				<!--��NAVI-->
				</td>
				<td align="right">
				<table width="515" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td><!--�������ȥ�--><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/subtitle05.gif" width="515" height="32" alt="��������ܺ�"></td>
					</tr>
					<tr><td height="15"></td></tr>
					<tr>
						<td align="center" bgcolor="#fff5e8">
						<!--������������������-->
						<table width="495" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="10"></td></tr>
							<tr>
								<td class="fs12"><strong>����������&nbsp;</strong><!--{$arrDisp.create_date|sfDispDBDate}--><br>
								<strong>��ʸ�ֹ桧&nbsp;</strong><!--{$arrDisp.order_id}--><br>
								<strong>����ʧ����ˡ��&nbsp;</strong><!--{$arrPayment[$arrDisp.payment_id]|escape}-->
								<!--{if $arrDisp.deliv_time_id != ""}--><br />
								<strong>���Ϥ����ֻ��ꡧ&nbsp;</strong><!--{$arrDelivTime[$arrDisp.deliv_time_id]|escape}-->
								<!--{/if}-->
								<!--{if $arrDisp.deliv_date != ""}--><br />
								<strong>���Ϥ������ꡧ&nbsp;</strong><!--{$arrDisp.deliv_date|escape}-->
								<!--{/if}-->
								</td>
							</tr>
							<tr><td height="10"></td></tr>
						</table>
						<!--���������������ޤ�-->
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td align="center" bgcolor="#cccccc">
						<!--��������ܺ٤�������-->
						<table width="515" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr align="center" bgcolor="#f0f0f0">
								<td class="fs12n">���ʥ�����</td>
								<td class="fs12n">����̾</td>
								<td class="fs12n">ñ��</td>
								<td class="fs12n">�Ŀ�</td>
								<td class="fs12n">����</td>
							</tr>

							<!--{section name=cnt loop=$arrDisp.quantity}-->
							<tr bgcolor="#ffffff">
								<td class="fs12"><!--{$arrDisp.product_code[cnt]|escape}--></td>
								<td class="fs12"><a href="<!--{$smarty.const.URL_DIR}-->products/detail.php?product_id=<!--{$arrDisp.product_id[cnt]}-->"><!--{$arrDisp.product_name[cnt]|escape}--><a></td>
								<!--{assign var=price value=`$arrDisp.price[cnt]`}-->
								<!--{assign var=quantity value=`$arrDisp.quantity[cnt]`}-->
								<td align="right" class="fs12"><!--{$price|escape|number_format}-->��</td>
								<td align="center" class="fs12"><!--{$quantity|escape}--></td>
								<td align="right" class="fs12"><!--{$price|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|sfMultiply:$quantity|number_format}-->��</td>
							</tr>
							<!--{/section}-->

							<tr align="right">
								<td colspan="4" bgcolor="#f0f0f0"><span class="fs12">����</span></td>
								<td bgcolor="#ffffff"><span class="fs12"><!--{$arrDisp.subtotal|number_format}-->��</span><br><span class="fs10"></span></td>
							</tr>
							<!--{assign var=point_discount value="`$arrDisp.use_point*$smarty.const.POINT_VALUE`"}-->
							<!--{if $point_discount > 0}-->							
							<tr align="right">
								<td colspan="4" bgcolor="#f0f0f0" class="fs12">�ݥ�����Ͱ���</td>
								<td bgcolor="#ffffff" class="fs12"><!--{$point_discount|number_format}-->��</td>
							</tr>
							<!--{/if}-->
							<!--{assign var=key value="discount"}-->
							<!--{if $arrDisp[$key] != "" && $arrDisp[$key] > 0}-->
							<tr align="right">
								<td colspan="4" bgcolor="#f0f0f0" class="fs12">�Ͱ���</td>
								<td bgcolor="#ffffff" class="fs12"><!--{$arrDisp[$key]|number_format}-->��</td>
							</tr>
							<!--{/if}-->
							<tr align="right">
								<td colspan="4" bgcolor="#f0f0f0" class="fs12">����</td>
								<td bgcolor="#ffffff" class="fs12"><!--{assign var=key value="deliv_fee"}--><!--{$arrDisp[$key]|escape|number_format}-->��</td>
							</tr>
							<tr align="right">
								<td colspan="4" bgcolor="#f0f0f0" class="fs12">�����</td>
								<!--{assign var=key value="charge"}-->
								<td bgcolor="#ffffff" class="fs12"><!--{$arrDisp[$key]|escape|number_format}-->��</td>
							</tr>
							<tr align="right">
								<td colspan="4" bgcolor="#f0f0f0" class="fs12">���</td>
								<td bgcolor="#ffffff" class="fs12"><span class="redst"><!--{$arrDisp.payment_total|number_format}-->��</span></td>
							</tr>

						</table>
						<!--��������ܺ٤����ޤ�-->
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td bgcolor="#cccccc">
						<!-- ���ѥݥ���Ȥ������� -->
						<table width="515" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr align="right" bgcolor="#f0f0f0">
								<td class="fs12n" width="415">�����ѥݥ����</td>
								<td class="fs12n" width="75"bgcolor="#ffffff"><!--{assign var=key value="use_point"}--><!--{$arrDisp[$key]|number_format|default:0}--> pt</td>
							</tr>
							<tr align="right" bgcolor="#f0f0f0">
								<td class="fs12n" width="400">����û������ݥ����</td>
								<td class="fs12n" width="75" bgcolor="#ffffff"><!--{$arrDisp.add_point|number_format|default:0}--> pt</td>
							</tr>
						</table>
						<!-- ���ѥݥ���Ȥ����ޤ� -->
						</td>
					</tr>
					
					<tr><td height="20"></td></tr>
					<tr>
						<td bgcolor="#cccccc">
						<!--���Ϥ��褳������-->						
						<table width="515" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr bgcolor="#f0f0f0">
								<td colspan="2" class="fs12n"><strong>�����Ϥ���</strong></td>
							</tr>
							<tr>
								<td width="130" bgcolor="#f0f0f0" class="fs12n">��̾��</td>
								<!--{assign var=key1 value="deliv_name01"}--><!--{assign var=key2 value="deliv_name02"}-->
								<td width="367" bgcolor="#ffffff" class="fs12n"><!--{$arrDisp[$key1]|escape}-->&nbsp;<!--{$arrDisp[$key2]|escape}--></</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">��̾���ʥեꥬ�ʡ�</td>
								<!--{assign var=key1 value="deliv_kana01"}--><!--{assign var=key2 value="deliv_kana02"}-->
								<td bgcolor="#ffffff" class="fs12n"><!--{$arrDisp[$key1]|escape}-->&nbsp;<!--{$arrDisp[$key2]|escape}--></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">͹���ֹ�</td>
								<!--{assign var=key1 value="deliv_zip01"}--><!--{assign var=key2 value="deliv_zip02"}-->
								<td bgcolor="#ffffff" class="fs12n">��<!--{$arrDisp[$key1]}-->-<!--{$arrDisp[$key2]}--></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12">����</td>
								<td bgcolor="#ffffff" class="fs12"><!--{assign var=pref value=`$arrDisp.deliv_pref`}--><!--{$arrPref[$pref]}--><!--{assign var=key value="deliv_addr01"}--><!--{$arrDisp[$key]|escape}--><!--{assign var=key value="deliv_addr02"}--><!--{$arrDisp[$key]|escape}--></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">�����ֹ�</td>
								<!--{assign var=key1 value="deliv_tel01"}--><!--{assign var=key2 value="deliv_tel02"}--><!--{assign var=key3 value="deliv_tel03"}-->
								<td bgcolor="#ffffff" class="fs12n"><!--{$arrDisp[$key1]}-->-<!--{$arrDisp[$key2]}-->-<!--{$arrDisp[$key3]}--></td>
							</tr>
						</table>
						<!--���Ϥ��褳���ޤ�-->
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td align="center">
							<a href="./index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_back_on.gif','change');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif','change');"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="���" name="change" id="change" /></a>
						</td>
					</tr>
				</form>
				</table>
				</td>
			</tr>
		</table>
		<!--��MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--��CONTENTS-->
