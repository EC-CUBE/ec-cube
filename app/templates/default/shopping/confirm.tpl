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
<script type="text/javascript">//<![CDATA[
    var sent = false;

    function fnCheckSubmit() {
        if (sent) {
            alert("只今、処理中です。しばらくお待ちください。");
            return false;
        }
        sent = true;
        return true;
    }
//]]></script>

<!--CONTENTS-->
<article id="article_shopping" class="undercolumn">
	<p class="flow_area"><img src="<!--{$TPL_URLPATH}-->img/picture/img_flow_03.jpg" alt="購入手続きの流れ" /></p>
	<h1 class="title"><!--{$tpl_title|h}--></h1>

	<p class="information">下記ご注文内容で送信してもよろしいでしょうか？<br />
		よろしければ、「<!--{if $use_module}-->次へ<!--{else}-->ご注文完了ページへ<!--{/if}-->」ボタンをクリックしてください。</p>

	<form name="form1" id="form1" method="post" action="?">
		<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
		<input type="hidden" name="mode" value="confirm" />
		<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />

		<div class="btn_area">
			<ul>
				<li>
					<a href="./payment.php" class="btn btn-default">戻る</a>
				</li>
					<!--{if $use_module}-->
				<li>
					<input type="submit" onclick="return fnCheckSubmit();" class="btn btn-success" value="次へ" name="next-top" id="next-top" />
				</li>
					<!--{else}-->
				<li>
					<input type="submit" onclick="return fnCheckSubmit();" class="btn btn-success" value="ご注文完了ページへ" name="next-top" id="next-top" />
				</li>
				<!--{/if}-->
			</ul>
		</div>

		<table summary="ご注文内容確認">
			<col width="66%" />
			<col width="34%" />
			<tr>
				<th class="alignC">商品詳細</th>
				<th class="alignC">小計</th>
			</tr>
			<!--{foreach from=$arrCartItems item=item}-->
				<tr>
					<td>
						<a
							<!--{if $item.productsClass.main_image|strlen >= 1}--> href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->" class="expansion" target="_blank"
							<!--{/if}-->
						>
							<img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->" style="max-width: 65px;max-height: 65px;" alt="<!--{$item.productsClass.name|h}-->" class="fr" />
						</a>
						<strong><!--{$item.productsClass.name|h}--></strong>
					<!--{if $item.productsClass.classcategory_name1 != ""}-->
						<br><!--{$item.productsClass.class_name1|h}-->：<!--{$item.productsClass.classcategory_name1|h}-->
					<!--{/if}-->
					<!--{if $item.productsClass.classcategory_name2 != ""}-->
						<br><!--{$item.productsClass.class_name2|h}-->：<!--{$item.productsClass.classcategory_name2|h}-->
					<!--{/if}-->
					</td>
					<td class="alignR" rowspan="2"><!--{$item.total_inctax|n2s}-->円</td>
				</tr><tr>
					<td>
						<!--{$item.price_inctax|n2s}-->円 x <!--{$item.quantity|n2s}-->
					</td>
				</tr>
			<!--{/foreach}-->
			<tr>
				<th class="alignR" scope="row">小計</th>
				<td class="alignR"><!--{$tpl_total_inctax[$cartKey]|n2s}-->円</td>
			</tr>
			<!--{if $smarty.const.USE_POINT !== false}-->
				<!--{if $arrForm.use_point > 0}-->
				<tr>
					<th class="alignR" scope="row">値引き（ポイントご使用時）</th>
					<td class="alignR">
						<!--{assign var=discount value=`$arrForm.use_point*$smarty.const.POINT_VALUE`}-->
						-<!--{$discount|n2s|default:0}-->円</td>
				</tr>
				<!--{/if}-->
			<!--{/if}-->
			<tr>
				<th class="alignR" scope="row">送料</th>
				<td class="alignR"><!--{$arrForm.deliv_fee|n2s}-->円</td>
			</tr>
			<tr>
				<th class="alignR" scope="row">手数料</th>
				<td class="alignR"><!--{$arrForm.charge|n2s}-->円</td>
			</tr>
			<tr>
				<th class="alignR" scope="row">合計</th>
				<td class="alignR"><span class="price"><!--{$arrForm.payment_total|n2s}-->円</span></td>
			</tr>
		</table>

		<!--{* ログイン済みの会員のみ *}-->
		<!--{if $tpl_login == 1 && $smarty.const.USE_POINT !== false}-->
			<dl title="ポイント確認" class="delivname table">
				<dt>ご注文前のポイント</dt>
				<dd><!--{$tpl_user_point|n2s|default:0}-->Pt&nbsp;</dd>

				<dt scope="row">ご使用ポイント</dt>
				<dd>-<!--{$arrForm.use_point|n2s|default:0}-->Pt&nbsp;</dd>

			<!--{if $arrForm.birth_point > 0}-->
				<dt>お誕生月ポイント</dt>
				<dd>+<!--{$arrForm.birth_point|n2s|default:0}-->Pt&nbsp;</dd>
			<!--{/if}-->
				<dt>今回加算予定のポイント</dt>
				<dd>+<!--{$arrForm.add_point|n2s|default:0}-->Pt&nbsp;</dd>
			<!--{assign var=total_point value=`$tpl_user_point-$arrForm.use_point+$arrForm.add_point`}-->
				<dt>加算後のポイント</dt>
				<dd><!--{$total_point|n2s}-->Pt&nbsp;</dd>
			</dl>
		<!--{/if}-->
		<!--{* ログイン済みの会員のみ *}-->

		<!--{* ▼注文者 *}-->
		<h3>ご注文者</h3>
		<dl title="ご注文者" class="customer table">
			<dt>お名前</dt>
			<dd><!--{$arrForm.order_name01|h}--> <!--{$arrForm.order_name02|h}-->&nbsp;</dd>

			<dt>お名前(フリガナ)</dt>
			<dd><!--{$arrForm.order_kana01|h}--> <!--{$arrForm.order_kana02|h}-->&nbsp;</dd>

			<dt>会社名</dt>
			<dd><!--{$arrForm.order_company_name|h}-->&nbsp;</dd>

		<!--{if $smarty.const.FORM_COUNTRY_ENABLE}-->
			<dt>国</dt>
			<dd><!--{$arrCountry[$arrForm.order_country_id]|h}-->&nbsp;</dd>

			<dt>ZIPCODE</dt>
			<dd><!--{$arrForm.order_zipcode|h}-->&nbsp;</dd>
		<!--{/if}-->
			<dt>郵便番号</dt>
			<dd>〒<!--{$arrForm.order_zip01|h}-->-<!--{$arrForm.order_zip02|h}-->&nbsp;</dd>

			<dt>住所</dt>
			<dd><!--{$arrPref[$arrForm.order_pref]}--><!--{$arrForm.order_addr01|h}--><!--{$arrForm.order_addr02|h}-->&nbsp;</dd>

			<dt>電話番号</dt>
			<dd><!--{$arrForm.order_tel01}-->-<!--{$arrForm.order_tel02}-->-<!--{$arrForm.order_tel03}-->&nbsp;</dd>

			<dt>FAX番号</dt>
			<dd>
				<!--{if $arrForm.order_fax01 > 0}-->
					<!--{$arrForm.order_fax01}-->-<!--{$arrForm.order_fax02}-->-<!--{$arrForm.order_fax03}-->
				<!--{/if}-->
			&nbsp;</dd>

			<dt>メールアドレス</dt>
			<dd><!--{$arrForm.order_email|h}-->&nbsp;</dd>

			<dt>性別</dt>
			<dd><!--{$arrSex[$arrForm.order_sex]|h}-->&nbsp;</dd>

			<dt>職業</dt>
			<dd><!--{$arrJob[$arrForm.order_job]|default:'(未登録)'|h}-->&nbsp;</dd>

			<dt>生年月日</dt>
			<dd>
				<!--{$arrForm.order_birth|regex_replace:"/ .+/":""|regex_replace:"/-/":"/"|default:'(未登録)'|h}-->
			&nbsp;</dd>
		</dl>

		<!--{* ▼お届け先 *}-->
		<!--{foreach item=shippingItem from=$arrShipping name=shippingItem}-->
			<h3>お届け先<!--{if $is_multiple}--><!--{$smarty.foreach.shippingItem.iteration}--><!--{/if}--></h3>
			<!--{if $is_multiple}-->
				<table summary="ご注文内容確認">
					<col width="66%" />
					<col width="34%" />
					<tr>
						<th class="alignC">商品詳細</th>
						<th class="alignC">小計</th>
					</tr>
					<!--{foreach item=item from=$shippingItem.shipment_item}-->
						<tr>
							<td>
								<a
									<!--{if $item.productsClass.main_image|strlen >= 1}--> href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->" class="expansion" target="_blank"
									<!--{/if}-->
								>
									<img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->" style="max-width: 65px;max-height: 65px;" alt="<!--{$item.productsClass.name|h}-->" class="fr" /></a>
							<!--{* 商品名 *}--><strong><!--{$item.productsClass.name|h}--></strong>
								<!--{if $item.productsClass.classcategory_name1 != ""}-->
									<br><!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}-->
								<!--{/if}-->
								<!--{if $item.productsClass.classcategory_name2 != ""}-->
									<br><!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}-->
								<!--{/if}-->
									<br>数量： <!--{$item.quantity}-->
							</td>
							<td class="alignR">
								<!--{$item.total_inctax|n2s}-->円
							</td>
						</tr>
					<!--{/foreach}-->
				</table>
			<!--{/if}-->

			<dl title="お届け先確認" class="delivname table">
				<dt>お名前</dt>
				<dd><!--{$shippingItem.shipping_name01|h}--> <!--{$shippingItem.shipping_name02|h}-->&nbsp;</dd>

				<dt>お名前(フリガナ)</dt>
				<dd><!--{$shippingItem.shipping_kana01|h}--> <!--{$shippingItem.shipping_kana02|h}-->&nbsp;</dd>

				<dt>会社名</dt>
				<dd><!--{$shippingItem.shipping_company_name|h}-->&nbsp;</dd>

			<!--{if $smarty.const.FORM_COUNTRY_ENABLE}-->
				<dt>国</dt>
				<dd><!--{$arrCountry[$shippingItem.shipping_country_id]|h}-->&nbsp;</dd>

				<dt>ZIPCODE</dt>
				<dd><!--{$shippingItem.shipping_zipcode|h}-->&nbsp;</dd>
			<!--{/if}-->

				<dt>郵便番号</dt>
				<dd>〒<!--{$shippingItem.shipping_zip01|h}-->-<!--{$shippingItem.shipping_zip02|h}-->&nbsp;</dd>

				<dt>住所</dt>
				<dd><!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01|h}--><!--{$shippingItem.shipping_addr02|h}-->&nbsp;</dd>

				<dt>電話番号</dt>
				<dd><!--{$shippingItem.shipping_tel01}-->-<!--{$shippingItem.shipping_tel02}-->-<!--{$shippingItem.shipping_tel03}-->&nbsp;</dd>

				<dt>FAX番号</dt>
				<dd>
					<!--{if $shippingItem.shipping_fax01 > 0}-->
						<!--{$shippingItem.shipping_fax01}-->-<!--{$shippingItem.shipping_fax02}-->-<!--{$shippingItem.shipping_fax03}-->
					<!--{/if}-->
				&nbsp;</dd>

			<!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
					<dt>お届け日</dt>
					<dd><!--{$shippingItem.shipping_date|default:"指定なし"|h}-->&nbsp;</dd>

					<dt>お届け時間</dt>
					<dd><!--{$shippingItem.shipping_time|default:"指定なし"|h}-->&nbsp;</dd>
			<!--{/if}-->
			</dl>
		<!--{/foreach}-->
		<!--{* ▲お届け先 *}-->

		<h3>配送方法・お支払方法・その他お問い合わせ</h3>
		<dl title="配送方法・お支払方法・その他お問い合わせ" class="delivname table">
			<dt>配送方法</dt>
			<dd><!--{$arrDeliv[$arrForm.deliv_id]|h}-->&nbsp;</dd>

			<dt>お支払方法</dt>
			<dd><!--{$arrForm.payment_method|h}-->&nbsp;</dd>

			<dt>その他お問い合わせ</dt>
			<dd><!--{$arrForm.message|h|nl2br}-->&nbsp;</dd>
		</dl>

		<div class="btn_area">
			<ul>
				<li>
					<a href="./payment.php" class="btn btn-default" name="back<!--{$key}-->" id="back<!--{$key}-->">戻る</a>
				</li>
				<!--{if $use_module}-->
				<li>
					<input type="submit" onclick="return fnCheckSubmit();" class="btn btn-success" value="次へ" name="next" id="next" />
				</li>
				<!--{else}-->
				<li>
					<input type="submit" onclick="return fnCheckSubmit();" class="btn btn-success" value="ご注文完了ページへ"  name="next" id="next" />
				</li>
				<!--{/if}-->
			</ul>
		</div>
	</form>
</article>
