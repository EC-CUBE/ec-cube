<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->admin/css/contents.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/admin.js"></script>
<!--{include file='css/contents.tpl'}-->
<title>Ģɼ�κ���</title>
<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();
//-->
</script>
</head>

<body bgcolor="#ffffff" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_ADMIN_CSS}-->common.css" type="text/css" />
</noscript>

<div align="center">
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$tpl_recv}-->">
<input type="hidden" name="order_id" value="<!--{$tpl_order_id}-->">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
	<tr valign="top">
		<td class="mainbg">
			<!--����Ͽ�ơ��֥뤳������-->
			<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--�ᥤ�󥨥ꥢ-->
				<tr>
					<td align="center">
						<table width="470" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_top.jpg" width="470" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
									
									<table width="440" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="440" height="7" alt=""></td>
										</tr>
										<tr>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
											<td bgcolor="#636469" width="400" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->Ģɼ�κ���</span></td>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="440" height="7" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="440" height="10" alt=""></td>
										</tr>
									</table>
									
									<table width="440" border="0" cellspacing="1" cellpadding="8" summary=" ">
										<tr class="fs12n">
											<td width="120" bgcolor="#f3f3f3">�����ֹ�</td>
											<td width="307" bgcolor="#ffffff"><!--{$tpl_order_id}--></td>
										</tr>
										<tr class="fs12n">
											<td width="120" bgcolor="#f3f3f3">ȯ����</td>
											<td width="307" bgcolor="#ffffff"><!--{if $arrErr.year}--><span class="red"><!--{$arrErr.year}--></span><!--{/if}-->
											<select name="year">
											<!--{html_options options=$arrYear selected=$arrForm.year}-->
											</select>ǯ
											<select name="month">
											<!--{html_options options=$arrMonth selected=$arrForm.month}-->
											</select>��
											<select name="day">
											<!--{html_options options=$arrDay selected=$arrForm.day}-->
											</select>����
											<span class="red">��ɬ������</span>
											</td>
										</tr>
										<tr class="fs12n">
											<td width="120" bgcolor="#f3f3f3">Ģɼ�μ���</td>
											<td width="307" bgcolor="#ffffff"><!--{if $arrErr.download}--><span class="red"><!--{$arrErr.download}--></span><!--{/if}-->
											<select name="chohyo_mode">
											<!--{html_options options=$arrMode selected=$arrForm.chohyo_mode}-->
											</select>
											</td>
										</tr>
										<tr class="fs12n">
											<td width="120" bgcolor="#f3f3f3">�����������ˡ</td>
											<td width="307" bgcolor="#ffffff"><!--{if $arrErr.download}--><span class="red"><!--{$arrErr.download}--></span><!--{/if}-->
											<select name="download">
											<!--{html_options options=$arrDownload selected=$arrForm.download}-->
											</select>
											</td>
										</tr>
										<tr class="fs12">
											<td width="120" bgcolor="#f3f3f3">Ģɼ�����ȥ�</td>
											<td width="307" bgcolor="#ffffff"><!--{if $arrErr.chohyo_title}--><span class="red"><!--{$arrErr.chohyo_title}--></span><!--{/if}-->
											<input type="text" name="chohyo_title" size="40" value="<!--{$arrForm.chohyo_title}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
											<span class="red">��̤���ϻ��ϡ��ǥե���ȤΥ����ȥ뤬ɽ������ޤ���</span><br />
											</td>
										</tr>
										<tr class="fs12">
											<td width="120" bgcolor="#f3f3f3">Ģɼ��å�����</td>
											<td width="307" bgcolor="#ffffff"><!--{if $arrErr.chohyo_msg1}--><span class="red"><!--{$arrErr.chohyo_msg1}--></span><!--{/if}-->
											1���ܡ�<input type="text" name="chohyo_msg1" size="40" value="<!--{$arrForm.chohyo_msg1}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
											<!--{if $arrErr.chohyo_msg2}--><span class="red"><!--{$arrErr.chohyo_msg1}--></span><!--{/if}-->
											2���ܡ�<input type="text" name="chohyo_msg2" size="40" value="<!--{$arrForm.chohyo_msg2}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
											<!--{if $arrErr.chohyo_msg3}--><span class="red"><!--{$arrErr.chohyo_msg3}--></span><!--{/if}-->
											3���ܡ�<input type="text" name="chohyo_msg3" size="40" value="<!--{$arrForm.chohyo_msg3}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
											<span class="red">��̤���ϻ��ϡ��ǥե���ȤΥ�å�������ɽ������ޤ���</span><br />
											</td>
										</tr>
										<tr class="fs12">
											<td width="120" bgcolor="#f3f3f3">����</td>
											<td width="307" bgcolor="#ffffff">
											1���ܡ�<input type="text" name="chohyo_etc1" size="40" value="" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
											<!--{if $arrErr.chohyo_etc2}--><span class="red"><!--{$arrErr.chohyo_msg1}--></span><!--{/if}-->
											2���ܡ�<input type="text" name="chohyo_etc2" size="40" value="" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
											<!--{if $arrErr.chohyo_etc3}--><span class="red"><!--{$arrErr.chohyo_msg3}--></span><!--{/if}-->
											3���ܡ�<input type="text" name="chohyo_etc3" size="40" value="" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
											<span class="red">��̤���ϻ��ϡ�ɽ������ޤ���</span><br />
											</td>
										</tr>


										<tr class="fs12">
											<td width="120" bgcolor="#f3f3f3">�ݥ����ɽ��</td>
											<td width="307" bgcolor="#ffffff">
											<input type="radio" name="disp_point" value="1" checked="checked" />���롡<input type="radio" name="disp_point" value="0" />���ʤ�<br />
											<span style="font-size: 80%;">���֤���פ����򤵤�Ƥ⡢�����ͤ������ξ���ɽ������ޤ���</span>
											</td>
										</tr>
									</table>

									<table width="440" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
											<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_top.gif" width="438" height="7" alt=""></td>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
										</tr>
										<tr>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
											<td bgcolor="#e9e7de" align="center">
											<table border="0" cellspacing="0" cellpadding="0" summary=" ">
												<tr>
													<td><input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="�������Ƥ���Ͽ����" border="0" name="subm" ></td>
												</tr>
											</table>
											</td>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="440" height="8" alt=""></td>
										</tr>
									</table>
								</td>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bottom.jpg" width="470" height="14" alt=""></td>
							</tr>
							<tr><td height="30"></td></tr>
						</table>
					</td>
				</tr>
				<!--�ᥤ�󥨥ꥢ-->
			</table>
			<!--����Ͽ�ơ��֥뤳���ޤ�-->
		</td>
	</tr>
</form>
</table>
<!--�����ᥤ�󥳥�ƥ�ġ���-->
</div>

</body>
</html>


