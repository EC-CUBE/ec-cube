<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-2022-jp">
<title>�ȡ���Ʋ�����󥿡��ͥåȥ���åԥ�</title>
<style type="text/css">
<!--
.fs10 { font-size: 62.5%; line-height: 150%; }
.fs12 { font-size: 75%; line-height: 150%; }
.red10 { color: #cc0000; font-size: 62.5%; line-height: 150%; }
.red12 { color: #cc0000; font-size: 75%; line-height: 150%; }
.red12st { color: #cc0000; font-weight: bold; font-size: 75%; line-height: 150%; }
.red14st { color: #cc0000; font-weight: bold; font-size: 87.5%; line-height: 150%; }
.fs12st { font-weight: bold; font-size: 75%; line-height: 150%; }
.fs14st { font-weight: bold; font-size: 87.5%; line-height: 150%; }
-->
</style>
</head>

<body text="#333333" bgcolor="#ffffff" link="#4a6fa6" vlink="#800080" alink="#7d9ac6" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<div align="center">
<table width="600" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td height="10"></td></tr>
	<tr>
		<td colspan="3" class="fs12">{name} ��</td>
	</tr>
	<tr><td height="5"></td></tr>
</table>
<table width="600" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc"><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/_.gif" width="1" height="10" alt=""></td>
		<td align="center">
		<table width="598" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><a href="http://www.tokado.jp/" target="_blank"><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/header.jpg" width="598" height="52" alt="�ȡ���Ʋ�����󥿡��ͥåȥ���åԥ�" border="0"></a></td>
			</tr>
		</table>
		<!--���᡼��ô���ΤҤȤ���-->
		<table width="560" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td height="20"><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/_.gif" width="118" height="1" alt=""></td>
				<td><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail//mail_img/_.gif" width="442" height="1" alt=""></td>
			</tr>
			<tr valign="top">
				<td>
				<table width="112" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<!--�᡼��ô���μ̿�-->
					<tr>
						<td bgcolor="#cccccc" height="122" align="center">
						<!--{assign var=key value="charge_image"}-->
						<!--{assign var=file_url value="`$arrFile[$key].filepath`"}-->
						<img src="<!--{$smarty.const.SITE_URL}--><!--{$arrFile[$key].filepath}-->" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].width}-->" alt="�᡼��ô���̿�"></td>
					</tr>
					<!--�᡼��ô���μ̿�-->
				</table>
				</td>
				<td>
				<table width="442" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td colspan="3"><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/comment_top.gif" width="442" height="9" alt=""></td>
					</tr>
					<tr>
						<td background="<!--{$smarty.const.SITE_URL}-->img/html_mail/comment_bg.gif"><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/comment_a.gif" width="27" height="26" alt=""></td>
						<td bgcolor="#f7f7f9">
						<!--�᡼��ô���Υ�����-->
						<table width="403" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12">{name}�ͤ�<br>
								<!--{$list_data.header|escape|nl2br}-->
							</tr>
						</table>
						<!--�᡼��ô���Υ�����-->
						</td>
						<td background="<!--{$smarty.const.SITE_URL}-->img/html_mail/comment_bgright.gif"><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/_.gif" width="12" height="1" alt=""></td>
					</tr>
					<tr>
						<td colspan="3"><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/comment_bottom.gif" width="442" height="9" alt=""></td>
					</tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
		</table>
		<!--���̤���ΤҤȤ���-->
		<!--���ȡ���Ʋ�������ᾦ��-->
		<table width="560" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td class="red14st"><!--{$list_data.main_title|escape}--></td>
			</tr>
			<tr><td bgcolor="#9f0000"><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/_.gif" width="10" height="2" alt=""></td></tr>
			<tr><td bgcolor="#cc0000"><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/_.gif" width="10" height="3" alt=""></td></tr>
			<tr><td height="20"></td></tr>
		</table>
		<table width="560" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr valign="top">
				<td>
				<table width="262" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<!--260��260�̿�-->
					<tr>
						<td bgcolor="#cccccc" height="262" align="center"><a href="<!--{$smarty.const.SITE_URL}-->products/detail-p<!--{$list_data.main_product_id|escape}-->.html" target="_blank"><img src="<!--{$smarty.const.SITE_URL}--><!--{$smarty.const.IMAGE_SAVE_URL}--><!--{$list_data.main.main_image|escape}-->" width="260" height="260" alt="<!--{$list_data.main.name|escape}-->" border="0"></a></td>
					</tr>
					<!--260��260�̿�-->
				</table>
				</td>
				<td align="right">
				<table width="290" border="0" cellspacing="0" cellpadding="7" summary=" ">
					<!--�������ᾦ��̾-->
					<tr>
						<td bgcolor="#eeece3" class="fs14st"><a href="<!--{$smarty.const.SITE_URL}-->products/detail-p<!--{$list_data.main_product_id|escape}-->.html" target="_blank"><!--{$list_data.main.name|escape}--></a></td>
					</tr>
					<!--�������ᾦ��̾-->
				</table>
				<table width="290" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="5"></td></tr>
					<!--��������ƥ�����-->
					<tr>
						<td class="fs12"><!--{$list_data.main_comment|escape|nl2br}--></td>
					</tr>
					<!--��������ƥ�����-->
					<tr><td height="5"></td></tr>
					<tr>
						<td bgcolor="#cccccc">
						<!--����������ʡ��ݥ���ȡ�����-->
						<table width="290" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr>
								<td width="97" bgcolor="#f7f7f9" class="fs12st">���̲���</td>
								<td width="170" bgcolor="#ffffff">
									<span class="red14st">

									<!--{if $list_data.main.price02_min == $list_data.main.price02_max}-->				
										<!--{$list_data.main.price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{else}-->
										<!--{$list_data.main.price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->��<!--{$list_data.main.price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{/if}-->
									��</span><span class="red10">���ǹ���</span><br />
								
									<span class="fs12">���̾���ʡ�
									<!--{if $list_data.main.price01_min == $list_data.main.price01_max}-->				
										<!--{$list_data.main.price01_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{else}-->
										<!--{$list_data.main.price01_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->��<!--{$list_data.main.price01_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{/if}-->
									��</span><span class="red10">���ǹ���</span>)
								</td>
							</tr>
							<tr>
								<td width="97" bgcolor="#f7f7f9" class="fs12st">�ݥ����</td>
								<td width="170" bgcolor="#ffffff">
									<span class="red14st">
										<!--{if $list_data.main.price02_min == $list_data.main.price02_max}-->				
											<!--{$list_data.main.price02_min|sfPrePoint:$list_data.main.point_rate:$smarty.const.POINT_RULE:$list_data.main_product_id}-->
										<!--{else}-->
											<!--{$list_data.main.price02_min|sfPrePoint:$list_data.main.point_rate:$smarty.const.POINT_RULE:$list_data.main_product_id}-->��<!--{$list_data.main.price02_max|sfPrePoint:$list_data.main.point_rate:$smarty.const.POINT_RULE:$list_data.main_product_id}-->
										<!--{/if}-->
									</span><span class="red12">pt</span></td>
							</tr>
							<tr>
								<td width="97" bgcolor="#f7f7f9" class="fs12st">����</td>
								<td width="170" bgcolor="#ffffff"><span class="fs12"><!--{$list_data.main.deliv_fee|escape|number_format}-->��</span>&nbsp;<span class="fs10">(�ǹ�)</span></td>
							</tr>
						</table>
						<!--����������ʡ��ݥ���ȡ�����-->
						</td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr>
						<td><a href="<!--{$smarty.const.SITE_URL}-->products/detail-p<!--{$list_data.main_product_id|escape}-->.html" target="_blank"><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/button.gif" width="124" height="28" alt="�ܤ����Ϥ�����" border="0"></a></td>
					</tr>
				</table>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
		</table>
		<!--���ȡ���Ʋ�������ᾦ��-->
		<!--���ȡ���Ʋ���徦�ʡ�-->
		<table width="560" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td class="red14st"><!--{$list_data.sub_title|escape}--></td>
			</tr>
			<tr><td bgcolor="#9f0000"><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/_.gif" width="10" height="2" alt=""></td></tr>
			<tr><td bgcolor="#cc0000"><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/_.gif" width="10" height="3" alt=""></td></tr>
			<tr><td height="20"></td></tr>
			<!--���徦�ʥ�����-->
			<tr>
				<td class="fs12"><!--{$list_data.sub_comment|escape|nl2br}--></td>
			</tr>
			<!--���徦�ʥ�����-->
		</table>

		<table width="560" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td height="20"></td>
				<td><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/_.gif" width="11" height="1" alt=""></td>
				<td></td>
				<td><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/_.gif" width="10" height="1" alt=""></td>
				<td></td>
				<td><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/_.gif" width="11" height="1" alt=""></td>
				<td></td>
			</tr>

<!--{if $list_data.sub[0].data_exists eq "OK"}-->
			<tr valign="top">
			
	<!--{section name=data loop=4}-->
		<!--{if is_numeric($list_data.sub[0][data].product_id)}-->
				<td>
				<!--1����<!--{$smarty.section.data.iteration}-->-->
				<table width="132" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<!--130��130�̿�-->
					<tr>
						<td bgcolor="#cccccc" height="132" align="center"><a href="<!--{$smarty.const.SITE_URL}-->products/detail-p<!--{$list_data.sub[0][data].product_id|escape}-->.html" target="_blank"><img src="<!--{$smarty.const.SITE_URL}--><!--{$smarty.const.IMAGE_SAVE_URL}--><!--{$list_data.sub[0][data].main_list_image|escape}-->" width="130" height="130" alt="<!--{$list_data.sub[0][data].name|escape}-->" border="0"></a></td>
					</tr>
					<!--130��130�̿�-->
					<tr>
						<td><span class="red12st"><a href="<!--{$smarty.const.SITE_URL}-->products/detail-p<!--{$list_data.sub[0][data].product_id|escape}-->.html" target="_blank"><!--{$list_data.sub[0][data].name|escape}--></a></span><br>
						<span class="fs10">���̲���</span><br>
						<span class="red12st">
						
						<!--{if $list_data.sub[0][data].price02_min == $list_data.sub[0][data].price02_max}-->				
							<!--{$list_data.sub[0][data].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
						<!--{else}-->
							<!--{$list_data.sub[0][data].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->��<!--{$list_data.sub[0][data].price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
						<!--{/if}-->��
						
						</span><span class="red10">���ǹ���</span></td>
					</tr>
				</table>
				<!--1����<!--{$smarty.section.data.iteration}-->-->
				</td>
				<!--{if ! $smarty.section.data.last}--><td></td><!--{/if}-->

		<!--{/if}-->
	<!--{/section}-->
				
			</tr>
<!--{/if}-->

<!--{if $list_data.sub[1].data_exists eq "OK"}-->
			<tr><td height="20"></td></tr>
			<tr valign="top">

	<!--{section name=data1 loop=4}-->
		<!--{if is_numeric($list_data.sub[1][data1].product_id)}-->
				
				<td>
				<!--2����<!--{$smarty.section.data1.iteration}-->-->
				<table width="132" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<!--130��130�̿�-->
					<tr>
						<td bgcolor="#cccccc" height="132" align="center"><a href="<!--{$smarty.const.SITE_URL}-->products/detail-p<!--{$list_data.sub[1][data1].product_id|escape}-->.html" target="_blank"><img src="<!--{$smarty.const.SITE_URL}--><!--{$smarty.const.IMAGE_SAVE_URL}--><!--{$list_data.sub[1][data1].main_list_image|escape}-->" width="130" height="130" alt="<!--{$list_data.sub[1][data1].name|escape}-->" border="0"></a></td>
					</tr>
					<!--130��130�̿�-->
					<tr>
						<td><span class="red12st"><a href="<!--{$smarty.const.SITE_URL}-->products/detail-p<!--{$list_data.sub[1][data1].product_id|escape}-->.html" target="_blank"><!--{$list_data.sub[1][data1].name|escape}--></a></span><br>
						<span class="fs10">���̲���</span><br>
						<span class="red12st">
						
						<!--{if $list_data.sub[1][data1].price02_min == $list_data.sub[1][data1].price02_max}-->				
							<!--{$list_data.sub[1][data1].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
						<!--{else}-->
							<!--{$list_data.sub[1][data1].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->��<!--{$list_data.sub[1][data1].price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
						<!--{/if}-->��
						
						</span><span class="red10">���ǹ���</span></td>
					</tr>
				</table>
				<!--2����<!--{$smarty.section.data1.iteration}-->-->
				</td>
				<!--{if ! $smarty.section.data1.last}--><td></td><!--{/if}-->

		<!--{/if}-->
	<!--{/section}-->
	
			</tr>
<!--{/if}-->
<!--{if $list_data.sub[2].data_exists eq "OK"}-->
			<tr><td height="20"></td></tr>
			<tr valign="top">

	<!--{section name=data2 loop=4}-->
		<!--{if is_numeric($list_data.sub[2][data2].product_id)}-->
				
				<td>
				<!--3����<!--{$smarty.section.data1.iteration}-->-->
				<table width="132" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<!--130��130�̿�-->
					<tr>
						<td bgcolor="#cccccc" height="132" align="center"><a href="<!--{$smarty.const.SITE_URL}-->products/detail-p<!--{$list_data.sub[2][data2].product_id|escape}-->.html" target="_blank"><img src="<!--{$smarty.const.SITE_URL}--><!--{$smarty.const.IMAGE_SAVE_URL}--><!--{$list_data.sub[2][data2].main_list_image|escape}-->" width="130" height="130" alt="<!--{$list_data.sub[2][data2].name|escape}-->" border="0"></a></td>
					</tr>
					<!--130��130�̿�-->
					<tr>
						<td><span class="red12st"><a href="<!--{$smarty.const.SITE_URL}-->products/detail-p<!--{$list_data.sub[2][data2].product_id|escape}-->.html" target="_blank"><!--{$list_data.sub[2][data2].name|escape}--></a></span><br>
						<span class="fs10">���̲���</span><br>
						<span class="red12st">
						
						<!--{if $list_data.sub[2][data2].price02_min == $list_data.sub[2][data2].price02_max}-->				
							<!--{$list_data.sub[2][data2].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
						<!--{else}-->
							<!--{$list_data.sub[2][data2].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->��<!--{$list_data.sub[2][data2].price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
						<!--{/if}-->��
						
						</span><span class="red10">���ǹ���</span></td>
					</tr>
				</table>
				<!--3����<!--{$smarty.section.data2.iteration}-->-->
				</td>
				<!--{if ! $smarty.section.data2.last}--><td></td><!--{/if}-->

		<!--{/if}-->
	<!--{/section}-->
	
			</tr>
<!--{/if}-->
		</table>
		<!--���ȡ���Ʋ���徦�ʡ�-->
		<table width="560" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="30"></td></tr>
			<tr>
				<td colspan="2" class="fs12">�ܥ᡼����ۿ���ߤ򤴴�˾�ξ��ϡ�MY�ڡ����˥����󤷤Ƥ�����������Ͽ�����ѹ��Ρ֥᡼��ޥ�����Τ����դˤĤ��ơ���ǡ֥ƥ����ȥ᡼������ϡִ�˾���ʤ��פ����򤷤ƺ���Ͽ���Ƥ���������<br>
				<a href="<!--{$smarty.const.SITE_URL}-->/mypage/login.php" target="_blank">��³���Ϥ����餫��</a></td>
			</tr>
			<tr><td height="10"></td></tr>
		</table>
		<table width="560" border="0" cellspacing="0" cellpadding="10" summary=" ">
			<tr>
				<td bgcolor="#f6f6f6" class="fs10">�Խ���ȯ�ԡ��������&nbsp;�ȡ���Ʋ<br>
				��811-2412&nbsp;ʡ�����������ķ�Į����1060���ϡ�TEL��092-947-5575��FAX��092-947-6606<br>
				�ĶȻ��֡�9:30��17:00(�ڡ������˺����٤�)<br>
				���䤤��碌��<a href="mailto:info@tokado.jp">info@tokado.jp</a></td>
			</tr>
		</table>
		<!--���եå�-->
		<table width="598" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="20"></td></tr>
			<tr>
				<td bgcolor="#eeeeee" align="center">
				<table width="560" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="10"></td></tr>
					<tr>
						<td class="fs10" align="right">Copyright&nbsp;(C)&nbsp;2005&nbsp;TOKADO&nbsp;Co.,&nbsp;Ltd.&nbsp;All&nbsp;Rights&nbsp;Reserved.</td>
					</tr>
					<tr><td height="10"></td></tr>
				</table>
				</td>
			</tr>
			<tr><td bgcolor="#cc0000"><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/_.gif" width="10" height="3" alt=""></td></tr>
			<tr><td bgcolor="#9f0000"><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/_.gif" width="10" height="2" alt=""></td></tr>
		</table>
		<!--���եå�-->
		</td>
		<td bgcolor="#cccccc"><img src="<!--{$smarty.const.SITE_URL}-->img/html_mail/_.gif" width="1" height="10" alt=""></td>
	</tr>
</table>
</div>

</body>
</html>
