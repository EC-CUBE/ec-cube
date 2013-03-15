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
<input type="hidden" name="mode" value="complete" />
<input type="hidden" name="review_id" value="<!--{$arrForm.review_id|h}-->" />
<!--{foreach key=key item=item from=$arrSearchHidden}-->
    <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->

<div id="products" class="contents-main">

    <!--▼編集テーブルここから-->
    <table>
        <tr>
            <th><!--{t string="tpl_Product name_01"}--></th>
            <td><!--{$arrForm.name|h}-->
            <input type="hidden" name="name" value="<!--{$arrForm.name|h}-->">
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Post date_01"}--></th>
            <td><!--{$arrForm.create_date|sfDispDBDate}-->
            <input type="hidden" name="create_date" value="<!--{$arrForm.create_date|h}-->">
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Review display_01"}--></th>
            <td>
                <!--{if $arrErr.status}--><span class="attention"><!--{$arrErr.status}--></span><!--{/if}-->
                <label><input type="radio" name="status" value="2" <!--{if $arrForm.status eq 2}-->checked<!--{/if}-->><!--{t string="tpl_Not displayed_01"}--></label><!--{if $arrForm.status eq 2 && !$tpl_status_change}--><!--{else}--><label><input type="radio" name="status" value="1" <!--{if $arrForm.status eq 1}-->checked<!--{/if}-->><!--{t string="tpl_Display_01"}--></label><!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Poster name_01"}--> <span class="attention">*</span></th>
            <td>
                <!--{if $arrErr.reviewer_name}--><span class="attention"><!--{$arrErr.reviewer_name}--></span><!--{/if}-->
                <input type="text" class="box60" name="reviewer_name" value="<!--{$arrForm.reviewer_name|h}-->" style="<!--{$arrErr.reviewer_name|sfGetErrorColor}-->" size=30>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Poster URL_01"}--></th>
            <td>
                <!--{if $arrErr.reviewer_url}--><span class="attention"><!--{$arrErr.reviewer_url}--></span><!--{/if}-->
                <input type="text" class="box60" name="reviewer_url" maxlength="<!--{$smarty.const.URL_LEN}-->" value="<!--{$arrForm.reviewer_url|h}-->" style="<!--{$arrErr.reviewer_url|sfGetErrorColor}-->" size=30>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Gender_01"}--></th>
            <td><!--{html_radios_ex name="sex" options=$arrSex selected=$arrForm.sex}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Recommendation level_01"}--> <span class="attention">*</span></th>
            <td>
                <!--{assign var=key value="recommend_level"}-->
                <!--{if $arrErr[$key]}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" >
                <option value="" selected="selected"><!--{t string="tpl_Please make a selection_01"}--></option>
                <!--{html_options options=$arrRECOMMEND selected=$arrForm[$key]}-->
                </select>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Title<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <!--{if $arrErr.title}--><span class="attention"><!--{$arrErr.title}--></span><!--{/if}-->
                <input type="text" class="box60" name="title" value="<!--{$arrForm.title|h}-->" style="<!--{$arrErr.title|sfGetErrorColor}-->" size=30><span class="attention">
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Comment <span class='attention'>*</span>_01" escape="none"}--></th>
            <td>
                <!--{if $arrErr.comment}--><span class="attention"><!--{$arrErr.comment}--></span><!--{/if}-->
                <textarea name="comment" rows="20" cols="60" class="area60" wrap="soft" style="<!--{$arrErr.comment|sfGetErrorColor}-->" ><!--{"\n"}--><!--{$arrForm.comment|h}--></textarea>
            </td>
        </tr>
    </table>
    <!--▲編集テーブルここまで-->

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="document.form1.action='./review.php'; fnModeSubmit('search','',''); return false;" ><span class="btn-prev"><!--{t string="tpl_Return to search screen_01"}--></span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('complete','',''); return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
        </ul>
    </div>
</div>
</form>
