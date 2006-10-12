<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/cart/title.jpg" width="700" height="40" alt="現在のカゴの中"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/cart/flame_top.gif" width="700" height="15" alt=""></td>
			</tr>
			<tr>
				<td align="center" background="<!--{$smarty.const.URL_DIR}-->img/cart/flame_bg.gif">
				<table width="680" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td align="center" class="fs14">
							<!--{if $tpl_login}-->
							<!--メインコメント--><!--{$tpl_name|escape}--> 様の、現在の所持ポイントは「<span class="redst"><!--{$tpl_user_point|number_format|default:0}--> pt</span>」です。<br />
							<!--{else}-->
							<!--メインコメント-->ポイント制度をご利用になられる場合は、会員登録後ログインしていだだきますようお願い致します。<br />
							<!--{/if}-->							
							ポイントは商品購入時に1pt＝<!--{$smarty.const.POINT_VALUE}-->円として使用することができます。<br/>

							<!-- カゴの中に商品がある場合にのみ表示 -->
							<!--{if count($arrProductsClass) > 0 }-->
								お買い上げ商品の合計金額は「<span class="redst"><!--{$tpl_total_pretax|number_format}-->円</span>」です。
								<!--{if $arrInfo.free_rule > 0}-->
								<!--{if $arrData.deliv_fee|number_format > 0}-->
									あと「<span class="redst"><!--{$tpl_deliv_free|number_format}-->円</span>」で送料無料です！！
								<!--{else}-->
									現在、「<span class="redst">送料無料</span>」です！！
								<!--{/if}-->
								<!--{/if}-->
							<!--{/if}-->
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/cart/flame_bottom.gif" width="700" height="15" alt=""></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td bgcolor="#cccccc" align="center">
					<!--{if $tpl_message != ""}-->
					<table cellspacing="0" cellpadding="0" summary=" " bgcolor="#ffffff" width=100%>
						<tr>
							<td class="fs12"><span class="redst"><!--{$tpl_message}--></span></td>
						</tr>
					</table>
					<!--{/if}-->
					<!--{if count($arrProductsClass) > 0}-->
					<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
					<input type="hidden" name="mode" value="confirm">
					<input type="hidden" name="cart_no" value="">
	
					<!--ご注文内容ここから-->
					
						<tr align="center" bgcolor="#f0f0f0">
							<td width="50" class="fs12">削除</td>
							<td width="85" class="fs12">商品写真</td>
							<td width="305" class="fs12">商品名</td>
							<td width="60" class="fs12">単価</td>
							<td width="50" class="fs12">個数</td>
							<td width="150" class="fs12">小計</td>
						</tr>
					
						<!--{section name=cnt loop=$arrProductsClass}-->
						<tr bgcolor="#ffffff" class="fs12n">
							<td align="center"><a href="<!--{$smarty.server.PHP_SELF}-->" onclick="fnChangeAction('<!--{$smarty.server.PHP_SELF}-->'); fnModeSubmit('delete', 'cart_no', '<!--{$arrProductsClass[cnt].cart_no}-->'); return false;">削除</a></td>
							<td ><a href="<!--{$smarty.server.PHP_SELF}-->" onclick="win01('/products/detail_image.php?product_id=<!--{$arrProductsClass[cnt].product_id}-->&image=main_image&width=260&height=260','detail_image','350','350'); return false;" target="_blank"><img src="<!--{$smarty.const.IMAGE_SAVE_URL}-->/<!--{$arrProductsClass[cnt].main_list_image}-->" width="65" height="65" alt="<!--{$arrProductsClass[cnt].name|escape}-->" /></a></td>
							<td ><!--{* 商品名 *}--><strong><!--{$arrProductsClass[cnt].name|escape}--></storng><br />
							<!--{if $arrProductsClass[cnt].classcategory_name1 != ""}-->
								<!--{$arrProductsClass[cnt].class_name1}-->：<!--{$arrProductsClass[cnt].classcategory_name1}--><br />
							<!--{/if}-->
							<!--{if $arrProductsClass[cnt].classcategory_name2 != ""}-->
								<!--{$arrProductsClass[cnt].class_name2}-->：<!--{$arrProductsClass[cnt].classcategory_name2}-->
							<!--{/if}-->
							</td>
							<td align="right">
							<!--{if $arrProductsClass[cnt].price02 != ""}-->
								<!--{$arrProductsClass[cnt].price02|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
							<!--{else}-->
								<!--{$arrProductsClass[cnt].price01|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
							<!--{/if}-->						
							</td>
							<td align="center" >
							<table cellspacing="0" cellpadding="0" summary=" " id="form">
								<tr>
									<td colspan="3" align="center" class="fs12n"><!--{$arrProductsClass[cnt].quantity}--></td>
								</tr>
								<tr><td height="5"></td></tr>
								<tr>
									<td><a href="<!--{$smarty.server.PHP_SELF}-->" onclick="fnChangeAction('<!--{$smarty.server.PHP_SELF}-->'); fnModeSubmit('up','cart_no','<!--{$arrProductsClass[cnt].cart_no}-->'); return false"><img src="../img/button/plus.gif" width="16" height="16" alt="＋" /></a></td>
									<td><img src="../img/_.gif" width="10" height="1" alt="" /></td>
									<td><a href="<!--{$smarty.server.PHP_SELF}-->" onclick="fnChangeAction('<!--{$smarty.server.PHP_SELF}-->'); fnModeSubmit('down','cart_no','<!--{$arrProductsClass[cnt].cart_no}-->'); return false"><img src="../img/button/minus.gif" width="16" height="16" alt="-" /></a></td>
								</tr>
							</table>
							</td>
							<td id="price_c" align="right"><!--{$arrProductsClass[cnt].total_pretax|number_format}-->円</td>
						</tr>
						<!--{/section}-->
						
						<tr align="right">
							<td colspan="5" class="fs12n" bgcolor="#f0f0f0">小計</td>
							<td class="fs12n" bgcolor="#ffffff"><!--{$tpl_total_pretax|number_format}-->円</td>
						</tr>
						<tr align="right">
							<td colspan="5" class="fs12n" bgcolor="#f0f0f0">合計</td>
							<td class="fs12st" bgcolor="#ffffff"><!--{$arrData.total-$arrData.deliv_fee|number_format}-->円</td>
						</tr>
						<!--{if $arrData.birth_point > 0}-->
						<tr align="right">
							<td colspan="5" class="fs12n" bgcolor="#f0f0f0">お誕生月ポイント</td>
							<td class="fs12st" bgcolor="#ffffff"><!--{$arrData.birth_point|number_format}-->pt</td>
						</tr>
						<!--{/if}-->
						<tr align="right">
							<td colspan="5" class="fs12n" bgcolor="#f0f0f0">今回加算ポイント</td>
							<td class="fs12st" bgcolor="#ffffff"><!--{$arrData.add_point|number_format}-->pt</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td height="10"></td></tr>

			<tr>
				<td class="fs10">
					※商品写真は参考用写真です。ご注文のカラーと異なる写真が表示されている場合でも、商品番号に記載されているカラー表示で間違いございませんのでご安心ください。<br>
					※上記料金に別途送料手数料が発生します。ご注意ください。
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td align="center"><img src="../img/cart/text.gif" width="390" height="13" alt="上記内容でよろしければ「レジへ行く」ボタンをクリックしてください。"></td>
			</tr>
			<tr><td height="20"></td></tr>

			<tr>
				<td align="center">
					<a href="javascript:history.back()" onmouseOver="chgImg('<!--{$smarty.const.URL_DIR}-->img/cart/b_pageback_on.gif','back');" onmouseOut="chgImg('<!--{$smarty.const.URL_DIR}-->img/cart/b_pageback.gif','back');"><img src="../img/cart/b_pageback.gif" width="150" height="30" alt="前のページへ戻る" name="back" id="back" /></a>　
					<input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/cart/b_buystep_on.gif',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/cart/b_buystep.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/cart/b_buystep.gif" width="150" height="30" alt="購入手続きへ" name="confirm" />
				</td>
			</tr>
			</form>
					<!--{else}-->
						<table width=100% cellspacing="0" cellpadding="10" summary=" ">
							<tr bgcolor="#ffffff" align="center">
								<td class="fs12"><span class="redst">※ 現在カート内に商品はございません。</span><br />
							</tr>
						</table>
					<!--{/if}-->
				</td>
				<!--▲CONTENTS-->	
		</table>
		<!--▲MAIN CONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->
