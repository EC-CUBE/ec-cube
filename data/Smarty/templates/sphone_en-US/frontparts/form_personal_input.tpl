<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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

<dt>Name&nbsp;<span class="attention">*</span></dt>
<dd>
    <!--{assign var=key1 value="`$prefix`name01"}-->
    <!--{assign var=key2 value="`$prefix`name02"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
        <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
    <!--{/if}-->
    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" class="boxHarf text data-role-none" placeholder="Last name" />&nbsp;&nbsp;
    <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" class="boxHarf text data-role-none" placeholder="First Name" />
</dd>

<dt>Postal code&nbsp;</dt>
<dd>
    <!--{* <!--{assign var=key1 value="`$prefix`zip01"}--> *}-->
    <!--{* <!--{assign var=key2 value="`$prefix`zip02"}--> *}-->
    <!--{assign var=key1 value="`$prefix`zipcode"}-->
    <!--{assign var=key4 value="`$prefix`addr01"}-->
    <!--{assign var=key5 value="`$prefix`addr02"}-->

    <!--{*
    <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
        <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
    <!--{/if}-->
    *}-->
    <!--{if $arrErr[$key1]}-->
        <div class="attention"><!--{$arrErr[$key1]}--></div>
    <!--{/if}-->

    <!--{* <p><input type="tel" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" max="<!--{$smarty.const.ZIP01_LEN}-->" class="boxShort text data-role-none" />&nbsp;-&nbsp;<input type="tel" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" class="boxShort text data-role-none" /></p> *}-->
    <p><input type="tel" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" max="<!--{$smarty.const.ZIPCODE_LEN}-->" class="boxShort text data-role-none" /></p>

    <!--{* <a href="javascript:fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', '<!--{$key1}-->', '<!--{$key2}-->', '<!--{$key3}-->', '<!--{$key4}-->');" class="btn_sub btn_inputzip">Automatic address input from postal code</a> *}-->
</dd>

<dt>Address&nbsp;<span class="attention">*</span></dt>
<dd>
    <!--{if $arrErr[$key4] || $arrErr[$key5]}-->
        <div class="attention"><!--{$arrErr[$key4]}--><!--{$arrErr[$key5]}--></div>
    <!--{/if}-->

    <input type="text" name="<!--{$key4}-->" value="<!--{$arrForm[$key4]|h}-->" class="boxLong text top data-role-none" placeholder="Municipality name" />
    <input type="text" name="<!--{$key5}-->" value="<!--{$arrForm[$key5]|h}-->" class="boxLong text data-role-none" placeholder="House number/building name" />
</dd>

<dt>Phone number&nbsp;<span class="attention">*</span></dt>
<dd>
    <!--{assign var=key1 value="`$prefix`tel01"}-->
    <!--{assign var=key2 value="`$prefix`tel02"}-->
    <!--{assign var=key3 value="`$prefix`tel03"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2] || $arrErr[$key3]}-->
        <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--><!--{$arrErr[$key3]}--></div>
    <!--{/if}-->
    <input type="tel" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" class="boxShort text data-role-none" />&nbsp;-&nbsp;<input type="tel" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" class="boxShort text data-role-none" />&nbsp;-&nbsp;<input type="tel" name="<!--{$key3}-->" value="<!--{$arrForm[$key3]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" class="boxShort text data-role-none" />
</dd>

<dt>FAX</dt>
<dd>
    <!--{assign var=key1 value="`$prefix`fax01"}-->
    <!--{assign var=key2 value="`$prefix`fax02"}-->
    <!--{assign var=key3 value="`$prefix`fax03"}-->
    <!--{if $arrErr[$key1] || $arrErr[$key2] || $arrErr[$key3]}-->
        <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--><!--{$arrErr[$key3]}--></div>
    <!--{/if}-->
    <input type="tel" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" class="boxShort text data-role-none" />&nbsp;-&nbsp;<input type="tel" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" class="boxShort text data-role-none" />&nbsp;-&nbsp;<input type="tel" name="<!--{$key3}-->" value="<!--{$arrForm[$key3]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" class="boxShort text data-role-none" />
</dd>

<!--{if $flgFields > 1}-->

    <dt>E-mail address&nbsp;<span class="attention">*</span></dt>
    <dd>
        <!--{assign var=key1 value="`$prefix`email"}-->
        <!--{assign var=key2 value="`$prefix`email02"}-->
        <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
            <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
        <!--{/if}-->
        <input type="email" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" class="boxLong text top data-role-none" />
        <input type="email" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" class="boxLong text data-role-none" placeholder="Input twice for confirmation" />
    </dd>

    <!--{if $emailMobile}-->
        <dt>Mobile e-mail address&nbsp;<span class="attention">*</span></dt>
        <dd>
            <!--{assign var=key1 value="`$prefix`email_mobile"}-->
            <!--{assign var=key2 value="`$prefix`email_mobile02"}-->
            <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
                <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
            <!--{/if}-->
            <input type="email" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" class="boxLong text top data-role-none" />
            <input type="email" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" class="boxLong text data-role-none" placeholder="Input twice for confirmation" />
        </dd>
    <!--{/if}-->

    <dt>Gender&nbsp;<span class="attention">*</span></dt>
    <dd>
        <!--{assign var=key1 value="`$prefix`sex"}-->
        <!--{if $arrErr[$key1]}-->
            <div class="attention"><!--{$arrErr[$key1]}--></div>
        <!--{/if}-->
        <span style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
            <p><input type="radio" id="man" name="<!--{$key1}-->" value="1" <!--{if $arrForm[$key1] eq 1}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="man">Male</label>&nbsp;&nbsp;
            <input type="radio" id="woman" name="<!--{$key1}-->" value="2" <!--{if $arrForm[$key1] eq 2}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="woman">Female</label></p>
        </span>
    </dd>

    <dt>Occupation</dt>
    <dd>
        <!--{assign var=key1 value="`$prefix`job"}-->
        <!--{if $arrErr[$key1]}-->
            <div class="attention"><!--{$arrErr[$key1]}--></div>
        <!--{/if}-->
        <select name="<!--{$key1}-->" class="boxLong data-role-none">
            <option value="" selected="selected">Please make a selection</option>
            <!--{html_options options=$arrJob selected=$arrForm[$key1]}-->
        </select>
    </dd>

    <dt>Date of birth</dt>
    <dd>
        <!--{assign var=errBirth value="`$arrErr.year``$arrErr.month``$arrErr.day`"}-->
        <!--{if $errBirth}-->
            <div class="attention"><!--{$errBirth}--></div>
        <!--{/if}-->
        <select name="year" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
            <!--{html_options options=$arrYear selected=$arrForm.year|default:''}-->
        </select><span class="selectdate">Year</span>
        <select name="month" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
            <!--{html_options options=$arrMonth selected=$arrForm.month|default:''}-->
        </select><span class="selectdate">Month</span>
        <select name="day" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
            <!--{html_options options=$arrDay selected=$arrForm.day|default:''}-->
        </select><span class="selectdate">Day</span>
    </dd>

    <!--{if $flgFields > 2}-->
        <dt>Desired password&nbsp;<span class="attention">*</span></dt>
        <dd>
            <!--{if $arrErr.password || $arrErr.password02}-->
                <div class="attention"><!--{$arrErr.password}--><!--{$arrErr.password02}--></div>
            <!--{/if}-->
            <input type="password" name="password" value="<!--{$arrForm.password|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" style="<!--{$arrErr.password|sfGetErrorColor}-->" class="boxLong text top data-role-none" />
            <input type="password" name="password02" value="<!--{$arrForm.password02|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" style="<!--{$arrErr.password|cat:$arrErr.password02|sfGetErrorColor}-->" class="boxLong text data-role-none" placeholder="Enter twice for confirmation" />
            <p class="attention mini">Alphanumeric characters <!--{$smarty.const.PASSWORD_MIN_LEN}--> to <!--{$smarty.const.PASSWORD_MAX_LEN}--> characters</p>
        </dd>

        <dt>Hint for when you have forgotten your password&nbsp;<span class="attention">*</span></dt>
        <dd>
            <!--{if $arrErr.reminder || $arrErr.reminder_answer}-->
                <div class="attention"><!--{$arrErr.reminder}--><!--{$arrErr.reminder_answer}--></div>
            <!--{/if}-->
            <select name="reminder" style="<!--{$arrErr.reminder|sfGetErrorColor}-->" class="boxLong top data-role-none">
                <option value="" selected="selected">Select a question</option>
                <!--{html_options options=$arrReminder selected=$arrForm.reminder}-->
            </select>

            <input type="text" name="reminder_answer" value="<!--{$arrForm.reminder_answer|h}-->" class="boxLong text data-role-none" placeholder="Enter the answer to the question" />
        </dd>

        <dt>Mail magazine&nbsp;<span class="attention">*</span></dt>
        <dd>
            <!--{if $arrErr.mailmaga_flg}-->
                <div class="attention"><!--{$arrErr.mailmaga_flg}--></div>
            <!--{/if}-->
            <ul style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->">
                <li><input type="radio" name="mailmaga_flg" value="1" id="html" <!--{if $arrForm.mailmaga_flg eq 1}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="html">Receive HTML mail + text mail</label></li>
                <li><input type="radio" name="mailmaga_flg" value="2" id="text" <!--{if $arrForm.mailmaga_flg eq 2}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="text">Receive a text mail</label></li>
                <li><input type="radio" name="mailmaga_flg" value="3" id="no" <!--{if $arrForm.mailmaga_flg eq 3}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="no">Do not send an email</label></li>
            </ul>
        </dd>
    <!--{/if}-->
<!--{/if}-->
