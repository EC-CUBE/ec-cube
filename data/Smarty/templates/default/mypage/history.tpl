<!--{*
 * Copyright (c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<div id="mypagecolumn">
  <h2 class="title"><img src="<!--{$TPL_DIR}-->img/mypage/title.jpg" width="700" height="40" alt="MYページ" /></h2>
  <!--{include file=$tpl_navi}-->
  <div id="mycontentsarea">
    <h3><img src="<!--{$TPL_DIR}-->img/mypage/subtitle05.gif" width="515" height="32" alt="購入履歴詳細" /></h3>
    <p class="myconditionarea">
    <strong>購入日時：&nbsp;</strong><!--{$arrDisp.create_date|sfDispDBDate}--><br />
    <strong>注文番号：&nbsp;</strong><!--{$arrDisp.order_id}--><br />
    <strong>お支払い方法：&nbsp;</strong><!--{$arrPayment[$arrDisp.payment_id]|escape}-->
    <!--{if $arrDisp.deliv_time_id != ""}--><br />
    <strong>お届け時間指定：&nbsp;</strong><!--{$arrDelivTime[$arrDisp.deliv_time_id]|escape}-->
    <!--{/if}-->
    <!--{if $arrDisp.deliv_date != ""}--><br />
    <strong>お届け日指定：&nbsp;</strong><!--{$arrDisp.deliv_date|escape}-->
    <!--{/if}-->
    </p>

    <table summary="購入商品詳細">
      <tr>
        <th>商品コード</th>
        <th>商品名</th>
        <th>単価</th>
        <th>個数</th>
        <th>小計</th>
      </tr>
      <!--{section name=cnt loop=$arrDisp.quantity}-->
      <tr>
        <td><!--{$arrDisp.product_code[cnt]|escape}--></td>
        <td><a href="<!--{$smarty.const.URL_DIR}-->products/detail.php?product_id=<!--{$arrDisp.product_id[cnt]}-->"><!--{$arrDisp.product_name[cnt]|escape}--></a></td>
        <!--{assign var=price value=`$arrDisp.price[cnt]`}-->
        <!--{assign var=quantity value=`$arrDisp.quantity[cnt]`}-->
        <td class="pricetd"><!--{$price|escape|number_format}-->円</td>
        <td><!--{$quantity|escape}--></td>
        <td class="pricetd"><!--{$price|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|sfMultiply:$quantity|number_format}-->円</td>
      </tr>
      <!--{/section}-->
      <tr>
        <th colspan="4" class="resulttd">小計</th>
        <td class="pricetd"><!--{$arrDisp.subtotal|number_format}-->円</td>
      </tr>
      <!--{assign var=point_discount value="`$arrDisp.use_point*$smarty.const.POINT_VALUE`"}-->
      <!--{if $point_discount > 0}-->
      <tr>
        <th colspan="4" class="resulttd">ポイント値引き</th>
        <td class="pricetd"><!--{$point_discount|number_format}-->円</td>
      </tr>
      <!--{/if}-->
      <!--{assign var=key value="discount"}-->
      <!--{if $arrDisp[$key] != "" && $arrDisp[$key] > 0}-->
      <tr>
        <th colspan="4" class="resulttd">値引き</th>
        <td class="pricetd"><!--{$arrDisp[$key]|number_format}-->円</td>
      </tr>
      <!--{/if}-->
      <tr>
        <th colspan="4" class="resulttd">送料</th>
        <td class="pricetd"><!--{assign var=key value="deliv_fee"}--><!--{$arrDisp[$key]|escape|number_format}-->円</td>
      </tr>
      <tr>
        <th colspan="4" class="resulttd">手数料</th>
        <!--{assign var=key value="charge"}-->
        <td class="pricetd"><!--{$arrDisp[$key]|escape|number_format}-->円</td>
      </tr>
      <tr>
        <th colspan="4" class="resulttd">合計</th>
        <td class="pricetd"><em><!--{$arrDisp.payment_total|number_format}-->円</em></td>
      </tr>
    </table>

    <!-- 使用ポイントここから -->
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
    <!-- 使用ポイントここまで -->

    <table summary="お届け先" class="delivname">
      <thead>
        <tr>
          <th colspan="5">▼お届け先</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>お名前</th>
          <!--{assign var=key1 value="deliv_name01"}-->
          <!--{assign var=key2 value="deliv_name02"}-->
          <td><!--{$arrDisp[$key1]|escape}-->&nbsp;<!--{$arrDisp[$key2]|escape}--></td>
        </tr>
        <tr>
          <th>お名前（フリガナ）</th>
          <!--{assign var=key1 value="deliv_kana01"}-->
          <!--{assign var=key2 value="deliv_kana02"}-->
          <td><!--{$arrDisp[$key1]|escape}-->&nbsp;<!--{$arrDisp[$key2]|escape}--></td>
        </tr>
        <tr>
          <th>郵便番号</th>
          <!--{assign var=key1 value="deliv_zip01"}-->
          <!--{assign var=key2 value="deliv_zip02"}-->
          <td>〒<!--{$arrDisp[$key1]}-->-<!--{$arrDisp[$key2]}--></td>
        </tr>
        <tr>
          <th>住所</th>
          <!--{assign var=pref value=`$arrDisp.deliv_pref`}-->
          <!--{assign var=key value="deliv_addr01"}-->
          <td><!--{$arrPref[$pref]}--><!--{$arrDisp[$key]|escape}--><!--{assign var=key value="deliv_addr02"}--><!--{$arrDisp[$key]|escape}--></td>
        </tr>
        <tr>
          <th>電話番号</th>
          <!--{assign var=key1 value="deliv_tel01"}-->
          <!--{assign var=key2 value="deliv_tel02"}-->
          <!--{assign var=key3 value="deliv_tel03"}-->
          <td><!--{$arrDisp[$key1]}-->-<!--{$arrDisp[$key2]}-->-<!--{$arrDisp[$key3]}--></td>
        </tr>
      </tbody>
    </table>

    <div class="tblareabtn">
      <a href="./index.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/b_back_on.gif','change');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/b_back.gif','change');"><img src="<!--{$TPL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="戻る" name="change" id="change" /></a>
    </div>
  </div>
</div>
<!--▲CONTENTS-->
