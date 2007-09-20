<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<!--購入手続きの流れ-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$TPL_DIR}-->img/shopping/flow03.gif" width="700" height="36" alt="購入手続きの流れ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<!--購入手続きの流れ-->
		
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$TPL_DIR}-->img/shopping/confirm_title.jpg" width="700" height="40" alt="ご入力内容のご確認"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">下記ご注文内容で送信してもよろしいでしょうか？<br>
				よろしければ、一番下の「<!--{if $payment_type != ""}-->次へ<!--{else}-->ご注文完了ページへ<!--{/if}-->」ボタンをクリックしてください。</td>
			</tr>
			<tr><td height="20"></td></tr>
			<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
			<input type="hidden" name="mode" value="confirm">
			<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
			<tr>
				<td bgcolor="#cccccc">
				<!--ご注文内容ここから-->
				<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr align="center" bgcolor="#f0f0f0">
						<td width="85" class="fs12n">商品写真</td>
						<td width="298" class="fs12n">商品名</td>
						<td width="60" class="fs12n">単価</td>
						<td width="40" class="fs12n">個数</td>
						<td width="90" class="fs12n">小計</td>
					</tr>
					<!--{section name=cnt loop=$arrProductsClass}-->
					<tr bgcolor="#ffffff">
						<td align="center">
							<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="win01('../products/detail_image.php?product_id=<!--{$arrProductsClass[cnt].product_id}-->&image=main_image','detail_image','<!--{$arrProductsClass[cnt].tpl_image_width}-->','<!--{$arrProductsClass[cnt].tpl_image_height}-->'); return false;" target="_blank">
								<img src="<!--{$smarty.const.SITE_URL}-->resize_image.php?image=<!--{$arrProductsClass[cnt].main_list_image}-->&width=65&height=65" alt="<!--{$arrProductsClass[cnt].name|escape}-->">
							</a>
						</td>
						<td class="fs12">
							<strong><!--{$arrProductsClass[cnt].name|escape}--></strong><br>
							<!--{if $arrProductsClass[cnt].classcategory_name1 != ""}-->
								<!--{$arrProductsClass[cnt].class_name1}-->：<!--{$arrProductsClass[cnt].classcategory_name1}--><br>
							<!--{/if}-->
							<!--{if $arrProductsClass[cnt].classcategory_name2 != ""}-->
								<!--{$arrProductsClass[cnt].class_name2}-->：<!--{$arrProductsClass[cnt].classcategory_name2}-->
							<!--{/if}-->
						</td>
						<td align="right" class="fs12">
							<!--{if $arrProductsClass[cnt].price02 != ""}-->
							<!--{$arrProductsClass[cnt].price02|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
							<!--{else}-->
							<!--{$arrProductsClass[cnt].price01|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
							<!--{/if}-->
						</td>
						<td align="right" class="fs12"><!--{$arrProductsClass[cnt].quantity|number_format}-->個</td>
						<td align="right" class="fs12"><!--{$arrProductsClass[cnt].total_pretax|number_format}-->円</td>
					</tr>
					<!--{/section}-->
					<tr align="right">
						<td colspan="4" bgcolor="#f0f0f0" class="fs12">小計</td>
						<td colspan="2" bgcolor="#ffffff" class="fs12"><!--{$tpl_total_pretax|number_format}-->円</span><br>
					</tr>
					<tr align="right">
						<td colspan="4" bgcolor="#f0f0f0" class="fs12">値引き（ポイントご使用時）</td>
						<!--{assign var=discount value=`$arrData.use_point*$smarty.const.POINT_VALUE`}-->
						<td colspan="2" bgcolor="#ffffff" class="fs12">-<!--{$discount|number_format|default:0}-->円</td>
					</tr>
					<tr align="right">
						<td colspan="4" bgcolor="#f0f0f0" class="fs12">送料</td>
						<td colspan="2" bgcolor="#ffffff" class="fs12"><!--{$arrData.deliv_fee|number_format}-->円</td>
					</tr>
					<tr align="right">
						<td colspan="4" bgcolor="#f0f0f0" class="fs12">手数料</td>
						<td colspan="2" bgcolor="#ffffff" class="fs12"><!--{$arrData.charge|number_format}-->円</td>
					</tr>
					<tr align="right">
						<td colspan="4" bgcolor="#f0f0f0" class="fs12">合計</td>
						<td colspan="2" bgcolor="#ffffff" class="fs12"><span class="redst"><!--{$arrData.payment_total|number_format}-->円</span></td>
					</tr>
				</table>
				<!--ご注文内容ここまで-->

				<!--{* ログイン済みの会員のみ *}-->
				<!--{if $tpl_login == 1 || $arrData.member_check == 1}-->
				<table bgcolor="#ffffff" width=100%><tr><td height="15"></td></tr></table>
				
				<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr class="fs12" align="right">
						<td bgcolor="#f0f0f0" width="610">ご注文前のポイント</td>
						<td bgcolor="#ffffff" width="90"><!--{$tpl_user_point|number_format|default:0}-->Pt</td>
					</tr>
					<tr class="fs12" align="right">
						<td bgcolor="#f0f0f0" width="610">ご使用ポイント</td>
						<td bgcolor="#ffffff" width="90">-<!--{$arrData.use_point|number_format|default:0}-->Pt</td>
					</tr>
					<!--{if $arrData.birth_point > 0}-->
					<tr class="fs12" align="right">
						<td bgcolor="#f0f0f0" width="610">お誕生月ポイント</td>
						<td bgcolor="#ffffff" width="90">+<!--{$arrData.birth_point|number_format|default:0}-->Pt</td>
					</tr>
					<!--{/if}-->
					<tr class="fs12" align="right">
						<td bgcolor="#f0f0f0" width="610">今回加算されるポイント</td>
						<td bgcolor="#ffffff" width="90">+<!--{$arrData.add_point|number_format|default:0}-->Pt</td>
					</tr>
					<tr class="fs12st" align="right">
						<!--{assign var=total_point value=`$tpl_user_point-$arrData.use_point+$arrData.add_point`}-->
						<td bgcolor="#f0f0f0" width="610">ご注文完了後のポイント</td>
						<td bgcolor="#ffffff" width="90"><!--{$total_point|number_format}-->Pt</td>
					</tr>
				</table>
				<!--{/if}-->
				<!--{* ログイン済みの会員のみ *}-->
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td bgcolor="#cccccc">
				<!--お届け先ここから-->
				<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td colspan="2" bgcolor="#f0f0f0" class="fs12n"><strong>▼お届け先</strong></td>
					</tr>
					<!--{* 別のお届け先が選択されている場合 *}-->
					<!--{if $arrData.deliv_check >= 1}-->
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">お名前</td>
							<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.deliv_name01|escape}--> <!--{$arrData.deliv_name02|escape}--></td>
						</tr>
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">お名前（フリガナ）</td>
							<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.deliv_kana01|escape}--> <!--{$arrData.deliv_kana02|escape}--></td>
						</tr>
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">郵便番号</td>
							<td width="507" bgcolor="#ffffff" class="fs12">〒<!--{$arrData.deliv_zip01|escape}-->-<!--{$arrData.deliv_zip02|escape}--></td>
						</tr>
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">住所</td>
							<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrPref[$arrData.deliv_pref]}--><!--{$arrData.deliv_addr01|escape}--><!--{$arrData.deliv_addr02|escape}--></td>
						</tr>
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">電話番号</td>
							<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.deliv_tel01}-->-<!--{$arrData.deliv_tel02}-->-<!--{$arrData.deliv_tel03}--></td>
						</tr>
					<!--{else}-->
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">お名前</td>
							<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.order_name01|escape}--> <!--{$arrData.order_name02|escape}--></td>
						</tr>
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">お名前（フリガナ）</td>
							<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.order_kana01|escape}--> <!--{$arrData.order_kana02|escape}--></td>
						</tr>
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">郵便番号</td>
							<td width="507" bgcolor="#ffffff" class="fs12">〒<!--{$arrData.order_zip01|escape}-->-<!--{$arrData.order_zip02|escape}--></td>
						</tr>
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">住所</td>
							<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrPref[$arrData.order_pref]}--><!--{$arrData.order_addr01|escape}--><!--{$arrData.order_addr02|escape}--></td>
						</tr>
						<tr>
							<td width="150" bgcolor="#f0f0f0" class="fs12">電話番号</td>
							<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.order_tel01}-->-<!--{$arrData.order_tel02}-->-<!--{$arrData.order_tel03}--></td>
						</tr>
					<!--{/if}-->
				</table>
				<!--お届け先ここまで-->
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td bgcolor="#cccccc">
				<!--お支払方法・お届け時間の指定・その他お問い合わせここから-->		
				<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td colspan="2" bgcolor="#f0f0f0" class="fs12n"><strong>▼お支払方法・お届け時間の指定・その他お問い合わせ</strong></td>
					</tr>
					<tr>
						<td width="150" bgcolor="#f0f0f0" class="fs12">お支払方法</td>
						<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.payment_method|escape}--></td>
					</tr>
					<tr>
						<td width="150" bgcolor="#f0f0f0" class="fs12">お届け日</td>
						<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.deliv_date|escape|default:"指定なし"}--></td>
					</tr>
					<tr>
						<td width="150" bgcolor="#f0f0f0" class="fs12">お届け時間</td>
						<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.deliv_time|escape|default:"指定なし"}--></td>
					</tr>
					<tr>
						<td width="150" bgcolor="#f0f0f0" class="fs12">その他お問い合わせ</td>
						<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.message|escape|nl2br}--></td>
					</tr>
					
					<!--{if $tpl_login == 1}-->
					<tr>
						<td width="150" bgcolor="#f0f0f0" class="fs12">ポイント使用</td>
						<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.use_point|default:0}-->Pt</td>
					</tr>
					<!--{/if}-->
					
				</table>
				<!--お支払方法・お届け時間の指定・その他お問い合わせここまで-->
				</td>
			</tr>
			<tr><td height="20"></td></tr>

			<tr>
				<td align="center">
					<a href="./payment.php" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_back_on.gif',back03)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_back.gif',back03)"><img src="<!--{$TPL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="戻る" border="0" name="back03" id="back03"/></a><img src="<!--{$TPL_DIR}-->img/_.gif" width="12" height="" alt="" />
					<!--{if $payment_type != ""}-->
						<input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_next_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_next.gif',this)" src="<!--{$TPL_DIR}-->img/common/b_next.gif" width="150" height="30" alt="次へ" border="0" name="next" id="next" />
					<!--{else}-->
						<input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/shopping/b_ordercomp_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/shopping/b_ordercomp.gif',this)" src="<!--{$TPL_DIR}-->img/shopping/b_ordercomp.gif" width="150" height="30" alt="ご注文完了ページへ" border="0" name="next" id="next" />
					<!--{/if}-->
				</td>
			</tr>
			</form>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->

