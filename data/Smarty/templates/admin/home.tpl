<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td class="mainbg">
		<table width="588" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<!--メインエリア-->
			<tr>
				<td align="center">
				<table width="562" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<form name="form1" method="post" action="#">
					<tr><td height="14"></td></tr>
					<tr>
						<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/home_top.jpg" width="562" height="14" alt=""></td>
					</tr>
					<tr>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
						<td bgcolor="#cccccc">
						<table width="534" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/homettl_shop.gif" width="534" height="26" alt="ショップの状況"></td>
							</tr>
							<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/home_bar.jpg" width="534" height="10" alt=""></td></tr>
						</table>
						
						<!--システム情報ここから-->
						<table width="534" border="0" cellspacing="1" cellpadding="4" summary=" ">
							<tr>
								<td bgcolor="#f2f1ec" width="170" class="fs12">EC-CUBEバージョン</td>
								<td bgcolor="#ffffff" width="329" class="fs12" align="right"><!--{$smarty.const.ECCUBE_VERSION}--></td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" width="170" class="fs12">PHPバージョン</td>
								<td bgcolor="#ffffff" width="329" class="fs12" align="right"><!--{$php_version}--></td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" width="170" class="fs12">DBバージョン</td>
								<td bgcolor="#ffffff" width="329" class="fs12" align="right"><!--{$db_version}--></td>
							</tr>							
						</table>
						<table width="534" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/home_bar02.jpg" width="534" height="20" alt=""></td></tr>
							<tr>
								<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/homettl_shop.gif" width="534" height="26" alt="ショップの状況"></td>
							</tr>
							<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/home_bar.jpg" width="534" height="10" alt=""></td></tr>
						</table>
						
						<!--ショップの状況ここから-->
						<table width="534" border="0" cellspacing="1" cellpadding="4" summary=" ">
							<tr>
								<td bgcolor="#f2f1ec" width="170" class="fs12">現在の会員数</td>
								<td bgcolor="#ffffff" width="329" class="fs12" align="right"><!--{$customer_cnt|default:"0"|number_format}-->名</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">昨日の売上高</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><!--{$order_yesterday_amount|default:"0"|number_format}-->円</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">昨日の売上件数</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><!--{$order_yesterday_cnt|default:"0"|number_format}-->件</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec"><span class="fs12">今月の売上高</span><span class="fs10">(昨日まで) </span></td>
								<td bgcolor="#ffffff" class="fs12" align="right"><!--{$order_month_amount|default:"0"|number_format}-->円</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec"><span class="fs12">今月の売上件数 </span><span class="fs10">(昨日まで) </span></td>
								<td bgcolor="#ffffff" class="fs12" align="right"><!--{$order_month_cnt|default:"0"|number_format}-->件</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">昨日のレビュー書き込み数</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><!--{$review_yesterday_cnt|default:"0"}-->件</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">顧客の保持ポイント合計</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><!--{$customer_point|default:"0"}-->pt</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">レビュー書き込み非表示数</td>
								<td bgcolor="#ffffff" class="fs12" align="right"><!--{$review_nondisp_cnt|default:"0"}-->件</td>
							</tr>
							<tr>
								<td bgcolor="#f2f1ec" class="fs12">品切れ商品</td>
								<td bgcolor="#ffffff" class="fs12">
								<!--{section name=i loop=$arrSoldout}-->
								<!--{$arrSoldout[i].product_id}-->:<!--{$arrSoldout[i].name|escape}--><br>
								<!--{/section}-->			
								</td>
							</tr>
						</table>
						<!--ショップの状況ここまで-->
						<table width="534" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/home_bar02.jpg" width="534" height="20" alt=""></td></tr>
							<tr>
								<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/homettl_list.gif" width="534" height="26" alt="新規受付一覧"></td>
							</tr>
							<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/home_bar.jpg" width="534" height="10" alt=""></td></tr>
						</table>
						<!--新規受付一覧ここから-->
						<table width="534" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr bgcolor="#636469" align="center" class="fs10n">
								<td width="100"><span class="white">受注日</span></td>
								<td width="90"><span class="white">顧客名</span></td>
								<td width="159"><span class="white">購入商品</span></td>
								<td width="70"><span class="white">支払方法</span></td>
								<td width="70"><span class="white">購入金額(円)</span></td>
							</tr>
							<!--{section name=i loop=$arrNewOrder}-->
							<tr bgcolor="#ffffff" class="fs10">
								<td><!--{$arrNewOrder[i].create_date}--></td>
								<td><!--{$arrNewOrder[i].name01|escape}--> <!--{$arrNewOrder[i].name02|escape}--></td>
								<td><!--{$arrNewOrder[i].product_name|escape}--></td>
								<td><!--{$arrNewOrder[i].payment_method|escape}--></td>
								<td align="right"><!--{$arrNewOrder[i].total|number_format}-->円</td>
							</tr>
							<!--{/section}-->
						</table>
						<!--新規受付一覧ここまで-->
						</td>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
					</tr>
					<tr>
						<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/home_bottom.jpg" width="562" height="14" alt=""></td>
					</tr>
					<tr><td height="30"></td></tr>
				</form>
				</table>
				</td>
			</tr>
			<!--メインエリア-->
		</table>
		</td>
		<td bgcolor="#a8a8a8"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
		<td class="infobg" bgcolor="#e3e3e3">
		<table width="288" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td align="center">
				<table width="266" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<!--お知らせここから-->
					<!--{section name=i loop=$arrInfo}-->
					<tr><td height="15"></td></tr>
					<tr>
						<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_top_left.jpg" width="12" height="5" alt="" border="0"></td>
						<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_top.jpg" width="249" height="5" alt="" border="0"></td>
						<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_top_right.jpg" width="5" height="5" alt="" border="0"></td>
					</tr>
					<tr>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_day_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_day_left.jpg" width="12" height="10" alt="" border="0"></td>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_bg01.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt=""><span class="infodate"><!--{$arrInfo[i][0]}--></span></td>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_day_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_day_right.jpg" width="5" height="10" alt="" border="0"></td>
					</tr>
					<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_middle.jpg" width="266" height="8" alt="" border="0"></td></tr>
					<tr>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_bottom_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="12" height="1" alt=""></td>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_bg02.jpg" class="infottl"><!--{$arrInfo[i][1]}--></td>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_bottom_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="5" height="1" alt=""></td>
					</tr>
					<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/infottl_bottom.jpg" width="266" height="7" alt="" border="0"></td></tr>
					<tr><td height="5"></td></tr>
					<tr>
						<td colspan="3" class="fs10"><span class="info"><!--{$arrInfo[i][2]}--></span></td>
					</tr>
					<!--{/section}-->
					<!--お知らせここまで-->
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<!--★★メインコンテンツ★★-->		
<!--▲CONTENTS-->