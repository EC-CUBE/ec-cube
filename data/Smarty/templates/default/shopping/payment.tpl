<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<div id="under02column">
  <div id="under02column_shopping">
    <p class="flowarea">
      <img src="<!--{$TPL_DIR}-->img/shopping/flow02.gif" width="700" height="36" alt="購入手続きの流れ" />
    </p>
    <h2 class="title">
      <img src="<!--{$TPL_DIR}-->img/shopping/payment_title.jpg" width="700" height="40" alt="お支払い方法、お届け時間等の指定" /></h2>

    <form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
      <input type="hidden" name="mode" value="confirm" />
      <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
      <div class="payarea">
        <h3><img src="<!--{$TPL_DIR}-->img/shopping/subtitle01.gif" width="670" height="33" alt="お支払方法の指定" /></h3>
        <p>お支払方法をご選択ください。</p>

        <!--{assign var=key value="payment_id"}-->
        <!--{if $arrErr[$key] != ""}-->
        <p class="attention"><!--{$arrErr[$key]}--></p>
        <!--{/if}-->
        <table summary="お支払方法選択">
          <tr>
            <th>選択</th>
            <th colspan="<!--{if $arrPayment[cnt].payment_image == ""}-->2<!--{else}-->3<!--{/if}-->">お支払方法</th>
          </tr>
          <!--{section name=cnt loop=$arrPayment}-->
          <tr>
            <td><input type="radio" id="pay_<!--{$smarty.section.cnt.iteration}-->" name="<!--{$key}-->" onclick="fnModeSubmit('payment', '', '');" value="<!--{$arrPayment[cnt].payment_id}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" <!--{$arrPayment[cnt].payment_id|sfGetChecked:$arrForm[$key].value}--> />
            </td>
            <td>
              <label for="pay_<!--{$smarty.section.cnt.iteration}-->"><!--{$arrPayment[cnt].payment_method|escape}--><!--{if $arrPayment[cnt].note != ""}--><!--{/if}--></label>
            </td>
            <!--{if $arrPayment[cnt].payment_image != ""}-->
            <td>
              <img src="<!--{$smarty.const.IMAGE_SAVE_URL}--><!--{$arrPayment[cnt].payment_image}-->" />
            </td>
            <!--{/if}-->
          </tr>
          <!--{/section}-->
        </table>
      </div>

      <div class="payarea02">
        <h3><img src="<!--{$TPL_DIR}-->img/shopping/subtitle02.gif" width="670" height="33" alt="お届け時間の指定"></h3>
        <p>ご希望の方は、お届け時間を選択してください。</p>
        <div>
          <!--★配達日指定★-->
          <!--{assign var=key value="deliv_date"}-->
          <span class="attention"><!--{$arrErr[$key]}--></span>
          <em>お届け日指定：</em>
          <!--{if !$arrDelivDate}-->
            ご指定頂けません。
          <!--{else}-->
            <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
              <option value="" selected="">指定なし</option>
              <!--{html_options options=$arrDelivDate selected=$arrForm[$key].value}-->
            </select>
          <!--{/if}-->
          <!--{assign var=key value="deliv_time_id"}-->
          <span class="attention"><!--{$arrErr[$key]}--></span>
          <em>お届け時間指定：</em>
          <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
            <option value="" selected="">指定なし</option>
            <!--{html_options options=$arrDelivTime selected=$arrForm[$key].value}-->
          </select>
         </div>
      </div>

      <div class="payarea02">
        <h3><img src="<!--{$TPL_DIR}-->img/shopping/subtitle03.gif" width="670" height="33" alt="その他お問い合わせ"></h3>
        <p>その他お問い合わせ事項がございましたら、こちらにご入力ください。</p>
        <div>
         <!--★その他お問い合わせ事項★-->
         <!--{assign var=key value="message"}-->
         <span class="attention"><!--{$arrErr[$key]}--></span>
         <textarea name="<!--{$key}-->"  style="<!--{$arrErr[$key]|sfGetErrorColor}-->" cols="80" rows="8" class="area660" wrap="head"><!--{$arrForm[$key].value|escape}--></textarea>
         <span class="attention"> （<!--{$smarty.const.LTEXT_LEN}-->文字まで）</span>
      </div>

      <!-- ▼ポイント使用 ここから -->
      <!--{if $tpl_login == 1}-->
    <div class="pointarea">
      <h3><img src="<!--{$TPL_DIR}-->img/shopping/subtitle_point.jpg" width="670" height="32" alt="ポイント使用の指定" /></h3>

        <p><span class="attention">1ポイントを1円</span>として使用する事ができます。<br />
          使用する場合は、「ポイントを使用する」にチェックを入れた後、使用するポイントをご記入ください。</p>
      <div>
        <p><!--{$objCustomer->getValue('name01')|escape}--> <!--{$objCustomer->getValue('name02')|escape}-->様の、現在の所持ポイントは「<em><!--{$tpl_user_point|default:0}-->Pt</em>」です。</p>
        <p>今回ご購入合計金額：<span class="price"><!--{$arrData.subtotal|number_format}-->円</span><span class="attention">（送料、手数料を含みません。）</span></p>
        <ul>
          <li><input type="radio" id="point_on" name="point_check" value="1" <!--{$arrForm.point_check.value|sfGetChecked:1}--> onclick="fnCheckInputPoint();" /><label for="point_on">ポイントを使用する</label></li>
           <!--{assign var=key value="use_point"}-->
           <span class="attention"><!--{$arrErr[$key]}--></span>
           <li class="underline">今回のお買い物で、<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|default:$tpl_user_point}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="6" class="box60" />&nbsp;ポイントを使用する。</li>
           <li><input type="radio" id="point_off" name="point_check" value="2" <!--{$arrForm.point_check.value|sfGetChecked:2}--> onclick="fnCheckInputPoint();" /><label for="point_off">ポイントを使用しない</label></li>
         </ul>
      </div>
    </div>
      <!--{/if}-->
      <!-- ▲ポイント使用 ここまで -->

      <div class="tblareabtn">
        <a href="<!--{$tpl_back_url|escape}-->" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/b_back_on.gif','back03')" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/b_back.gif','back03')">
          <img src="<!--{$TPL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="戻る" border="0" name="back03" id="back03" / >
        </a>&nbsp;
        <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_next_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_next.gif',this)" src="<!--{$TPL_DIR}-->img/common/b_next.gif" class="box150" alt="次へ" name="next" id="next" />
      </div>
    </form>
  </div>
</div>
<!--▲CONTENTS-->


