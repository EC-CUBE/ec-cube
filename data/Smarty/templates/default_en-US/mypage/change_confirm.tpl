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

<div id="mypagecolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <!--{include file=$tpl_navi}-->
    <div id="mycontents_area">
        <h3><!--{$tpl_subtitle|h}--></h3>
        <p>Do you want to send the information below?<br />
           Click the "Send" button at the bottom of the page</p>

        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="complete" />
        <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|h}-->" />
        <!--{foreach from=$arrForm key=key item=item}-->
            <!--{if $key ne "mode" && $key ne "subm"}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
            <!--{/if}-->
        <!--{/foreach}-->
        <table summary=" " class="delivname">
            <col width="30%" />
            <col width="70%" />
            <tr>
                <th>Name</th>
                <td><!--{$arrForm.name01|h}--> <!--{$arrForm.name02|h}--></td>
            </tr>
            <tr>
                <th>Postal code</th>
                <!--{* <td><!--{$arrForm.zip01}-->-<!--{$arrForm.zip02}--></td> *}-->
                <td><!--{$arrForm.zipcode}--></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><!--{$arrPref[$arrForm.pref]}--><!--{$arrForm.addr01|h}--><!--{$arrForm.addr02|h}--></td>
            </tr>
            <tr>
                <th>Phone number</th>
                <td><!--{$arrForm.tel01|h}-->-<!--{$arrForm.tel02}-->-<!--{$arrForm.tel03}--></td>
            </tr>
            <tr>
                <th>FAX</th>
                <td><!--{if strlen($arrForm.fax01) > 0}--><!--{$arrForm.fax01}-->-<!--{$arrForm.fax02}-->-<!--{$arrForm.fax03}--><!--{else}-->Not registered<!--{/if}--></td>
            </tr>
            <tr>
                <th>E-mail address</th>
                <td><a href="<!--{$arrForm.email|escape:'hex'}-->"><!--{$arrForm.email|escape:'hexentity'}--></a></td>
            </tr>
            <tr>
                <th>Mobile e-mail address</th>
                <td>
                    <!--{if strlen($arrForm.email_mobile) > 0}-->
                    <a href="<!--{$arrForm.email_mobile|escape:'hex'}-->"><!--{$arrForm.email_mobile|escape:'hexentity'}--></a>
                    <!--{else}-->
                    Not registered
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>Gender</th>
                <td><!--{$arrSex[$arrForm.sex]}--></td>
            </tr>
            <tr>
                <th>Occupation</th>
                <td><!--{$arrJob[$arrForm.job]|default:"Not registered"|h}--></td>
            </tr>
            <tr>
                <th>Date of birth</th>
                <td><!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}--><!--{$arrForm.year|h}--> / <!--{$arrForm.month|h}--> / <!--{$arrForm.day|h}--><!--{else}-->Not registered<!--{/if}--></td>
            </tr>
            <tr>
                <th>Desired password<br />
                </th>
                <td><!--{$passlen}--></td>
            </tr>
            <tr>
                <th>Hint for when you have forgotten your password</th>
                <td>Question:&nbsp;<!--{$arrReminder[$arrForm.reminder]|h}--><br />
                      Answer:&nbsp;<!--{$arrForm.reminder_answer|h}--></td>
            </tr>
            <tr>
                <th>About delivery of the mail magazine</th>
                <td><!--{$arrMAILMAGATYPE[$arrForm.mailmaga_flg]}--></td>
            </tr>
        </table>

        <div class="btn_area">
            <ul>
                <li>
                    <a class="bt04" href="?" onclick="fnModeSubmit('return', '', ''); return false;">Go back</a>
                </li>
                <li><button class="bt02">Send</button></li>
            </ul>
        </div>
        </form>
    </div>
</div>
