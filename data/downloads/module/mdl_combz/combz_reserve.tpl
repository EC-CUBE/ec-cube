<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--�@-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=Shift-JIS" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->admin/css/contents.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/win_op.js"></script>
<title>�R���r�[�Y�\�񔭍s</title>
<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();
//-->
</script>
</head>

<body bgcolor="#ffffff" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload=''>
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->admin/css/common.css" type="text/css" />
</noscript>

<div align="center">
<!--���������C���R���e���c������-->
<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="http://menu2.combzmail.jp/ex_eccube.cgi">
<input type="hidden" name="mode" value="reserve">
	<tr valign="top">
		<td class="mainbg">
			<!--���o�^�e�[�u����������-->
			<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--���C���G���A-->
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
											<td bgcolor="#636469" width="402" class="fs14n"><span class="white"><!--�R���e���c�^�C�g��-->�R���r�[�Y�\�񔭍s</span></td>
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
												�� �o�^��A�R���r�[�Y�Ǘ���ʏ�Ŕ��s�������w�肵�Ă��������B<br />
												�� �����}�K�����A�����}�K�{���͓o�^��ɁA�C�����邱�Ƃ��ł��܂��B
											</td>
										</tr>
									</table>
									<table width="442" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="442" height="10" alt=""></td>
										</tr>
									</table>
									<table width="442" border="0" cellspacing="1" cellpadding="5" summary=" ">
										<tr class="fs12n">
											<td width="" bgcolor="#f3f3f3">���s��ID<span class="red"> ��</span></td>
											<td width="300" bgcolor="#ffffff">
											<!--{assign var=key value="pubid"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<input type="text" name="<!--{$key}-->" style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key]}-->" class="box20" maxlength="<!--{$arrForm[$key].length}-->">
											</td>
										</tr>
										<tr class="fs12n">
											<td width="" bgcolor="#f3f3f3">�p�X���[�h<span class="red"> ��</span></td>
											<td width="300" bgcolor="#ffffff">
											<!--{assign var=key value="pw"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<input type="password" name="<!--{$key}-->" style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key]}-->" class="box20" maxlength="<!--{$arrForm[$key].length}-->">
											</td>
										</tr>
										<tr class="fs12n">
											<td width="" bgcolor="#f3f3f3">�����}�KID<span class="red"> ��</span></td>
											<td width="300" bgcolor="#ffffff">
											<!--{assign var=key value="magid"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<input type="text" name="<!--{$key}-->" style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key]}-->" class="box20" maxlength="<!--{$arrForm[$key].length}-->">
											</td>
										</tr>
										<tr class="fs12n">
											<td width="" bgcolor="#f3f3f3">�����}�K����</td>
											<td width="300" bgcolor="#ffffff">
											<!--{assign var=key value="subject"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<input type="text" name="<!--{$key}-->" style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key]}-->" class="box20" maxlength="<!--{$arrForm[$key].length}-->">
											</td>
										</tr>
										<tr class="fs12n">
											<td width="" align="center" bgcolor="#f3f3f3" colspan="2">�����}�K�{��</td>
										</tr>
										<tr class="fs12n"  align="center">
											<td width="300" align="center" bgcolor="#ffffff" colspan="2">
											<!--{assign var=key value="body"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<textarea name="<!--{$key}-->" cols="55" rows="20" class="area30" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.comment3|sfGetErrorColor}-->"><!--{$arrForm[$key]}--></textarea>
											</td>
										</tr>										
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
													<td><input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="���̓��e�œo�^����" border="0" name="subm" onClick="document.body.style.cursor = 'wait';"></td>
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
				<!--���C���G���A-->
			</table>
			<!--���o�^�e�[�u�������܂�-->
		</td>
	</tr>
</form>
</table>
<!--�������C���R���e���c����-->
</div>

</body>
</html>



