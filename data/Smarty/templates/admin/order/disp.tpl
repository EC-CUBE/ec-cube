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
<div align="center">

<!--▼CONTENTS-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td class="mainbg" >
		<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<!--メインエリア-->
			<tr>
				<td align="center">
				<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">

					<tr><td height="14"></td></tr>
					<tr>
						<td colspan="3"><img src="/img/contents/main_top.jpg" width="706" height="14" alt=""></td>
					</tr>
					<tr>
						<td background="/img/contents/main_left.jpg"><img src="/img/common/_.gif" width="14" height="1" alt=""></td>
						<td bgcolor="#cccccc">
						
						<!--登録テーブルここから-->
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="3"><img src="/img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td background="/img/contents/contents_title_left_bg.gif"><img src="/img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->受注詳細</span></td>
								<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>
						
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
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

						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
						</table>
						
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
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
						
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
						</table>
						
						<!--▼配送先情報ここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="717" colspan="4">▼配送先情報</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">お名前</td>
								<td bgcolor="#ffffff" width="248">
								<!--{assign var=key1 value="deliv_name01"}-->
								<!--{assign var=key2 value="deliv_name02"}-->
								<!--{$arrForm[$key1].value|escape}-->
								<!--{$arrForm[$key2].value|escape}-->
								</td>
								<td bgcolor="#f2f1ec" width="110">お名前（カナ）</td>
								<td bgcolor="#ffffff" width="249">
								<!--{assign var=key1 value="deliv_kana01"}-->
								<!--{assign var=key2 value="deliv_kana02"}-->
								<!--{$arrForm[$key1].value|escape}-->
								<!--{$arrForm[$key2].value|escape}-->
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">郵便番号</td>
								<td bgcolor="#ffffff" width="248">
								<!--{assign var=key1 value="deliv_zip01"}-->
								<!--{assign var=key2 value="deliv_zip02"}-->
								〒<!--{$arrForm[$key1].value|escape}-->-<!--{$arrForm[$key2].value|escape}-->
								<input type="button" name="address_input" value="住所入力" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'deliv_zip01', 'deliv_zip02', 'deliv_pref', 'deliv_addr01');" />
								</td>
								<td bgcolor="#f2f1ec" width="110">TEL</td>
								<td bgcolor="#ffffff" width="249">
								<!--{assign var=key1 value="deliv_tel01"}-->
								<!--{assign var=key2 value="deliv_tel02"}-->
								<!--{assign var=key3 value="deliv_tel03"}-->
								<!--{$arrForm[$key1].value|escape}-->-<!--{$arrForm[$key2].value|escape}-->-<!--{$arrForm[$key3].value|escape}-->
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">住所</td>
								<td bgcolor="#ffffff" width="607" colspan="3">
										<!--{assign var=key value="deliv_pref"}-->
										<!--{$arrForm[$key].value}-->
										<!--{assign var=key value="deliv_addr01"}-->
										<!--{$arrForm[$key].value|escape}-->
										<!--{assign var=key value="deliv_addr02"}-->
										<!--{$arrForm[$key].value|escape}-->
								</td>
							</tr>
						</table>
						<!--▲配送先情報ここまで-->						
						
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
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
								<!--{assign var=key value="deliv_date"}-->
								<span class="red12"><!--{$arrErr[$key]}--></span>
								<!--{$arrForm[$key].value|default:"指定なし"}-->
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" colspan="6">▼メモ</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#ffffff" colspan="6">
								<!--{assign var=key value="note"}-->
								<!--{$arrForm[$key].value|escape|nl2br}-->
								</td>
							</tr>							
						</table>					
						
						
						<td background="/img/contents/main_right.jpg"><img src="/img/common/_.gif" width="14" height="1" alt=""></td>
					</tr>
					<tr>
						<td colspan="3"><img src="/img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
					</tr>
					<tr><td height="30"></td></tr>
				</table>
				</td>
			</tr>
			<!--メインエリア-->
		</table>
		</td>
	</tr>
</form>
</table>
<!--★★メインコンテンツ★★-->		
</div>

</body>
</html>
