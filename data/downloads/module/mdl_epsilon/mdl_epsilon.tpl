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
<title><!--{$tpl_subtitle}--></title>
<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();

function lfnCheckPayment(){
	var fm = document.form1;
	var val = 0;
	
	payment = new Array('payment[]');

	for(pi = 0; pi < payment.length; pi++) {
	
		list = new Array('credit[]');
		if(fm[payment[pi]][0].checked){
			fnChangeDisabled(list, false);
		}else{
			fnChangeDisabled(list);
		}

		list = new Array('convenience[]');
		if(fm[payment[pi]][1].checked){
			fnChangeDisabled(list, false);
		}else{
			fnChangeDisabled(list);
		}
	}
}

function fnChangeDisabled(list, disable) {
	len = list.length;

	if(disable == null) { disable = true; }
	
	for(i = 0; i < len; i++) {
		if(document.form1[list[i]]) {
			// �饸���ܥ��󡢥����å��ܥå�������������б�
			max = document.form1[list[i]].length
			if(max > 1) {
				for(j = 0; j < max; j++) {
					// ͭ����̵�����ڤ��ؤ�
					document.form1[list[i]][j].disabled = disable;
				}
			} else {
				// ͭ����̵�����ڤ��ؤ�
				document.form1[list[i]].disabled = disable;
			}
		}
	}
}

function win_open(URL){
	var WIN;
	WIN = window.open(URL);
	WIN.focus();
}
//-->
</script>
</head>

<body bgcolor="#ffffff" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload='lfnCheckPayment(); <!--{$tpl_onload}-->'>
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->admin/css/common.css" type="text/css" />
</noscript>

<div align="center">
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
<input type="hidden" name="mode" value="edit">
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
								<td bgcolor="#cccccc" >
									<table width="442" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="442" height="7" alt=""></td>
										</tr>
										<tr>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
											<td bgcolor="#636469" width="402" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�--><!--{$tpl_subtitle}--></span></td>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="442" height="7" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="442" height="10" alt=""></td>
										</tr>
									</table>

									<table width="442" border="0" cellspacing="1" cellpadding="8" summary=" ">
										<tr class="fs12n">
											<td bgcolor="#ffffff">
												���ץ�����ѥ⥸�塼�������ĺ���٤ˤϡ��桼���ͤ����Ȥ�
												���ץ�����������ͤȤ������ԤäƤ�������ɬ�פ�����ޤ��� <br/>
												���������ߤˤĤ��ޤ��Ƥϡ������Υڡ������顢���������ߤ�ԤäƲ�������<br/><br/>
												<a href="#" onClick="win_open('http://www.rapidsite.jp/product/support/shop/epsilon.html')" > ��� ���ץ�����ѥ����ƥ�ˤĤ���</a><br/>
												<a href="#" onClick="win_open('http://wiki.ec-cube.net/')" > ��� ���ץ�����ѥ⥸�塼������ޥ˥奢��</a>
											</td>
										</tr>
									</table>
									<table width="442" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="442" height="10" alt=""></td></tr>
									</table>
									
									<table width="442" border="0" cellspacing="1" cellpadding="8" summary=" ">
										<tr class="fs12n">
											<td width="" bgcolor="#f3f3f3">���󥳡���<span class="red">��</span></td>
											<td width="337" bgcolor="#ffffff">
											<!--{assign var=key value="code"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<input type="text" name="<!--{$key}-->" style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key].value}-->" class="box10" maxlength="<!--{$smarty.const.INT_LEN}-->">
											</td>
										</tr>
										<tr class="fs12n">
											<td width="" bgcolor="#f3f3f3">��³��URL<span class="red">��</span></td>
											<td width="337" bgcolor="#ffffff">
											<!--{assign var=key value="url"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<input type="text" name="<!--{$key}-->" style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key].value}-->" class="box40" maxlength="<!--{$smarty.const.URL_LEN}-->">
											</td>
										</tr>
										<tr class="fs12n">
											<td width="90" bgcolor="#f3f3f3">���ѷ��<span class="red">��</span></td>
											<td width="337" bgcolor="#ffffff">
											<!--{assign var=key value="payment"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<!--{html_checkboxes_ex name="$key" options=$arrPayment selected=$arrForm[$key].value style=$arrErr[$key]|sfGetErrorColor onclick="lfnCheckPayment();"}-->
											</td>
										</tr>
										<tr class="fs12n">
											<td width="90" bgcolor="#f3f3f3">���ѥ��쥸�å�</td>
											<td width="337" bgcolor="#ffffff">
											<!--{assign var=key value="credit"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<!--{html_checkboxes_ex name="$key" options=$arrCredit selected=$arrForm[$key].value style=$arrErr[$key]|sfGetErrorColor}-->
											</td>
										</tr>
										<tr class="fs12n">
											<td width="90" bgcolor="#f3f3f3">���ѥ���ӥ�</td>
											<td width="337" bgcolor="#ffffff">
											<!--{assign var=key value="convenience"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<!--{html_checkboxes_ex name="$key" options=$arrConvenience selected=$arrForm[$key].value style=$arrErr[$key]|sfGetErrorColor}-->
											</td>
										</tr>
										<!--{assign var=key value="service"}-->
										<!--{if $arrErr[$key] != ""}-->
										<tr class="fs12n">
											<td bgcolor="#ffffff" colspan=2>
											<span class="red12"><!--{$arrErr[$key]}--></span>
											</td>
										</tr>
										<!--{/if}-->
									</table>

									<table width="442" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
											<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_top.gif" width="440" height="7" alt=""></td>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
										</tr>
										<tr>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
											<td bgcolor="#e9e7de" align="center">
											<table border="0" cellspacing="0" cellpadding="0" summary=" ">
												<tr>
													<td><input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="�������Ƥ���Ͽ����" border="0" name="subm" onClick="document.body.style.cursor = 'wait';"></td>
												</tr>
											</table>
											</td>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="442" height="8" alt=""></td>
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


