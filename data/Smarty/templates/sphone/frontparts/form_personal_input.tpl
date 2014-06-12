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

<dt>お名前&nbsp;<span class="attention">※</span></dt>
<dd>
    <!--{assign var=key1 value="`$prefix`name01"}-->
    <!--{assign var=key2 value="`$prefix`name02"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
        <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
    <!--{/if}-->
    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="boxHarf text data-role-none" placeholder="姓" />&nbsp;&nbsp;
    <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" class="boxHarf text data-role-none" placeholder="名" />
</dd>

<dt>お名前(フリガナ)<!--{if !$smarty.const.FORM_COUNTRY_ENABLE}-->&nbsp;<span class="attention">※</span><!--{/if}--></dt>
<dd>
    <!--{assign var=key1 value="`$prefix`kana01"}-->
    <!--{assign var=key2 value="`$prefix`kana02"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
        <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
    <!--{/if}-->
    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="boxHarf text data-role-none" placeholder="セイ"/>&nbsp;&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" class="boxHarf text data-role-none" placeholder="メイ"/>
</dd>

<dt>会社名</dt>
<dd>
    <!--{assign var=key1 value="`$prefix`company_name"}-->
    <!--{if $arrErr[$key1]}-->
        <div class="attention"><!--{$arrErr[$key1]}--></div>
    <!--{/if}-->
    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="boxLong text data-role-none" />
</dd>

<!--{if $smarty.const.FORM_COUNTRY_ENABLE}-->
<dt>国&nbsp;<span class="attention">※</span></dt>
<dd>
    <!--{assign var=key1 value="`$prefix`country_id"}-->
    <!--{if $arrErr[$key1]}-->
        <div class="attention"><!--{$arrErr[$key1]}--></div>
    <!--{/if}-->
    <select name="<!--{$key1}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
        <option value="" selected="selected">国を選択</option>
        <!--{html_options options=$arrCountry selected=$arrForm[$key1].value|h|default:$smarty.const.DEFAULT_COUNTRY_ID}-->
    </select>
</dd>

<dt>ZIP CODE</dt>
<dd>
    <!--{assign var=key1 value="`$prefix`zipcode"}-->
    <!--{if $arrErr[$key1]}-->
        <div class="attention"><!--{$arrErr[$key1]}--></div>
    <!--{/if}-->
    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" class="boxLong text data-role-none" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" />
</dd>

<!--{else}-->
<!--{assign var=key1 value="`$prefix`country_id"}-->
<input type="hidden" name="<!--{$key1}-->" value="<!--{$smarty.const.DEFAULT_COUNTRY_ID}-->" />
<!--{/if}-->

<dt>郵便番号<!--{if !$smarty.const.FORM_COUNTRY_ENABLE}-->&nbsp;<span class="attention">※</span><!--{/if}--></dt>
<dd>
    <!--{assign var=key1 value="`$prefix`zip01"}-->
    <!--{assign var=key2 value="`$prefix`zip02"}-->
    <!--{assign var=key3 value="`$prefix`pref"}-->
    <!--{assign var=key4 value="`$prefix`addr01"}-->
    <!--{assign var=key5 value="`$prefix`addr02"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
        <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
    <!--{/if}-->
    <p><input type="tel" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;－&nbsp;<input type="tel" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;&nbsp;<a href="http://search.post.japanpost.jp/zipcode/" target="_blank" rel="external"><span class="fn">郵便番号検索</span></a></p>

    <a href="javascript:eccube.getAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', '<!--{$key1}-->', '<!--{$key2}-->', '<!--{$key3}-->', '<!--{$key4}-->');" class="btn_sub btn_inputzip">郵便番号から住所自動入力</a>
</dd>

<dt>住所&nbsp;<span class="attention">※</span></dt>
<dd>
    <!--{if $arrErr[$key3] || $arrErr[$key4] || $arrErr[$key5]}-->
        <div class="attention"><!--{$arrErr[$key3]}--><!--{$arrErr[$key4]}--><!--{$arrErr[$key5]}--></div>
    <!--{/if}-->
    <select name="<!--{$key3}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" class="boxHarf top data-role-none">
        <option value="">都道府県</option>
        <!--{html_options options=$arrPref selected=$arrForm[$key3].value}-->
    </select>

    <input type="text" name="<!--{$key4}-->" value="<!--{$arrForm[$key4].value|h}-->" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->" class="boxLong text top data-role-none" placeholder="市区町村名" />
    <input type="text" name="<!--{$key5}-->" value="<!--{$arrForm[$key5].value|h}-->" style="<!--{$arrErr[$key5]|sfGetErrorColor}-->" class="boxLong text data-role-none" placeholder="番地・ビル名" />
</dd>

<dt>電話番号&nbsp;<span class="attention">※</span></dt>
<dd>
    <!--{assign var=key1 value="`$prefix`tel01"}-->
    <!--{assign var=key2 value="`$prefix`tel02"}-->
    <!--{assign var=key3 value="`$prefix`tel03"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2] || $arrErr[$key3]}-->
        <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--><!--{$arrErr[$key3]}--></div>
    <!--{/if}-->
    <input type="tel" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;－&nbsp;<input type="tel" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;－&nbsp;<input type="tel" name="<!--{$key3}-->" value="<!--{$arrForm[$key3].value|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" class="boxShort text data-role-none" />
</dd>

<dt>FAX</dt>
<dd>
    <!--{assign var=key1 value="`$prefix`fax01"}-->
    <!--{assign var=key2 value="`$prefix`fax02"}-->
    <!--{assign var=key3 value="`$prefix`fax03"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2] || $arrErr[$key3]}-->
        <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--><!--{$arrErr[$key3]}--></div>
    <!--{/if}-->
    <input type="tel" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;－&nbsp;<input type="tel" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;－&nbsp;<input type="tel" name="<!--{$key3}-->" value="<!--{$arrForm[$key3].value|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" class="boxShort text data-role-none" />
</dd>

<!--{if $flgFields > 1}-->

    <dt>メールアドレス&nbsp;<span class="attention">※</span></dt>
    <dd>
        <!--{assign var=key1 value="`$prefix`email"}-->
        <!--{assign var=key2 value="`$prefix`email02"}-->
        <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
            <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
        <!--{/if}-->
        <input type="email" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="boxLong text top data-role-none" />
        <input type="email" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" class="boxLong text data-role-none" placeholder="確認のため2回入力してください" />
    </dd>

    <!--{if $emailMobile}-->
        <dt>携帯メールアドレス</dt>
        <dd>
            <!--{assign var=key1 value="`$prefix`email_mobile"}-->
            <!--{assign var=key2 value="`$prefix`email_mobile02"}-->
            <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
                <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
            <!--{/if}-->
            <input type="email" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="boxLong text top data-role-none" />
            <input type="email" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" class="boxLong text data-role-none" placeholder="確認のため2回入力してください" />
        </dd>
    <!--{/if}-->

    <dt>性別&nbsp;<span class="attention">※</span></dt>
    <dd>
        <!--{assign var=key1 value="`$prefix`sex"}-->
        <!--{if $arrErr[$key1]}-->
            <div class="attention"><!--{$arrErr[$key1]}--></div>
        <!--{/if}-->
        <p style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
            <!--{html_radios name=$key1 options=$arrSex selected=$arrForm[$key1].value separator='&nbsp;&nbsp;'}-->
        </p>
    </dd>

    <dt>職業</dt>
    <dd>
        <!--{assign var=key1 value="`$prefix`job"}-->
        <!--{if $arrErr[$key1]}-->
            <div class="attention"><!--{$arrErr[$key1]}--></div>
        <!--{/if}-->
        <select name="<!--{$key1}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="boxLong data-role-none">
            <option value="">選択してください</option>
            <!--{html_options options=$arrJob selected=$arrForm[$key1].value}-->
        </select>
    </dd>

    <dt>生年月日</dt>
    <dd>
        <!--{assign var=key1 value="`$prefix`year"}-->
        <!--{assign var=key2 value="`$prefix`month"}-->
        <!--{assign var=key3 value="`$prefix`day"}-->
        <!--{assign var=errBirth value="`$arrErr.$key1``$arrErr.$key2``$arrErr.$key3`"}-->
        <!--{if $errBirth}-->
        <div class="attention"><!--{$errBirth}--></div>
        <!--{/if}-->
        <select name="<!--{$key1}-->" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
            <!--{html_options options=$arrYear selected=$arrForm[$key1].value|default:''}-->
        </select><span class="selectdate">年</span>
        <select name="<!--{$key2}-->" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
            <!--{html_options options=$arrMonth selected=$arrForm[$key2].value|default:''}-->
        </select><span class="selectdate">月</span>
        <select name="<!--{$key3}-->" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
            <!--{html_options options=$arrDay selected=$arrForm[$key3].value|default:''}-->
        </select><span class="selectdate">日</span>
    </dd>

    <!--{if $flgFields > 2}-->
        <dt>希望するパスワード&nbsp;<span class="attention">※</span></dt>
        <dd>
            <!--{assign var=key1 value="`$prefix`password"}-->
            <!--{assign var=key2 value="`$prefix`password02"}-->
            <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
            <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
            <!--{/if}-->
            <input type="password" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="boxLong text top data-role-none" />
            <input type="password" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key1]|cat:$arrErr[$key2]|sfGetErrorColor}-->" class="boxLong text data-role-none" placeholder="確認のため2回入力してください" />
            <p class="attention mini">半角英数字<!--{$smarty.const.PASSWORD_MIN_LEN}-->～<!--{$smarty.const.PASSWORD_MAX_LEN}-->文字</p>
        </dd>

        <dt>パスワードを忘れた時のヒント&nbsp;<span class="attention">※</span></dt>
        <dd>
            <!--{assign var=key1 value="`$prefix`reminder"}-->
            <!--{assign var=key2 value="`$prefix`reminder_answer"}-->
            <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
            <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
            <!--{/if}-->
            <select name="<!--{$key1}-->" style="<!--{$arrErr.reminder|sfGetErrorColor}-->" class="boxLong top data-role-none">
                <option value="">質問を選択してください</option>
                <!--{html_options options=$arrReminder selected=$arrForm[$key1].value}-->
            </select>

            <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" class="boxLong text data-role-none" placeholder="質問の答えを入力してください" />
        </dd>

        <dt>メールマガジン&nbsp;<span class="attention">※</span></dt>
        <dd>
            <!--{assign var=key1 value="`$prefix`mailmaga_flg"}-->
            <!--{if $arrErr[$key1]}-->
                <div class="attention"><!--{$arrErr[$key1]}--></div>
            <!--{/if}-->
            <ul style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->">
                <!--{foreach from=$arrMAILMAGATYPE name=cnt item=type key=key}-->
                <li><input type="radio" name="<!--{$key1}-->" value="<!--{$key}-->" id="<!--{$key1}--><!--{$key}-->" <!--{if $arrForm[$key1].value == $key}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="<!--{$key1}--><!--{$key}-->"><!--{$type}--></label></li>
                <!--{/foreach}-->
            </ul>
        </dd>
    <!--{/if}-->
<!--{/if}-->
