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
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/cart/title.jpg" width="700" height="40" alt="���ߤΥ�������"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/cart/flame_top.gif" width="700" height="15" alt=""></td>
			</tr>
			<tr>
				<td align="center" background="<!--{$smarty.const.URL_DIR}-->img/cart/flame_bg.gif">
				<table width="680" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td align="center" class="fs14">
							<!--{if $tpl_login}-->
							<!--�ᥤ�󥳥���--><!--{$tpl_name|escape}--> �ͤΡ����ߤν���ݥ���Ȥϡ�<span class="redst"><!--{$tpl_user_point|number_format|default:0}--> pt</span>�פǤ���<br />
							<!--{else}-->
							<!--�ᥤ�󥳥���-->�ݥ�������٤����Ѥˤʤ�����ϡ������Ͽ������󤷤Ƥ��������ޤ��褦���ꤤ�פ��ޤ���<br />
							<!--{/if}-->							
							�ݥ���ȤϾ��ʹ�������1pt��<!--{$smarty.const.POINT_VALUE}-->�ߤȤ��ƻ��Ѥ��뤳�Ȥ��Ǥ��ޤ���<br/>

							<!-- ��������˾��ʤ�������ˤΤ�ɽ�� -->
							<!--{if count($arrProductsClass) > 0 }-->
								���㤤�夲���ʤι�׶�ۤϡ�<span class="redst"><!--{$tpl_total_pretax|number_format}-->��</span>�פǤ���
								<!--{if $arrInfo.free_rule > 0}-->
								<!--{if $arrData.deliv_fee|number_format > 0}-->
									���ȡ�<span class="redst"><!--{$tpl_deliv_free|number_format}-->��</span>�פ�����̵���Ǥ�����
								<!--{else}-->
									���ߡ���<span class="redst">����̵��</span>�פǤ�����
								<!--{/if}-->
								<!--{/if}-->
							<!--{/if}-->
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/cart/flame_bottom.gif" width="700" height="15" alt=""></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td bgcolor="#cccccc" align="center">
					<!--{if $tpl_message != ""}-->
					<table cellspacing="0" cellpadding="0" summary=" " bgcolor="#ffffff" width=100%>
						<tr>
							<td class="fs12"><span class="redst"><!--{$tpl_message}--></span></td>
						</tr>
					</table>
					<!--{/if}-->
					<!--{if count($arrProductsClass) > 0}-->
					<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
					<input type="hidden" name="mode" value="confirm">
					<input type="hidden" name="cart_no" value="">
	
					<!--����ʸ���Ƥ�������-->
					
						<tr align="center" bgcolor="#f0f0f0">
							<td width="50" class="fs12">���</td>
							<td width="85" class="fs12">���ʼ̿�</td>
							<td width="305" class="fs12">����̾</td>
							<td width="60" class="fs12">ñ��</td>
							<td width="50" class="fs12">�Ŀ�</td>
							<td width="150" class="fs12">����</td>
						</tr>
					
						<!--{section name=cnt loop=$arrProductsClass}-->
						<tr bgcolor="#ffffff" class="fs12n">
							<td align="center"><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnChangeAction('<!--{$smarty.server.PHP_SELF|escape}-->'); fnModeSubmit('delete', 'cart_no', '<!--{$arrProductsClass[cnt].cart_no}-->'); return false;">���</a></td>
							<td ><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="win01('../products/detail_image.php?product_id=<!--{$arrProductsClass[cnt].product_id}-->&image=main_image','detail_image','<!--{$arrProductsClass[cnt].tpl_image_width}-->','<!--{$arrProductsClass[cnt].tpl_image_height}-->'); return false;" target="_blank">
								<img src="<!--{$smarty.const.SITE_URL}-->resize_image.php?image=<!--{$smarty.const.IMAGE_SAVE_DIR}-->/<!--{$arrProductsClass[cnt].main_list_image}-->&width=65&height=65" alt="<!--{$arrProductsClass[cnt].name|escape}-->">
							</a></td>
							<td ><!--{* ����̾ *}--><strong><!--{$arrProductsClass[cnt].name|escape}--></storng><br />
							<!--{if $arrProductsClass[cnt].classcategory_name1 != ""}-->
								<!--{$arrProductsClass[cnt].class_name1}-->��<!--{$arrProductsClass[cnt].classcategory_name1}--><br />
							<!--{/if}-->
							<!--{if $arrProductsClass[cnt].classcategory_name2 != ""}-->
								<!--{$arrProductsClass[cnt].class_name2}-->��<!--{$arrProductsClass[cnt].classcategory_name2}-->
							<!--{/if}-->
							</td>
							<td align="right">
							<!--{if $arrProductsClass[cnt].price02 != ""}-->
								<!--{$arrProductsClass[cnt].price02|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->��
							<!--{else}-->
								<!--{$arrProductsClass[cnt].price01|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->��
							<!--{/if}-->						
							</td>
							<td align="center" >
							<table cellspacing="0" cellpadding="0" summary=" " id="form">
								<tr>
									<td colspan="3" align="center" class="fs12n"><!--{$arrProductsClass[cnt].quantity}--></td>
								</tr>
								<tr><td height="5"></td></tr>
								<tr>
									<td><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnChangeAction('<!--{$smarty.server.PHP_SELF|escape}-->'); fnModeSubmit('up','cart_no','<!--{$arrProductsClass[cnt].cart_no}-->'); return false"><img src="<!--{$smarty.const.URL_DIR}-->img/button/plus.gif" width="16" height="16" alt="��" /></a></td>
									<td><img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="10" height="1" alt="" /></td>
									<td><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnChangeAction('<!--{$smarty.server.PHP_SELF|escape}-->'); fnModeSubmit('down','cart_no','<!--{$arrProductsClass[cnt].cart_no}-->'); return false"><img src="<!--{$smarty.const.URL_DIR}-->img/button/minus.gif" width="16" height="16" alt="-" /></a></td>
								</tr>
							</table>
							</td>
							<td id="price_c" align="right"><!--{$arrProductsClass[cnt].total_pretax|number_format}-->��</td>
						</tr>
						<!--{/section}-->
						
						<tr align="right">
							<td colspan="5" class="fs12n" bgcolor="#f0f0f0">����</td>
							<td class="fs12n" bgcolor="#ffffff"><!--{$tpl_total_pretax|number_format}-->��</td>
						</tr>
						<tr align="right">
							<td colspan="5" class="fs12n" bgcolor="#f0f0f0">���</td>
							<td class="fs12st" bgcolor="#ffffff"><!--{$arrData.total-$arrData.deliv_fee|number_format}-->��</td>
						</tr>
						<!--{if $arrData.birth_point > 0}-->
						<tr align="right">
							<td colspan="5" class="fs12n" bgcolor="#f0f0f0">��������ݥ����</td>
							<td class="fs12st" bgcolor="#ffffff"><!--{$arrData.birth_point|number_format}-->pt</td>
						</tr>
						<!--{/if}-->
						<tr align="right">
							<td colspan="5" class="fs12n" bgcolor="#f0f0f0">����û��ݥ����</td>
							<td class="fs12st" bgcolor="#ffffff"><!--{$arrData.add_point|number_format}-->pt</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td height="10"></td></tr>

			<tr>
				<td class="fs10">
					�����ʼ̿��ϻ����Ѽ̿��Ǥ�������ʸ�Υ��顼�Ȱۤʤ�̿���ɽ������Ƥ�����Ǥ⡢�����ֹ�˵��ܤ���Ƥ��륫�顼ɽ���Ǵְ㤤�������ޤ���ΤǤ��¿�����������<br>
					���嵭��������������������ȯ�����ޤ�������դ���������
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td align="center"><img src="<!--{$smarty.const.URL_DIR}-->img/cart/text.gif" width="390" height="13" alt="�嵭���ƤǤ������С֥쥸�عԤ��ץܥ���򥯥�å����Ƥ���������"></td>
			</tr>
			<tr><td height="20"></td></tr>

			<tr>
				<td align="center">
					<!--{if $tpl_prev_url != ""}-->
					<a href="<!--{$tpl_prev_url}-->" onmouseOver="chgImg('<!--{$smarty.const.URL_DIR}-->img/cart/b_pageback_on.gif','back');" onmouseOut="chgImg('<!--{$smarty.const.URL_DIR}-->img/cart/b_pageback.gif','back');"><img src="<!--{$smarty.const.URL_DIR}-->img/cart/b_pageback.gif" width="150" height="30" alt="���Υڡ��������" name="back" id="back" /></a>��
					<!--{/if}-->
					<input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/cart/b_buystep_on.gif',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/cart/b_buystep.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/cart/b_buystep.gif" width="150" height="30" alt="������³����" name="confirm" />
				</td>
			</tr>
			</form>
					<!--{else}-->
						<table width=100% cellspacing="0" cellpadding="10" summary=" ">
							<tr bgcolor="#ffffff" align="center">
								<td class="fs12"><span class="redst">�� ���ߥ�������˾��ʤϤ������ޤ���</span><br />
							</tr>
						</table>
					<!--{/if}-->
				</td>
				<!--��CONTENTS-->	
		</table>
		<!--��MAIN CONTENTS-->
		</td>
	</tr>
</table>
<!--��CONTENTS-->
