<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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

<!--{strip}-->
    <col width="30%" />
    <col width="70%" />
    <tr>
        <th>お名前<span class="attention">※</span></th>
        <td>
            <!--{assign var=key1 value="`$prefix`name01"}-->
            <!--{assign var=key2 value="`$prefix`name02"}-->
            <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
                <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
            <!--{/if}-->
            姓&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: active;" class="box120" />&nbsp;
            名&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: active;" class="box120" />
        </td>
    </tr>
    <tr>
        <th>お名前(フリガナ)<!--{if !$smarty.const.FORM_COUNTRY_ENABLE}--><span class="attention">※</span><!--{/if}--></th>
        <td>
            <!--{assign var=key1 value="`$prefix`kana01"}-->
            <!--{assign var=key2 value="`$prefix`kana02"}-->
            <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
                <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
            <!--{/if}-->
            セイ&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: active;" class="box120" />&nbsp;
            メイ&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: active;" class="box120" />
        </td>
    </tr>
    <tr>
        <th>会社名</th>
        <td>
            <!--{assign var=key1 value="`$prefix`company_name"}-->
            <!--{if $arrErr[$key1]}-->
                <div class="attention"><!--{$arrErr[$key1]}--></div>
            <!--{/if}-->
            <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: active;" class="box300" />&nbsp;
        </td>
    </tr>
    <!--{assign var=key1 value="`$prefix`zip01"}-->
    <!--{assign var=key2 value="`$prefix`zip02"}-->
    <!--{assign var=key3 value="`$prefix`pref"}-->
    <!--{assign var=key4 value="`$prefix`addr01"}-->
    <!--{assign var=key5 value="`$prefix`addr02"}-->
    <!--{assign var=key6 value="`$prefix`country_id"}-->
    <!--{assign var=key7 value="`$prefix`zipcode"}-->
    <!--{if !$smarty.const.FORM_COUNTRY_ENABLE}-->
    <input type="hidden" name="<!--{$key6}-->" value="<!--{$smarty.const.DEFAULT_COUNTRY_ID}-->" />
    <!--{else}-->
    <tr>
        <th>国<span class="attention">※</span></th>
        <td>
            <!--{if $arrErr[$key6]}-->
                <div class="attention"><!--{$arrErr[$key6]}--></div>
            <!--{/if}-->
            <select name="<!--{$key6}-->" style="<!--{$arrErr[$key6]|sfGetErrorColor}-->">
                    <option value="" selected="selected">国を選択</option>
                    <!--{html_options options=$arrCountry selected=$arrForm[$key6].value|h|default:$smarty.const.DEFAULT_COUNTRY_ID}-->
            </select>
        </td>
    </tr>
    <tr>
        <th>ZIP CODE</th>
        <td>
            <!--{if $arrErr[$key7]}-->
                <div class="attention"><!--{$arrErr[$key7]}--></div>
            <!--{/if}-->
            <input type="text" name="<!--{$key7}-->" value="<!--{$arrForm[$key7].value|h}-->" maxlength="<!--{$arrForm[$key7].length}-->" class="box120" style="<!--{$arrErr[$key7]|sfGetErrorColor}-->; ime-mode: disabled;" />
        </td>
    </tr>
    <!--{/if}-->
    <tr>
        <th>郵便番号<!--{if !$smarty.const.FORM_COUNTRY_ENABLE}--><span class="attention">※</span><!--{/if}--></th>
        <td>
            <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
                <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
            <!--{/if}-->
            <p class="top">〒&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;
            <a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="mini">郵便番号検索</span></a>
            </p>
            <p class="zipimg">
                <a href="<!--{$smarty.const.ROOT_URLPATH}-->input_zip.php" onclick="eccube.getAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', '<!--{$key1}-->', '<!--{$key2}-->', '<!--{$key3}-->', '<!--{$key4}-->'); return false;" target="_blank">
                    <img src="<!--{$TPL_URLPATH}-->img/button/btn_address_input.jpg" alt="住所自動入力" /></a>
                &nbsp;<span class="mini">郵便番号を入力後、クリックしてください。</span>
            </p>
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
                    <!--{html_options options=$arrPref selected=$arrForm[$key3].value|h}-->
            </select>
            <p class="top"><input type="text" name="<!--{$key4}-->" value="<!--{$arrForm[$key4].value|h}-->" class="box300" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->; ime-mode: active;" /><br />
                <!--{$smarty.const.SAMPLE_ADDRESS1}--></p>
            <p class="top"><input type="text" name="<!--{$key5}-->" value="<!--{$arrForm[$key5].value|h}-->" class="box300" style="<!--{$arrErr[$key5]|sfGetErrorColor}-->; ime-mode: active;" /><br />
                <!--{$smarty.const.SAMPLE_ADDRESS2}--></p>
            <p class="mini"><span class="attention">住所は2つに分けてご記入ください。マンション名は必ず記入してください。</span></p>
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
            <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="<!--{$key3}-->" value="<!--{$arrForm[$key3].value|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />
        </td>
    </tr>
    <tr>
        <th>FAX</th>
        <td>
            <!--{assign var=key1 value="`$prefix`fax01"}-->
            <!--{assign var=key2 value="`$prefix`fax02"}-->
            <!--{assign var=key3 value="`$prefix`fax03"}-->
            <!--{if $arrErr[$key1] || $arrErr[$key2] || $arrErr[$key3]}-->
                <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--><!--{$arrErr[$key3]}--></div>
            <!--{/if}-->
            <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="<!--{$key3}-->" value="<!--{$arrForm[$key3].value|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />
        </td>
    </tr>
    <!--{if $flgFields > 1}-->
        <tr>
            <th>メールアドレス<span class="attention">※</span></th>
            <td>
                <!--{assign var=key1 value="`$prefix`email"}-->
                <!--{assign var=key2 value="`$prefix`email02"}-->
                <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
                    <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
                <!--{/if}-->
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box300 top" /><br />
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" style="<!--{$arrErr[$key1]|cat:$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box300" /><br />
                <span class="attention mini">確認のため2度入力してください。</span>
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
                    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" class="box300 top" /><br />
                    <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" style="<!--{$arrErr[$key1]|cat:$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" class="box300" /><br />
                    <span class="attention mini">確認のため2度入力してください。</span>
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
                <span style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                    <!--{html_radios name=$key1 options=$arrSex selected=$arrForm[$key1].value separator='<br />'}-->
                </span>
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
                    <!--{html_options options=$arrJob selected=$arrForm[$key1].value}-->
                </select>
            </td>
        </tr>
        <tr>
            <th>生年月日</th>
            <td>
                <!--{assign var=key1 value="`$prefix`year"}-->
                <!--{assign var=key2 value="`$prefix`month"}-->
                <!--{assign var=key3 value="`$prefix`day"}-->
                <!--{assign var=errBirth value="`$arrErr.$key1``$arrErr.$key2``$arrErr.$key3`"}-->
                <!--{if $errBirth}-->
                    <div class="attention"><!--{$errBirth}--></div>
                <!--{/if}-->
                <select name="<!--{$key1}-->" style="<!--{$errBirth|sfGetErrorColor}-->">
                    <!--{html_options options=$arrYear selected=$arrForm[$key1].value|default:''}-->
                </select>年&nbsp;
                <select name="<!--{$key2}-->" style="<!--{$errBirth|sfGetErrorColor}-->">
                    <!--{html_options options=$arrMonth selected=$arrForm[$key2].value|default:''}-->
                </select>月&nbsp;
                <select name="<!--{$key3}-->" style="<!--{$errBirth|sfGetErrorColor}-->">
                    <!--{html_options options=$arrDay selected=$arrForm[$key3].value|default:''}-->
                </select>日
            </td>
        </tr>
        <!--{if $flgFields > 2}-->
            <tr>
                <th>希望するパスワード<span class="attention">※</span><br />
                </th>
                <td>
                    <!--{assign var=key1 value="`$prefix`password"}-->
                    <!--{assign var=key2 value="`$prefix`password02"}-->
                    <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
                        <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
                    <!--{/if}-->
                    <input type="password" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="box120" />
                    <p><span class="attention mini">半角英数字<!--{$smarty.const.PASSWORD_MIN_LEN}-->～<!--{$smarty.const.PASSWORD_MAX_LEN}-->文字でお願いします。（記号可）</span></p>
                    <input type="password" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key1]|cat:$arrErr[$key2]|sfGetErrorColor}-->" class="box120" />
                    <p><span class="attention mini">確認のために2度入力してください。</span></p>
                </td>
            </tr>
            <tr>
                <th>パスワードを忘れた時のヒント<span class="attention">※</span></th>
                <td>
                    <!--{assign var=key1 value="`$prefix`reminder"}-->
                    <!--{assign var=key2 value="`$prefix`reminder_answer"}-->
                    <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
                        <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
                    <!--{/if}-->
                    質問：
                    <select name="<!--{$key1}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                        <option value="" selected="selected">選択してください</option>
                        <!--{html_options options=$arrReminder selected=$arrForm[$key1].value}-->
                    </select>
                    <br />
                    答え：<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: active;" class="box260" />
                </td>
            </tr>
            <tr>
                <th>メールマガジン送付について<span class="attention">※</span></th>
                <td>
                    <!--{assign var=key1 value="`$prefix`mailmaga_flg"}-->
                    <!--{if $arrErr[$key1]}-->
                        <div class="attention"><!--{$arrErr[$key1]}--></div>
                    <!--{/if}-->
                    <span style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                        <!--{html_radios name=$key1 options=$arrMAILMAGATYPE selected=$arrForm[$key1].value separator='<br />'}-->
                    </span>
                </td>
            </tr>
        <!--{/if}-->
    <!--{/if}-->
<!--{/strip}-->
