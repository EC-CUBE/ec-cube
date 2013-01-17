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

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="Address search" disable_wincol=1}-->

<div id="zipsearchcolumn">
    <h2>Address search</h2>
    <div id="zipsearch_area">
        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="state" value="<!--{$tpl_state}-->" />
            <input type="hidden" name="city" value="<!--{$tpl_city}-->" />
            <input type="hidden" name="town" value="<!--{$tpl_town}-->" />
            <div id="completebox">
                <p><!--{$tpl_message}--></p>
            </div>
        </form>
    </div>
    <div class="btn"><a href="javascript:window.close()" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_close_on.gif','b_close');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_close.gif','b_close');"><img src="<!--{$TPL_URLPATH}-->img/button/btn_close.gif" alt="Close" border="0" name="b_close" /></a></div>
</div>

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->
