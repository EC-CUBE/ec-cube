<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="content-script-type" content="text/javascript">
<meta http-equiv="content-style-type" content="text/css">
<link rel="stylesheet" href="/user_data/css/contents.css" type="text/css" media="all" />
<link rel="stylesheet" href="/css/common.css" type="text/css" media="all" />
<link rel="stylesheet" href="/css/layout/index.css" type="text/css" media="all" />
<title>テストの店/TOPページ</title>
<meta name="author" content="">
<meta name="description" content="">
<meta name="keywords" content="">

<script type="text/javascript">
<!--
//-->
</script>
</head>

<body bgcolor="#ffe9e6" text="#555555" link="#3a75af" vlink="#3a75af" alink="#3a75af" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="preLoadImg('<!--{$smarty.const.URL_DIR}-->');">
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->css/common.css" type="text/css" />
</noscript>

<div align="center">
<a name="top" id="top"></a>

<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffa85c">
		<table width="762" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs10n"><span class="white"></span></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		</td>
	</tr>
	<tr><td bgcolor="#ff6600"><img src="/img/common/_.gif" width="778" height="1" alt=""></td></tr>
</table>
<!--▲HEADER-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc" width="1"><img src="/img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff" width="1"><img src="/img/_.gif" width="5" height="1" alt="" /></td>
		<td bgcolor="#ffffff" align="left" height="5" width=100%></td>
		<td bgcolor="#ffffff"><img src="./img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#cccccc"><img src="./img/_.gif" width="1" height="10" alt="" /></td>
		</td>
	</tr>
</table>
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr align="center">
		<td bgcolor="#cccccc" width="1"><img src="/img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff" width="1"><img src="/img/_.gif" width="5" height="1" alt="" /></td>
		<td bgcolor="#ffffff" align="left" height="5" width=100% align="center">
			<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td align="center" bgcolor="#ffffff">
<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#cccccc">
		<table width="630" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="5"></td></tr>
			<tr>
				<td align="center" bgcolor="#ffffff">
				<table width="604" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="13"></td></tr>
					<tr>
						<td>キャンペーン開催中</td>
					</tr>
					<tr><td height="20"></td></tr>
				</table>
				
				<table width="530" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td class="fs12">キャンペーン内容</td>
					</tr>
					<tr><td height="10"></td></tr>
				</table>
				</td>
			</tr>
			<tr><td height="5"></td></tr>
		</table>
		</td>
	</tr>
</table>
<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
<tr><td height="20"></td></tr>
</table>

<!--{* ▼商品ID27669 *}-->
<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<div id="cart_tag_27669">
<!--{assign var=id value=$arrProducts[27669].product_id}-->
<!--▼買い物かご-->
<table width="420" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td height=5></td></tr>
	<tr valign="top" align="right" id="price">
		<td id="right" colspan=2>
			<table cellspacing="0" cellpadding="0" summary=" " id="price">
				<tr>
					<td align="center">
					<table width="285" cellspacing="0" cellpadding="0" summary=" ">
						<!--{if $tpl_classcat_find1[$id]}-->
						<!--{assign var=class1 value=classcategory_id`$id`_1}-->
						<!--{assign var=class2 value=classcategory_id`$id`_2}-->
						<tr><td colspan="2" height="10" align="center" class="fs12"><span class="redst"><!--{if $arrErr[$class1] != ""}-->※ <!--{$tpl_class_name1[$id]}-->を入力して下さい。<!--{/if}--></span></td></tr>
						<tr>
							<td align="right" class="fs12st"><!--{$tpl_class_name1[$id]|escape}-->： </td>
							<td>
								<select name="<!--{$class1}-->" style="<!--{$arrErr[$class1]|sfGetErrorColor}-->" onchange="lnSetSelect('<!--{$class1}-->', '<!--{$class2}-->', '<!--{$id}-->','');">
								<option value="">選択してください</option>
								<!--{html_options options=$arrClassCat1[$id] selected=$arrForm[$class1]}-->
								</select>
							</td>
						</tr>
						<!--{/if}-->
						<!--{if $tpl_classcat_find2[$id]}-->
						<tr><td colspan="2" height="5" align="center" class="fs12"><span class="redst"><!--{if $arrErr[$class2] != ""}-->※ <!--{$tpl_class_name2[$id]}-->を入力して下さい。<!--{/if}--></span></td></tr>
						<tr>
							<td align="right" class="fs12st"><!--{$tpl_class_name2[$id]|escape}-->： </td>
							<td>
								<select name="<!--{$class2}-->" style="<!--{$arrErr[$class2]|sfGetErrorColor}-->">
								<option value="">選択してください</option>
								</select>
							</td>
						</tr>
						<!--{/if}-->
						<!--{assign var=quantity value=quantity`$id`}-->		
						<tr><td colspan="2" height="10" align="center" class="fs12"><span class="redst"><!--{$arrErr[$quantity]}--></span></td></tr>
						<tr>
							<td align="right" width="115" class="fs12st">個数： 
								<!--{if $arrErr.quantity != ""}--><br/><span class="redst"><!--{$arrErr.quantity}--></span><!--{/if}-->
								<input type="text" name="<!--{$quantity}-->" size="3" class="box3" value="<!--{$arrForm[$quantity]|default:1}-->" maxlength=<!--{$smarty.const.INT_LEN}--> style="<!--{$arrErr[$quantity]|sfGetErrorColor}-->" >
							</td>
							<td width="170" align="center">
								<a href="" onclick="fnChangeAction('<!--{$smarty.server.REQUEST_URI|escape}-->#product<!--{$id}-->'); fnModeSubmit('cart','product_id','<!--{$id}-->'); return false;" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/products/b_cartin_on.gif','cart<!--{$id}-->');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/products/b_cartin.gif','cart<!--{$id}-->');"><img src="<!--{$smarty.const.URL_DIR}-->img/products/b_cartin.gif" width="115" height="25" alt="カゴに入れる" name="cart<!--{$id}-->" id="cart<!--{$id}-->" /></a>
							</td>
						</tr>
						<tr><td height="10"></td></tr>
					</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<!--{* ▼商品ID30050 *}-->
<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<div id="cart_tag_30050">
<!--{assign var=id value=$arrProducts[30050].product_id}-->
<!--▼買い物かご-->
<table width="420" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td height=5></td></tr>
	<tr valign="top" align="right" id="price">
		<td id="right" colspan=2>
			<table cellspacing="0" cellpadding="0" summary=" " id="price">
				<tr>
					<td align="center">
					<table width="285" cellspacing="0" cellpadding="0" summary=" ">
						<!--{if $tpl_classcat_find1[$id]}-->
						<!--{assign var=class1 value=classcategory_id`$id`_1}-->
						<!--{assign var=class2 value=classcategory_id`$id`_2}-->
						<tr><td colspan="2" height="10" align="center" class="fs12"><span class="redst"><!--{if $arrErr[$class1] != ""}-->※ <!--{$tpl_class_name1[$id]}-->を入力して下さい。<!--{/if}--></span></td></tr>
						<tr>
							<td align="right" class="fs12st"><!--{$tpl_class_name1[$id]|escape}-->： </td>
							<td>
								<select name="<!--{$class1}-->" style="<!--{$arrErr[$class1]|sfGetErrorColor}-->" onchange="lnSetSelect('<!--{$class1}-->', '<!--{$class2}-->', '<!--{$id}-->','');">
								<option value="">選択してください</option>
								<!--{html_options options=$arrClassCat1[$id] selected=$arrForm[$class1]}-->
								</select>
							</td>
						</tr>
						<!--{/if}-->
						<!--{if $tpl_classcat_find2[$id]}-->
						<tr><td colspan="2" height="5" align="center" class="fs12"><span class="redst"><!--{if $arrErr[$class2] != ""}-->※ <!--{$tpl_class_name2[$id]}-->を入力して下さい。<!--{/if}--></span></td></tr>
						<tr>
							<td align="right" class="fs12st"><!--{$tpl_class_name2[$id]|escape}-->： </td>
							<td>
								<select name="<!--{$class2}-->" style="<!--{$arrErr[$class2]|sfGetErrorColor}-->">
								<option value="">選択してください</option>
								</select>
							</td>
						</tr>
						<!--{/if}-->
						<!--{assign var=quantity value=quantity`$id`}-->		
						<tr><td colspan="2" height="10" align="center" class="fs12"><span class="redst"><!--{$arrErr[$quantity]}--></span></td></tr>
						<tr>
							<td align="right" width="115" class="fs12st">個数： 
								<!--{if $arrErr.quantity != ""}--><br/><span class="redst"><!--{$arrErr.quantity}--></span><!--{/if}-->
								<input type="text" name="<!--{$quantity}-->" size="3" class="box3" value="<!--{$arrForm[$quantity]|default:1}-->" maxlength=<!--{$smarty.const.INT_LEN}--> style="<!--{$arrErr[$quantity]|sfGetErrorColor}-->" >
							</td>
							<td width="170" align="center">
								<a href="" onclick="fnChangeAction('<!--{$smarty.server.REQUEST_URI|escape}-->#product<!--{$id}-->'); fnModeSubmit('cart','product_id','<!--{$id}-->'); return false;" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/products/b_cartin_on.gif','cart<!--{$id}-->');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/products/b_cartin.gif','cart<!--{$id}-->');"><img src="<!--{$smarty.const.URL_DIR}-->img/products/b_cartin.gif" width="115" height="25" alt="カゴに入れる" name="cart<!--{$id}-->" id="cart<!--{$id}-->" /></a>
							</td>
						</tr>
						<tr><td height="10"></td></tr>
					</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div><!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
				</td>
				</tr>
			</table>
		</td>
		<td bgcolor="#ffffff"><img src="./img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#cccccc"><img src="./img/_.gif" width="1" height="10" alt="" /></td>
		</td>
	</tr>
</table>

<!--▼FOTTER-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td rowspan="3" bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
		<td align="center" bgcolor="#ffffff">
		<table width="762" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="30"></td></tr>
			<tr>
				<td align="right" class="fs10n"><a href="#top"><img src="/img/common/pagetop.gif" width="100" height="10" alt="このページのTOPへ" border="0"></a></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		</td>
		<td rowspan="3" bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
	</tr>
	<tr><td bgcolor="#ff6600"><img src="/img/common/_.gif" width="778" height="1" alt=""></td></tr>
	<tr>
		<td align="center" bgcolor="#ffa85c">
		<table width="762" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs10n"><span class="white"></span></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		</td>
	</tr>
</table>
<!--▲FOTTER-->
</div>
</body>
</html>