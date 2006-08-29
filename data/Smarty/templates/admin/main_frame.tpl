<!--　-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=EUC-JP" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_ADMIN_CSS}-->contents.css" type="text/css" />
<script type="text/javascript" src="/js/css.js"></script>
<script type="text/javascript" src="/js/navi.js"></script>
<script type="text/javascript" src="/js/win_op.js"></script>
<script type="text/javascript" src="/js/site.js"></script>
<script type="text/javascript" src="/js/admin.js"></script>

<title><!--{$smarty.const.ADMIN_TITLE}--></title>
<script language="JavaScript">
<!--
<!--{$tpl_javascript}-->
//-->
</script>

</head>

<body bgcolor="#ffffff" text="#000000" link="#006699" vlink="#006699" alink="#006699" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="preLoadImg(); <!--{$tpl_onload}-->">
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_ADMIN_CSS}-->common.css" type="text/css" />
</noscript>
<style type="text/css">
<!--

@charset "euc-jp";

body {
	background: #fff url(/img/common/bg.jpg);
	background-repeat: repeat-x;
}


/*LINK*/
a:link { color: #006699; text-decoration: none; }
a:visited { color: #006699; text-decoration: none; }
a:hover { color: #f9a406; text-decoration: underline; }


/*FORM*/
.box6 { width: 54px; }	/*W6*/
.box10 { width: 82px; }	/*W10*/
.box20 { width: 152px; }	/*W20*/
.box25 { width: 187px; }	/*W25*/
.box30 { width: 222px; }	/*W30*/
.box33 { width: 243px; }	/*W30*/
.box40 { width: 292px; }	/*W40*/
.box60 { width: 432px; }	/*W60*/

.area40 { width: 302px; height: 134px; }	/*W40×H8*/
.area45 { width: 337px; height: 290px; }	/*W40×H20*/
.area46 { width: 337px; height: 134px; }	/*W40×H8*/
.area59 { width: 432px; height: 134px; }	/*W59×H8*/
.area65 { width: 444px; height: 290px; }	/*W65×H20*/
.area80 { width: 572px; height: 134px; }	/*W80×H8*/
.area96 { width: 694px; height: 420px; }	/*W80×H30*/
.area96_2 { width: 694px; height: 160px; }	/*W80×H10*/


/*COLOR*/
.ast { color: #cc0000; font-size: 90%; }
.darkred { color: #cc0000; }
.gray { color: #b6b7ba; }
.white { color: #ffffff; }
.whitest { color: #ffffff; font-weight: bold; }
.white10 { color: #ffffff; font-size: 62.5%;}
.red { color: #ff0000; }
.red10 { color:#ff0000; font-size: 10px; }
.red12 { color:#cc0000; font-size: 12px; }
.reselt { color: #ffcc00; font-size: 120%; font-weight: bold; }

.infodate {
	color: #cccccc; font-size: 62.5%; font-weight: bold;
	padding: 0 0 0 8px;
}

.infottl {
	color: #ffffff;
	font-size: 62.5%;
	line-height: 150%;
}

.info {
	padding: 0 4px;
	display: block;
}

.title {
	padding: 100px 0 20px 25px;
	color: #ffffff;
	font-weight: bold;
	line-height: 120%;
}

.mainbg {
	background: #fff url(/img/contents/main_bg.jpg);
	background-repeat: repeat-x;
}

.infobg {
	background: #fff url(/img/contents/home_bg.jpg);
	background-repeat: no-repeat;
	background-color: #e3e3e3;
}


/*navi*/
.navi a{
	background: url(/img/contents/navi_bar.gif);
	background-repeat: repeat-y;
	background-color: #636469;
	width:140px;
	padding: 10px 5px 10px 12px;
	color:#ffffff;
	text-decoration:none;
}

.navi a:visited {
	color:#ffffff;
	text-decoration:none;
}
/*
.navi a:hover {
	background-color: #a5a5a5;
	color:#000000;
	text-decoration:none;
}
*/
.navi_text {
	font-size: 75%;
	padding: 0 0 0 8px;
}

.navi-on a{
	background: url(/img/contents/navi_bar.gif);
	background-repeat: repeat-y;
	background-color: #a5a5a5;
	width:140px;
	padding: 10px 5px 10px 12px;
	color:#000000;
	text-decoration:none;
}

.navi-on a:visited {
	color:#000000;
	text-decoration:none;
}
/*
.navi-on a:hover {
	background-color: #a5a5a5;
	color:#000000;
	text-decoration:none;
}
*/

/*subnavi*/
.subnavi a{
	background-color: #818287;
	width:140px;
	padding: 6px 5px 4px 5px;
	color:#ffffff;
	text-decoration:none;
}

.subnavi a:visited {
	color:#ffffff;
	text-decoration:none;
}
/*
.subnavi a:hover {
	background-color: #b7b7b7;
	color:#000000;
	text-decoration:none;
}
*/
.subnavi_text {
	font-size: 71%;
	padding: 0 0 0 8px;
}

.subnavi-on a{
	background-color: #b7b7b7;
	width:140px;
	padding: 6px 5px 4px 5px;
	color:#000000;
	text-decoration:none;
}

.subnavi-on a:visited {
	color:#000000;
	text-decoration:none;
}
/*
.subnavi-on a:hover {
	background-color: #b7b7b7;
	color:#000000;
	text-decoration:none;
}
*/


/*icon*/
.icon_edit{
	background: url(/img/contents/icon_edit.jpg);
	background-repeat: no-repeat;
	padding: 0 0 0 15px;
}

.icon_mail {
	background: url(/img/contents/icon_mail.jpg);
	background-repeat: no-repeat;
	padding: 0 0 0 15px;
}

.icon_delete {
	background: url(/img/contents/icon_delete.jpg);
	background-repeat: no-repeat;
	padding: 0 0 0 15px;
}

.icon_class {
	background: url(/img/contents/icon_class.jpg);
	background-repeat: no-repeat;
	padding: 0 0 0 15px;
}

.icon_confirm {
	background: url(/img/contents/icon_confirm.jpg);
	background-repeat: no-repeat;
	padding: 0 0 0 15px;
}


/*send-page*/
.number a{
	background: url(/img/contents/number_bg.jpg);
	background-repeat: repeat-x;
	background-color: #505468;
	padding: 8px;
	color:#ffffff;
	font-size: 65%;
	line-height: 160%;
	font-weight: bold;
	text-decoration:none;
}

.number a:visited {
	color:#ffffff;
	text-decoration:none;
}

.number a:hover{
	background: url(/img/contents/number_bg_on.jpg);
	background-repeat: repeat-x;
	background-color: #f7c600;
	padding: 8px;
	color:#ffffff;
	font-size: 65%;
	line-height: 160%;
	font-weight: bold;
	text-decoration:none;
}

.number-on a{
	background: url(/img/contents/number_bg_on.jpg);
	background-repeat: repeat-x;
	background-color: #f7c600;
	padding: 8px;
	color:#ffffff;
	font-size: 65%;
	line-height: 160%;
	font-weight: bold;
	text-decoration:none;
}

.number-on a:visited {
	color:#ffffff;
	text-decoration:none;
}

.number-on a:hover{
	background: url(/img/contents/number_bg_on.jpg);
	background-repeat: repeat-x;
	background-color: #f7c600;
	padding: 8px;
	color:#ffffff;
	font-size: 65%;
	line-height: 160%;
	font-weight: bold;
	text-decoration:none;
}

/*IMG*/
img {
border: 0;
}



-->
</style> 
<div align="center">
<a name="top"></a>

<!--{if $smarty.const.ADMIN_MODE == '1'}-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td bgcolor="#cc0000" height="3" colspan="2" align="center"><font color="#ffffff"><span class="fs12n">ADMIN_MODE ON</span></font></td></tr>
</table>
<!--{/if}-->

<!--▼HEADER-->
<table width="912" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td><img src="/img/header/header_left.jpg" width="17" height="82" alt=""></td>
		<td>
		<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" " background="/img/header/header_bg2.jpg">
			<tr valign="top">
				<td><a href="/admin/home.php"><img src="/img/admin/header/logo.jpg" width="230" height="50" alt="EC CUBE" border="0"></a></td>
				<td width="648" align="right">
				<!--ヘッダーナビ-->
				<table border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="11"></td></tr>
					<tr>
						<td><a href="<!--{$smarty.const.URL_HOME}-->" onmouseover="chgImg('/img/header/mainpage_on.gif','mainpage');" onmouseout="chgImg('/img/header/mainpage.gif','mainpage');"><img src="/img/header/mainpage.gif" width="69" height="17" alt="MAIN PAGE" border="0" name="mainpage" id="mainpage"></a></td>
						<td><img src="/img/common/_.gif" width="10" height="1" alt=""></td>
						<td><a href="<!--{$smarty.const.SITE_URL}-->" onmouseover="chgImg('/img/header/sitecheck_on.gif','sitecheck');" onmouseout="chgImg('/img/header/sitecheck.gif','sitecheck');" target="_blank"><img src="/img/header/sitecheck.gif" width="69" height="17" alt="SITE CHECK" border="0" name="sitecheck" id="sitecheck"></a></td>
						<td><img src="/img/header/welcome.gif" width="91" height="17" alt="WELCOME!"></td>
						<td class="fs12"><span class="whitest"><!--ログイン名--><!--{$smarty.session.login_name}-->&nbsp;様</span></td>
						<td><img src="/img/common/_.gif" width="10" height="1" alt=""></td>
						<td><a href="<!--{$smarty.const.URL_LOGOUT}-->" onmouseover="chgImg('/img/admin/header/logout_on.gif','logout');" onmouseout="chgImg('/img/admin/header/logout.gif','logout');"><img src="/img/admin/header/logout.gif" width="56" height="15" alt="LOGOUT" border="0" name="logout" id="logout"></a></td>
						<td><img src="/img/common/_.gif" width="15" height="1" alt=""></td>
					</tr>
				</table>
				<!--ヘッダーナビ-->
			</tr>
		</table>
		<!--▼NAVI-->
		<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><!--{if $tpl_mainno eq "basis"}--><a href="/admin/basis/index.php"><img src="/img/header/basis_on.jpg" width="98" height="32" alt="基本情報管理" border="0"></a><!--{else}--><a href="/admin/basis/index.php" onmouseover="chgImg('/img/header/basis_on.jpg','basis');" onmouseout="chgImg('/img/header/basis.jpg','basis');"><img src="/img/header/basis.jpg" width="98" height="32" alt="基本情報管理" border="0" name="basis" id="basis"></a><!--{/if}--></td>
				<td><!--{if $tpl_mainno eq "products"}--><a href="/admin/products/index.php"><img src="/img/header/product_on.jpg" width="98" height="32" alt="商品管理" border="0"></a><!--{else}--><a href="/admin/products/index.php" onmouseover="chgImg('/img/header/product_on.jpg','product');" onmouseout="chgImg('/img/header/product.jpg','product');"><img src="/img/header/product.jpg" width="98" height="32" alt="商品管理" border="0" name="product" id="product"></a><!--{/if}--></td>
				<td><!--{if $tpl_mainno eq "customer"}--><a href="/admin/customer/index.php"><img src="/img/header/customer_on.jpg" width="98" height="32" alt="顧客管理" border="0"></a><!--{else}--><a href="/admin/customer/index.php" onmouseover="chgImg('/img/header/customer_on.jpg','customer');" onmouseout="chgImg('/img/header/customer.jpg','customer');"><img src="/img/header/customer.jpg" width="98" height="32" alt="顧客管理" border="0" name="customer" id="customer"></a><!--{/if}--></td>
				<td><!--{if $tpl_mainno eq "order"}--><a href="/admin/order/index.php"><img src="/img/header/order_on.jpg" width="98" height="32" alt="受注管理" border="0"></a><!--{else}--><a href="/admin/order/index.php" onmouseover="chgImg('/img/header/order_on.jpg','order');" onmouseout="chgImg('/img/header/order.jpg','order');"><img src="/img/header/order.jpg" width="98" height="32" alt="受注管理" border="0" name="order" id="order"></a><!--{/if}--></td>
				<td><!--{if $tpl_mainno eq "total"}--><a href="/admin/total/index.php"><img src="/img/header/sales_on.jpg" width="98" height="32" alt="売上集計" border="0"></a><!--{else}--><a href="/admin/total/index.php" onmouseover="chgImg('/img/header/sales_on.jpg','sales');" onmouseout="chgImg('/img/header/sales.jpg','sales');"><img src="/img/header/sales.jpg" width="98" height="32" alt="売上集計" border="0" name="sales" id="sales"></a><!--{/if}--></td>
				<td><!--{if $tpl_mainno eq "mail"}--><a href="/admin/mail/index.php"><img src="/img/header/mail_on.jpg" width="97" height="32" alt="メルマガ管理" border="0"></a><!--{else}--><a href="/admin/mail/index.php" onmouseover="chgImg('/img/header/mail_on.jpg','mail');" onmouseout="chgImg('/img/header/mail.jpg','mail');"><img src="/img/header/mail.jpg" width="97" height="32" alt="メルマガ管理" border="0" name="mail" id="mail"></a><!--{/if}--></td>
				<td><!--{if $tpl_mainno eq "contents"}--><a href="/admin/contents/index.php"><img src="/img/header/contents_on.jpg" width="97" height="32" alt="コンテンツ管理" border="0"></a><!--{else}--><a href="/admin/contents/index.php" onmouseover="chgImg('/img/header/contents_on.jpg','contents');" onmouseout="chgImg('/img/header/contents.jpg','contents');"><img src="/img/header/contents.jpg" width="97" height="32" alt="コンテンツ管理" border="0" name="contents" id="contents"></a><!--{/if}--></td>
				<td><!--{if $tpl_mainno eq "design"}--><a href="/admin/design/index.php"><img src="/img/header/design_on.jpg" width="97" height="32" alt="デザイン管理" border="0"></a><!--{else}--><a href="/admin/design/index.php" onmouseover="chgImg('/img/header/design_on.jpg','design');" onmouseout="chgImg('/img/header/design.jpg','design');"><img src="/img/header/design.jpg" width="97" height="32" alt="デザイン管理" border="0" name="design" id="design"></a><!--{/if}--></td>
				<td><!--{if $tpl_mainno eq "system"}--><a href="/admin/system/index.php"><img src="/img/header/system_on.jpg" width="97" height="32" alt="システム設定" border="0"></a><!--{else}--><a href="/admin/system/index.php" onmouseover="chgImg('/img/header/system_on.jpg','system');" onmouseout="chgImg('/img/header/system.jpg','system');"><img src="/img/header/system.jpg" width="97" height="32" alt="システム設定" border="0" name="system" id="system"></a><!--{/if}--></td>
			</tr>
		</table>
		<!--▲NAVI-->
		</td>
		<td><img src="/img/header/header_right.jpg" width="17" height="82" alt=""></td>
	</tr>
</table>
<!--▲HEADER-->

<!--▼CONTENTS-->
<table width="912" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td background="/img/common/left_bg.jpg"><img src="/img/common/left.jpg" width="17" height="443" alt=""></td>
		<td>
		<!--{if $smarty.server.REQUEST_URI != $smarty.const.URL_HOME}-->
			<!--★★タイトル★★-->
			<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr>
					<!--{assign var=title_image value="/img/title/title_`$tpl_mainno`.jpg"}-->
					<td bgcolor="#525363"><!--タイトル画像--><img src="<!--{$title_image}-->" width="141" height="33" ></td>
					<td background="/img/title/subtitle_bg.gif">
					<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
						<tr><td><img src="/img/title/subtitle_top.gif" width="737" height="8" alt=""></td></tr>
						<tr>
							<td class="fs14n"><span class="title"><!--サブタイトル--><!--{$tpl_subtitle}--></span></td>
						</tr>
						<tr><td><img src="/img/title/subtitle_bottom.gif" width="737" height="8" alt=""></td></tr>
					</table>
				  </td>
				</tr>
				<tr><td colspan="2"><img src="/img/title/bar.gif" width="878" height="9" alt=""></td></tr>
			</table>
			<!--★★タイトル★★-->
		<!--{/if}-->
		<!--{include file=$tpl_mainpage}-->
		</td>
		<td background="/img/common/right_bg.jpg"><div align="justify"><img src="/img/common/right.jpg" width="17" height="443" alt=""></div></td>
	</tr>
</table>
<!--▲CONTENTS-->

<!--▼FOOTER-->
<table width="912" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td background="/img/common/left_bg.jpg"><img src="/img/common/_.gif" width="17" height="1" alt=""></td>
		<td bgcolor="#636469">
		<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td align="center" bgcolor="#f0f0f0">
				<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td height="45" align="right"><a href="#top"><img src="/img/admin/common/pagetop.gif" width="105" height="17" alt="GO TO PAGE TOP" border="0"></a></td>					
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<table width="878" border="0" cellspacing="0" cellpadding="10" summary=" ">
			<tr>
				<td class="fs10n"><span class="gray">&nbsp;Copyright &copy; 2000-2006 LOCKON CO.,LTD. All Rights Reserved.</span></td>
			</tr>
		</table>
		</td>
		<td background="/img/common/right_bg.jpg"><img src="/img/common/_.gif" width="17" height="1" alt=""></td>
	</tr>
	<tr>
		<td colspan="3"><img src="/img/common/fotter.jpg" width="912" height="19" alt=""></td>
	</tr>
	<tr><td height="10"></td></tr>
</table>
<!--▲FOOTER-->

</div>

</body>
</html>
