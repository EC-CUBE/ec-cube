<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
<!--▼CONTENTS-->
<div id="mypagecolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <!--{include file=$tpl_navi}-->
    <div id="mycontentsarea">
        <h3><!--{$tpl_subtitle|h}--></h3>
        <p class="myconditionarea">
        <strong>購入日時：&nbsp;</strong><!--{$arrDisp.create_date|sfDispDBDate}--><br />
        <strong>注文番号：&nbsp;</strong><!--{$arrDisp.order_id}--><br />
        <strong>お支払い方法：&nbsp;</strong><!--{$arrPayment[$arrDisp.payment_id]|h}-->
        <!--{if $arrDisp.deliv_time_id != ""}--><br />
        <strong>お届け時間：&nbsp;</strong><!--{$arrDelivTime[$arrDisp.deliv_time_id]|h}-->
        <!--{/if}-->
        <!--{if $arrDisp.deliv_date != ""}--><br />
        <strong>お届け日：&nbsp;</strong><!--{$arrDisp.deliv_date|h}-->
        <!--{/if}-->
        </p>

        <form action="order.php" method="post">
            <input type="hidden" name="order_id" value="<!--{$arrDisp.order_id}-->">
            <input type="submit" name="submit" value="再注文">
        </form>

        <table summary="購入商品詳細">
            <tr>
                <th>商品コード</th>
                <th>商品名</th>
                <th>商品種別</th>
                <th>単価</th>
                <th>数量</th>
                <th>小計</th>
            </tr>
            <!--{foreach from=$tpl_arrOrderDetail item=orderDetail}-->
                <tr>
                    <td><!--{$orderDetail.product_code|h}--></td>
                    <td><a<!--{if $orderDetail.enable}--> href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$orderDetail.product_id|u}-->"<!--{/if}-->><!--{$orderDetail.product_name|h}--></a></td>
                    <td>
                    <!--{if $orderDetail.product_type_id == PRODUCT_TYPE_DOWNLOAD}-->
                        <!--{if $orderDetail.price == "0" || ( $orderDetail.status >= "4" && $orderDetail.effective == "1" )}-->
                            <a target="_self" href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/download.php?order_id=<!--{$arrDisp.order_id}-->&product_id=<!--{$orderDetail.product_id}-->&product_class_id=<!--{$orderDetail.product_class_id}-->">ダウンロード</a>
                        <!--{elseif $orderDetail.payment_date == "" || $orderDetail.status < "4"}-->
                            ダウンロード商品<BR />（入金確認中）
                        <!--{elseif $orderDetail.effective != "1"}-->
                            ダウンロード商品<BR />（期限切れ）
                        <!--{/if}-->
                    <!--{else if $orderDetail.product_type_id == PRODUCT_TYPE_NORMAL}-->
                            通常商品
                    <!--{/if}-->
                    </td>
                    <!--{assign var=price value=`$orderDetail.price`}-->
                    <!--{assign var=quantity value=`$orderDetail.quantity`}-->
                    <td class="pricetd"><!--{$price|number_format|h}-->円</td>
                    <td><!--{$quantity|h}--></td>
                    <td class="pricetd"><!--{$price|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|sfMultiply:$quantity|number_format}-->円</td>
                </tr>
            <!--{/foreach}-->
            <tr>
                <th colspan="5" class="resulttd">小計</th>
                <td class="pricetd"><!--{$arrDisp.subtotal|number_format}-->円</td>
            </tr>
            <!--{assign var=point_discount value="`$arrDisp.use_point*$smarty.const.POINT_VALUE`"}-->
            <!--{if $point_discount > 0}-->
            <tr>
                <th colspan="5" class="resulttd">ポイント値引き</th>
                <td class="pricetd"><!--{$point_discount|number_format}-->円</td>
            </tr>
            <!--{/if}-->
            <!--{assign var=key value="discount"}-->
            <!--{if $arrDisp[$key] != "" && $arrDisp[$key] > 0}-->
            <tr>
                <th colspan="5" class="resulttd">値引き</th>
                <td class="pricetd"><!--{$arrDisp[$key]|number_format}-->円</td>
            </tr>
            <!--{/if}-->
            <tr>
                <th colspan="5" class="resulttd">送料</th>
                <td class="pricetd"><!--{assign var=key value="deliv_fee"}--><!--{$arrDisp[$key]|number_format|h}-->円</td>
            </tr>
            <tr>
                <th colspan="5" class="resulttd">手数料</th>
                <!--{assign var=key value="charge"}-->
                <td class="pricetd"><!--{$arrDisp[$key]|number_format|h}-->円</td>
            </tr>
            <tr>
                <th colspan="5" class="resulttd">合計</th>
                <td class="pricetd"><em><!--{$arrDisp.payment_total|number_format}-->円</em></td>
            </tr>
        </table>

        <!-- 使用ポイントここから -->
        <!--{if $smarty.const.USE_POINT !== false}-->
            <table summary="使用ポイント">
                <tr>
                    <th>ご使用ポイント</th>
                    <td class="pricetd"><!--{assign var=key value="use_point"}--><!--{$arrDisp[$key]|number_format|default:0}--> pt</td>
                </tr>
                <tr>
                    <th>今回加算されるポイント</th>
                    <td class="pricetd"><!--{$arrDisp.add_point|number_format|default:0}--> pt</td>
                </tr>
            </table>
        <!--{/if}-->
        <!-- 使用ポイントここまで -->

        <!--{foreach item=shippingItem name=shippingItem from=$arrShipping}-->
        <h4>お届け先<!--{if $isMultiple}--><!--{$smarty.foreach.shippingItem.iteration}--><!--{/if}--></h4>
        <!--{if $isMultiple}-->
            <table summary="お届け内容確認">
              <tr>
                <th>商品コード</th>
                <th>商品名</th>
                <th>単価</th>
                <th>数量</th>
                <!--{* XXX 購入小計と誤差が出るためコメントアウト
                <th>小計</th>
                *}-->
              </tr>
              <!--{foreach item=item from=$shippingItem.shipment_item}-->
                  <tr>
                      <td><!--{$item.product_code|h}--></td>
                      <td><!--{* 商品名 *}--><!--{$item.productsClass.name|h}--><br />
                          <!--{if $item.productsClass.classcategory_name1 != ""}-->
                              <!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br />
                          <!--{/if}-->
                          <!--{if $item.productsClass.classcategory_name2 != ""}-->
                              <!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}-->
                          <!--{/if}-->
                      </td>
                      <td class="pricetd">
                          <!--{$item.productsClass.price02|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
                      </td>
                      <td id="quantity"><!--{$item.quantity}--></td>
                      <!--{* XXX 購入小計と誤差が出るためコメントアウト
                      <td class="pricetd"><!--{$item.total_inctax|number_format}-->円</td>
                      *}-->
                  </tr>
              <!--{/foreach}-->
            </table>
         <!--{/if}-->
        <table summary="お届け先" class="delivname">
            <tbody>
                <tr>
                    <th>お名前</th>
                    <td><!--{$shippingItem.shipping_name01|h}-->&nbsp;<!--{$shippingItem.shipping_name02|h}--></td>
                </tr>
                <tr>
                    <th>お名前(フリガナ)</th>
                    <td><!--{$shippingItem.shipping_kana01|h}-->&nbsp;<!--{$shippingItem.shipping_kana02|h}--></td>
                </tr>
                <tr>
                    <th>郵便番号</th>
                    <td>〒<!--{$shippingItem.shipping_zip01}-->-<!--{$shippingItem.shipping_zip02}--></td>
                </tr>
                <tr>
                    <th>住所</th>
                    <td><!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01|h}--><!--{$shippingItem.shipping_addr02|h}--></td>
                </tr>
                <tr>
                    <th>電話番号</th>
                    <td><!--{$shippingItem.shipping_tel01}-->-<!--{$shippingItem.shipping_tel02}-->-<!--{$shippingItem.shipping_tel03}--></td>
                </tr>
            </tbody>
        </table>
        <!--{/foreach}-->

        <br />

        <h3>メール配信履歴一覧</h3>
        <table>
            <tr>
                <th>処理日</th>
                <th>通知メール</th>
                <th>件名</th>
            </tr>
            <!--{section name=cnt loop=$arrMailHistory}-->
            <tr class="center">
                <td><!--{$arrMailHistory[cnt].send_date|sfDispDBDate|h}--></td>
                <!--{assign var=key value="`$arrMailHistory[cnt].template_id`"}-->
                <td><!--{$arrMAILTEMPLATE[$key]|h}--></td>
                <td><a href="#" onclick="win02('./mail_view.php?send_id=<!--{$arrMailHistory[cnt].send_id}-->','mail_view','650','800'); return false;"><!--{$arrMailHistory[cnt].subject|h}--></a></td>
            </tr>
            <!--{/section}-->
        </table>

        <div class="tblareabtn">
            <a href="./<!--{$smarty.const.DIR_INDEX_PATH}-->" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_back_on.gif','change');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_back.gif','change');"><img src="<!--{$TPL_URLPATH}-->img/button/btn_back.gif" width="150" height="30" alt="戻る" name="change" id="change" /></a>
        </div>

    </div>
</div>
<!--▲CONTENTS-->
