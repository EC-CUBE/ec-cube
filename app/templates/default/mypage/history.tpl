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
<article id="article_mypage" class="undercolumn">
    <h1 class="title"><!--{$tpl_title|h}--></h1>
    <!--{include file=$tpl_navi}-->
    <div id="mycontents_area">
        <h2><!--{$tpl_subtitle|h}--></h2>
        <div class="mycondition_area clearfix">
            <p>
                <span class="st">購入日時：&nbsp;</span><!--{$tpl_arrOrderData.create_date|sfDispDBDate}--><br />
                <span class="st">注文番号：&nbsp;</span><!--{$tpl_arrOrderData.order_id}--><br />
                <span class="st">お支払い方法：&nbsp;</span><!--{$arrPayment[$tpl_arrOrderData.payment_id]|h}--><br />
                <!--{if $smarty.const.MYPAGE_ORDER_STATUS_DISP_FLAG}-->
                    <span class="st">ご注文状況：&nbsp;</span>
                    <!--{if $tpl_arrOrderData.status != $smarty.const.ORDER_PENDING}-->
                        <!--{$arrCustomerOrderStatus[$tpl_arrOrderData.status]|h}-->
                    <!--{else}-->
                        <span class="attention"><!--{$arrCustomerOrderStatus[$tpl_arrOrderData.status]|h}--></span>
                    <!--{/if}-->
                <!--{/if}-->
                <!--{if $is_price_change == true}-->
                    <div class="attention" Align="right">※金額が変更されている商品があるため、再注文時はご注意ください。</div>
                <!--{/if}-->
            </p>
            <form action="order.php" method="post" class="fr">
                <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
                <p class="ecbtn">
                    <input type="hidden" name="order_id" value="<!--{$tpl_arrOrderData.order_id|h}-->" />
                    <input type="submit" src="<!--{$TPL_URLPATH}-->img/button/btn_order_re.jpg" alt="この購入内容で再注文する" name="submit" value="この購入内容で再注文する" />
                </p>
            </form>
        </div>

        <table summary="購入商品詳細" class="sm_font_mini">
            <col width="27%" />
            <col width="43%" />
            <col width="30%" />
            <tr class="fs80">
                <th>商品コード<br>商品種別</th>
                <th>商品名<br>単価 x 数量</th>
                <th class="alignR">小計</th>
            </tr>
            <!--{foreach from=$tpl_arrOrderDetail item=orderDetail}-->
                <tr>
                    <td>
						<!--{$orderDetail.product_code|h}--><br>
						<!--{if $orderDetail.product_type_id == $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
							<!--{if $orderDetail.is_downloadable}-->
								<a target="_self" href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/download.php?order_id=<!--{$tpl_arrOrderData.order_id}-->&product_class_id=<!--{$orderDetail.product_class_id}-->">ダウンロード</a>
							<!--{else}-->
								<!--{if $orderDetail.payment_date == "" && $orderDetail.effective == "0"}-->
									<!--{$arrProductType[$orderDetail.product_type_id]}--><br />（入金確認中）
								<!--{else}-->
									<!--{$arrProductType[$orderDetail.product_type_id]}--><br />（期限切れ）
								<!--{/if}-->
							<!--{/if}-->
						<!--{else}-->
							<!--{$arrProductType[$orderDetail.product_type_id]}-->
						<!--{/if}-->						
					</td>
                    <td><a<!--{if $orderDetail.enable}--> href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$orderDetail.product_id|u}-->"<!--{/if}-->><!--{$orderDetail.product_name|h}--></a><br />
                        <!--{if $orderDetail.classcategory_name1 != ""}-->
                            <!--{$orderDetail.classcategory_name1|h}--><br />
                        <!--{/if}-->
                        <!--{if $orderDetail.classcategory_name2 != ""}-->
                            <!--{$orderDetail.classcategory_name2|h}--><br />
                        <!--{/if}-->
					<!--{$orderDetail.price_inctax|n2s|h}-->円
                    <!--{if $orderDetail.price_inctax != $orderDetail.product_price_inctax}-->
                        <div class="attention">【現在価格】</div><span class="attention"><!--{$orderDetail.product_price_inctax|n2s|h}-->円</span>
                    <!--{/if}-->
					x <!--{$orderDetail.quantity|h}-->
                    </td>
                    <td class="alignR"><!--{$orderDetail.price_inctax|sfMultiply:$orderDetail.quantity|n2s}-->円</td>
                </tr>
            <!--{/foreach}-->
            <tr>
                <th colspan="2" class="alignR">小計</th>
                <td class="alignR"><!--{$tpl_arrOrderData.subtotal|n2s}-->円</td>
            </tr>
            <!--{assign var=point_discount value="`$tpl_arrOrderData.use_point*$smarty.const.POINT_VALUE`"}-->
            <!--{if $point_discount > 0}-->
            <tr>
                <th colspan="2" class="alignR">ポイント値引き</th>
                <td class="alignR">&minus;<!--{$point_discount|n2s}-->円</td>
            </tr>
            <!--{/if}-->
            <!--{assign var=key value="discount"}-->
            <!--{if $tpl_arrOrderData[$key] != "" && $tpl_arrOrderData[$key] > 0}-->
            <tr>
                <th colspan="2" class="alignR">値引き</th>
                <td class="alignR">&minus;<!--{$tpl_arrOrderData[$key]|n2s}-->円</td>
            </tr>
            <!--{/if}-->
            <tr>
                <th colspan="2" class="alignR">送料</th>
                <td class="alignR"><!--{assign var=key value="deliv_fee"}--><!--{$tpl_arrOrderData[$key]|n2s|h}-->円</td>
            </tr>
            <tr>
                <th colspan="2" class="alignR">手数料</th>
                <!--{assign var=key value="charge"}-->
                <td class="alignR"><!--{$tpl_arrOrderData[$key]|n2s|h}-->円</td>
            </tr>
            <tr>
                <th colspan="2" class="alignR">合計</th>
                <td class="alignR"><span class="price"><!--{$tpl_arrOrderData.payment_total|n2s}-->円</span></td>
            </tr>
        </table>

        <!-- 使用ポイントここから -->
        <!--{if $smarty.const.USE_POINT !== false}-->
            <dl class="table" title="使用ポイント">
				<dt>ご使用ポイント</dt>
				<dd><!--{assign var=key value="use_point"}--><!--{$tpl_arrOrderData[$key]|n2s|default:0}--> pt&nbsp;</dd>

				<dt>今回加算されるポイント</dt>
				<dd><!--{$tpl_arrOrderData.add_point|n2s|default:0}--> pt&nbsp;</dd>
            </dl>
        <!--{/if}-->
        <!-- 使用ポイントここまで -->

        <!--{foreach item=shippingItem name=shippingItem from=$arrShipping}-->
            <h2>お届け先<!--{if $isMultiple}--><!--{$smarty.foreach.shippingItem.iteration}--><!--{/if}--></h2>
            <!--{if $isMultiple}-->
                <table summary="お届け内容確認" class="sm_font_mini">
                    <col width="30%" />
                    <col width="40%" />
                    <col width="20%" />
                    <col width="10%" />
                    <tr>
                        <th class="alignC">商品コード</th>
                        <th class="alignC">商品名</th>
                        <th class="alignC">単価</th>
                        <th class="alignC">数量</th>
                        <!--{* XXX 購入小計と誤差が出るためコメントアウト
                        <th>小計</th>
                        *}-->
                    </tr>
                    <!--{foreach item=item from=$shippingItem.shipment_item}-->
                        <tr>
                            <td><!--{$item.productsClass.product_code|h}--></td>
                            <td><!--{* 商品名 *}--><!--{$item.productsClass.name|h}--><br />
                                <!--{if $item.productsClass.classcategory_name1 != ""}-->
                                    <!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br />
                                <!--{/if}-->
                                <!--{if $item.productsClass.classcategory_name2 != ""}-->
                                    <!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}-->
                                <!--{/if}-->
                            </td>
                            <td class="alignR">
                                <!--{$item.price|sfCalcIncTax:$tpl_arrOrderData.order_tax_rate:$tpl_arrOrderData.order_tax_rule|n2s}-->円
                            </td>
                            <td class="alignC"><!--{$item.quantity}--></td>
                            <!--{* XXX 購入小計と誤差が出るためコメントアウト
                            <td class="alignR"><!--{$item.total_inctax|n2s}-->円</td>
                            *}-->
                        </tr>
                    <!--{/foreach}-->
                </table>
            <!--{/if}-->
            <dl summary="お届け先" class="delivname table">
				<dt>お名前</dt>
				<dd><!--{$shippingItem.shipping_name01|h}-->&nbsp;<!--{$shippingItem.shipping_name02|h}-->&nbsp;</dd>

				<dt>お名前(フリガナ)</dt>
				<dd><!--{$shippingItem.shipping_kana01|h}-->&nbsp;<!--{$shippingItem.shipping_kana02|h}-->&nbsp;</dd>

				<dt>会社名</dt>
				<dd><!--{$shippingItem.shipping_company_name|h}-->&nbsp;</dd>

			<!--{if $smarty.const.FORM_COUNTRY_ENABLE}-->
				<dt>国</dt>
				<dd><!--{$arrCountry[$shippingItem.shipping_country_id]|h}-->&nbsp;</dd>

				<dt>ZIPCODE</dt>
				<dd><!--{$shippingItem.shipping_zipcode|h}-->&nbsp;</dd>

			<!--{/if}-->
				<dt>郵便番号</dt>
				<dd>〒<!--{$shippingItem.shipping_zip01}-->-<!--{$shippingItem.shipping_zip02}-->&nbsp;</dd>

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

				<dt>お届け日</dt>
				<dd><!--{$shippingItem.shipping_date|default:'指定なし'|h}-->&nbsp;</dd>

				<dt>お届け時間</dt>
				<dd><!--{$shippingItem.shipping_time|default:'指定なし'|h}-->&nbsp;</dd>
            </dl>
        <!--{/foreach}-->

        <br />

        <h2>メール配信履歴一覧</h2>
        <table class="sm_font_mini">
            <tr>
                <th class="alignC">メール</th>
            </tr>
            <!--{section name=cnt loop=$tpl_arrMailHistory}-->
            <tr>
                <!--{assign var=key value="`$tpl_arrMailHistory[cnt].template_id`"}-->
                <td><!--{*処理日*}--><!--{$tpl_arrMailHistory[cnt].send_date|sfDispDBDate|h}--> - <!--{*件名*}--><!--{$arrMAILTEMPLATE[$key]|h}--><br>
				<a href="#" onclick="eccube.openWindow('./mail_view.php?send_id=<!--{$tpl_arrMailHistory[cnt].send_id}-->','mail_view','650','800'); return false;"><!--{$tpl_arrMailHistory[cnt].subject|h}--></a></td>
            </tr>
            <!--{/section}-->
        </table>

        <div class="btn_area">
            <ul>
                <li>
                    <a href="./<!--{$smarty.const.DIR_INDEX_PATH}-->" class="btn btn-default">戻る</a>
                </li>
            </ul>
        </div>

    </div>
</article>
