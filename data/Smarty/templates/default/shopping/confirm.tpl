<!--{*
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
 *}-->
<script type="text/javascript" src="<!--{$smarty.const.URL_PATH}-->js/jquery.facebox/facebox.js"></script>
<link rel="stylesheet" type="text/css" href="<!--{$smarty.const.URL_PATH}-->js/jquery.facebox/facebox.css" media="screen" />
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
    $('a.expansion').facebox({
        loadingImage : '<!--{$smarty.const.URL_PATH}-->js/jquery.facebox/loading.gif',
        closeImage   : '<!--{$smarty.const.URL_PATH}-->js/jquery.facebox/closelabel.png'
    });
});
//]]></script>

<!--▼CONTENTS-->
<div id="under02column">
    <div id="under02column_shopping">
        <p class="flowarea"><img src="<!--{$TPL_DIR}-->img/picture/img_flow_03.gif" width="700" height="36" alt="購入手続きの流れ" /></p>
        <h2 class="title"><!--{$tpl_title|h}--></h2>

        <p>下記ご注文内容で送信してもよろしいでしょうか？<br />
            よろしければ、「<!--{if $payment_type != ""}-->次へ<!--{else}-->ご注文完了ページへ<!--{/if}-->」ボタンをクリックしてください。</p>

        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="mode" value="confirm" />
            <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />

            <div class="tblareabtn">
                <a href="./payment.php" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_back_on.gif',back03)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_back.gif',back03)"><img src="<!--{$TPL_DIR}-->img/button/btn_back.gif" width="150" height="30" alt="戻る" border="0" name="back03-top" id="back03-top" /></a>&nbsp;
                <!--{if $payment_type != ""}-->
                <input type="image" onclick="return fnCheckSubmit();" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_next_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_next.gif',this)" src="<!--{$TPL_DIR}-->img/button/btn_next.gif" alt="次へ" class="box150" name="next-top" id="next-top" />
                <!--{else}-->
                <input type="image" onclick="return fnCheckSubmit();" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_order_complete_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_order_complete.gif',this)" src="<!--{$TPL_DIR}-->img/button/btn_order_complete.gif" alt="ご注文完了ページへ" class="box150" name="next-top" id="next-top" />                <!--{/if}-->
            </div>

            <table summary="ご注文内容確認">
                <tr>
                    <th>商品写真</th>
                    <th>商品名</th>
                    <th>単価</th>
                    <th>数量</th>
                    <th>小計</th>
                </tr>
                <!--{foreach from=$cartItems item=item}-->
                <tr>
                    <td class="phototd">
                        <a
                            <!--{if $item.productsClass.main_image|strlen >= 1}-->
                                href="<!--{$smarty.const.IMAGE_SAVE_URL_PATH}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->"
                                class="expansion"
                                target="_blank"
                            <!--{/if}-->
                        >
                            <img src="<!--{$smarty.const.URL_PATH}-->resize_image.php?image=<!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$item.productsClass.name|h}-->" /></a>
                    </td>
                    <td>
                        <ul>
                            <li><strong><!--{$item.productsClass.name|h}--></strong></li>
                            <!--{if $item.productsClass.classcategory_name1 != ""}-->
                            <li><!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--></li>
                            <!--{/if}-->
                            <!--{if $item.productsClass.classcategory_name2 != ""}-->
                            <li><!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}--></li>
                            <!--{/if}-->
                        </ul>
                 </td>
                 <td class="pricetd">
                     <!--{$item.productsClass.price02|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
                 </td>
                 <td><!--{$item.quantity|number_format}--></td>
                 <td class="pricetd"><!--{$item.total_inctax|number_format}-->円</td>
             </tr>
             <!--{/foreach}-->
                <tr>
                    <th colspan="4" class="resulttd">小計</th>
                    <td class="pricetd"><!--{$tpl_total_inctax[$cartKey]|number_format}-->円</td>
                </tr>
                <!--{if $smarty.const.USE_POINT !== false}-->
                    <tr>
                        <th colspan="4" class="resulttd">値引き（ポイントご使用時）</th>
                        <td class="pricetd">
                        <!--{assign var=discount value=`$arrData.use_point*$smarty.const.POINT_VALUE`}-->
                         -<!--{$discount|number_format|default:0}-->円</td>
                    </tr>
                <!--{/if}-->
                <tr>
                    <th colspan="4" class="resulttd">送料</th>
                    <td class="pricetd"><!--{$arrData.deliv_fee|number_format}-->円</td>
                </tr>
                <tr>
                    <th colspan="4" class="resulttd">手数料</th>
                    <td class="pricetd"><!--{$arrData.charge|number_format}-->円</td>
                </tr>
                <tr>
                    <th colspan="4" class="resulttd">合計</th>
                    <td class="pricetd"><em><!--{$arrData.payment_total|number_format}-->円</em></td>
                </tr>
            </table>

            <!--{* ログイン済みの会員のみ *}-->
            <!--{if $tpl_login == 1 && $smarty.const.USE_POINT !== false}-->
                <table summary="ポイント確認" class="delivname">
                    <tr>
                        <th>ご注文前のポイント</th>
                        <td><!--{$tpl_user_point|number_format|default:0}-->Pt</td>
                    </tr>
                    <tr>
                        <th>ご使用ポイント</th>
                        <td>-<!--{$arrData.use_point|number_format|default:0}-->Pt</td>
                    </tr>
                    <!--{if $arrData.birth_point > 0}-->
                    <tr>
                        <th>お誕生月ポイント</th>
                        <td>+<!--{$arrData.birth_point|number_format|default:0}-->Pt</td>
                    </tr>
                    <!--{/if}-->
                    <tr>
                        <th>今回加算予定のポイント</th>
                        <td>+<!--{$arrData.add_point|number_format|default:0}-->Pt</td>
                    </tr>
                    <tr>
                    <!--{assign var=total_point value=`$tpl_user_point-$arrData.use_point+$arrData.add_point`}-->
                        <th>加算後のポイント</th>
                        <td><!--{$total_point|number_format}-->Pt</td>
                    </tr>
                </table>
            <!--{/if}-->
            <!--{* ログイン済みの会員のみ *}-->

            <!--お届け先ここから-->
            <!--{* 販売方法判定（ダウンロード販売のみの場合はお届け先を表示しない） *}-->
            <!--{if $cartdown != "2"}-->
            <table summary="お届け先確認" class="delivname">
                <thead>
                    <tr>
                        <th colspan="2">▼お届け先</th>
                    </tr>
                </thead>
                <tbody>
                    <!--{* 別のお届け先が選択されている場合 *}-->
                    <!--{if $arrData.deliv_check >= 1}-->
                        <tr>
                            <th>お名前</th>
                            <td><!--{$arrData.deliv_name01|h}--> <!--{$arrData.deliv_name02|h}--></td>
                        </tr>
                        <tr>
                            <th>お名前(フリガナ)</th>
                            <td><!--{$arrData.deliv_kana01|h}--> <!--{$arrData.deliv_kana02|h}--></td>
                        </tr>
                        <tr>
                            <th>郵便番号</th>
                            <td>〒<!--{$arrData.deliv_zip01|h}-->-<!--{$arrData.deliv_zip02|h}--></td>
                        </tr>
                        <tr>
                            <th>住所</th>
                            <td><!--{$arrPref[$arrData.deliv_pref]}--><!--{$arrData.deliv_addr01|h}--><!--{$arrData.deliv_addr02|h}--></td>
                        </tr>
                        <tr>
                            <th>電話番号</th>
                            <td><!--{$arrData.deliv_tel01}-->-<!--{$arrData.deliv_tel02}-->-<!--{$arrData.deliv_tel03}--></td>
                        </tr>
                    <!--{else}-->
                        <tr>
                            <th>お名前</th>
                            <td><!--{$arrData.order_name01|h}--> <!--{$arrData.order_name02|h}--></td>
                        </tr>
                        <tr>
                            <th>お名前(フリガナ)</th>
                            <td><!--{$arrData.order_kana01|h}--> <!--{$arrData.order_kana02|h}--></td>
                        </tr>
                        <tr>
                            <th>郵便番号</th>
                            <td>〒<!--{$arrData.order_zip01|h}-->-<!--{$arrData.order_zip02|h}--></td>
                        </tr>
                        <tr>
                            <th>住所</th>
                            <td><!--{$arrPref[$arrData.order_pref]}--><!--{$arrData.order_addr01|h}--><!--{$arrData.order_addr02|h}--></td>
                        </tr>
                        <tr>
                            <th>電話番号</th>
                            <td><!--{$arrData.order_tel01}-->-<!--{$arrData.order_tel02}-->-<!--{$arrData.order_tel03}--></td>
                        </tr>
                    <!--{/if}-->
                </tbody>
            </table>
            <!--{/if}-->
            <!--お届け先ここまで-->

            <table summary="お支払方法・お届け日時の指定・その他お問い合わせ" class="delivname">
                <thead>
                <tr>
                    <th colspan="2">▼お支払方法・お届け日時の指定・その他お問い合わせ</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th>お支払方法</th>
                    <td><!--{$arrData.payment_method|h}--></td>
                </tr>
                <!--{* 販売方法判定（ダウンロード販売のみの場合はお届け日、時間を表示しない） *}-->
                <!--{if $cartdown != "2"}-->
                <tr>
                    <th>お届け日</th>
                    <td><!--{$arrData.deliv_date|default:"指定なし"|h}--></td>
                </tr>
                <tr>
                    <th>お届け時間</th>
                    <td><!--{$arrData.deliv_time|default:"指定なし"|h}--></td>
                </tr>
                <!--{/if}-->
                <tr>
                    <th>その他お問い合わせ</th>
                    <td><!--{$arrData.message|h|nl2br}--></td>
                </tr>
                </tbody>
            </table>

            <!--{if 'sfTSPrintOrderBox'|function_exists}-->
                <!--{'sfTSPrintOrderBox'|call_user_func}-->
            <!--{/if}-->

            <div class="tblareabtn">
                <a href="./payment.php" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_back_on.gif',back03)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_back.gif',back03)"><img src="<!--{$TPL_DIR}-->img/button/btn_back.gif" width="150" height="30" alt="戻る" border="0" name="back03" id="back03" /></a>&nbsp;
                <!--{if $payment_type != ""}-->
                <input type="image" onclick="return fnCheckSubmit();" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_next_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_next.gif',this)" src="<!--{$TPL_DIR}-->img/button/btn_next.gif" alt="次へ" class="box150" name="next" id="next" />
                <!--{else}-->
                <input type="image" onclick="return fnCheckSubmit();" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_order_complete_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_order_complete.gif',this)" src="<!--{$TPL_DIR}-->img/button/btn_order_complete.gif" alt="ご注文完了ページへ" class="box150" name="next" id="next" />
                <!--{/if}-->
            </div>
        </form>
    </div>
</div>
<!--▲CONTENTS-->
