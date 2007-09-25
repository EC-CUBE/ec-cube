<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
 <!--▼CONTENTS-->
<div id="undercolumn">
  <div id="undercolumn_entry">
    <h2 class="title">
      <img src="<!--{$TPL_DIR}-->img/entry/title.jpg" width="580" height="40" alt="会員登録" />
    </h2>
    <p>ご登録されますと、まずは仮会員となります。<br />
      入力されたメールアドレスに、ご連絡が届きますので、本会員になった上でお買い物をお楽しみください。</p>
    <form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
      <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
      <input type="hidden" name="mode" value="confirm" />

      <table summary="会員登録フォーム">
        <tr>
          <th>お名前<span class="attention">※</span></th>
          <td>
            <span class="attention"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
            姓&nbsp;<input type="text" name="name01" size="15" value="<!--{$name01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name01|sfGetErrorColor}-->; ime-mode: active;" class="box120" />&nbsp;
            名&nbsp;<input type="text" name="name02" size="15" value="<!--{$name02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name02|sfGetErrorColor}-->; ime-mode: active;" class="box120" />
          </td>
        </tr>
        <tr>
          <th>お名前（フリガナ）<span class="attention">※</span></th>
          <td>
            <span class="attention"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>
            セイ&nbsp;<input type="text" name="kana01" size="15" class="box120" value="<!--{$kana01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana01|sfGetErrorColor}-->; ime-mode: active;" />&nbsp;
            メイ&nbsp;<input type="text" name="kana02" size="15" class="box120" value="<!--{$kana02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana02|sfGetErrorColor}-->; ime-mode: active;" />
          </td>
        </tr>
        <tr>
          <th>郵便番号<span class="attention">※</span></th>
          <td>
            <!--{assign var=key1 value="zip01"}-->
            <!--{assign var=key2 value="zip02"}-->
            <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
            <p>〒&nbsp;<input type="text" name="zip01" value="<!--{if $zip01 == ""}--><!--{$arrOtherDeliv.zip01|escape}--><!--{else}--><!--{$zip01|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" style="<!--{$arrErr.zip01|sfGetErrorColor}-->; ime-mode: disabled;" size="6" class="box60" />&nbsp;-&nbsp;<input type="text" name="zip02" value="<!--{if $zip02 == ""}--><!--{$arrOtherDeliv.zip02|escape}--><!--{else}--><!--{$zip02|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" style="<!--{$arrErr.zip02|sfGetErrorColor}-->; ime-mode: disabled;" size="6" class="box60" />&nbsp;
              <a href="http://search.post.japanpost.jp/7zip/" target="_blank"><span class="fs10">郵便番号検索</span></a></p>
              <p class="zipimg">
                <a href="<!--{$smarty.const.URL_DIR}-->address/index.php" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'zip01', 'zip02', 'pref', 'addr01'); return false;" target="_blank"><img src="<!--{$TPL_DIR}-->img/common/address.gif" width="86" height="20" alt="住所自動入力" />
                </a>
                <span class="mini">&nbsp;郵便番号を入力後、クリックしてください。</span>
              </p>
          </td>
        </tr>
        <tr>
          <th>住所<span class="attention">※</span></th>
          <td><span class="attention"><!--{$arrErr.pref}--><!--{$arrErr.addr01}--><!--{$arrErr.addr02}--></span>
              <select name="pref" style="<!--{$arrErr.pref|sfGetErrorColor}-->">
                <option value="" selected="selected">都道府県を選択</option>
                <!--{html_options options=$arrPref selected=$pref}-->
              </select>
             <p class="mini"><input type="text" name="addr01" size="40" class="box380" value="<!--{$addr01|escape}-->" style="<!--{$arrErr.addr01|sfGetErrorColor}-->; ime-mode: active;" /><br />
             <!--{$smarty.const.SAMPLE_ADDRESS1}--></p>
             <p class="mini"><input type="text" name="addr02" size="40" value="<!--{$addr02|escape}-->" style="<!--{$arrErr.addr02|sfGetErrorColor}-->; ime-mode: active;" class="box380" /><br />
             <!--{$smarty.const.SAMPLE_ADDRESS2}--></p>
            <p class="mini"><em>住所は2つに分けてご記入いただけます。マンション名は必ず記入してください。</em></p>
          </td>
        </tr>
        <tr>
          <th>電話番号<span class="attention">※</span></th>
          <td>
            <span class="attention"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span>
            <input type="text" name="tel01" size="6" value="<!--{$tel01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" class="box60" style="<!--{$arrErr.tel01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="tel02" size="6" value="<!--{$tel02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel02|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="tel03" size="6" value="<!--{$tel03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel03|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />
          </td>
        </tr>
        <tr>
          <th>FAX</th>
          <td><span class="attention"><!--{$arrErr.fax01}--><!--{$arrErr.fax02}--><!--{$arrErr.fax03}--></span>
            <input type="text" name="fax01" size="6" value="<!--{$fax01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->"  style="<!--{$arrErr.fax01|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="fax02" size="6" value="<!--{$fax02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.fax01|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="fax03" size="6" value="<!--{$fax02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.fax01|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />
          </td>
        </tr>
        <tr>
          <th>メールアドレス<span class="attention">※</span></th>
          <td>
            <span class="attention"><!--{$arrErr.email}--><!--{$arrErr.email02}--></span>
            <div><input type="text" name="email" size="40" class="box380" value="<!--{$email|escape}-->" style="<!--{$arrErr.email|sfGetErrorColor}-->; ime-mode: disabled;" /></div>
            <div><input type="text" name="email02" size="40" class="box380" value="<!--{$email02|escape}-->" style="<!--{$arrErr.email02|sfGetErrorColor}-->; ime-mode: disabled;" /></div>
            <p class="mini"><em>確認のため2度入力してください。</em></p>
          </td>
        </tr>
        <tr>
          <th>性別<span class="attention">※</span></th>
          <td>
            <span class="attention"><!--{$arrErr.sex}--></span>
            <input type="radio" name="sex" id="man" value="1" style="<!--{$arrErr.sex|sfGetErrorColor}-->" <!--{if $sex eq 1}--> checked="checked"<!--{/if}--> /><label for="man">男性</label>&nbsp;
            <input type="radio" name="sex" id="woman" value="2" style="<!--{$arrErr.sex|sfGetErrorColor}-->" <!--{if $sex eq 2}--> checked="checked"<!--{/if}--> /><label for="woman">女性</label>
          </td>
        </tr>
        <tr>
          <th>職業</th>
          <td>
            <span class="attention"><!--{$arrErr.job}--></span>
            <select name="job" style="<!--{$arrErr.job|sfGetErrorColor}-->">
              <option value="" selected="selected">選択してください</option>
              <!--{html_options options=$arrJob selected=$job}-->
            </select>
          </td>
        </tr>
        <tr>
          <th>生年月日</th>
          <td>
            <span class="attention"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></span>
            <select name="year" style="<!--{$arrErr.year|sfGetErrorColor}-->">
              <!--{html_options options=$arrYear selected=$year}-->
            </select>年
            <select name="month" style="<!--{$arrErr.year|sfGetErrorColor}-->">
              <option value="">--</option>
              <!--{html_options options=$arrMonth selected=$month}-->
            </select>月
            <select name="day" style="<!--{$arrErr.year|sfGetErrorColor}-->">
              <option value="">--</option>
              <!--{html_options options=$arrDay selected=$day}-->
            </select>日
          </td>
        </tr>
        <tr>
          <th>希望するパスワード<span class="attention">※</span><br />
            <span class="mini">パスワードは購入時に必要です</span></th>
          <td>
            <span class="attention"><!--{$arrErr.password}--><!--{$arrErr.password02}--></span>
            <div><input type="password" name="password" value="<!--{$arrForm.password}-->" size="15" class="box120" style="<!--{$arrErr.password|sfGetErrorColor}-->"/></div>
            <p class="mini attention">半角英数字4〜10文字でお願いします。（記号不可）</p>
            <div><input type="password" name="password02" value="<!--{$arrForm.password02}-->" size="15" class="box120"  style="<!--{$arrErr.password02|sfGetErrorColor}-->"/></div>
            <p class="mini attention">確認のために2度入力してください。</p>
        </tr>
        <tr>
          <th>パスワードを忘れた時のヒント<span class="attention">※</span></th>
          <td>
            <span class="attention"><!--{$arrErr.reminder}--><!--{$arrErr.reminder_answer}--></span>
            質問：
            <select name="reminder" style="<!--{$arrErr.reminder|sfGetErrorColor}-->">
              <option value="" selected="selected">選択してください</option>
              <!--{html_options options=$arrReminder selected=$reminder}-->
            </select><br />
              答え：<input type="text" name="reminder_answer" size="33" value="<!--{$reminder_answer|escape}-->" style="<!--{$arrErr.reminder_answer|sfGetErrorColor}-->; ime-mode: active;" class="box320" />
           </td>
        </tr>
        <tr>
          <th>メールマガジン送付について<span class="attention">※</span></th>
          <td>
            <span class="attention"><!--{$arrErr.mailmaga_flg}--></span>
            <div>
              <input type="radio" name="mailmaga_flg" id="html" value="1"
                     style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->"
              <!--{if $mailmaga_flg eq 1 || $mailmaga_flg eq ""}--> checked="checked"<!--{/if}--> /><label for="html">HTMLメール＋テキストメールを受け取る</label>
            </div>
            <div>
              <input type="radio" name="mailmaga_flg" id="text" value="2"
                     style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->"
              <!--{if $mailmaga_flg eq 2}--> checked="checked"<!--{/if}--> /><label for="text">テキストメールを受け取る</label>
            </div>
            <div>
              <input type="radio" name="mailmaga_flg" id="no" value="3"
                     style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->"
              <!--{if $mailmaga_flg eq 3}--> checked="checked"<!--{/if}--> /><label for="no">受け取らない</label>
            </div>
          </td>
        </tr>
      </table>

      <div class="tblareabtn">
        <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_confirm_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_confirm.gif',this)" src="<!--{$TPL_DIR}-->img/common/b_confirm.gif" class="box150" alt="確認ページへ" name="confirm" id="confirm" />
      </div>
    </form>
  </div>
</div>
<!--▲CONTENTS-->
