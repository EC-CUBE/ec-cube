<meta http-equiv='Content-Type' content='text/html; charset=EUC-JP'>
<table width='100%' border='0' cellspacing='0' cellpadding='0' summary=' '>
<tr>
<td bgcolor='#ffeebb' height='25' colspan='2' align='center'>
<SPAN style='color:red; font-size:12px'><strong>>> /install/index.phpは、インストール完了後にファイルを削除してください。</strong></span>
</td>
	</tr>
</table>
<!--　-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=EUC-JP" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="/ec-cube/admin/css/contents.css" type="text/css" media="all" />
<script type="text/javascript" src="/ec-cube/js/css.js"></script>
<script type="text/javascript" src="/ec-cube/js/navi.js"></script>
<script type="text/javascript" src="/ec-cube/js/win_op.js"></script>
<script type="text/javascript" src="/ec-cube/js/site.js"></script>
<script type="text/javascript" src="/ec-cube/js/admin.js"></script>

<style type="text/css">
@charset "euc-jp";

body {
	background: #fff url(/ec-cube/img/common/bg.jpg);
	background-repeat: repeat-x;
	font-family:"ＭＳ Ｐゴシック","Hiragino Maru Gothic Pro","ヒラギノ丸ゴ Pro W4",Osaka,sans-serif;
}


.mainbg {
	background: #fff url(/ec-cube/img/contents/main_bg.jpg);
	background-repeat: repeat-x;
}

.infobg {
	background: #fff url(/ec-cube/img/contents/home_bg.jpg);
	background-repeat: no-repeat;
	background-color: #e3e3e3;
}


.navi a{
	background: url(/ec-cube/img/contents/navi_bar.gif);
	background-repeat: repeat-y;
	background-color: #636469;
	width:140px;
	padding: 10px 5px 10px 12px;
	color:#ffffff;
	text-decoration:none;
	display:block ;
}

.navi-on a{
	background: url(/ec-cube/img/contents/navi_bar.gif);
	background-repeat: repeat-y;
	background-color: #a5a5a5;
	width:140px;
	padding: 10px 5px 10px 12px;
	color:#000000;
	text-decoration:none;
	display:block;
}

/*icon*/
.icon_edit{
	background: url(/ec-cube/img/contents/icon_edit.jpg);
	background-repeat: no-repeat;
	padding: 0 0 0 15px;
}

.icon_mail {
	background: url(/ec-cube/img/contents/icon_mail.jpg);
	background-repeat: no-repeat;
	padding: 0 0 0 15px;
}

.icon_delete {
	background: url(/ec-cube/img/contents/icon_delete.jpg);
	background-repeat: no-repeat;
	padding: 0 0 0 15px;
}

.icon_class {
	background: url(/ec-cube/img/contents/icon_class.jpg);
	background-repeat: no-repeat;
	padding: 0 0 0 15px;
}

.icon_confirm {
	background: url(/ec-cube/img/contents/icon_confirm.jpg);
	background-repeat: no-repeat;
	padding: 0 0 0 15px;
}


/*send-page*/

.number a{
	background: url(/ec-cube/img/contents/number_bg.jpg);
	background-repeat: repeat-x;
	background-color: #505468;
	padding-top: 3px;
	padding-bottom: 5px;
	padding-left: 8px;
	padding-right: 8px;
	color:#ffffff;
	font-size: 65%;
	line-height: 160%;
	font-weight: bold;
	text-decoration:none;
}

.number a:hover{
	background: url(/ec-cube/img/contents/number_bg_on.jpg);
	background-repeat: repeat-x;
	background-color: #f7c600;
	padding-top: 3px;
	padding-bottom: 5px;
	padding-left: 8px;
	padding-right: 8px;
	color:#ffffff;
	font-size: 65%;
	line-height: 160%;
	font-weight: bold;
	text-decoration:none;
}

.number-on a{
	padding-top: 3px;
	padding-bottom: 5px;
	padding-left: 8px;
	padding-right: 8px;
	background: url(/ec-cube/img/contents/number_bg_on.jpg);
	background-repeat: repeat-x;
	background-color: #f7c600;
	color:#ffffff;
	font-size: 65%;
	line-height: 160%;
	font-weight: bold;
	text-decoration:none;
	
}

.number-on a:hover{
	background: url(/ec-cube/img/contents/number_bg_on.jpg);
	background-repeat: repeat-x;
	background-color: #f7c600;
	padding-top: 3px;
	padding-bottom: 5px;
	padding-left: 8px;
	padding-right: 8px;
	color:#ffffff;
	font-size: 65%;
	line-height: 160%;
	font-weight: bold;
	text-decoration:none;
}

</style><title>メンバー登録・編集</title>
<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();
//-->
</script>
</head>

<body bgcolor="#ffffff" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="">
<noscript>
<link rel="stylesheet" href="/ec-cube/admin/css/common.css" type="text/css" />
</noscript>

<div align="center">
<!--★★メインコンテンツ★★-->
<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="input.php" onsubmit="return fnRegistMember();">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="member_id" value="2">
<input type="hidden" name="pageno" value="1">
<input type="hidden" name="old_login_id" value="admin">
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
											<td bgcolor="#636469" width="400" class="fs14n"><span class="white"><!--コンテンツタイトル-->メンバー登録/編集</span></td>
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
											<td width="90" bgcolor="#f3f3f3">名前</td>
											<td width="337" bgcolor="#ffffff"><input type="text" name="name" size="30" class="box30" value="admin" maxlength="50"/>　<span class="red">※必須入力</span>
											</td>
										</tr>
										<tr class="fs12n">
											<td width="90" bgcolor="#f3f3f3">所属</td>
											<td width="337" bgcolor="#ffffff"><input type="text" name="department" size="30" class="box30" value="" maxlength="50"/>
											</td>
										</tr>
										<tr class="fs12">
											<td width="90" bgcolor="#f3f3f3">ログインＩＤ</td>
											<td width="337" bgcolor="#ffffff"><input type="text" name="login_id" size="20" class="box20"  value="admin" maxlength="50"/>　<span class="red">※必須入力</span><br />
											※半角英数字・15文字以内</td>
										</tr>
										<tr class="fs12">
											<td width="90" bgcolor="#f3f3f3">パスワード</td>
											<td width="337" bgcolor="#ffffff"><input type="password" name="password" size="20" class="box20" value="########" onfocus="fnClearText(this.name);" maxlength="50"/>　<span class="red">※必須入力</span><br />
											※半角英数字・15文字以内</td>
										</tr>
										<tr class="fs12n">
											<td width="90" bgcolor="#f3f3f3">管理権限</td>
											<td width="337" bgcolor="#ffffff"><select name="authority">
											<option value="" >選択してください</option>
											<option label="管理者" value="0" selected="selected">管理者</option>

											</select>　<span class="red">※必須入力</span></td>
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


