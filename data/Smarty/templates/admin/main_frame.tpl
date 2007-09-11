<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->">
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->admin/css/contents.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->admin/css/file_manager.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/admin.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/file_manager.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/jquery.js"></script>

<!--{include file='css/contents.tpl'}-->

<title><!--{$smarty.const.ADMIN_TITLE}--></title>
<script language="JavaScript">
<!--
<!--{$tpl_javascript}-->
//-->
</script>

</head>

<body bgcolor="#ffffff" text="#000000" link="#006699" vlink="#006699" alink="#006699" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="preLoadImg('<!--{$smarty.const.URL_DIR}-->'); <!--{$tpl_onload}-->">
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_ADMIN_CSS}-->common.css" type="text/css" />
</noscript>
<div align="center" class="" id="">
<a name="top"></a>

<!--{if $smarty.const.ADMIN_MODE == '1'}-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td bgcolor="#cc0000" height="3" colspan="2" align="center"><font color="#ffffff"><span class="fs12n">ADMIN_MODE ON</span></font></td></tr>
</table>
<!--{/if}-->

<!--▼HEADER-->
<table width="912" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td><img src="<!--{$smarty.const.URL_DIR}-->img/header/header_left.jpg" width="17" height="82" alt=""></td>
		<td>
		<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" " background="<!--{$smarty.const.URL_DIR}-->img/header/header_bg2.jpg">
			<tr valign="top">
				<td><a href="<!--{$smarty.const.URL_DIR}-->admin/home.php"><img src="<!--{$smarty.const.URL_DIR}-->img/admin/header/logo.jpg" width="230" height="50" alt="EC CUBE" border="0"></a></td>
				<td width="648" align="right">
				<!--ヘッダーナビ-->
				<table border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="11"></td></tr>
					<tr>
						<td><a href="<!--{$smarty.const.URL_HOME}-->" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/mainpage_on.gif','mainpage');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/mainpage.gif','mainpage');"><img src="<!--{$smarty.const.URL_DIR}-->img/header/mainpage.gif" width="69" height="17" alt="MAIN PAGE" border="0" name="mainpage" id="mainpage"></a></td>
						<td><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="10" height="1" alt=""></td>
						<td><a href="<!--{$smarty.const.SITE_URL}-->" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/sitecheck_on.gif','sitecheck');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/sitecheck.gif','sitecheck');" target="_blank"><img src="<!--{$smarty.const.URL_DIR}-->img/header/sitecheck.gif" width="69" height="17" alt="SITE CHECK" border="0" name="sitecheck" id="sitecheck"></a></td>
						<td><img src="<!--{$smarty.const.URL_DIR}-->img/header/welcome.gif" width="91" height="17" alt="WELCOME!"></td>
						<td class="fs12"><span class="whitest"><!--ログイン名--><!--{$smarty.session.login_name|escape}-->&nbsp;様</span></td>
						<td><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="10" height="1" alt=""></td>
						<td><a href="<!--{$smarty.const.URL_LOGOUT}-->" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/admin/header/logout_on.gif','logout');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/admin/header/logout.gif','logout');"><img src="<!--{$smarty.const.URL_DIR}-->img/admin/header/logout.gif" width="56" height="15" alt="LOGOUT" border="0" name="logout" id="logout"></a></td>
						<td><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="15" height="1" alt=""></td>
					</tr>
				</table>
				<!--ヘッダーナビ-->
			</tr>
		</table>
		<!--▼NAVI-->
		<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><!--{if $tpl_mainno eq "basis"}--><a href="<!--{$smarty.const.URL_DIR}-->admin/basis/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/header/basis_on.jpg" width="98" height="32" alt="基本情報管理" border="0"></a><!--{else}--><a href="<!--{$smarty.const.URL_DIR}-->admin/basis/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/basis_on.jpg','basis');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/basis.jpg','basis');"><img src="<!--{$smarty.const.URL_DIR}-->img/header/basis.jpg" width="98" height="32" alt="基本情報管理" border="0" name="basis" id="basis"></a><!--{/if}--></td>
				<td><!--{if $tpl_mainno eq "products"}--><a href="<!--{$smarty.const.URL_DIR}-->admin/products/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/header/product_on.jpg" width="98" height="32" alt="商品管理" border="0"></a><!--{else}--><a href="<!--{$smarty.const.URL_DIR}-->admin/products/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/product_on.jpg','product');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/product.jpg','product');"><img src="<!--{$smarty.const.URL_DIR}-->img/header/product.jpg" width="98" height="32" alt="商品管理" border="0" name="product" id="product"></a><!--{/if}--></td>
				<td><!--{if $tpl_mainno eq "customer"}--><a href="<!--{$smarty.const.URL_DIR}-->admin/customer/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/header/customer_on.jpg" width="98" height="32" alt="顧客管理" border="0"></a><!--{else}--><a href="<!--{$smarty.const.URL_DIR}-->admin/customer/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/customer_on.jpg','customer');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/customer.jpg','customer');"><img src="<!--{$smarty.const.URL_DIR}-->img/header/customer.jpg" width="98" height="32" alt="顧客管理" border="0" name="customer" id="customer"></a><!--{/if}--></td>
				<td><!--{if $tpl_mainno eq "order"}--><a href="<!--{$smarty.const.URL_DIR}-->admin/order/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/header/order_on.jpg" width="98" height="32" alt="受注管理" border="0"></a><!--{else}--><a href="<!--{$smarty.const.URL_DIR}-->admin/order/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/order_on.jpg','order');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/order.jpg','order');"><img src="<!--{$smarty.const.URL_DIR}-->img/header/order.jpg" width="98" height="32" alt="受注管理" border="0" name="order" id="order"></a><!--{/if}--></td>
				<td><!--{if $tpl_mainno eq "total"}--><a href="<!--{$smarty.const.URL_DIR}-->admin/total/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/header/sales_on.jpg" width="98" height="32" alt="売上集計" border="0"></a><!--{else}--><a href="<!--{$smarty.const.URL_DIR}-->admin/total/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/sales_on.jpg','sales');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/sales.jpg','sales');"><img src="<!--{$smarty.const.URL_DIR}-->img/header/sales.jpg" width="98" height="32" alt="売上集計" border="0" name="sales" id="sales"></a><!--{/if}--></td>
				<td><!--{if $tpl_mainno eq "mail"}--><a href="<!--{$smarty.const.URL_DIR}-->admin/mail/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/header/mail_on.jpg" width="97" height="32" alt="メルマガ管理" border="0"></a><!--{else}--><a href="<!--{$smarty.const.URL_DIR}-->admin/mail/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/mail_on.jpg','mail');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/mail.jpg','mail');"><img src="<!--{$smarty.const.URL_DIR}-->img/header/mail.jpg" width="97" height="32" alt="メルマガ管理" border="0" name="mail" id="mail"></a><!--{/if}--></td>
				<td><!--{if $tpl_mainno eq "contents"}--><a href="<!--{$smarty.const.URL_DIR}-->admin/contents/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/header/contents_on.jpg" width="97" height="32" alt="コンテンツ管理" border="0"></a><!--{else}--><a href="<!--{$smarty.const.URL_DIR}-->admin/contents/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/contents_on.jpg','contents');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/contents.jpg','contents');"><img src="<!--{$smarty.const.URL_DIR}-->img/header/contents.jpg" width="97" height="32" alt="コンテンツ管理" border="0" name="contents" id="contents"></a><!--{/if}--></td>
				<td><!--{if $tpl_mainno eq "design"}--><a href="<!--{$smarty.const.URL_DIR}-->admin/design/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/header/design_on.jpg" width="97" height="32" alt="デザイン管理" border="0"></a><!--{else}--><a href="<!--{$smarty.const.URL_DIR}-->admin/design/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/design_on.jpg','design');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/design.jpg','design');"><img src="<!--{$smarty.const.URL_DIR}-->img/header/design.jpg" width="97" height="32" alt="デザイン管理" border="0" name="design" id="design"></a><!--{/if}--></td>
				<td><!--{if $tpl_mainno eq "system"}--><a href="<!--{$smarty.const.URL_DIR}-->admin/system/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/header/system_on.jpg" width="97" height="32" alt="システム設定" border="0"></a><!--{else}--><a href="<!--{$smarty.const.URL_DIR}-->admin/system/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/system_on.jpg','system');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/header/system.jpg','system');"><img src="<!--{$smarty.const.URL_DIR}-->img/header/system.jpg" width="97" height="32" alt="システム設定" border="0" name="system" id="system"></a><!--{/if}--></td>
			</tr>
		</table>
		<!--▲NAVI-->
		</td>
		<td><img src="<!--{$smarty.const.URL_DIR}-->img/header/header_right.jpg" width="17" height="82" alt=""></td>
	</tr>
</table>
<!--▲HEADER-->

<!--▼CONTENTS-->
<table width="912" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/common/left_bg.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/left.jpg" width="17" height="443" alt=""></td>
		<td>
		<!--{if $smarty.server.REQUEST_URI != $smarty.const.URL_HOME}-->
			<!--★★タイトル★★-->
			<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr>
					<!--{assign var=title_image value="`$smarty.const.URL_DIR`img/title/title_`$tpl_mainno`.jpg"}-->
					<td bgcolor="#525363"><!--タイトル画像--><img src="<!--{$title_image}-->" width="141" height="33" ></td>
					<td background="<!--{$smarty.const.URL_DIR}-->img/title/subtitle_bg.gif">
					<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
						<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/title/subtitle_top.gif" width="737" height="8" alt=""></td></tr>
						<tr>
							<td class="fs14n"><span class="title"><!--サブタイトル--><!--{$tpl_subtitle}--></span></td>
						</tr>
						<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/title/subtitle_bottom.gif" width="737" height="8" alt=""></td></tr>
					</table>
				  </td>
				</tr>
				<tr><td colspan="2"><img src="<!--{$smarty.const.URL_DIR}-->img/title/bar.gif" width="878" height="9" alt=""></td></tr>
			</table>
			<!--★★タイトル★★-->
		<!--{/if}-->
		<!--{include file=$tpl_mainpage}-->
		</td>
		<td background="<!--{$smarty.const.URL_DIR}-->img/common/right_bg.jpg"><div align="justify" class="" id=""><img src="<!--{$smarty.const.URL_DIR}-->img/common/right.jpg" width="17" height="443" alt=""></div></td>
	</tr>
</table>
<!--▲CONTENTS-->

<!--▼FOOTER-->
<table width="912" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/common/left_bg.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="17" height="1" alt=""></td>
		<td bgcolor="#636469">
		<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td align="center" bgcolor="#f0f0f0">
				<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td height="45" align="right"><a href="#top"><img src="<!--{$smarty.const.URL_DIR}-->img/admin/common/pagetop.gif" width="105" height="17" alt="GO TO PAGE TOP" border="0"></a></td>					
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<table width="878" border="0" cellspacing="0" cellpadding="10" summary=" ">
			<tr>
				<td class="fs10n"><span class="gray">&nbsp;Copyright &copy; 2000-2007 LOCKON CO.,LTD. All Rights Reserved.</span></td>
			</tr>
		</table>
		</td>
		<td background="<!--{$smarty.const.URL_DIR}-->img/common/right_bg.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="17" height="1" alt=""></td>
	</tr>
	<tr>
		<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/common/fotter.jpg" width="912" height="19" alt=""></td>
	</tr>
	<tr><td height="10"></td></tr>
</table>
<!--▲FOOTER-->

</div>

</body>
</html>
