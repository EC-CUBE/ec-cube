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

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="send" />
<input type="hidden" name="order_id_array" value="<!--{$order_id_array}-->" />
<!--{foreach key=key item=item from=$arrForm}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item.value|h}-->" />
<!--{/foreach}-->
<!--{foreach key=key item=item from=$arrSearchHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
        <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
<div id="order" class="contents-main">
    <h2><!--{t string="tpl_E-mail delivery_01"}--></h2>
    
    
    <!--{if $order_id_count > 1}-->
    <span class="red"><!--{t string="tpl_* This is an example of a single e-mail. The order information differs for each e-mail_01"}--></span><br /><br />
    <!--{/if}-->
    <table class="form">
        <tr>
            <th><!--{t string="tpl_Item name_01"}--></th>
            <td><!--{$tpl_subject|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Text_02"}--></th>
            <td><!--{$tpl_body|h|nl2br}--></td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('return', '', ''); return false;"><span class="btn-prev"><!--{t string="tpl_Return to previous page_01"}--></span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'send', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_E-mail sending_01"}--></span></a></li>
        </ul>
    </div>
</div>
</form>
