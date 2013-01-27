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
<!--{foreach key=key item=item from=$arrHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
            <input type="hidden" name="<!--{$key}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
<input type="hidden" name="mode" value="template" />
<input type="hidden" name="mail_method" value="<!--{$arrForm.mail_method.value}-->" />
<div id="mail" class="contents-main">
    <table class="form">
        <tr>
            <th><!--{t string="tpl_Template selection<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <!--{assign var=key value="template_id"}-->
                <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
                <select name="<!--{$key}-->" onchange="return fnInsertValAndSubmit( document.form1, 'mode', 'template', '' ) " style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                <option value="" selected="selected"><!--{t string="tpl_Please make a selection_01"}--></option>
                <!--{html_options options=$arrTemplate selected=$arrForm[$key].value}-->
                </select>
            </td>
        </tr>
    </table>

    <!--{if $arrForm.template_id.value}-->
    <table class="form">
        <tr>
            <th><!--{t string="tpl_Subject<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <!--{assign var=key value="subject"}-->
                <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
                <input type="text" name="subject" size="65" class="box65" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key].value|h}-->" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Text<span class='attention'> *</span><br /> (When inserting a name, use {name})_01" escape="none"}--></th>
            <td>
                <!--{assign var=key value="body"}-->
                <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
                <textarea name="body" cols="90" rows="40" class="area90" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{"\n"}--><!--{$arrForm[$key].value|h}--></textarea>
            </td>
        </tr>
    </table>
    <!--{/if}-->

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="return fnInsertValAndSubmit( document.form1, 'mode', 'back', '' ); return false;"><span class="btn-prev"><!--{t string="tpl_Return to search screen_01"}--></span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="return fnInsertValAndSubmit( document.form1, 'mode', 'regist_confirm', '' ); return false;" ><span class="btn-next"><!--{t string="tpl_Confirmation page_01"}--></span></a></li>
        </ul>
    </div>
</div>
</form>
