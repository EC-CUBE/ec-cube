<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
 *}-->
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
<script type="text/javascript">//<![CDATA[
var send = true;

function fnCheckSubmit() {
    if(send) {
        send = false;
        return true;
    } else {
        alert("只今、処理中です。しばらくお待ち下さい。");
        return false;
    }
}

$(document).ready(function() {
    $('a.expansion').fancybox();
});
//]]></script>

<!--▼CONTENTS-->
<h2 class="title"><!--{$tpl_title|h}--></h2>

<p>下記ご注文内容で送信してもよろしいでしょうか？<br />
    よろしければ、「<!--{if $use_module}-->次へ<!--{else}-->ご注文完了ページへ<!--{/if}-->」ボタンをクリックしてください。</p>

<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="confirm" />
    <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
    <table summary="ご注文内容確認" class="entryform">
        <tr>
            <th class="alignC confirm_ph valignM">商品写真</th>
            <th class="alignC valignM">商品名</th>
            <th class="alignC valignM">数量</th>
            <th class="alignC valignM">小計</th>
        </tr>
        <!--{foreach from=$arrCartItems item=item}-->
        <tr>
            <td class="phototd">
                <a
                    <!--{if $item.productsClass.main_image|strlen >= 1}-->
                        href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->"
                        class="expansion"
                        target="_blank"
                    <!--{/if}-->
                >
                    <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->&amp;width=40&amp;height=40" alt="<!--{$item.productsClass.name|h}-->" /></a>
            </td>
            <td class="detailtdName"><strong><!--{$item.productsClass.name|h}--></strong>
                    <!--{if $item.productsClass.classcategory_name1 != ""}-->
                    <!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br />
                    <!--{/if}-->
                    <!--{if $item.productsClass.classcategory_name2 != ""}-->
                    <!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}-->
                    <!--{/if}--><br />
<!--{$item.price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
         </td>
         <td class="detailtdNumber"><!--{$item.quantity|number_format}--></td>
         <td class="alignR"><!--{$item.total_inctax|number_format}-->円</td>
     </tr>
     <!--{/foreach}-->
        <tr>
            <th colspan="3" class="resulttd">小計</th>
            <td class="alignR"><!--{$tpl_total_inctax[$cartKey]|number_format}-->円</td>
        </tr>
        <!--{if $smarty.const.USE_POINT !== false}-->
            <tr>
                <th colspan="3" class="resulttd">値引き（ポイントご使用時）</th>
                <td class="alignR">
                <!--{assign var=discount value=`$arrForm.use_point*$smarty.const.POINT_VALUE`}-->
                 -<!--{$discount|number_format|default:0}-->円</td>
            </tr>
        <!--{/if}-->
        <tr>
            <th colspan="3" class="resulttd">送料</th>
            <td class="pricetd"><!--{$arrForm.deliv_fee|number_format}-->円</td>
        </tr>
        <tr>
            <th colspan="3" class="resulttd">手数料</th>
            <td class="pricetd"><!--{$arrForm.charge|number_format}-->円</td>
        </tr>
        <tr>
            <th colspan="3" class="resulttd">合計</th>
            <td class="pricetd"><em><!--{$arrForm.payment_total|number_format}-->円</em></td>
        </tr>
    </table>

    <!--{* ログイン済みの会員のみ *}-->
    <!--{if $tpl_login == 1 && $smarty.const.USE_POINT !== false}-->
        <table summary="ポイント確認" class="entryform">
            <tr>
                <th class="trpoint">ご注文前のポイント</th>
                <td class="alignR"><!--{$tpl_user_point|number_format|default:0}-->Pt</td>
            </tr>
            <tr>
                <th class="trpoint">ご使用ポイント</th>
                <td class="alignR">-<!--{$arrForm.use_point|number_format|default:0}-->Pt</td>
            </tr>
            <!--{if $arrForm.birth_point > 0}-->
            <tr>
                <th class="trpoint">お誕生月ポイント</th>
                <td class="alignR">+<!--{$arrForm.birth_point|number_format|default:0}-->Pt</td>
            </tr>
            <!--{/if}-->
            <tr>
                <th class="trpoint">今回加算予定のポイント</th>
                <td class="alignR">+<!--{$arrForm.add_point|number_format|default:0}-->Pt</td>
            </tr>
            <tr>
            <!--{assign var=total_point value=`$tpl_user_point-$arrForm.use_point+$arrForm.add_point`}-->
                <th class="trpoint">加算後のポイント</th>
                <td class="alignR"><!--{$total_point|number_format}-->Pt</td>
            </tr>
        </table>
    <!--{/if}-->
    <!--{* ログイン済みの会員のみ *}-->
    
    <!--お届け先ここから-->
    <!--{* 販売方法判定（ダウンロード販売のみの場合はお届け先を表示しない） *}-->
    <!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
    <!--{foreach item=shippingItem from=$arrShipping name=shippingItem}-->
    <h2>お届け先<!--{if $is_multiple}--><!--{$smarty.foreach.shippingItem.iteration}--><!--{/if}--></h2>
   <!--{if $is_multiple}-->
    <table summary="ご注文内容確認" class="entryform">
      <tr>
        <th class="alignC valignM">商品写真</th>
        <th class="alignC valignM">商品名</th>
        <th class="alignC valignM">単価</th>
        <th class="alignC valignM">数量</th>
        <!--{* XXX 購入小計と誤差が出るためコメントアウト
        <th>小計</th>
        *}-->
      </tr>
      <!--{foreach item=item from=$shippingItem.shipment_item}-->
          <tr>
              <td class="phototd">
                <a
                    <!--{if $item.productsClass.main_image|strlen >= 1}-->
                        href="<!--{$smarty.const.IMAGE_SAVE_URL}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->"
                        class="expansion"
                        target="_blank"
                    <!--{/if}-->
                >
                    <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$item.productsClass.name|h}-->" /></a>
              </td>
              <td><!--{* 商品名 *}--><strong><!--{$item.productsClass.name|h}--></strong><br />
                  <!--{if $item.productsClass.classcategory_name1 != ""}-->
                      <!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br />
                  <!--{/if}-->
                  <!--{if $item.productsClass.classcategory_name2 != ""}-->
                      <!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}-->
                  <!--{/if}-->
              </td>
              <td class="alignR">
                  <!--{$item.price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
              </td>
              <td class="detailtdNumber"><!--{$item.quantity}--></td>
              <!--{* XXX 購入小計と誤差が出るためコメントアウト
              <td class="alignR"><!--{$item.total_inctax|number_format}-->円</td>
              *}-->
          </tr>
      <!--{/foreach}-->
    </table>
   <!--{/if}-->

    <table summary="お届け先確認" class="entryform">
        <tbody>
            <tr>
                <th>お名前</th>
                <td><!--{$shippingItem.shipping_name01|h}--> <!--{$shippingItem.shipping_name02|h}--></td>
            </tr>
            <tr>
                <th>お名前(フリガナ)</th>
                <td><!--{$shippingItem.shipping_kana01|h}--> <!--{$shippingItem.shipping_kana02|h}--></td>
            </tr>
            <tr>
                <th>郵便番号</th>
                <td>〒<!--{$shippingItem.shipping_zip01|h}-->-<!--{$shippingItem.shipping_zip02|h}--></td>
            </tr>
            <tr>
                <th>住所</th>
                <td><!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01|h}--><!--{$shippingItem.shipping_addr02|h}--></td>
            </tr>
            <tr>
                <th>電話番号</th>
                <td><!--{$shippingItem.shipping_tel01}-->-<!--{$shippingItem.shipping_tel02}-->-<!--{$shippingItem.shipping_tel03}--></td>
            </tr>
        <!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
            <tr>
                <th>お届け日</th>
                <td><!--{$shippingItem.shipping_date|default:"指定なし"|h}--></td>
            </tr>
            <tr>
               <th>お届け時間</th>
                <td><!--{$shippingItem.shipping_time|default:"指定なし"|h}--></td>
            </tr>
        <!--{/if}-->
        </tbody>
    </table>
    <!--{/foreach}-->
    <!--{/if}-->
    <!--お届け先ここまで-->
    <h2>その他指定・お問い合わせ</h2>
    <table summary="配送方法・お支払方法・お届け日時の指定・その他お問い合わせ" class="entryform">
        <tbody>
        <tr>
            <th>配送方法</th>
            <td><!--{$arrDeliv[$arrForm.deliv_id]|h}--></td>
        </tr>
        <tr>
            <th>お支払方法</th>
            <td><!--{$arrForm.payment_method|h}--></td>
        </tr>
        <tr>
            <th>その他お問い合わせ</th>
            <td><!--{$arrForm.message|h|nl2br}--></td>
        </tr>
        </tbody>
    </table>

    <div class="tblareabtn">
        <!--{if $use_module}--><p>
         <input type="submit" value="次へ" class="spbtn spbtn-shopping" width="130" height="30" alt="次へ" name="next" id="next" />
        <!--{else}-->
         <input type="submit" value="ご注文完了ページへ" class="spbtn spbtn-shopping" width="130" height="30" alt="ご注文完了ページへ" name="next" id="next" />
        </p><!--{/if}-->
        <p><a href="./payment.php" class="spbtn spbtn-medeum">戻る</a></p>
    </div>
</form>
<!--▲CONTENTS-->
