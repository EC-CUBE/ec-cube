<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--　-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="/ec-cube/admin/css/contents.css" type="text/css" media="all" />
<script type="text/javascript" src="/ec-cube/js/css.js"></script>
<script type="text/javascript" src="/ec-cube/js/navi.js"></script>
<script type="text/javascript" src="/ec-cube/js/win_op.js"></script>
<script type="text/javascript" src="/ec-cube/js/site.js"></script>
<script type="text/javascript" src="/ec-cube/js/admin.js"></script>
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
<link rel="stylesheet" href="/ec-cube/admin/css/common.css" type="text/css" />
</noscript>

<div align="center">
<!--★★メインコンテンツ★★-->
<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
<input type="hidden" name="mode" value="edit">
	<tr valign="top">
		<td class="mainbg">
			<!--▼登録テーブルここから-->
			<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--メインエリア-->
				<tr>
					<td align="center">
						<table width="470" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="/ec-cube/img/contents/main_top.jpg" width="470" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="/ec-cube/img/contents/main_left.jpg"><img src="/ec-cube/img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
									
									<table width="440" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td colspan="3"><img src="/ec-cube/img/contents/contents_title_top.gif" width="440" height="7" alt=""></td>
										</tr>
										<tr>
											<td background="/ec-cube/img/contents/contents_title_left_bg.gif"><img src="/ec-cube/img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
											<td bgcolor="#636469" width="400" class="fs14n"><span class="white"><!--コンテンツタイトル--><!--{$tpl_subtitle}--></span></td>
											<td background="/ec-cube/img/contents/contents_title_right_bg.gif"><img src="/ec-cube/img/common/_.gif" width="18" height="1" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="/ec-cube/img/contents/contents_title_bottom.gif" width="440" height="7" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="/ec-cube/img/contents/main_bar.jpg" width="440" height="10" alt=""></td>
										</tr>
									</table>
									
									<table width="440" border="0" cellspacing="1" cellpadding="8" summary=" ">
										<tr class="fs12n">
											<td width="90" bgcolor="#f3f3f3">ユーザID<span class="red">※</span></td>
											<td width="337" bgcolor="#ffffff">
											<span class="red"><!--{$arrErr.user}--></span>
											<input type="text" name="user" size="30" style="<!--{$arrErr.user|sfGetErrorColor}-->" value="<!--{$arrForm.user.value}-->" class="box30" maxlength="50"/>
											</td>
										</tr>	
										<tr class="fs12n">
											<td width="90" bgcolor="#f3f3f3">パスワード<span class="red">※</span></td>
											<td width="337" bgcolor="#ffffff">
											<span class="red"><!--{$arrErr.pass}--></span>
											<input type="password" name="pass" size="30" style="<!--{$arrErr.pass|sfGetErrorColor}-->" value="<!--{$arrForm.pass.value}-->" class="box30" maxlength="50"/>
											</td>
										</tr>	
										<tr class="fs12n">
											<td width="90" bgcolor="#f3f3f3">タグ識別ID<span class="red">※</span></td>
											<td width="337" bgcolor="#ffffff">
											<span class="red"><!--{$arrErr.cid}--></span>
											<input type="text" name="cid" size="30" style="<!--{$arrErr.cid|sfGetErrorColor}-->" value="<!--{$arrForm.cid.value}-->" class="box30" maxlength="50"/>
											</td>
										</tr>										
									</table>

									<table width="440" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td bgcolor="#cccccc"><img src="/ec-cube/img/common/_.gif" width="1" height="5" alt=""></td>
											<td><img src="/ec-cube/img/contents/tbl_top.gif" width="438" height="7" alt=""></td>
											<td bgcolor="#cccccc"><img src="/ec-cube/img/common/_.gif" width="1" height="5" alt=""></td>
										</tr>
										<tr>
											<td bgcolor="#cccccc"><img src="/ec-cube/img/common/_.gif" width="1" height="10" alt=""></td>
											<td bgcolor="#e9e7de" align="center">
											<table border="0" cellspacing="0" cellpadding="0" summary=" ">
												<tr>
													<td><input type="image" onMouseover="chgImgImageSubmit('/ec-cube/img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('/ec-cube/img/contents/btn_regist.jpg',this)" src="/ec-cube/img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" ></td>
												</tr>
											</table>
											</td>
											<td bgcolor="#cccccc"><img src="/ec-cube/img/common/_.gif" width="1" height="10" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="/ec-cube/img/contents/tbl_bottom.gif" width="440" height="8" alt=""></td>
										</tr>
									</table>
								</td>
								<td background="/ec-cube/img/contents/main_right.jpg"><img src="/ec-cube/img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/ec-cube/img/contents/main_bottom.jpg" width="470" height="14" alt=""></td>
							</tr>
							<tr><td height="30"></td></tr>
						</table>
					</td>
				</tr>
				<!--メインエリア-->
			</table>
			<!--▲登録テーブルここまで-->
		</td>
	</tr>
</form>
</table>
<!--★★メインコンテンツ★★-->
</div>

</body>
</html>


