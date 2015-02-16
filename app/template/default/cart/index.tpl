<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
*}-->
<article id="article_cart" class="undercolumn">
	<h1 class="title"><!--{$tpl_title|h}--></h1>

	<!--{if $smarty.const.USE_POINT !== false || count($arrProductsClass) > 0}-->
		<!--★ポイント案内★-->
		<!--{if $smarty.const.USE_POINT !== false}-->
			<p class="point_announce">
				<!--{if $tpl_login}-->
					<span class="user_name"><!--{$tpl_name|h}--> 様</span>の、現在の所持ポイントは「<span class="point"><!--{$tpl_user_point|n2s|default:0|h}--> pt</span>」です。<br />
				<!--{else}-->
					ポイント制度をご利用になられる場合は、会員登録後ログインしてくださいますようお願い致します。<br />
				<!--{/if}-->
				ポイントは商品購入時に<span class="price">1pt＝<!--{$smarty.const.POINT_VALUE|h}-->円</span>として使用することができます。<br />
			</p>
		<!--{/if}-->
	<!--{/if}-->

	<p class="totalmoney_area">
		<!--{* カゴの中に商品がある場合にのみ表示 *}-->
		<!--{if count($cartKeys) > 1}-->
			<span class="attentionSt"><!--{foreach from=$cartKeys item=key name=cartKey}--><!--{$arrProductType[$key]|h}--><!--{if !$smarty.foreach.cartKey.last}-->、<!--{/if}--><!--{/foreach}-->は同時購入できません。<br />
				お手数ですが、個別に購入手続きをお願い致します。
			</span>
		<!--{/if}-->

		<!--{if strlen($tpl_error) != 0}-->
			<p class="attention"><!--{$tpl_error|h}--></p>
		<!--{/if}-->

		<!--{if strlen($tpl_message) != 0}-->
			<p class="attention"><!--{$tpl_message|h|nl2br}--></p>
		<!--{/if}-->
	</p>

	<!--{if count($cartItems) > 0}-->
		<!--{foreach from=$cartKeys item=key}-->
			<div class="form_area">
				<form name="form<!--{$key|h}-->" id="form<!--{$key|h}-->" method="post" action="?">
					<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME|h}-->" value="<!--{$transactionid|h}-->" />
					<input type="hidden" name="mode" value="confirm" />
					<input type="hidden" name="cart_no" value="" />
					<input type="hidden" name="cartKey" value="<!--{$key|h}-->" />
					<input type="hidden" name="category_id" value="<!--{$tpl_category_id|h}-->" />
					<input type="hidden" name="product_id" value="<!--{$tpl_product_id|h}-->" />
					<!--{if count($cartKeys) > 1}-->
						<h2><!--{$arrProductType[$key]|h}--></h2>
						<!--{assign var=purchasing_goods_name value=$arrProductType[$key]}-->
					<!--{else}-->
						<!--{assign var=purchasing_goods_name value="カゴの中の商品"}-->
					<!--{/if}-->
					<p>
						<!--{$purchasing_goods_name|h}-->の合計金額は「<span class="price"><!--{$tpl_total_inctax[$key]|n2s|h}-->円</span>」です。
						<!--{if $key != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
							<!--{if $arrInfo.free_rule > 0}-->
								<!--{if !$arrData[$key].is_deliv_free}-->
									あと「<span class="price"><!--{$tpl_deliv_free[$key]|n2s|h}-->円</span>」で送料無料です！！
								<!--{else}-->
									現在、「<span class="attention">送料無料</span>」です！！
								<!--{/if}-->
							<!--{/if}-->
						<!--{/if}-->
					</p>

					<table summary="商品情報">
						<col width="15%" />
						<col width="60%" />
						<col width="25%" />
						<tr>
							<th class="alignC">削除</th>
							<th class="alignC">商品詳細</th>
							<th class="alignC">小計</th>
						</tr>
						<!--{foreach from=$cartItems[$key] item=item}-->
							<tr style="<!--{if $item.error}-->background-color: <!--{$smarty.const.ERR_COLOR|h}-->;<!--{/if}-->">
								<td class="alignC" rowspan="3">
									<a href="?" onclick="eccube.fnFormModeSubmit('form<!--{$key|h}-->', 'cartDelete', 'cart_no', '<!--{$item.cart_no|h}-->'); return false;">削除</a>
								</td>
								<td class="alignL">
								<!--{if $item.productsClass.main_image|strlen >= 1}-->
									<a class="expansion cart_expansion" target="_blank" href="<!--{$smarty.const.IMAGE_SAVE_URLPATH|h}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->">
								<!--{/if}-->
										<img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->" style="max-width: 65px;max-height: 65px;" alt="<!--{$item.productsClass.name|h}-->" class="cart_img fr" />
								<!--{if $item.productsClass.main_image|strlen >= 1}-->
									</a>
								<!--{/if}-->
								<!--{* 商品名 *}--><strong class="cart_pname"><!--{$item.productsClass.name|h}--></strong>
								</td>
								<td class="alignR" rowspan="3"><!--{$item.total_inctax|n2s|h}-->円</td>
							</tr>
							<tr>
								<td class="cart_class_name">
									<!--{if $item.productsClass.classcategory_name1 != ""}-->
										<!--{$item.productsClass.class_name1|h}-->：<!--{$item.productsClass.classcategory_name1|h}-->
									<!--{/if}-->
									<!--{if $item.productsClass.classcategory_name2 != ""}-->
										<br><!--{$item.productsClass.class_name2|h}-->：<!--{$item.productsClass.classcategory_name2|h}-->
									<!--{/if}-->
								</td>
							</tr>
							<tr>
								<td>
									<!--{$item.price_inctax|n2s|h}-->円
									x
									<!--{$item.quantity|h}-->
									<ul id="quantity_level" class="cart_price">
										<li>（増減:<a href="?" onclick="eccube.fnFormModeSubmit('form<!--{$key|h}-->','up','cart_no','<!--{$item.cart_no|h}-->'); return false"><img src="<!--{$TPL_URLPATH|h}-->img/button/btn_plus.jpg" width="16" height="16" alt="＋" /></a></li>
									<!--{if $item.quantity > 1}-->
										<li><a href="?" onclick="eccube.fnFormModeSubmit('form<!--{$key|h}-->','down','cart_no','<!--{$item.cart_no|h}-->'); return false"><img src="<!--{$TPL_URLPATH|h}-->img/button/btn_minus.jpg" width="16" height="16" alt="-" /></a></li>
									<!--{/if}-->
									</ul>）
								</td>
							</tr>
						<!--{/foreach}-->
						<tr>
							<th colspan="2" class="alignR">合計</th>
							<td class="alignR"><span class="price"><!--{$arrData[$key].total-$arrData[$key].deliv_fee|n2s|h}-->円</span></td>
						</tr>
						<!--{if $smarty.const.USE_POINT !== false}-->
							<!--{if $arrData[$key].birth_point > 0}-->
								<tr>
									<th colspan="3" class="alignR">お誕生月ポイント</th>
									<td class="alignR"><!--{$arrData[$key].birth_point|n2s|h}-->pt</td>
								</tr>
							<!--{/if}-->
							<tr>
								<th colspan="2" class="alignR">今回加算ポイント</th>
								<td class="alignR"><!--{$arrData[$key].add_point|n2s|h}-->pt</td>
							</tr>
						<!--{/if}-->
					</table>
					<!--{if strlen($tpl_error) == 0}-->
						<p class="alignC">上記内容でよろしければ「購入手続きへ」ボタンをクリックしてください。</p>
					<!--{/if}-->
					<div class="btn_area">
						<ul>
							<li>
								<!--{if $tpl_prev_url != ""}-->
									<a class="btn btn-default" href="<!--{$tpl_prev_url|h}-->">
										<span name="back<!--{$key|h}-->">戻る</span>
									</a>
								<!--{/if}-->
							</li>
							<li>
								<!--{if strlen($tpl_error) == 0}-->
									<input type="submit" class="btn btn-success" value="購入手続きへ" name="confirm" />
								<!--{/if}-->
							</li>
						</ul>
					</div>
				</form>
			</div>
		<!--{/foreach}-->
	<!--{else}-->
		<p class="empty"><span class="attention">※ 現在カート内に商品はございません。</span></p>
	<!--{/if}-->
</article>
