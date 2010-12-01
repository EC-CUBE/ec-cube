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
<!--▼CONTENTS-->
<div id="under02column">
  <div id="under02column_customer">
    <p class="flowarea"><img src="<!--{$TPL_DIR}-->img/picture/img_flow_01.gif" width="700" height="36" alt="購入手続きの流れ" /></p>
    <h2 class="title"><!--{$tpl_title|escape}--></h2>

    <p>下記項目にご入力ください。「<span class="attention">※</span>」印は入力必須項目です。<br />
      入力後、一番下の「確認ページへ」ボタンをクリックしてください。</p>
    <form name="form1" id="form1" method="post" action="?">
      <input type="hidden" name="mode" value="nonmember_confirm" />
      <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
      <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
      <table summary=" ">
        <tr>
          <th>お名前<span class="attention">※</span></th>
          <td>
            <!--{assign var=key1 value="order_name01"}-->
            <!--{assign var=key2 value="order_name02"}-->
            <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
            姓&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box120" />&nbsp;
            名&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box120" />
          </td>
        </tr>
        <tr>
          <th>お名前(フリガナ)<span class="attention">※</span></th>
          <td>
            <!--{assign var=key1 value="order_kana01"}-->
            <!--{assign var=key2 value="order_kana02"}-->
            <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
            セイ&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box120" />&nbsp;
            メイ&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box120" />
          </td>
        </tr>
        <tr>
          <th>郵便番号<span class="attention">※</span></th>
          <td>
            <!--{assign var=key1 value="order_zip01"}-->
            <!--{assign var=key2 value="order_zip02"}-->
            <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
            <p>〒&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"  size="6" class="box60" />&nbsp;-&nbsp;  <input type="text"  name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6" class="box60" />　
              <a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="fs12">郵便番号検索</span></a></p>

            <p class="zipimg"><a href="<!--{$smarty.const.URL_DIR}-->address/<!--{$smarty.const.DIR_INDEX_URL}-->" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'order_zip01', 'order_zip02', 'order_pref', 'order_addr01'); return false;" target="_blank"><img src="<!--{$TPL_DIR}-->img/button/btn_address_input.gif" width="86" height="20" alt="住所自動入力" /></a>
               <span class="mini">&nbsp;郵便番号を入力後、クリックしてください。</span></p>
          </td>
        </tr>
        <tr>
          <th>住所<span class="attention">※</span></th>
          <td>
            <!--{assign var=key value="order_pref"}-->
            <span class="attention"><!--{$arrErr.order_pref}--><!--{$arrErr.order_addr01}--><!--{$arrErr.order_addr02}--></span>
            <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
              <option value="">都道府県を選択</option>
              <!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
            </select>
            <p class="mini">
              <!--{assign var=key value="order_addr01"}-->
              <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" size="40" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box380" /><br />
              <!--{$smarty.const.SAMPLE_ADDRESS1}--></p>
            <p class="mini">
              <!--{assign var=key value="order_addr02"}-->
              <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" size="40"  maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box380" /><br />
              <!--{$smarty.const.SAMPLE_ADDRESS2}--></p>
            <p class="mini"><em>住所は2つに分けてご記入ください。マンション名は必ず記入してください。</em></p></td>
        </tr>
        <tr>
          <th>電話番号<span class="attention">※</span></th>
          <td>
            <!--{assign var=key1 value="order_tel01"}-->
            <!--{assign var=key2 value="order_tel02"}-->
            <!--{assign var=key3 value="order_tel03"}-->
            <span class="attention"><!--{$arrErr[$key1]}--></span>
            <span class="attention"><!--{$arrErr[$key2]}--></span>
            <span class="attention"><!--{$arrErr[$key3]}--></span>
            <input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box60" /> -
            <input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6" class="box60" /> -
            <input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|escape}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box60" />
          </td>
        </tr>
        <tr>
          <th>FAX</th>
          <td>
            <!--{assign var=key1 value="order_fax01"}-->
            <!--{assign var=key2 value="order_fax02"}-->
            <!--{assign var=key3 value="order_fax03"}-->
            <span class="attention"><!--{$arrErr[$key1]}--></span>
            <span class="attention"><!--{$arrErr[$key2]}--></span>
            <span class="attention"><!--{$arrErr[$key3]}--></span>
            <input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box60" /> -
            <input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6" class="box60" /> -
            <input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|escape}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box60" />
          </td>
        </tr>
        <tr>
          <th>メールアドレス<span class="attention">※</span></th>
          <td>
            <!--{assign var=key value="order_email"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box380" /><br />
            <!--{assign var=key value="order_email02"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box380" /><br />
            <p class="mini"><em>確認のため2度入力してください。</em></p>
          </td>
        </tr>
        <tr>
          <th>性別<span class="attention">※</span></th>
          <td>
            <!--{assign var=key value="order_sex"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{if $arrErr[$key]}-->
              <!--{assign var=err value="background-color: `$smarty.const.ERR_COLOR`"}-->
            <!--{/if}-->
            <!--{html_radios name="$key" options=$arrSex selected=$arrForm[$key].value style="$err"}-->
          </td>
        </tr>
        <tr>
          <th>職業</th>
          <td>
            <!--{assign var=key value="order_job"}-->
            <!--{if $arrErr[$key]}-->
              <!--{assign var=err value="background-color: `$smarty.const.ERR_COLOR`"}-->
            <!--{/if}-->
            <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
              <option value="">選択して下さい</option>
              <!--{html_options options=$arrJob selected=$arrForm[$key].value}-->
            </select>
          </td>
        </tr>
        <tr>
          <th>生年月日</th>
          <td>
            <!--{assign var=errBirth value="`$arrErr.year``$arrErr.month``$arrErr.day`"}-->
            <span class="attention"><!--{$errBirth}--></span>
            <select name="year" style="<!--{$errBirth|sfGetErrorColor}-->">
              <!--{html_options options=$arrYear selected=$arrForm.year.value|default:''}-->
            </select>年
            <select name="month" style="<!--{$errBirth|sfGetErrorColor}-->">
              <!--{html_options options=$arrMonth selected=$arrForm.month.value|default:''}-->
            </select>月
            <select name="day" style="<!--{$errBirth|sfGetErrorColor}-->">
              <!--{html_options options=$arrDay selected=$arrForm.day.value|default:''}-->
            </select>日
          </td>
        </tr>
        <tr>
          <th colspan="2">
          <!--{assign var=key value="deliv_check"}-->
          <input type="checkbox" name="<!--{$key}-->" value="1" onclick="fnCheckInputDeliv();" <!--{$arrForm[$key].value|sfGetChecked:1}--> id="deliv_label" />
          <label for="deliv_label"><em>お届け先を指定</em>　※上記に入力された住所と同一の場合は省略可能です。</label>
          </th>
        </tr>
        <tr>
          <th>お名前<span class="attention">※</span></th>
          <td>
            <!--{assign var=key1 value="deliv_name01"}-->
            <!--{assign var=key2 value="deliv_name02"}-->
            <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
            姓&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box120" />&nbsp;
            名&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box120" />
          </td>
        </tr>
        <tr>
          <th>お名前(フリガナ)<span class="attention">※</span></th>
          <td>
            <!--{assign var=key1 value="deliv_kana01"}-->
            <!--{assign var=key2 value="deliv_kana02"}-->
            <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
            セイ&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box120" />&nbsp;
            メイ&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box120" />
          </td>
        </tr>
        <tr>
          <th>郵便番号<span class="attention">※</span></th>
          <td>
           <!--{assign var=key1 value="deliv_zip01"}-->
           <!--{assign var=key2 value="deliv_zip02"}-->
            <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
            <p>〒&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"  size="6" class="box60" />&nbsp;-&nbsp;  <input type="text"  name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6" class="box60" />　
              <a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="fs12">郵便番号検索</span></a></p>

            <p class="zipimg"><a href="<!--{$smarty.const.URL_DIR}-->address/<!--{$smarty.const.DIR_INDEX_URL}-->" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'deliv_zip01', 'deliv_zip02', 'deliv_pref', 'deliv_addr01'); return false;" target="_blank"><img src="<!--{$TPL_DIR}-->img/button/btn_address_input.gif" width="86" height="20" alt="住所自動入力" /></a>
              <span class="mini">&nbsp;郵便番号を入力後、クリックしてください。</span></p>
          </td>
        </tr>
        <tr>
          <th>住所<span class="attention">※</span></th>
          <td>
            <!--{assign var=key value="deliv_pref"}-->
            <span class="attention"><!--{$arrErr.deliv_pref}--><!--{$arrErr.deliv_addr01}--><!--{$arrErr.deliv_addr02}--></span>
            <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
              <option value="">都道府県を選択</option>
              <!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
            </select>
            <p class="mini">
              <!--{assign var=key value="deliv_addr01"}-->
              <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" size="40" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box380" /><br />
              <!--{$smarty.const.SAMPLE_ADDRESS1}--></p>
            <p class="mini">
              <!--{assign var=key value="deliv_addr02"}-->
              <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" size="40"  maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box380" /><br />
              <!--{$smarty.const.SAMPLE_ADDRESS2}--></p>
            <p class="mini"><em>住所は2つに分けてご記入ください。マンション名は必ず記入してください。</em></p>

          </td>
        </tr>
        <tr>
          <th>電話番号<span class="attention">※</span></th>
          <td>
            <!--{assign var=key1 value="deliv_tel01"}-->
            <!--{assign var=key2 value="deliv_tel02"}-->
            <!--{assign var=key3 value="deliv_tel03"}-->
            <span class="attention"><!--{$arrErr[$key1]}--></span>
            <span class="attention"><!--{$arrErr[$key2]}--></span>
            <span class="attention"><!--{$arrErr[$key3]}--></span>
            <input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box60" /> -
            <input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6" class="box60" /> -
            <input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|escape}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box60" />
          </td>
        </tr>
      </table>

      <div class="tblareabtn">
       <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_next_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_next.gif',this)" src="<!--{$TPL_DIR}-->img/button/btn_next.gif" class="box150" alt="次へ" name="next" id="next" />
      </div>
    </form>
  </div>
</div>
<!--▲CONTENTS-->
