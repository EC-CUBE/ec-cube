<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`popup_header.tpl" subtitle="Form for customer's opinion (confirmation page)"}-->
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

<div id="windowarea">
    <h2 class="title">Add a comment</h2>
    <form name="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="complete" />
        <!--{foreach from=$arrForm key=key item=item}-->
            <!--{if $key ne "mode"}-->
                <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
            <!--{/if}-->
        <!--{/foreach}-->

        <table summary="Form for customer's opinion">
            <tr>
                <th>Product name</th>
                <td><!--{$arrForm.name|h}--></td>
            </tr>
            <tr>
                <th>Poster name<span class="attention">*</span></th>
                <td><!--{$arrForm.reviewer_name|h}--></td>
            </tr>
            <tr>
                <th>Poster URL</th>
                <td><!--{$arrForm.reviewer_url|h}--></td>
            </tr>
            <tr>
                <th>Gender</th>
                <td><!--{if $arrForm.sex eq 1}-->Male<!--{elseif $arrForm.sex eq 2}-->Female<!--{/if}--></td>
            </tr>
            <tr>
                <th>Rating<span class="attention">*</span></th>
                <td><span class="recommend_level"><!--{$arrRECOMMEND[$arrForm.recommend_level]}--></span></td>
            </tr>
            <tr>
                <th>Title<span class="attention">*</span></th>
                <td><!--{$arrForm.title|h}--></td>
            </tr>
            <tr>
                <th>Comment<span class="attention">*</span></th>
                <td><!--{$arrForm.comment|h|nl2br}--></td>
            </tr>
        </table>
        <div class="btn_area">
            <ul class="btn_btm">
                <li><input type="submit" value="Send" class="btn data-role-none" alt="Complete" name="complete" id="complete" /></li>
                <li><a class="btn_back" href="Javascript:fnModeSubmit('return', '', '');" rel="external">Go back</a></li>
            </ul>
        </div>
    </form>
</div>

<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`popup_footer.tpl"}-->
