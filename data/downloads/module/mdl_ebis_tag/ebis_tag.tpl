<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--��-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=EUC-JP" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->admin/css/contents.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/admin.js"></script>
<!--{include file='css/contents.tpl'}-->
<title><!--{$tpl_subtitle}--></title>
<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();
//-->
</script>
</head>

<body bgcolor="#ffffff" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="<!--{$tpl_onload}-->">
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->admin/css/common.css" type="text/css" />
</noscript>

<div align="center">
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI}-->">
<input type="hidden" name="mode" value="edit">
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
											<td bgcolor="#636469" width="400" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�--><!--{$tpl_subtitle}--></span></td>
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
											<td width="90" bgcolor="#f3f3f3">������URL<span class="red">��</span></td>
											<td width="337" bgcolor="#ffffff">
											<span class="red"><!--{$arrErr.login_url}--></span>
											<input type="text" name="login_url" size="30" style="<!--{$arrErr.login_url|sfGetErrorColor}-->" value="<!--{$arrForm.login_url.value}-->" class="box30" maxlength="50"/>
											</td>
										</tr>
										<tr class="fs12n">
											<td width="90" bgcolor="#f3f3f3">EBiS����<span class="red">��</span></td>
											<td width="337" bgcolor="#ffffff">
											<span class="red"><!--{$arrErr.cid}--></span>
											<input type="text" name="cid" size="30" style="<!--{$arrErr.cid|sfGetErrorColor}-->" value="<!--{$arrForm.cid.value}-->" class="box30" maxlength="50"/>
											</td>
										</tr>
										<tr class="fs12n">
											<td width="90" bgcolor="#f3f3f3">�ܵ�ID</td>
											<td width="337" bgcolor="#ffffff">
											<!--{* �ܵ�ID��ebis�����Ǥ�"m1id" *}-->
											<!--{assign var=key value="m1id"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<!--{html_radios name="$key" options=$arrEBiSTagCustomerId selected=$arrForm[$key].value}-->
											</td>
										</tr>
										<tr class="fs12n">
											<td width="90" bgcolor="#f3f3f3">�������</td>
											<td width="337" bgcolor="#ffffff">
											<!--{* ������ۡ�ebis�����Ǥ�"a1id" *}-->
											<!--{assign var=key value="a1id"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<!--{html_radios name="$key" options=$arrEBiSTagPayment selected=$arrForm[$key].value}-->
											</td>
										</tr>
										<!--{section name="options_loop" loop=$smarty.const.EBiS_TAG_OPTIONS_MAX}-->
										<tr class="fs12n">
											<!--{assign var=index value="`$smarty.section.options_loop.iteration`"}-->
											<td width="90" bgcolor="#f3f3f3">Ǥ�չ���<!--{$index}--></td>
											<td width="337" bgcolor="#ffffff">
											<!--{* Ǥ�չ��ܡ�ebis�����Ǥ�o1id, o2id, o3id... *}-->
											<!--{assign var=key value="o`$index`id"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
											<!--{html_options options=$arrEBiSTagOptions selected=$arrForm[$key].value}-->
											</select>
											</td>
										</tr>
										<!--{/section}-->
									</table>
									
									<table width="440" border="0" cellspacing="1" cellpadding="8" summary=" ">
										<tr class="fs12n">
											<td width="90" bgcolor="#f3f3f3" align="center">
											<input type="button" onClick="fnModeSubmit('csv','','');document.form1.mode.value='edit';return false;" value="�ڡ�����ϿCSV����������">
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


