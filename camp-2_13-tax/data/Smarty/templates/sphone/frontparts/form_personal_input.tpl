<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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
    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" class="boxHarf text data-role-none" placeholder="姓" />&nbsp;&nbsp;
    <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" class="boxHarf text data-role-none" placeholder="名" />
</dd>

<dt>お名前(フリガナ)&nbsp;<span class="attention">※</span></dt>
<dd>
    <!--{assign var=key1 value="`$prefix`kana01"}-->
    <!--{assign var=key2 value="`$prefix`kana02"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
        <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
    <!--{/if}-->
    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" class="boxHarf text data-role-none" placeholder="セイ"/>&nbsp;&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" class="boxHarf text data-role-none" placeholder="メイ"/>
</dd>

<dt>郵便番号&nbsp;<span class="attention">※</span></dt>
<dd>
    <!--{assign var=key1 value="`$prefix`zip01"}-->
    <!--{assign var=key2 value="`$prefix`zip02"}-->
    <!--{assign var=key3 value="`$prefix`pref"}-->
    <!--{assign var=key4 value="`$prefix`addr01"}-->
    <!--{assign var=key5 value="`$prefix`addr02"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
        <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
    <!--{/if}-->
    <p><input type="tel" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" max="<!--{$smarty.const.ZIP01_LEN}-->" class="boxShort text data-role-none" />&nbsp;－&nbsp;<input type="tel" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" class="boxShort text data-role-none" />&nbsp;&nbsp;<a href="http://search.post.japanpost.jp/zipcode/" target="_blank" rel="external"><span class="fn">郵便番号検索</span></a></p>

    <a href="javascript:fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', '<!--{$key1}-->', '<!--{$key2}-->', '<!--{$key3}-->', '<!--{$key4}-->');" class="btn_sub btn_inputzip">郵便番号から住所自動入力</a>
</dd>

<dt>住所&nbsp;<span class="attention">※</span></dt>
<dd>
    <!--{if $arrErr[$key3] || $arrErr[$key4] || $arrErr[$key5]}-->
        <div class="attention"><!--{$arrErr[$key3]}--><!--{$arrErr[$key4]}--><!--{$arrErr[$key5]}--></div>
    <!--{/if}-->
    <select name="<!--{$key3}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" class="boxHarf top data-role-none">
        <option value="">都道府県</option>
        <!--{html_options options=$arrPref selected=$arrForm[$key3]}-->
    </select>

    <input type="text" name="<!--{$key4}-->" value="<!--{$arrForm[$key4]|h}-->" class="boxLong text top data-role-none" placeholder="市区町村名" />
    <input type="text" name="<!--{$key5}-->" value="<!--{$arrForm[$key5]|h}-->" class="boxLong text data-role-none" placeholder="番地・ビル名" />
</dd>

<dt>電話番号&nbsp;<span class="attention">※</span></dt>
<dd>
    <!--{assign var=key1 value="`$prefix`tel01"}-->
    <!--{assign var=key2 value="`$prefix`tel02"}-->
    <!--{assign var=key3 value="`$prefix`tel03"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2] || $arrErr[$key3]}-->
        <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--><!--{$arrErr[$key3]}--></div>
    <!--{/if}-->
    <input type="tel" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" class="boxShort text data-role-none" />&nbsp;－&nbsp;<input type="tel" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" class="boxShort text data-role-none" />&nbsp;－&nbsp;<input type="tel" name="<!--{$key3}-->" value="<!--{$arrForm[$key3]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" class="boxShort text data-role-none" />
</dd>

<dt>FAX</dt>
<dd>
    <!--{assign var=key1 value="`$prefix`fax01"}-->
    <!--{assign var=key2 value="`$prefix`fax02"}-->
    <!--{assign var=key3 value="`$prefix`fax03"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2] || $arrErr[$key3]}-->
        <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--><!--{$arrErr[$key3]}--></div>
    <!--{/if}-->
    <input type="tel" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" class="boxShort text data-role-none" />&nbsp;－&nbsp;<input type="tel" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" class="boxShort text data-role-none" />&nbsp;－&nbsp;<input type="tel" name="<!--{$key3}-->" value="<!--{$arrForm[$key3]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" class="boxShort text data-role-none" />
</dd>

<!--{if $flgFields > 1}-->

    <dt>メールアドレス&nbsp;<span class="attention">※</span></dt>
    <dd>
        <!--{assign var=key1 value="`$prefix`email"}-->
        <!--{assign var=key2 value="`$prefix`email02"}-->
        <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
            <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
        <!--{/if}-->
        <input type="email" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" class="boxLong text top data-role-none" />
        <input type="email" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" class="boxLong text data-role-none" placeholder="確認のため2回入力してください" />
    </dd>

    <!--{if $emailMobile}-->
        <dt>携帯メールアドレス&nbsp;<span class="attention">※</span></dt>
        <dd>
            <!--{assign var=key1 value="`$prefix`email_mobile"}-->
            <!--{assign var=key2 value="`$prefix`email_mobile02"}-->
            <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
                <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
            <!--{/if}-->
            <input type="email" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" class="boxLong text top data-role-none" />
            <input type="email" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" class="boxLong text data-role-none" placeholder="確認のため2回入力してください" />
        </dd>
    <!--{/if}-->

    <dt>性別&nbsp;<span class="attention">※</span></dt>
    <dd>
        <!--{assign var=key1 value="`$prefix`sex"}-->
        <!--{if $arrErr[$key1]}-->
            <div class="attention"><!--{$arrErr[$key1]}--></div>
        <!--{/if}-->
        <span style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
            <input type="radio" id="man" name="<!--{$key1}-->" value="1" <!--{if $arrForm[$key1] eq 1}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="man">男性</label>&nbsp;&nbsp;
            <input type="radio" id="woman" name="<!--{$key1}-->" value="2" <!--{if $arrForm[$key1] eq 2}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="woman">女性</label>
        </span>
    </dd>

    <dt>職業</dt>
    <dd>
        <!--{assign var=key1 value="`$prefix`job"}-->
        <!--{if $arrErr[$key1]}-->
            <div class="attention"><!--{$arrErr[$key1]}--></div>
        <!--{/if}-->
        <select name="<!--{$key1}-->" class="boxLong data-role-none">
            <option value="">選択してください</option>
            <!--{html_options options=$arrJob selected=$arrForm[$key1]}-->
        </select>
    </dd>

    <dt>生年月日</dt>
    <dd>
        <!--{assign var=errBirth value="`$arrErr.year``$arrErr.month``$arrErr.day`"}-->
        <!--{if $errBirth}-->
            <div class="attention"><!--{$errBirth}--></div>
        <!--{/if}-->
        <select name="year" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
            <!--{html_options options=$arrYear selected=$arrForm.year|default:''}-->
        </select><span class="selectdate">年</span>
        <select name="month" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
            <!--{html_options options=$arrMonth selected=$arrForm.month|default:''}-->
        </select><span class="selectdate">月</span>
        <select name="day" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
            <!--{html_options options=$arrDay selected=$arrForm.day|default:''}-->
        </select><span class="selectdate">日</span>
    </dd>

    <!--{if $flgFields > 2}-->
        <dt>希望するパスワード&nbsp;<span class="attention">※</span></dt>
        <dd>
            <!--{if $arrErr.password || $arrErr.password02}-->
                <div class="attention"><!--{$arrErr.password}--><!--{$arrErr.password02}--></div>
            <!--{/if}-->
            <input type="password" name="password" value="<!--{$arrForm.password|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" style="<!--{$arrErr.password|sfGetErrorColor}-->" class="boxLong text top data-role-none" />
            <input type="password" name="password02" value="<!--{$arrForm.password02|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" style="<!--{$arrErr.password|cat:$arrErr.password02|sfGetErrorColor}-->" class="boxLong text data-role-none" placeholder="確認のため2回入力してください" />
            <p class="attention mini">半角英数字<!--{$smarty.const.PASSWORD_MIN_LEN}-->～<!--{$smarty.const.PASSWORD_MAX_LEN}-->文字</p>
        </dd>

        <dt>パスワードを忘れた時のヒント&nbsp;<span class="attention">※</span></dt>
        <dd>
            <!--{if $arrErr.reminder || $arrErr.reminder_answer}-->
                <div class="attention"><!--{$arrErr.reminder}--><!--{$arrErr.reminder_answer}--></div>
            <!--{/if}-->
            <select name="reminder" style="<!--{$arrErr.reminder|sfGetErrorColor}-->" class="boxLong top data-role-none">
                <option value="">質問を選択してください</option>
                <!--{html_options options=$arrReminder selected=$arrForm.reminder}-->
            </select>

            <input type="text" name="reminder_answer" value="<!--{$arrForm.reminder_answer|h}-->" class="boxLong text data-role-none" placeholder="質問の答えを入力してください" />
        </dd>

        <dt>メールマガジン&nbsp;<span class="attention">※</span></dt>
        <dd>
            <!--{if $arrErr.mailmaga_flg}-->
                <div class="attention"><!--{$arrErr.mailmaga_flg}--></div>
            <!--{/if}-->
            <ul style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->">
                <li><input type="radio" name="mailmaga_flg" value="1" id="html" <!--{if $arrForm.mailmaga_flg eq 1}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="html">HTMLメール＋テキストメールを受け取る</label></li>
                <li><input type="radio" name="mailmaga_flg" value="2" id="text" <!--{if $arrForm.mailmaga_flg eq 2}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="text">テキストメールを受け取る</label></li>
                <li><input type="radio" name="mailmaga_flg" value="3" id="no" <!--{if $arrForm.mailmaga_flg eq 3}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="no">受け取らない</label></li>
            </ul>
        </dd>
    <!--{/if}-->
<!--{/if}-->
