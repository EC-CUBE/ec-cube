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
        <p class="message">[Important] Before registering, please read the terms of use below.</p>
        <p>In the agreement, your rights and obligations pertaining to use of this service are stipulated.<br />
           When you click the "Agree, proceed to member registration" button, you are agreeing to all of the conditions of this agreement.
        </p>

        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <textarea name="textfield" class="kiyaku_text" cols="80" rows="30" readonly="readonly"><!--{"\n"}--><!--{$tpl_kiyaku_text|h}--></textarea>

        <div class="btn_area">
            <ul>
                <li>
                    <a href="<!--{$smarty.const.TOP_URLPATH}-->" class="bt04">Decline</a>
                </li>
                <li>
                    <a href="<!--{$smarty.const.ENTRY_URL}-->" class="bt02">Agree</a>
                </li>
            </ul>
        </div>

        </form>
    </div>
</div>
