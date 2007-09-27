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
  <h3><img src="<!--{$TPL_DIR}-->img/mypage/subtitle02.gif" width="515" height="32" alt="会員登録内容変更" /></h3>
  <p>下記項目にご入力ください。「<span class="attention">※</span>」印は入力必須項目です。<br />
    入力後、一番下の「確認ページへ」ボタンをクリックしてください。</p>

  <form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
  <input type="hidden" name="mode" value="confirm" />
  <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|escape}-->" />
    <table summary="会員登録内容変更 " class="delivname">
      <tr>
        <th>お名前<span class="attention">※</span></th>
        <td>
          姓&nbsp;<input type="text" name="name01" value="<!--{$arrForm.name01}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name01|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box120" />&nbsp;
          名&nbsp;<input type="text" name="name02" value="<!--{$arrForm.name02}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name02|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box120" />
          <div class="attention"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></div></td>
      </tr>
      <tr>
        <th>お名前（フリガナ）<span class="attention">※</span></th>
        <td>
          セイ&nbsp;<input type="text" name="kana01" value="<!--{$arrForm.kana01}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana01|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box120" />&nbsp;
          メイ&nbsp;<input type="text" name="kana02" value="<!--{$arrForm.kana02}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana02|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box120" />
          <div class="attention"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></div>
        </td>
      </tr>
      <tr>
        <th>郵便番号<span class="attention">※</span></th>
        <td>
          <!--{assign var=key1 value="zip01"}-->
          <!--{assign var=key2 value="zip02"}-->
          〒&nbsp;<input type="text" name="zip01" value="<!--{$arrForm.zip01}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" style="<!--{$arrErr.zip01|sfGetErrorColor}-->; ime-mode: disabled;" size="6" class="box60" />&nbsp;-&nbsp;<input type="text" name="zip02" value="<!--{$arrForm.zip02}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" style="<!--{$arrErr.zip02|sfGetErrorColor}-->; ime-mode: disabled;" size="6" class="box60" />&nbsp;
          <a href="http://search.post.japanpost.jp/7zip/" target="_blank"><span class="fs12">郵便番号検索</span></a>
          <p class="zipimg"><a href="<!--{$smarty.const.URL_DIR}-->input_zip.php" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'zip01', 'zip02', 'pref', 'addr01'); return false;" target="_blank"><img src="<!--{$TPL_DIR}-->img/common/address.gif" width="86" height="20" alt="住所自動入力" /></a>
          <span class="mini">&nbsp;郵便番号を入力後、クリックしてください。</span></p>
          <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
        </td>
      </tr>
      <tr>
        <th>住所<span class="attention">※</span></th>
        <td>
          <select name="pref" style="<!--{$arrErr.pref|sfGetErrorColor}-->">
            <option value="" selected="selected">都道府県を選択</option>
            <!--{html_options options=$arrPref selected=$arrForm.pref}-->
          </select>
          <p class="mini"><input type="text" name="addr01" value="<!--{$arrForm.addr01}-->" size="60" class="box300" style="<!--{$arrErr.addr01|sfGetErrorColor}-->; ime-mode: active;" /><br />
           <!--{$smarty.const.SAMPLE_ADDRESS1}--></p>

          <p class="mini"><input type="text" name="addr02" value="<!--{$arrForm.addr02}-->" size="60" class="box300" style="<!--{$arrErr.addr02|sfGetErrorColor}-->; ime-mode: active;" /><br />
            <!--{$smarty.const.SAMPLE_ADDRESS2}--></p>
           <span class="attention"><!--{$arrErr.pref}--><!--{$arrErr.addr01}--><!--{$arrErr.addr02}--></span>
          <p class="mini"><em>住所は2つに分けてご記入いただけます。マンション名は必ず記入してください。</em></p></td>
      </tr>
      <tr>
        <th>電話番号<span class="attention">※</span></th>
        <td>
          <input type="text" name="tel01" value="<!--{$arrForm.tel01}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.tel01|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="tel02" value="<!--{$arrForm.tel02}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.tel02|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="tel03" value="<!--{$arrForm.tel03}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.tel03|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />
         <div class="attention"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></div>
         </td>
      </tr>
      <tr>
        <th>FAX</th>
        <td>
         <input type="text" name="fax01" value="<!--{$arrForm.fax01}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.fax01|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="fax02" value="<!--{$arrForm.fax02}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.fax02|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="fax03" value="<!--{$arrForm.fax03}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.fax03|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />
         <div class="attention"><!--{$arrErr.fax01}--><!--{$arrErr.fax02}--><!--{$arrErr.fax03}--></div>
        </td>
      </tr>
      <tr>
        <th>メールアドレス<span class="attention">※</span></th>
        <td>
          <input type="text" name="email" value="<!--{$arrForm.email}-->" style="<!--{$arrErr.email|sfGetErrorColor}-->; ime-mode: disabled;" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" size="40" class="box300" /><div class="attention"><!--{$arrErr.email}--></div><br />
          <input type="text" name="email02" value="<!--{if $arrForm.email02 == ""}--><!--{$arrForm.email}--><!--{else}--><!--{$arrForm.email02}--><!--{/if}-->" style="<!--{$arrErr.email02|sfGetErrorColor}-->; ime-mode: disabled;" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" size="40" class="box300" /><div class="attention"><!--{$arrErr.email02}--></div><br />
          <p class="mini"><em>確認のため2度入力してください。</em></p>
        </td>
      </tr>
      <tr>
        <th>性別<span class="attention">※</span></th>
        <td>
          <input type="radio" id="man" name="sex" value="1" style="<!--{$arrErr.sex|sfGetErrorColor}-->" <!--{if $arrForm.sex eq 1}--> checked="checked" <!--{/if}--> /><label for="man">男性</label>
          <input type="radio" id="woman" name="sex" value="2" style="<!--{$arrErr.sex|sfGetErrorColor}-->" <!--{if $arrForm.sex eq 2}--> checked="checked" <!--{/if}--> /><label for="woman">女性</label>
          <div class="attention"><!--{$arrErr.sex}--></div>
        </td>
      </tr>
      <tr>
        <th>職業</th>
        <td>
          <select name="job">
            <option value="" selected="selected">選択してください</option>
              <!--{html_options options=$arrJob selected=$arrForm.job}-->
          </select>
          <div class="attention"><!--{$arrErr.job}--></div>
        </td>
      </tr>
      <tr>
        <th>生年月日</th>
        <td>
          <select name="year" style="<!--{$arrErr.year|sfGetErrorColor}-->">
            <option value="" selected="selected">--</option>
            <!--{html_options options=$arrYear selected=$arrForm.year}-->
          </select>&nbsp;年
          <select name="month" style="<!--{$arrErr.month|sfGetErrorColor}-->">
            <option value="" selected="selected">--</option>
              <!--{html_options options=$arrMonth selected=$arrForm.month}-->
          </select>&nbsp;月
          <select name="day" style="<!--{$arrErr.day|sfGetErrorColor}-->">
            <option value="" selected="selected">--</option>
            <!--{html_options options=$arrDay selected=$arrForm.day}-->
          </select>&nbsp;日
          <div class="attention"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></div>
        </td>
      </tr>
      <tr>
        <th>希望するパスワード<span class="attention">※</span><br />
          <span class="mini">パスワードは購入時に必要です</span></th>
        <td>
          <input type="password" name="password" value="<!--{$arrForm.password}-->" maxlength="<!--{$smarty.const.PASSWORD_LEN2}-->" style="<!--{$arrErr.password|sfGetErrorColor}-->" size="15" class="box120" />
          <div class="attention"><!--{$arrErr.password}--></div>
          <p><em>半角英数字4〜10文字でお願いします。（記号不可）</em></p>
          <input type="password" name="password02" value="<!--{$arrForm.password02}-->" maxlength="<!--{$smarty.const.PASSWORD_LEN2}-->" style="<!--{$arrErr.password02|sfGetErrorColor}-->" size="15" class="box120" />
          <div class="attention"><!--{$arrErr.password02}--></div>
          <p><em>確認のために2度入力してください。</em></p>
        </td>
      </tr>
      <tr>
        <th>パスワードを忘れた時のヒント<span class="attention">※</span></th>
        <td>質問：
         <select name="reminder" style="<!--{$arrErr.reminder|sfGetErrorColor}-->">
           <option value="" selected="selected">選択してください</option>
           <!--{html_options options=$arrReminder selected=$arrForm.reminder}-->
         </select>
         <div class="attention"><!--{$arrErr.reminder}--></div>
          <br />
          答え：<input type="text" name="reminder_answer" value="<!--{$arrForm.reminder_answer}-->" style="<!--{$arrErr.reminder_answer|sfGetErrorColor}-->; ime-mode: active;" size="40" class="box260" />
          <div class="attention"><!--{$arrErr.reminder_answer}--></div>
        </td>
      </tr>
      <tr>
        <th>メールマガジン送付について<span class="attention">※</span></th>
        <td>
          <input type="radio" name="mailmaga_flg" value="1" id="html" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $arrForm.mailmaga_flg eq 1}--> checked="checked" <!--{/if}--> /><label for="html">HTMLメール＋テキストメールを受け取る</label><br />
          <input type="radio" name="mailmaga_flg" value="2" id="text" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $arrForm.mailmaga_flg eq 2}--> checked="checked" <!--{/if}--> /><label for="text">テキストメールを受け取る</label><br />
          <input type="radio" name="mailmaga_flg" value="3" id="no" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $arrForm.mailmaga_flg eq 3}--> checked="checked" <!--{/if}--> /><label for="no">受け取らない</label>
          <div class="attention"><!--{$arrErr.mailmaga_flg}--></div>
        </td>
      </tr>
    </table>

    <div class="tblareabtn">
      <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_confirm_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_confirm.gif',this)" src="<!--{$TPL_DIR}-->img/common/b_confirm.gif" class="box150" alt="確認ページへ" name="refusal" id="refusal" />
    </div>
  </form>
</div>

</div>
<!--▲CONTENTS-->
