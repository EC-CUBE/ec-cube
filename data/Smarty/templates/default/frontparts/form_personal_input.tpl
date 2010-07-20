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
<tr>
  <th>お名前<span class="attention">※</span></th>
  <td>
    <!--{assign var=key1 value="`$prefix`name01"}-->
    <!--{assign var=key2 value="`$prefix`name02"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
    <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
    <!--{/if}-->
    姓&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box120" />&nbsp;
    名&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box120" />
  </td>
</tr>
<tr>
  <th>お名前（フリガナ）<span class="attention">※</span></th>
  <td>
    <!--{assign var=key1 value="`$prefix`kana01"}-->
    <!--{assign var=key2 value="`$prefix`kana02"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
    <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
    <!--{/if}-->
    セイ&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box120" />&nbsp;
    メイ&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box120" />
  </td>
</tr>
<tr>
  <th>郵便番号<span class="attention">※</span></th>
  <td>
    <!--{assign var=key1 value="`$prefix`zip01"}-->
    <!--{assign var=key2 value="`$prefix`zip02"}-->
    <!--{assign var=key3 value="`$prefix`pref"}-->
    <!--{assign var=key4 value="`$prefix`addr01"}-->
    <!--{assign var=key5 value="`$prefix`addr02"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
    <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
    <!--{/if}-->
    〒&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|escape}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" size="6" class="box60" />&nbsp;-&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|escape}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" size="6" class="box60" />&nbsp;
    <a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="fs12">郵便番号検索</span></a>
    <p class="zipimg"><a href="<!--{$smarty.const.URL_DIR}-->input_zip.php" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', '<!--{$key1}-->', '<!--{$key2}-->', '<!--{$key3}-->', '<!--{$key4}-->'); return false;" target="_blank"><img src="<!--{$TPL_DIR}-->img/common/address.gif" width="86" height="20" alt="住所自動入力" /></a>
    <span class="mini">&nbsp;郵便番号を入力後、クリックしてください。</span></p>
  </td>
</tr>
<tr>
  <th>住所<span class="attention">※</span></th>
  <td>
    <!--{if $arrErr[$key3] || $arrErr[$key4] || $arrErr[$key5]}-->
    <div class="attention"><!--{$arrErr[$key3]}--><!--{$arrErr[$key4]}--><!--{$arrErr[$key5]}--></div>
    <!--{/if}-->
    <select name="<!--{$key3}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->">
      <option value="" selected="selected">都道府県を選択</option>
      <!--{html_options options=$arrPref selected=$arrForm[$key3]}-->
    </select>
    <p class="mini"><input type="text" name="<!--{$key4}-->" value="<!--{$arrForm[$key4]|escape}-->" size="60" class="box300" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->; ime-mode: active;" /><br />
     <!--{$smarty.const.SAMPLE_ADDRESS1}--></p>
    <p class="mini"><input type="text" name="<!--{$key5}-->" value="<!--{$arrForm[$key5]|escape}-->" size="60" class="box300" style="<!--{$arrErr[$key5]|sfGetErrorColor}-->; ime-mode: active;" /><br />
      <!--{$smarty.const.SAMPLE_ADDRESS2}--></p>
    <p class="mini"><em>住所は2つに分けてご記入いただけます。マンション名は必ず記入してください。</em></p>
  </td>
</tr>
<tr>
  <th>電話番号<span class="attention">※</span></th>
  <td>
    <!--{assign var=key1 value="`$prefix`tel01"}-->
    <!--{assign var=key2 value="`$prefix`tel02"}-->
    <!--{assign var=key3 value="`$prefix`tel03"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2] || $arrErr[$key3]}-->
    <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--><!--{$arrErr[$key3]}--></div>
    <!--{/if}-->
    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="<!--{$key3}-->" value="<!--{$arrForm[$key3]|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />
  </td>
</tr>
<!--{if $flgFields > 1}-->
<tr>
  <th>FAX</th>
  <td>
    <!--{assign var=key1 value="`$prefix`fax01"}-->
    <!--{assign var=key2 value="`$prefix`fax02"}-->
    <!--{assign var=key3 value="`$prefix`fax03"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2] || $arrErr[$key3]}-->
    <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--><!--{$arrErr[$key3]}--></div>
    <!--{/if}-->
    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="<!--{$key3}-->" value="<!--{$arrForm[$key3]|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />
  </td>
</tr>
<tr>
  <th>メールアドレス<span class="attention">※</span></th>
  <td>
    <!--{assign var=key1 value="`$prefix`email"}-->
    <!--{assign var=key2 value="`$prefix`email02"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
    <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
    <!--{/if}-->
    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|escape}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" size="40" class="box300" /><br />
    <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|default:$arrForm[$key1]|escape}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" size="40" class="box300" /><br />
    <p class="mini"><em>確認のため2度入力してください。</em></p>
  </td>
</tr>
<!--{if $emailMobile}-->
<tr>
  <th>携帯メールアドレス</th>
  <td>
    <!--{assign var=key1 value="`$prefix`email_mobile"}-->
    <!--{assign var=key2 value="`$prefix`email_mobile02"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
    <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
    <!--{/if}-->
    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|escape}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" size="40" class="box300" /><br />
    <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|default:$arrForm[$key1]|escape}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" size="40" class="box300" /><br />
    <p class="mini"><em>確認のため2度入力してください。</em></p>
  </td>
</tr>
<!--{/if}-->
<tr>
  <th>性別<span class="attention">※</span></th>
  <td>
    <!--{assign var=key1 value="`$prefix`sex"}-->
    <!--{if $arrErr[$key1]}-->
    <div class="attention"><!--{$arrErr[$key1]}--></div>
    <!--{/if}-->
    <input type="radio" id="man" name="<!--{$key1}-->" value="1" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" <!--{if $arrForm[$key1] eq 1}--> checked="checked" <!--{/if}--> /><label for="man">男性</label>
    <input type="radio" id="woman" name="<!--{$key1}-->" value="2" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" <!--{if $arrForm[$key1] eq 2}--> checked="checked" <!--{/if}--> /><label for="woman">女性</label>
  </td>
</tr>
<tr>
  <th>職業</th>
  <td>
    <!--{assign var=key1 value="`$prefix`job"}-->
    <!--{if $arrErr[$key1]}-->
    <div class="attention"><!--{$arrErr[$key1]}--></div>
    <!--{/if}-->
    <select name="<!--{$key1}-->">
      <option value="" selected="selected">選択してください</option>
      <!--{html_options options=$arrJob selected=$arrForm[$key1]}-->
    </select>
  </td>
</tr>
<tr>
  <th>生年月日</th>
  <td>
    <!--{if $arrErr.year || $arrErr.month || $arrErr.day}-->
    <div class="attention"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></div>
    <!--{/if}-->
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
  </td>
</tr>
<!--{if $flgFields > 2}-->
<tr>
  <th>希望するパスワード<span class="attention">※</span><br />
    <span class="mini">パスワードは購入時に必要です</span></th>
  <td>
    <!--{if $arrErr.password || $arrErr.password02}-->
    <div class="attention"><!--{$arrErr.password}--><!--{$arrErr.password02}--></div>
    <!--{/if}-->
    <input type="password" name="password" value="<!--{$arrForm.password|escape}-->" maxlength="<!--{$smarty.const.PASSWORD_LEN2}-->" style="<!--{$arrErr.password|sfGetErrorColor}-->" size="15" class="box120" />
    <p><em>半角英数字<!--{$smarty.const.PASSWORD_LEN1}-->〜<!--{$smarty.const.PASSWORD_LEN2}-->文字でお願いします。（記号不可）</em></p>
    <input type="password" name="password02" value="<!--{$arrForm.password02|escape}-->" maxlength="<!--{$smarty.const.PASSWORD_LEN2}-->" style="<!--{$arrErr.password02|sfGetErrorColor}-->" size="15" class="box120" />
    <p><em>確認のために2度入力してください。</em></p>
  </td>
</tr>
<tr>
  <th>パスワードを忘れた時のヒント<span class="attention">※</span></th>
  <td>
    <!--{if $arrErr.reminder || $arrErr.reminder_answer}-->
    <div class="attention"><!--{$arrErr.reminder}--><!--{$arrErr.reminder_answer}--></div>
    <!--{/if}-->
    質問：
    <select name="reminder" style="<!--{$arrErr.reminder|sfGetErrorColor}-->">
      <option value="" selected="selected">選択してください</option>
      <!--{html_options options=$arrReminder selected=$arrForm.reminder}-->
    </select>
    <br />
    答え：<input type="text" name="reminder_answer" value="<!--{$arrForm.reminder_answer|escape}-->" style="<!--{$arrErr.reminder_answer|sfGetErrorColor}-->; ime-mode: active;" size="40" class="box260" />
  </td>
</tr>
<tr>
  <th>メールマガジン送付について<span class="attention">※</span></th>
  <td>
    <!--{if $arrErr.mailmaga_flg}-->
    <div class="attention"><!--{$arrErr.mailmaga_flg}--></div>
    <!--{/if}-->
    <input type="radio" name="mailmaga_flg" value="1" id="html" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $arrForm.mailmaga_flg eq 1}--> checked="checked" <!--{/if}--> /><label for="html">HTMLメール＋テキストメールを受け取る</label><br />
    <input type="radio" name="mailmaga_flg" value="2" id="text" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $arrForm.mailmaga_flg eq 2}--> checked="checked" <!--{/if}--> /><label for="text">テキストメールを受け取る</label><br />
    <input type="radio" name="mailmaga_flg" value="3" id="no" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $arrForm.mailmaga_flg eq 3}--> checked="checked" <!--{/if}--> /><label for="no">受け取らない</label>
  </td>
</tr>
<!--{/if}-->
<!--{/if}-->
