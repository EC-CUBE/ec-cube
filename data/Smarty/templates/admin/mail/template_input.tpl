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
<input type="hidden" name="mode" value="<!--{$mode}-->" />
<input type="hidden" name="template_id" value="<!--{$arrForm.template_id|h}-->" />
<div id="mail" class="contents-main">
    <table class="form">
        <tr>
            <th><!--{t string="tpl_E-mail format<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <span <!--{if $arrErr.mail_method}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{html_radios name="mail_method" options=$arrMagazineType separator="&nbsp;" selected=$arrForm.mail_method}--></span>
                <!--{if $arrErr.mail_method}--><br /><span class="attention"><!--{$arrErr.mail_method}--></span><!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Subject<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <input type="text" name="subject" size="65" class="box65" <!--{if $arrErr.subject}--><!--{sfSetErrorStyle}--><!--{/if}--> value="<!--{$arrForm.subject|h}-->" />
                <!--{if $arrErr.subject}--><br /><span class="attention"><!--{$arrErr.subject}--></span><!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Text<span class='attention'> *</span><br /> (When inserting a name, use {name})_01" escape="none"}--></th>
            <td>
                <textarea name="body" cols="90" rows="40" class="area90 top" <!--{if $arrErr.body}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{"\n"}--><!--{$arrForm.body|h}--></textarea>
                <!--{if $arrErr.body}--><br /><span class="attention"><!--{$arrErr.body}--></span><!--{/if}-->
                <div>
                    <a class="btn-normal" href="javascript:;" onclick="fnCharCount('form1','body','cnt_footer'); return false;" name="next" id="next"><span><!--{t string="tpl_Text count_01"}--></span></a>
                    <span><!--{t string="tpl_Character count_01"}--><input type="text" name="cnt_footer" size="4" class="box4" readonly = true style="text-align:right" /></span>
                </div>
            </td>
        </tr>
    </table>
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', '<!--{$mode}-->', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
        </ul>
    </div>
</div>
</form>
