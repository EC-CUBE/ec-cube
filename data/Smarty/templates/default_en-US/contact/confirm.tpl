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
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <div id="undercolumn_contact">
        <p>Do you want to send the contents below?<br />
           Click the "Send" button at the bottom of the page.</p>
        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="complete" />
        <!--{foreach key=key item=item from=$arrForm}-->
            <!--{if $key ne 'mode'}-->
                <input type="hidden" name="<!--{$key}-->" value="<!--{$item.value|h}-->" />
            <!--{/if}-->
        <!--{/foreach}-->
        <table summary="Confirmation of inquiry details">
            <col width="30%" />
            <col width="70%" />
            <tr>
                <th>First Name</th>
                <td><!--{$arrForm.name01.value|h}--> <!--{$arrForm.name02.value|h}--></td>
            </tr>
            <tr>
                <th>Postal code</th>
                <td>
                    <!--{*
                    <!--{if strlen($arrForm.zip01.value) > 0 && strlen($arrForm.zip02.value) > 0}-->
                        <!--{$arrForm.zip01.value|h}-->-<!--{$arrForm.zip02.value|h}-->
                    <!--{/if}-->
                    *}-->
                    <!--{if strlen($arrForm.zipcode.value) > 0}-->
                        <!--{$arrForm.zipcode.value|h}-->
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>Address</th>
                <td><!--{$arrForm.addr01.value|h}--><!--{$arrForm.addr02.value|h}--></td>
            </tr>
            <tr>
                <th>Phone number</th>
                <td>
                    <!--{if strlen($arrForm.tel01.value) > 0 && strlen($arrForm.tel02.value) > 0 && strlen($arrForm.tel03.value) > 0}-->
                        <!--{$arrForm.tel01.value|h}-->-<!--{$arrForm.tel02.value|h}-->-<!--{$arrForm.tel03.value|h}-->
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>E-mail address</th>
                <td><a href="mailto:<!--{$arrForm.email.value|escape:'hex'}-->"><!--{$arrForm.email.value|escape:'hexentity'}--></a></td>
            </tr>
            <tr>
                <th>Details of inquiry</th>
                <td><!--{$arrForm.contents.value|h|nl2br}--></td>
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
