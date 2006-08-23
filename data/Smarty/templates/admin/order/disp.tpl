!--　-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="content-script-type" content="text/javascript">
<meta http-equiv="content-style-type" content="text/css">
<link rel="stylesheet" href="./css/contents.css" type="text/css">
<script type="text/javascript" src="/js/css.js"></script>
<script type="text/javascript" src="/js/navi.js"></script>
<style type="text/css">
body {
	background: #fff url(/img/login/bg.jpg);
	background-repeat: repeat-x;
}
</style>

<title>EC CUBE 管理者画面</title>
</head>

<body bgcolor="#ffffff" text="#494E5F" link="#006699" vlink="#006699" alink="#006699" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="preLoadImg()">
<noscript>
<link rel="stylesheet" href="/admin/css/common.css" type="text/css" >
</noscript>
<div align="center">

<!--▼CONTENTS-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#ffffff" align="center" valign="top" height="400">
		
		<!--▼MAIN CONTENTS-->
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="25"></td></tr>
			<tr>
				<td class="fs14n"><strong>■受注詳細</strong></td>
			</tr>
			<tr><td height="25"></td></tr>
		</table>
						
		<!--▼お客様情報ここから-->
		<table width="740" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">対応状況</td>
				<td bgcolor="#ffffff">
					<!--{if $arrDisp.delete == 1}-->削除済み
					<!--{else}-->
					<!--{assign var=status value=`$arrForm.status.value`}-->
					<!--{$arrORDERSTATUS[$status]}-->
					<!--{/if}-->
				</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">発送日</td>
				<td bgcolor="#ffffff"><!--{$arrDisp.commit_date|sfDispDBDate|default:"未発送"}--></td>
			</tr>
		</table>
		<br />
		
		<table width="740" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" colspan="4">▼お客様情報</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">受注番号</td>
				<td bgcolor="#ffffff" width="248"><!--{$arrDisp.order_id}--></td>
				<td bgcolor="#f0f0f0" width="110">顧客ID</td>
				<td bgcolor="#ffffff" width="249">
				<!--{if $arrDisp.customer_id > 0}-->
					<!--{$arrDisp.customer_id}-->
				<!--{else}-->
					（非会員）
				<!--{/if}-->
				</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0">受注日</td>
				<td bgcolor="#ffffff" colspan="3"><!--{$arrDisp.create_date|sfDispDBDate}--></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" >顧客名</td>
				<td bgcolor="#ffffff" ><!--{$arrDisp.order_name01|escape}--> <!--{$arrDisp.order_name02|escape}--></td>
				<td bgcolor="#f0f0f0" >顧客名（カナ）</td>
				<td bgcolor="#ffffff" ><!--{$arrDisp.order_kana01|escape}--> <!--{$arrDisp.order_kana02|escape}--></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" >メールアドレス</td>
				<td bgcolor="#ffffff" ><a href="mailto:<!--{$arrDisp.order_email|escape}-->"><!--{$arrDisp.order_email|escape}--></a></td>
				<td bgcolor="#f0f0f0" >TEL</td>
				<td bgcolor="#ffffff" ><!--{$arrDisp.order_tel01}-->-<!--{$arrDisp.order_tel02}-->-<!--{$arrDisp.order_tel03}--></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" >住所</td>
				<td bgcolor="#ffffff" colspan="3">〒<!--{$arrDisp.order_zip01}-->-<!--{$arrDisp.order_zip02}--><br>
				<!--{assign var=key value=$arrDisp.order_pref}-->
				<!--{$arrPref[$key]}--><!--{$arrDisp.order_addr01}--><!--{$arrDisp.order_addr02}--></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" >備考</td>
				<td bgcolor="#ffffff" colspan="3"><!--{$arrDisp.message|escape|nl2br}--></td>
			</tr>
		</table>
		<!--▲お客様情報ここまで-->
		
		<br />
		
		<!--▼配送先情報ここから-->
		<table width="740" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="740" colspan="4">▼配送先情報</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">お名前</td>
				<td bgcolor="#ffffff" width="248">
				<!--{assign var=key1 value="deliv_name01"}-->
				<!--{assign var=key2 value="deliv_name02"}-->
				<!--{$arrForm[$key1].value|escape}-->&nbsp;<!--{$arrForm[$key2].value|escape}-->
				</td>
				<td bgcolor="#f0f0f0" width="110">お名前（カナ）</td>
				<td bgcolor="#ffffff" width="249">
				<!--{assign var=key1 value="deliv_kana01"}-->
				<!--{assign var=key2 value="deliv_kana02"}-->
				<!--{$arrForm[$key1].value|escape}-->&nbsp;<!--{$arrForm[$key2].value|escape}-->
				</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" >郵便番号</td>
				<td bgcolor="#ffffff" >
				<!--{assign var=key1 value="deliv_zip01"}-->
				<!--{assign var=key2 value="deliv_zip02"}-->
				〒<!--{$arrForm[$key1].value}-->-<!--{$arrForm[$key2].value}-->
				</td>
				<td bgcolor="#f0f0f0" >TEL</td>
				<td bgcolor="#ffffff" >
				<!--{assign var=key1 value="deliv_tel01"}-->
				<!--{assign var=key2 value="deliv_tel02"}-->
				<!--{assign var=key3 value="deliv_tel03"}-->
				<!--{$arrForm[$key1].value}-->-<!--{$arrForm[$key2].value}-->-<!--{$arrForm[$key3].value}-->
				</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" >住所</td>
				<td bgcolor="#ffffff"  colspan="3">
				<!--{assign var=pref value=`$arrForm.deliv_pref.value`}-->
				<!--{$arrPref[$pref]}-->
				<!--{assign var=key value="deliv_addr01"}-->
				<!--{$arrForm[$key].value|escape}-->
				<!--{assign var=key value="deliv_addr02"}-->
				<!--{$arrForm[$key].value|escape}-->
				</td>
			</tr>
		</table>
		<!--▲配送先情報ここまで-->
		
		<br />
		
		<!--▼受注商品情報ここから-->
		<table width="740" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="717" colspan="7">▼受注商品情報</td>
			</tr>
			<tr bgcolor="#f0f0f0" align="center" class="fs12n">
				<td width="140">商品コード</td>
				<td width="215">商品名/規格1/規格2</td>
				<td width="84">単価</td>
				<td width="45">数量</td>
				<td width="94">小計</td>
			</tr>
			<!--{section name=cnt loop=$arrForm.quantity.value}-->
			<!--{assign var=key value="`$smarty.section.cnt.index`"}-->
			<tr bgcolor="#ffffff" class="fs12">
				<td width="140"><!--{$arrDisp.product_code[$key]|escape}--></td>
				<td width="215"><!--{$arrDisp.product_name[$key]|escape}-->/<!--{$arrDisp.classcategory_name1[$key]|escape|default:"(なし)"}-->/<!--{$arrDisp.classcategory_name2[$key]|escape|default:"(なし)"}--></td>
				<td width="84" align="center"><!--{if $arrForm.price.value[$key] != 0}--><!--{$arrForm.price.value[$key]|escape}-->円<!--{else}-->無料<!--{/if}--></td>
				<td width="45" align="center"><!--{$arrForm.quantity.value[$key]|escape}--></td>
				<!--{assign var=price value=`$arrForm.price.value[$key]`}-->
				<!--{assign var=quantity value=`$arrForm.quantity.value[$key]`}-->
				<td width="94" align="right"><!--{if $price != 0}--><!--{$price|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|sfMultiply:$quantity|number_format}-->円<!--{else}-->無料<!--{/if}--></td>
			</tr>
			<!--{/section}-->
			<tr bgcolor="#ffffff" class="fs12n">
				<td colspan="4" align="right">小計</td>
				<td align="right"><!--{$arrForm.subtotal.value|number_format}-->円</td>
			</tr>
			<tr bgcolor="#ffffff" class="fs12n">
				<td colspan="4" align="right">ポイント値引き</td>
				<td align="right"><!--{assign var=point_discount value="`$arrForm.use_point.value*$smarty.const.POINT_VALUE`"}--><!--{$point_discount}-->円</td>
			</tr>
			<!--{assign var=discount value="`$arrForm.discount.value`"}-->
			<!--{if $discount != "" && $discount > 0}-->
 			<tr bgcolor="#ffffff" class="fs12n">
				<td colspan="4" align="right">値引き</td>
				<td align="right"><!--{$discount}-->円</td>
			</tr>
			<!--{/if}-->
			<tr bgcolor="#ffffff" class="fs12n">
				<td colspan="4" align="right">送料</td>
				<td align="right"><!--{assign var=key value="deliv_fee"}--><!--{$arrForm[$key].value|escape|number_format}--> 円</td>
			</tr>
			<tr bgcolor="#ffffff" class="fs12n">
				<td colspan="4" align="right">手数料</td>
				<td align="right"><!--{assign var=key value="charge"}-->
			<span class="red12"><!--{$arrErr[$key]}--></span><!--{$arrForm[$key].value|escape|number_format}--> 円</td>
			</tr>
			<tr bgcolor="#ffffff" class="fs12n">
				<td colspan="4" align="right">合計</td>
				<td align="right"><!--{$arrForm.total.value|number_format}--> 円</td>
			</tr>
			<tr bgcolor="#ffffff" class="fs12n">
				<td colspan="4" align="right">お支払い合計</td>
				<td align="right"><!--{$arrForm.payment_total.value|number_format}--> 円</td>
			</tr>
			<tr bgcolor="#ffffff" class="fs12n">
				<td colspan="4" align="right">使用ポイント</td>
				<td align="right"><!--{assign var=key value="use_point"}--><!--{if $arrForm[$key].value != ""}--><!--{$arrForm[$key].value}--><!--{else}-->0<!--{/if}--> pt</td>
			</tr>
			<!--{if $arrForm.birth_point.value > 0}-->
			<tr bgcolor="#ffffff" class="fs12n">
				<td colspan="4" align="right">お誕生日ポイント</td>
				<td align="right">
				<!--{$arrForm.birth_point.value}-->
				 pt</td>
			</tr>
			<!--{/if}-->
			<tr bgcolor="#ffffff" class="fs12n">
				<td colspan="4" align="right">加算ポイント</td>
				<td align="right">
				<!--{$arrForm.add_point.value|default:0}-->
				 pt</td>
			</tr>
			<tr bgcolor="#ffffff" class="fs12n">
				<!--{if $arrDisp.customer_id > 0}-->
				<td colspan="4" align="right">現在ポイント</td>
				<td align="right">
				<!--{$arrForm.point.value}-->
				 pt</td>
				<!--{else}-->
				<td colspan="4" align="right">現在ポイント</td><td align="center">（なし）</td>
				<!--{/if}-->
			</tr>
			<!--{*
			<tr bgcolor="#ffffff" class="fs12n">
				<td colspan="5" align="right">反映後ポイント（ポイントの変更は<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="return fnEdit('<!--{$arrDisp.customer_id}-->');">顧客編集</a>から手動にてお願い致します。）</td>
				<td align="right">
				<span class="red12"><!--{$arrErr.total_point}--></span>
				<!--{$arrForm.total_point.value}-->
				 pt</td>
			</tr>
			*}-->
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" colspan="5">▼お支払方法</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#ffffff" colspan="5" height="24">
				<!--{assign var=payment_id value="`$arrForm.payment_id.value`"}-->
				<!--{$arrPayment[$payment_id]|escape}--></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" colspan="5">▼時間指定</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#ffffff" colspan="5" height="24">
				<!--{assign var=deliv_time_id value="`$arrForm.deliv_time_id.value`"}-->
				<!--{$arrDelivTime[$deliv_time_id]|default:"指定なし"}--></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" colspan="6">▼配達日指定</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#ffffff" colspan="6">
				<span class="red12"><!--{$arrErr[$key]}--></span>
				<!--{$arrForm[$key].value|default:"指定なし"}-->
				</td>
			</tr>
		</table>
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="25"></td></tr>
		</table>		
		<!--▲受注商品情報ここまで-->
		<!--▲MAIN CONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->
</div>

</body>
</html>
