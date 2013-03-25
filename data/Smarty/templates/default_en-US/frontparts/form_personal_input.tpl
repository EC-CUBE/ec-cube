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

<col width="24%" />
<col width="" />
<tr>
    <th>Name<span class="attention">*</span></th>
    <td>
        <!--{assign var=key1 value="`$prefix`name01"}-->
        <!--{assign var=key2 value="`$prefix`name02"}-->
        <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
            <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
        <!--{/if}-->
        Last name&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->;" class="box120" />&nbsp;
        First Name&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->;" class="box120" />
    </td>
</tr>
<tr>
    <th>Postal code</th>
    <td>
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
        
        <p class="top">
        <!--{* &nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp; *}-->
        &nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.ZIPCODE_LEN}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;
        </p>
        
        <!--{*
        <p class="zipimg">
            <a class="bt01" href="<!--{$smarty.const.ROOT_URLPATH}-->input_zip.php" onclick="fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', '<!--{$key1}-->', '<!--{$key2}-->', '<!--{$key3}-->', '<!--{$key4}-->'); return false;" target="_blank">Automatic address input</a>
            &nbsp;<span class="mini">Click after inputting the postal code.</span>
        </p>
        *}-->
    </td>
</tr>
<tr>
    <th>Address<span class="attention">*</span></th>
    <td>
        <!--{if $arrErr[$key4] || $arrErr[$key5]}-->
            <div class="attention"><!--{$arrErr[$key4]}--><!--{$arrErr[$key5]}--></div>
        <!--{/if}-->
        <p class="top"><input type="text" name="<!--{$key4}-->" value="<!--{$arrForm[$key4]|h}-->" class="box300" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->;" /><br />
            <!--{$smarty.const.SAMPLE_ADDRESS1}--></p>
        <p class="top"><input type="text" name="<!--{$key5}-->" value="<!--{$arrForm[$key5]|h}-->" class="box300" style="<!--{$arrErr[$key5]|sfGetErrorColor}-->;" /><br />
            <!--{$smarty.const.SAMPLE_ADDRESS2}--></p>
        <p class="mini"><span class="attention">Separate the address into two fields. Make sure to write down the building name.</span></p>
    </td>
</tr>
<tr>
    <th>Phone number<span class="attention">*</span></th>
    <td>
        <!--{assign var=key1 value="`$prefix`tel01"}-->
        <!--{assign var=key2 value="`$prefix`tel02"}-->
        <!--{assign var=key3 value="`$prefix`tel03"}-->
        <!--{if $arrErr[$key1] || $arrErr[$key2] || $arrErr[$key3]}-->
            <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--><!--{$arrErr[$key3]}--></div>
        <!--{/if}-->
        <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="<!--{$key3}-->" value="<!--{$arrForm[$key3]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />
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
        <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;<input type="text" name="<!--{$key3}-->" value="<!--{$arrForm[$key3]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />
    </td>
</tr>
<!--{if $flgFields > 1}-->
    <tr>
        <th>E-mail address<span class="attention">*</span></th>
        <td>
            <!--{assign var=key1 value="`$prefix`email"}-->
            <!--{assign var=key2 value="`$prefix`email02"}-->
            <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
                <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
            <!--{/if}-->
            <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box300 top" /><br />
            <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" style="<!--{$arrErr[$key1]|cat:$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box300" /><br />
            <span class="attention mini">Please enter email address twice</span>
        </td>
    </tr>
    <!--{if $emailMobile}-->
        <tr>
            <th>Mobile e-mail address</th>
            <td>
                <!--{assign var=key1 value="`$prefix`email_mobile"}-->
                <!--{assign var=key2 value="`$prefix`email_mobile02"}-->
                <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
                <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
                <!--{/if}-->
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" class="box300 top" /><br />
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" style="<!--{$arrErr[$key1]|cat:$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" class="box300" /><br />
                <span class="attention mini">Please enter password twice.</span>
            </td>
        </tr>
    <!--{/if}-->
    <tr>
        <th>Gender<span class="attention">*</span></th>
        <td>
            <!--{assign var=key1 value="`$prefix`sex"}-->
            <!--{if $arrErr[$key1]}-->
                <div class="attention"><!--{$arrErr[$key1]}--></div>
            <!--{/if}-->
            <span style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                <input type="radio" id="man" name="<!--{$key1}-->" value="1" <!--{if $arrForm[$key1] eq 1}--> checked="checked" <!--{/if}--> /><label for="man">Male</label>
                <input type="radio" id="woman" name="<!--{$key1}-->" value="2" <!--{if $arrForm[$key1] eq 2}--> checked="checked" <!--{/if}--> /><label for="woman">Female</label>
            </span>
        </td>
    </tr>
    <tr>
        <th>Occupation</th>
        <td>
            <!--{assign var=key1 value="`$prefix`job"}-->
            <!--{if $arrErr[$key1]}-->
                <div class="attention"><!--{$arrErr[$key1]}--></div>
            <!--{/if}-->
            <select name="<!--{$key1}-->">
                <option value="" selected="selected">Please make a selection</option>
                <!--{html_options options=$arrJob selected=$arrForm[$key1]}-->
            </select>
        </td>
    </tr>
    <tr>
        <th>Date of birth</th>
        <td>
            <!--{assign var=errBirth value="`$arrErr.year``$arrErr.month``$arrErr.day`"}-->
            <!--{if $errBirth}-->
                <div class="attention"><!--{$errBirth}--></div>
            <!--{/if}-->
            <select name="year" style="<!--{$errBirth|sfGetErrorColor}-->">
                <!--{html_options options=$arrYear selected=$arrForm.year|default:''}-->
            </select>Year
            <select name="month" style="<!--{$errBirth|sfGetErrorColor}-->">
                <!--{html_options options=$arrMonth selected=$arrForm.month|default:''}-->
            </select>Month
            <select name="day" style="<!--{$errBirth|sfGetErrorColor}-->">
                <!--{html_options options=$arrDay selected=$arrForm.day|default:''}-->
            </select>Day
        </td>
    </tr>
    <!--{if $flgFields > 2}-->
        <tr>
            <th>Desired password<span class="attention">*</span><br />
            </th>
            <td>
                <!--{if $arrErr.password || $arrErr.password02}-->
                    <div class="attention"><!--{$arrErr.password}--><!--{$arrErr.password02}--></div>
                <!--{/if}-->
                <input type="password" name="password" value="<!--{$arrForm.password|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" style="<!--{$arrErr.password|sfGetErrorColor}-->" class="box120" />
                <p><span class="attention mini">Letters and numbers only. Must be between <!--{$smarty.const.PASSWORD_MIN_LEN}--> to <!--{$smarty.const.PASSWORD_MAX_LEN}--> characters. (Symbols cannot be used)</span></p>
                <input type="password" name="password02" value="<!--{$arrForm.password02|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" style="<!--{$arrErr.password|cat:$arrErr.password02|sfGetErrorColor}-->" class="box120" />
                <p><span class="attention mini">Please enter password twice.</span></p>
            </td>
        </tr>
        <tr>
            <th>Hint for when you have forgotten your password<span class="attention">*</span></th>
            <td>
                <!--{if $arrErr.reminder || $arrErr.reminder_answer}-->
                    <div class="attention"><!--{$arrErr.reminder}--><!--{$arrErr.reminder_answer}--></div>
                <!--{/if}-->
                Question:
                <select name="reminder" style="<!--{$arrErr.reminder|sfGetErrorColor}-->">
                    <option value="" selected="selected">Please make a selection</option>
                    <!--{html_options options=$arrReminder selected=$arrForm.reminder}-->
                </select>
                <br />
                Answer:<input type="text" name="reminder_answer" value="<!--{$arrForm.reminder_answer|h}-->" style="<!--{$arrErr.reminder_answer|sfGetErrorColor}-->;" class="box260" />
            </td>
        </tr>
        <tr>
            <th>About delivery of the mail magazine<span class="attention">*</span></th>
            <td>
                <!--{if $arrErr.mailmaga_flg}-->
                    <div class="attention"><!--{$arrErr.mailmaga_flg}--></div>
                <!--{/if}-->
                <span style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->">
                    <input type="radio" name="mailmaga_flg" value="1" id="html" <!--{if $arrForm.mailmaga_flg eq 1}--> checked="checked" <!--{/if}--> /><label for="html">Receive HTML mail + text mail</label><br />
                    <input type="radio" name="mailmaga_flg" value="2" id="text" <!--{if $arrForm.mailmaga_flg eq 2}--> checked="checked" <!--{/if}--> /><label for="text">Receive a text mail</label><br />
                    <input type="radio" name="mailmaga_flg" value="3" id="no" <!--{if $arrForm.mailmaga_flg eq 3}--> checked="checked" <!--{/if}--> /><label for="no">Do not send an email</label>
                </span>
            </td>
        </tr>
    <!--{/if}-->
<!--{/if}-->
