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

<!--{if $tpl_mode|strlen == 0 || $arrErr|@count >= 1}-->
    <style type="text/css">

    </style>
    <form name="form1" id="form1" method="get" action="?" onsubmit="return false;">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="">
        <p><!--{t string="tpl_Date and time of saved postal code CSV update: T_ARG1_01" T_ARG1=$tpl_csv_datetime|h}--></p>
        <p><!--{t string="tpl_Postal code There are T_ARG1 lines of data in the CSV._01" T_ARG1=$tpl_line|h}--></p>
        <p><!--{t string="tpl_Postal code There are T_ARG1 lines of data in the DB._01" T_ARG1=$tpl_count_mtb_zip|h}--></p>
        <!--{if $tpl_count_mtb_zip == 0}-->
            <p class="attention"><!--{t string="tpl_Please carry out registration._01"}--></p>
        <!--{elseif $tpl_line <> $tpl_count_mtb_zip}-->
            <p class="attention"><!--{t string="tpl_There is a difference in the number of lines. There may be an abnormality in registration._01"}--></p>
        <!--{/if}-->

        <div class="basis-zip-item info">
            <p><!--{t string="tpl_Under normal conditions_01" escape="none"}--></p>
        </div>

        <div class="basis-zip-item">
            <h2><!--{t string="tpl_Automatic registration_01"}--></h2>
            <p>
                <!--{if !$tpl_skip_update_csv}-->
                    <!--{t string="tpl_[Delete] [Postal code CSV update] and [DB manual registration] below will be carried out in order._01"}-->
                <!--{else}-->
                    <!--{t string="tpl_[Delete] and [DB manual registration] below will be carried out in order. _01"}-->
                <!--{/if}-->
            </p>
            <p><a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('auto', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Automatic registration_01"}--></span></a></p>
        </div>

        <div class="basis-zip-item">
            <h2><!--{t string="tpl_DB manual registration_01"}--></h2>
            <p><!--{t string="tpl_Postal codes will be registered starting with the designated line number._01"}--></p>
            <p><a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('manual', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Manual registration_01"}--></span></a> <!--{t string="tpl_Start line_01"}-->: <input type="text" name="startRowNum" value="<!--{$arrForm.startRowNum|default:$tpl_count_mtb_zip+1|h}-->" size="8"><span class="attention"><!--{$arrErr.startRowNum}--></span></p>
        </div>

        <div class="basis-zip-item">
            <h2><!--{t string="tpl_Postal code CSV update_01"}--></h2>
            <!--{if $tpl_skip_update_csv}-->
                <!--{t string="tpl_Cannot be used._01"}-->
                <!--{if $tpl_zip_download_url_empty}-->
                    <p class="attention"><!--{t string="tpl_* PHP extension module 'zip' is invalid._01"}--></p>
                <!--{/if}-->
                <!--{if $tpl_zip_function_not_exists}-->
                    <p class="attention"><!--{t string="tpl_* PHP extension module 'zip' is invalid._01"}--></p>
                <!--{/if}-->
            <!--{else}-->
                <p><!--{t string="tpl_A postal code CSV will be obtained from the Japan Post Web site._01"}--></p>
                <p><a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('update_csv', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Update_01"}--></span></a><span class="attention"><!--{$arrErr.startRowNum}--></span></p>
            <!--{/if}-->
        </div>

        <div class="basis-zip-item end">
            <h2><!--{t string="tpl_Remove_01"}--></h2>
            <p><!--{t string="tpl_All postal codes will be deleted. _01"}--></p>
            <p><a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('delete', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Remove_01"}--></span></a></p>
        </div>
    </form>
<!--{else}-->
    <iframe src="?mode=<!--{$tpl_mode|h}-->&amp;exec=yes&amp;startRowNum=<!--{$arrForm.startRowNum|h}-->" name="progress" height="200" width="750" frameborder="0"></iframe>
<!--{/if}-->
