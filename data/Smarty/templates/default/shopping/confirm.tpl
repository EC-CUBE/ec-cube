<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<div id="under02column">
  <div id="under02column_shopping">
    <p class="flowarea"><img src="<!--{$TPL_DIR}-->img/shopping/flow03.gif" width="700" height="36" alt="購入手続きの流れ" /></p>
    <h2 class="title"><img src="<!--{$TPL_DIR}-->img/shopping/confirm_title.jpg" width="700" height="40" alt="ご入力内容のご確認" /></h2>

    <p>下記ご注文内容で送信してもよろしいでしょうか？<br />
      よろしければ、一番下の「<!--{if $payment_type != ""}-->次へ<!--{else}-->ご注文完了ページへ<!--{/if}-->」ボタンをクリックしてください。</p>

    <form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
      <input type="hidden" name="mode" value="confirm" />
      <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
      <table summary="ご注文内容確認">
        <tr>
          <th>商品写真</th>
          <th>商品名</th>
          <th>単価</th>
          <th>個数</th>
          <th>小計</th>
        </tr>
        <!--{section name=cnt loop=$arrProductsClass}-->
        <tr>
          <td class="phototd">
            <a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="win01('<!--{$smarty.const.URL_DIR}-->products/detail_image.php?product_id=<!--{$arrProductsClass[cnt].product_id}-->&amp;image=main_image','detail_image','<!--{$arrProductsClass[cnt].tpl_image_width}-->','<!--{$arrProductsClass[cnt].tpl_image_height}-->'); return false;" target="_blank">
              <img src="<!--{$smarty.const.SITE_URL}-->resize_image.php?image=<!--{$arrProductsClass[cnt].main_list_image}-->&amp;width=65&amp;height=65" alt="<!--{$arrProductsClass[cnt].name|escape}-->" />
            </a>
          </td>
          <td>
            <ul>
              <li><strong><!--{$arrProductsClass[cnt].name|escape}--></strong></li>
              <!--{if $arrProductsClass[cnt].classcategory_name1 != ""}-->
              <li><!--{$arrProductsClass[cnt].class_name1}-->：<!--{$arrProductsClass[cnt].classcategory_name1}--></li>
              <!--{/if}-->
              <!--{if $arrProductsClass[cnt].classcategory_name2 != ""}-->
              <li><!--{$arrProductsClass[cnt].class_name2}-->：<!--{$arrProductsClass[cnt].classcategory_name2}--></li>
              <!--{/if}-->
            </ul>
         </td>
         <td class="pricetd">
         <!--{if $arrProductsClass[cnt].price02 != ""}-->
           <!--{$arrProductsClass[cnt].price02|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
         <!--{else}-->
           <!--{$arrProductsClass[cnt].price01|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
         <!--{/if}-->
         </td>
         <td><!--{$arrProductsClass[cnt].quantity|number_format}-->個</td>
         <td class="pricetd"><!--{$arrProductsClass[cnt].total_pretax|number_format}-->円</td>
       </tr>
       <!--{/section}-->
        <tr>
          <th colspan="4" class="resulttd">小計</th>
          <td class="pricetd"><!--{$tpl_total_pretax|number_format}-->円</td>
        </tr>
        <tr>
          <th colspan="4" class="resulttd">値引き（ポイントご使用時）</th>
          <td class="pricetd">
          <!--{assign var=discount value=`$arrData.use_point*$smarty.const.POINT_VALUE`}-->
           -<!--{$discount|number_format|default:0}-->円</td>
        </tr>
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
    <!--{if $tpl_login == 1 || $arrData.member_check == 1}-->
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
          <th>+<!--{$arrData.birth_point|number_format|default:0}-->Pt</td>
        </tr>
        <!--{/if}-->
        <tr>
          <th>今回加算されるポイント</th>
          <td>+<!--{$arrData.add_point|number_format|default:0}-->Pt</td>
        </tr>
        <tr>
        <!--{assign var=total_point value=`$tpl_user_point-$arrData.use_point+$arrData.add_point`}-->
          <th>ご注文完了後のポイント</th>
          <td><!--{$total_point|number_format}-->Pt</td>
        </tr>
      </table>
    <!--{/if}-->
    <!--{* ログイン済みの会員のみ *}-->

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
          <td><!--{$arrData.deliv_name01|escape}--> <!--{$arrData.deliv_name02|escape}--></td>
        </tr>
        <tr>
          <th>お名前（フリガナ）</th>
          <td><!--{$arrData.deliv_kana01|escape}--> <!--{$arrData.deliv_kana02|escape}--></td>
        </tr>
        <tr>
          <th>郵便番号</th>
          <td>〒<!--{$arrData.deliv_zip01|escape}-->-<!--{$arrData.deliv_zip02|escape}--></td>
        </tr>
        <tr>
          <th>住所</th>
          <td><!--{$arrPref[$arrData.deliv_pref]}--><!--{$arrData.deliv_addr01|escape}--><!--{$arrData.deliv_addr02|escape}--></td>
        </tr>
        <tr>
          <th>電話番号</th>
          <td><!--{$arrData.deliv_tel01}-->-<!--{$arrData.deliv_tel02}-->-<!--{$arrData.deliv_tel03}--></td>
        </tr>
      </table>
      <!--{else}-->
      <tr>
          <th>お名前</th>
          <td><!--{$arrData.order_name01|escape}--> <!--{$arrData.order_name02|escape}--></td>
        </tr>
        <tr>
          <th>お名前（フリガナ）</th>
          <td><!--{$arrData.order_kana01|escape}--> <!--{$arrData.order_kana02|escape}--></td>
        </tr>
        <tr>
          <th>郵便番号</th>
          <td>〒<!--{$arrData.order_zip01|escape}-->-<!--{$arrData.order_zip02|escape}--></td>
        </tr>
        <tr>
          <th>住所</th>
          <td><!--{$arrPref[$arrData.order_pref]}--><!--{$arrData.order_addr01|escape}--><!--{$arrData.order_addr02|escape}--></td>
        </tr>
        <tr>
          <th>電話番号</th>
          <td><!--{$arrData.order_tel01}-->-<!--{$arrData.order_tel02}-->-<!--{$arrData.order_tel03}--></td>
        </tr>
        <!--{/if}-->
        </tbody>
      </table>
      <!--お届け先ここまで-->

      <table summary="お支払方法・お届け時間の指定・その他お問い合わせ" class="delivname">
        <thead>
        <tr>
          <th colspan="2">▼お支払方法・お届け時間の指定・その他お問い合わせ</th>
        </tr>
        </thead>
        <tbody>
        <tr>
          <th>お支払方法</th>
          <td><!--{$arrData.payment_method|escape}--></td>
        </tr>
        <tr>
          <th>お届け日</th>
          <td><!--{$arrData.deliv_date|escape|default:"指定なし"}--></td>
        </tr>
        <tr>
          <th>お届け時間</th>
          <td><!--{$arrData.deliv_time|escape|default:"指定なし"}--></td>
        </tr>
        <tr>
          <th>その他お問い合わせ</th>
          <td><!--{$arrData.message|escape|nl2br}--></td>
        </tr>
        <!--{if $tpl_login == 1}-->
        <tr>
          <th>ポイント使用</th>
          <td><!--{$arrData.use_point|default:0}-->Pt</td>
        </tr>
        <!--{/if}-->
        </tbody>
      </table>

      <div class="tblareabtn">
        <a href="./payment.php" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_back_on.gif',back03)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_back.gif',back03)"><img src="<!--{$TPL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="戻る" border="0" name="back03" id="back03" /></a>&nbsp;
        <!--{if $payment_type != ""}-->
        <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_next_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_next.gif',this)" src="<!--{$TPL_DIR}-->img/common/b_next.gif" alt="次へ" class="box150" name="next" id="next" />
        <!--{else}-->
        <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/shopping/b_ordercomp_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/shopping/b_ordercomp.gif',this)" src="<!--{$TPL_DIR}-->img/shopping/b_ordercomp.gif" alt="ご注文完了ページへ" class="box150" name="next" id="next" />
        <!--{/if}-->
      </div>
    </form>
  </div>
</div>
<!--▲CONTENTS-->
