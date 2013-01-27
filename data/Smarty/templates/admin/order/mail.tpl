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
<input type="hidden" name="mode" value="confirm" />
<input type="hidden" name="order_id_array" value="<!--{$order_id_array}-->" />
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
    
    <!--{if $order_id_count == 1}-->
    <table class="list">
        <tr>
            <th><!--{t string="tpl_Processing date_01"}--></th>
            <th><!--{t string="tpl_Notification e-mail_01"}--></th>
            <th><!--{t string="tpl_Item name_01"}--></th>
        </tr>
        <!--{section name=cnt loop=$arrMailHistory}-->
        <tr class="center">
            <td><!--{$arrMailHistory[cnt].send_date|sfDispDBDate|h}--></td>
            <!--{assign var=key value="`$arrMailHistory[cnt].template_id`"}-->
            <td><!--{$arrMAILTEMPLATE[$key]|h}--></td>
            <td><a href="?" onclick="win02('./mail_view.php?send_id=<!--{$arrMailHistory[cnt].send_id}-->','mail_view','650','800'); return false;"><!--{$arrMailHistory[cnt].subject|h}--></a></td>
        </tr>
        <!--{/section}-->
    </table>
    <!--{/if}-->

    <table class="form">
        <tr>
            <th><!--{t string="tpl_Template<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <!--{assign var=key value="template_id"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="template_id" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" onchange="fnModeSubmit('change', '', '');">
                <option value="" selected="selected"><!--{t string="tpl_Please make a selection_01"}--></option>
                <!--{html_options options=$arrMAILTEMPLATE selected=$arrForm[$key].value|h}-->
                </select>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_E-mail title<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <!--{assign var=key value="subject"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Header_01"}--></th>
            <td>
                <!--{assign var=key value="header"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <textarea name="<!--{$arrForm[$key].keyname}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" cols="75" rows="12" class="area75"><!--{"\n"}--><!--{$arrForm[$key].value|h}--></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="center"><!--{t string="tpl_Dynamic data insertion section_01"}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Footer_01"}--></th>
            <td>
                <!--{assign var=key value="footer"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <textarea name="<!--{$arrForm[$key].keyname}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" cols="75" rows="12" class="area75"><!--{"\n"}--><!--{$arrForm[$key].value|h}--></textarea>
            </td>
        </tr>
    </table>
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_ORDER_URLPATH}-->'); fnModeSubmit('search','',''); return false;"><span class="btn-prev"><!--{t string="tpl_Return_01"}--></span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', '', 'mode', 'confirm'); return false;"><span class="btn-next"><!--{t string="tpl_Check the sending details_01"}--></span></a></li>
        </ul>
    </div>
</div>
</form>
