　
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<!--{*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=EUC-JP" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="./css/contents.css" type="text/css" />
<script type="text/javascript" src="./js/css.js"></script>
<title>ECサイト管理者ページ</title>
</head>

<body bgcolor="#f3f3f3" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<noscript>
<link rel="stylesheet" href="./css/common.css" type="text/css" />
</noscript>

<div align="center">
<!--▼HEADER-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td bgcolor="#bfbf9f" height="3" colspan="2"></td></tr>
	<tr bgcolor="#dedecd">
		<td height="40" width="365"><a href="./home.php"><img src="./misc/logo.gif" width="365" height="16" alt="ECサイト管理者ページ" border="0" /></a></td>
		<td width="415" align="right"><a href="./home.php" onmouseover="chgImg('./misc/home_on.gif','home');" onmouseout="chgImg('./misc/home.gif','home');"><img src="./misc/home.gif" width="70" height="20" alt="HOME" border="0" name="home" id="home" /></a><img src="./misc/_.gif" width="3" height="1" alt="" />
		<a href="#" onmouseover="chgImg('./misc/hp_on.gif','hp');" onmouseout="chgImg('./misc/hp.gif','hp');" target="_blank"><img src="./misc/hp.gif" width="70" height="20" alt="HPを見る" border="0" name="hp" id="hp" /></a><img src="./misc/_.gif" width="3" height="1" alt="" />
		<a href="./index.html" onmouseover="chgImg('./misc/logout_on.gif','logout');" onmouseout="chgImg('./misc/logout.gif','logout');"><img src="./misc/logout.gif" width="70" height="20" alt="ログアウト" border="0" name="logout" id="logout" /></a><img src="./misc/_.gif" width="20" height="1" alt="" /></td>
	</tr>
	<tr><td bgcolor="#ffffff" height="1" colspan="3"></td></tr>
</table>
<!--▲HEADER-->

<!--▼MAIN NAVI-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td><a href="./basis/index.php" onmouseover="chgImg('./misc/basis_on.gif','basis');" onmouseout="chgImg('./misc/basis.gif','basis');"><img src="./misc/basis.gif" width="108" height="26" alt="基本情報" border="0" name="basis" id="basis" /></a></td>
		<td><a href="./product/index.php" onmouseover="chgImg('./misc/product_on.gif','product');" onmouseout="chgImg('./misc/product.gif','product');"><img src="./misc/product.gif" width="108" height="26" alt="商品管理" border="0" name="product" id="product" /></a></td>
		<td><a href="./customer/index.php" onmouseover="chgImg('./misc/customer_on.gif','customer');" onmouseout="chgImg('./misc/customer.gif','customer');"><img src="./misc/customer.gif" width="108" height="26" alt="顧客管理" border="0" name="customer" id="customer" /></a></td>
		<td><a href="./order/index.php" onmouseover="chgImg('./misc/order_on.gif','order');" onmouseout="chgImg('./misc/order.gif','order');"><img src="./misc/order.gif" width="108" height="26" alt="受注管理" border="0" name="order" id="order" /></a></td>
		<td><a href="./sales/index.php" onmouseover="chgImg('./misc/sales_on.gif','sales');" onmouseout="chgImg('./misc/sales.gif','sales');"><img src="./misc/sales.gif" width="108" height="26" alt="売上管理" border="0" name="sales" id="sales" /></a></td>
		<td><a href="./mail/index.php" onmouseover="chgImg('./misc/mail_on.gif','mail');" onmouseout="chgImg('./misc/mail.gif','mail');"><img src="./misc/mail.gif" width="108" height="26" alt="メール配信" border="0" name="mail" id="mail" /></a></td>
		<td><a href="./contents/index.php" onmouseover="chgImg('./misc/contents_on.gif','contents');" onmouseout="chgImg('./misc/contents.gif','contents');"><img src="./misc/contents.gif" width="132" height="26" alt="コンテンツ管理" border="0" name="contents" id="contents" /></a></td>
	</tr>
	<tr><td colspan="7" height="1" bgcolor="#dedecd"></td></tr>
	<tr><td colspan="7" bgcolor="#ffffff" height="15"></td></tr>
</table>
<!--▲MAIN NAVI-->

<!--▼CONTENTS-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td height="350" bgcolor="#ffffff" align="center" valign="top">
		<form name="form1" id="form1" method="post" action="">
		<table width="600" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="30"></td></tr>
			<tr>
				<td height="20" class="fs12n"><span class="red">お手数ですが、下記項目を記入または修正してください。</span></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td height="20" class="fs12">お名前を入力してください。<br />
				電話番号は正しく入力してください。</td>
			</tr>
		</table>
		<br />
		<input type="button" name="subm" value="戻る" onclick="#" />
		</form>
		</td>
	</tr>
</table>

<!--▼FOOTER-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td bgcolor="#ffffff" height="60"></td></tr>
	<tr><td bgcolor="#e5e5df" height="5"></td></tr>
</table>
<!--▲FOOTER-->
</div>
</body>
</html>