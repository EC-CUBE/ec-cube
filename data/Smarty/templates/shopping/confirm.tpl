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
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/flow03.gif" width="700" height="36" alt="������³����ή��"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<!--������³����ή��-->
		
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/confirm_title.jpg" width="700" height="40" alt="���������ƤΤ���ǧ"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">��������ʸ���Ƥ��������Ƥ������Ǥ��礦����<br>
				�������С����ֲ��Ρ�<!--{if $payment_type != ""}-->����<!--{else}-->����ʸ��λ�ڡ�����<!--{/if}-->�ץܥ���򥯥�å����Ƥ���������</td>
			</tr>
			<tr><td height="20"></td></tr>
			<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
			<input type="hidden" name="mode" value="confirm">
			<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
			<tr>
				<td bgcolor="#cccccc">
				<!--����ʸ���Ƥ�������-->
				<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr align="center" bgcolor="#f0f0f0">
						<td width="85" class="fs12n">���ʼ̿�</td>
						<td width="298" class="fs12n">����̾</td>
						<td width="60" class="fs12n">ñ��</td>
						<td width="40" class="fs12n">�Ŀ�</td>
						<td width="90" class="fs12n">����</td>
					</tr>
					<!--{section name=cnt loop=$arrProductsClass}-->
					<tr bgcolor="#ffffff">
						<td align="center">
							<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="win01('../products/detail_image.php?product_id=<!--{$arrProductsClass[cnt].product_id}-->&image=main_image','detail_image','<!--{$arrProductsClass[cnt].tpl_image_width}-->','<!--{$arrProductsClass[cnt].tpl_image_height}-->'); return false;" target="_blank">
								<img src="<!--{$smarty.const.SITE_URL}-->resize_image.php?image=<!--{$smarty.const.IMAGE_SAVE_DIR}-->/<!--{$arrProductsClass[cnt].main_list_image}-->&width=65&height=65" alt="<!--{$arrProductsClass[cnt].name|escape}-->">
							</a>
						</td>
						<td class="fs12">
							<strong><!--{$arrProductsClass[cnt].name}--></strong><br>
							<!--{if $arrProductsClass[cnt].classcategory_name1 != ""}-->
								<!--{$arrProductsClass[cnt].class_name1}-->��<!--{$arrProductsClass[cnt].classcategory_name1}--><br>
							<!--{/if}-->
							<!--{if $arrProductsClass[cnt].classcategory_name2 != ""}-->
								<!--{$arrProductsClass[cnt].class_name2}-->��<!--{$arrProductsClass[cnt].classcategory_name2}-->
							<!--{/if}-->
						</td>
						<td align="right" class="fs12">
							<!--{if $arrProductsClass[cnt].price02 != ""}-->
							<!--{$arrProductsClass[cnt].price02|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->��
							<!--{else}-->
							<!--{$arrProductsClass[cnt].price01|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->��
							<!--{/if}-->
						</td>
						<td align="right" class="fs12"><!--{$arrProductsClass[cnt].quantity|number_format}-->��</td>
						<td align="right" class="fs12"><!--{$arrProductsClass[cnt].total_pretax|number_format}-->��</td>
					</tr>
					<!--{/section}-->
					<tr align="right">
						<td colspan="4" bgcolor="#f0f0f0" class="fs12">����</td>
						<td colspan="2" bgcolor="#ffffff" class="fs12"><!--{$tpl_total_pretax|number_format}-->��</span><br>
					</tr>
					<tr align="right">
						<td colspan="4" bgcolor="#f0f0f0" class="fs12">�Ͱ����ʥݥ���Ȥ����ѻ���</td>
						<!--{assign var=discount value=`$arrData.use_point*$smarty.const.POINT_VALUE`}-->
						<td colspan="2" bgcolor="#ffffff" class="fs12">-<!--{$discount|number_format|default:0}-->��</td>
					</tr>
					<tr align="right">
						<td colspan="4" bgcolor="#f0f0f0" class="fs12">����</td>
						<td colspan="2" bgcolor="#ffffff" class="fs12"><!--{$arrData.deliv_fee|number_format}-->��</td>
					</tr>
					<tr align="right">
						<td colspan="4" bgcolor="#f0f0f0" class="fs12">�����</td>
						<td colspan="2" bgcolor="#ffffff" class="fs12"><!--{$arrData.charge|number_format}-->��</td>
					</tr>
					<tr align="right">
						<td colspan="4" bgcolor="#f0f0f0" class="fs12">���</td>
						<td colspan="2" bgcolor="#ffffff" class="fs12"><span class="redst"><!--{$arrData.payment_total|number_format}-->��</span></td>
					</tr>
				</table>
				<!--����ʸ���Ƥ����ޤ�-->

				<!--{* ������Ѥߤβ���Τ� *}-->
				<!--{if $tpl_login == 1 || $arrData.member_check == 1}-->
				<table bgcolor="#ffffff" width=100%><tr><td height="15"></td></tr></table>
				
				<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr class="fs12" align="right">
						<td bgcolor="#f0f0f0" width="610">����ʸ���Υݥ����</td>
						<td bgcolor="#ffffff" width="90"><!--{$tpl_user_point|number_format|default:0}-->Pt</td>
					</tr>
					<tr class="fs12" align="right">
						<td bgcolor="#f0f0f0" width="610">�����ѥݥ����</td>
						<td bgcolor="#ffffff" width="90">-<!--{$arrData.use_point|number_format|default:0}-->Pt</td>
					</tr>
					<!--{if $arrData.birth_point > 0}-->
					<tr class="fs12" align="right">
						<td bgcolor="#f0f0f0" width="610">��������ݥ����</td>
						<td bgcolor="#ffffff" width="90">+<!--{$arrData.birth_point|number_format|default:0}-->Pt</td>
					</tr>
					<!--{/if}-->
					<tr class="fs12" align="right">
						<td bgcolor="#f0f0f0" width="610">����û������ݥ����</td>
						<td bgcolor="#ffffff" width="90">+<!--{$arrData.add_point|number_format|default:0}-->Pt</td>
					</tr>
					<tr class="fs12st" align="right">
						<!--{assign var=total_point value=`$tpl_user_point-$arrData.use_point+$arrData.add_point`}-->
						<td bgcolor="#f0f0f0" width="610">����ʸ��λ��Υݥ����</td>
						<td bgcolor="#ffffff" width="90"><!--{$total_point|number_format}-->Pt</td>
					</tr>
				</table>
				<!--{/if}-->
				<!--{* ������Ѥߤβ���Τ� *}-->
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td bgcolor="#cccccc">
				<!--���Ϥ��褳������-->
				<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td colspan="2" bgcolor="#f0f0f0" class="fs12n"><strong>�����Ϥ���</strong></td>
					</tr>
					<!--{if $arrData.deliv_check == 1}-->
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">��̾��</td>
							<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.deliv_name01|escape}--> <!--{$arrData.deliv_name02|escape}--></td>
						</tr>
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">��̾���ʥեꥬ�ʡ�</td>
							<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.deliv_kana01|escape}--> <!--{$arrData.deliv_kana02|escape}--></td>
						</tr>
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">͹���ֹ�</td>
							<td width="507" bgcolor="#ffffff" class="fs12">��<!--{$arrData.deliv_zip01|escape}-->-<!--{$arrData.deliv_zip02|escape}--></td>
						</tr>
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">����</td>
							<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrPref[$arrData.deliv_pref]}--><!--{$arrData.deliv_addr01|escape}--><!--{$arrData.deliv_addr02|escape}--></td>
						</tr>
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">�����ֹ�</td>
							<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.deliv_tel01}-->-<!--{$arrData.deliv_tel02}-->-<!--{$arrData.deliv_tel03}--></td>
						</tr>
					<!--{else}-->
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">��̾��</td>
							<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.order_name01|escape}--> <!--{$arrData.order_name02|escape}--></td>
						</tr>
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">��̾���ʥեꥬ�ʡ�</td>
							<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.order_kana01|escape}--> <!--{$arrData.order_kana02|escape}--></td>
						</tr>
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">͹���ֹ�</td>
							<td width="507" bgcolor="#ffffff" class="fs12">��<!--{$arrData.order_zip01|escape}-->-<!--{$arrData.order_zip02|escape}--></td>
						</tr>
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">����</td>
							<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrPref[$arrData.order_pref]}--><!--{$arrData.order_addr01|escape}--><!--{$arrData.order_addr02|escape}--></td>
						</tr>
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">�����ֹ�</td>
							<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.order_tel01}-->-<!--{$arrData.order_tel02}-->-<!--{$arrData.order_tel03}--></td>
						</tr>
					<!--{/if}-->
				</table>
				<!--���Ϥ��褳���ޤ�-->
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td bgcolor="#cccccc">
				<!--����ʧ��ˡ�����Ϥ����֤λ��ꡦ����¾���䤤��碌��������-->		
				<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td colspan="2" bgcolor="#f0f0f0" class="fs12n"><strong>������ʧ��ˡ�����Ϥ����֤λ��ꡦ����¾���䤤��碌</strong></td>
					</tr>
					<tr>
						<td width="150" bgcolor="#f0f0f0" class="fs12">����ʧ��ˡ</td>
						<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.payment_method|escape}--></td>
					</tr>
					<tr>
						<td width="150" bgcolor="#f0f0f0" class="fs12">���Ϥ���</td>
						<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.deliv_date|escape|default:"����ʤ�"}--></td>
					</tr>
					<tr>
						<td width="150" bgcolor="#f0f0f0" class="fs12">���Ϥ�����</td>
						<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.deliv_time|escape|default:"����ʤ�"}--></td>
					</tr>
					<tr>
						<td width="150" bgcolor="#f0f0f0" class="fs12">����¾���䤤��碌</td>
						<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.message|escape|nl2br}--></td>
					</tr>
					
					<!--{if $tpl_login == 1}-->
					<tr>
						<td width="150" bgcolor="#f0f0f0" class="fs12">�ݥ���Ȼ���</td>
						<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.use_point|default:0}-->Pt</td>
					</tr>
					<!--{/if}-->
					
				</table>
				<!--����ʧ��ˡ�����Ϥ����֤λ��ꡦ����¾���䤤��碌�����ޤ�-->
				</td>
			</tr>
			<tr><td height="20"></td></tr>

			<tr>
				<td align="center">
					<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_back_on.gif',back03)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif',back03)" onclick="fnModeSubmit('return', '', ''); return false;"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="���" border="0" name="back03" id="back03"/></a>
					<img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="20" height="" alt="" />
					<!--{if $payment_type != ""}-->
						<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_next_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_next.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/common/b_next.gif" width="150" height="30" alt="����" border="0" name="next" id="next" />
					<!--{else}-->
						<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/shopping/b_ordercomp_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/shopping/b_ordercomp.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/shopping/b_ordercomp.gif" width="150" height="30" alt="����ʸ��λ�ڡ�����" border="0" name="next" id="next" />
					<!--{/if}-->
				</td>
			</tr>
			</form>
		</table>
		<!--��MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--��CONTENTS-->

