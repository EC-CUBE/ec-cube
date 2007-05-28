<!--{*
 * Copyright (c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/title.jpg" width="700" height="40" alt="MYページ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr valign="top">
				<td>
				<!--▼NAVI-->
				<!--{include file=$tpl_navi}-->
				<!--▲NAVI-->
				</td>
				<td align="right">
				<table width="515" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td><!--★タイトル--><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/subtitle05.gif" width="515" height="32" alt="購入履歴詳細"></td>
					</tr>
					<tr><td height="15"></td></tr>
					<tr>
						<td align="center" bgcolor="#fff5e8">
						<!--購入日時等ここから-->
						<table width="495" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="10"></td></tr>
							<tr>
								<td class="fs12"><strong>購入日時：&nbsp;</strong><!--{$arrDisp.create_date|sfDispDBDate}--><br>
								<strong>注文番号：&nbsp;</strong><!--{$arrDisp.order_id}--><br>
								<strong>お支払い方法：&nbsp;</strong><!--{$arrPayment[$arrDisp.payment_id]|escape}-->
								<!--{if $arrDisp.deliv_time_id != ""}--><br />
								<strong>お届け時間指定：&nbsp;</strong><!--{$arrDelivTime[$arrDisp.deliv_time_id]|escape}-->
								<!--{/if}-->
								<!--{if $arrDisp.deliv_date != ""}--><br />
								<strong>お届け日指定：&nbsp;</strong><!--{$arrDisp.deliv_date|escape}-->
								<!--{/if}-->
								</td>
							</tr>
							<tr><td height="10"></td></tr>
						</table>
						<!--購入日時等ここまで-->
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td align="center" bgcolor="#cccccc">
						<!--購入履歴詳細ここから-->
						<table width="515" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr align="center" bgcolor="#f0f0f0">
								<td class="fs12n">商品コード</td>
								<td class="fs12n">商品名</td>
								<td class="fs12n">単価</td>
								<td class="fs12n">個数</td>
								<td class="fs12n">小計</td>
							</tr>

							<!--{section name=cnt loop=$arrDisp.quantity}-->
							<tr bgcolor="#ffffff">
								<td class="fs12"><!--{$arrDisp.product_code[cnt]|escape}--></td>
								<td class="fs12"><a href="<!--{$smarty.const.URL_DIR}-->products/detail.php?product_id=<!--{$arrDisp.product_id[cnt]}-->"><!--{$arrDisp.product_name[cnt]|escape}--><a></td>
								<!--{assign var=price value=`$arrDisp.price[cnt]`}-->
								<!--{assign var=quantity value=`$arrDisp.quantity[cnt]`}-->
								<td align="right" class="fs12"><!--{$price|escape|number_format}-->円</td>
								<td align="center" class="fs12"><!--{$quantity|escape}--></td>
								<td align="right" class="fs12"><!--{$price|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|sfMultiply:$quantity|number_format}-->円</td>
							</tr>
							<!--{/section}-->

							<tr align="right">
								<td colspan="4" bgcolor="#f0f0f0"><span class="fs12">小計</span></td>
								<td bgcolor="#ffffff"><span class="fs12"><!--{$arrDisp.subtotal|number_format}-->円</span><br><span class="fs10"></span></td>
							</tr>
							<!--{assign var=point_discount value="`$arrDisp.use_point*$smarty.const.POINT_VALUE`"}-->
							<!--{if $point_discount > 0}-->							
							<tr align="right">
								<td colspan="4" bgcolor="#f0f0f0" class="fs12">ポイント値引き</td>
								<td bgcolor="#ffffff" class="fs12"><!--{$point_discount|number_format}-->円</td>
							</tr>
							<!--{/if}-->
							<!--{assign var=key value="discount"}-->
							<!--{if $arrDisp[$key] != "" && $arrDisp[$key] > 0}-->
							<tr align="right">
								<td colspan="4" bgcolor="#f0f0f0" class="fs12">値引き</td>
								<td bgcolor="#ffffff" class="fs12"><!--{$arrDisp[$key]|number_format}-->円</td>
							</tr>
							<!--{/if}-->
							<tr align="right">
								<td colspan="4" bgcolor="#f0f0f0" class="fs12">送料</td>
								<td bgcolor="#ffffff" class="fs12"><!--{assign var=key value="deliv_fee"}--><!--{$arrDisp[$key]|escape|number_format}-->円</td>
							</tr>
							<tr align="right">
								<td colspan="4" bgcolor="#f0f0f0" class="fs12">手数料</td>
								<!--{assign var=key value="charge"}-->
								<td bgcolor="#ffffff" class="fs12"><!--{$arrDisp[$key]|escape|number_format}-->円</td>
							</tr>
							<tr align="right">
								<td colspan="4" bgcolor="#f0f0f0" class="fs12">合計</td>
								<td bgcolor="#ffffff" class="fs12"><span class="redst"><!--{$arrDisp.payment_total|number_format}-->円</span></td>
							</tr>

						</table>
						<!--購入履歴詳細ここまで-->
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td bgcolor="#cccccc">
						<!-- 使用ポイントここから -->
						<table width="515" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr align="right" bgcolor="#f0f0f0">
								<td class="fs12n" width="415">ご使用ポイント</td>
								<td class="fs12n" width="75"bgcolor="#ffffff"><!--{assign var=key value="use_point"}--><!--{$arrDisp[$key]|number_format|default:0}--> pt</td>
							</tr>
							<tr align="right" bgcolor="#f0f0f0">
								<td class="fs12n" width="400">今回加算されるポイント</td>
								<td class="fs12n" width="75" bgcolor="#ffffff"><!--{$arrDisp.add_point|number_format|default:0}--> pt</td>
							</tr>
						</table>
						<!-- 使用ポイントここまで -->
						</td>
					</tr>
					
					<tr><td height="20"></td></tr>
					<tr>
						<td bgcolor="#cccccc">
						<!--お届け先ここから-->						
						<table width="515" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr bgcolor="#f0f0f0">
								<td colspan="2" class="fs12n"><strong>▼お届け先</strong></td>
							</tr>
							<tr>
								<td width="130" bgcolor="#f0f0f0" class="fs12n">お名前</td>
								<!--{assign var=key1 value="deliv_name01"}--><!--{assign var=key2 value="deliv_name02"}-->
								<td width="367" bgcolor="#ffffff" class="fs12n"><!--{$arrDisp[$key1]|escape}-->&nbsp;<!--{$arrDisp[$key2]|escape}--></</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">お名前（フリガナ）</td>
								<!--{assign var=key1 value="deliv_kana01"}--><!--{assign var=key2 value="deliv_kana02"}-->
								<td bgcolor="#ffffff" class="fs12n"><!--{$arrDisp[$key1]|escape}-->&nbsp;<!--{$arrDisp[$key2]|escape}--></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">郵便番号</td>
								<!--{assign var=key1 value="deliv_zip01"}--><!--{assign var=key2 value="deliv_zip02"}-->
								<td bgcolor="#ffffff" class="fs12n">〒<!--{$arrDisp[$key1]}-->-<!--{$arrDisp[$key2]}--></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12">住所</td>
								<td bgcolor="#ffffff" class="fs12"><!--{assign var=pref value=`$arrDisp.deliv_pref`}--><!--{$arrPref[$pref]}--><!--{assign var=key value="deliv_addr01"}--><!--{$arrDisp[$key]|escape}--><!--{assign var=key value="deliv_addr02"}--><!--{$arrDisp[$key]|escape}--></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">電話番号</td>
								<!--{assign var=key1 value="deliv_tel01"}--><!--{assign var=key2 value="deliv_tel02"}--><!--{assign var=key3 value="deliv_tel03"}-->
								<td bgcolor="#ffffff" class="fs12n"><!--{$arrDisp[$key1]}-->-<!--{$arrDisp[$key2]}-->-<!--{$arrDisp[$key3]}--></td>
							</tr>
						</table>
						<!--お届け先ここまで-->
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td align="center">
							<a href="./index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_back_on.gif','change');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif','change');"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="戻る" name="change" id="change" /></a>
						</td>
					</tr>
				</form>
				</table>
				</td>
			</tr>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->
