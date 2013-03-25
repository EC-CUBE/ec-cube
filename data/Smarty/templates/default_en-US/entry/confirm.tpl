<!--{*
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
 *}-->

<div id="undercolumn">
    <div id="undercolumn_entry">
        <h2 class="title"><!--{$tpl_title|h}--></h2>
        <p>Please confirm the information below.<br />
          When finished, please click the "Register" button at the bottom of the page.</p>
        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="complete">
        <!--{foreach from=$arrForm key=key item=item}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
        <!--{/foreach}-->

        <table summary="Confirmation of input content">
            <col width="30%" />
            <col width="70%" />
            <tr>
                <th>Name</th>
                <td>
                    <!--{$arrForm.name01|h}-->&nbsp;
                    <!--{$arrForm.name02|h}-->
                </td>
            </tr>
            <tr>
                <th>Postal code</th>
                <td>
                    <!--{* <!--{$arrForm.zip01|h}--> - <!--{$arrForm.zip02|h}--> *}-->
                    <!--{$arrForm.zipcode|h}-->
                </td>
            </tr>
            <tr>
                <th>Address</th>
                <td>
                    <!--{$arrForm.addr01|h}--> <!--{$arrForm.addr02|h}-->
                </td>
            </tr>
            <tr>
                <th>Phone number</th>
                <td>
                    <!--{$arrForm.tel01|h}--> - <!--{$arrForm.tel02|h}--> - <!--{$arrForm.tel03|h}-->
                </td>
            </tr>
            <tr>
                <th>FAX</th>
                <td>
                    <!--{if strlen($arrForm.fax01) > 0 && strlen($arrForm.fax02) > 0 && strlen($arrForm.fax03) > 0}-->
                        <!--{$arrForm.fax01|h}--> - <!--{$arrForm.fax02|h}--> - <!--{$arrForm.fax03|h}-->
                    <!--{else}-->
                        Not registered
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>E-mail address</th>
                <td>
                    <a href="mailto:<!--{$arrForm.email|escape:'hex'}-->"><!--{$arrForm.email|escape:'hexentity'}--></a>
                </td>
            </tr>
            <tr>
                <th>Gender</th>
                <td>
                    <!--{if $arrForm.sex eq 1}-->
                    Male
                    <!--{else}-->
                    Female
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>Occupation</th>
                <td><!--{$arrJob[$arrForm.job]|default:"Not registered"|h}--></td>
            </tr>
            <tr>
                <th>Date of birth</th>
                <td>
                    <!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}-->
                        <!--{$arrForm.year|h}-->/<!--{$arrForm.month|h}-->/<!--{$arrForm.day|h}-->
                    <!--{else}-->
                    Not registered
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>Desired password<br />
                </th>
                <td><!--{$passlen}--></td>
            </tr>
            <tr>
                <th>Hint for when you have forgotten your password</th>
                <td>
                    Question:<!--{$arrReminder[$arrForm.reminder]|h}--><br />
                    Answer:<!--{$arrForm.reminder_answer|h}-->
                </td>
            </tr>
            <tr>
                <th>About delivery of the mail magazine</th>
                <td>
                    <!--{if $arrForm.mailmaga_flg eq 1}-->
                    Receive HTML mail + text mail
                    <!--{elseif $arrForm.mailmaga_flg eq 2}-->
                    Receive a text mail
                    <!--{else}-->
                    Not accepted
                    <!--{/if}-->
                </td>
            </tr>
        </table>

        <div class="btn_area">
            <ul>
                <li>
                    <a class="bt04" href="?" onclick="fnModeSubmit('return', '', ''); return false;">Go back</a>
                </li>
                <li>
					<button class="bt02">Register</button>
                </li>
            </ul>
        </div>

        </form>
    </div>
</div>
