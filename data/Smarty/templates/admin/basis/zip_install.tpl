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
        <p><!--{t string="tpl_121" T_FIELD=$tpl_csv_datetime|h}--></p>
        <p><!--{t string="tpl_122" T_FIELD=$tpl_line|h}--></p>
        <p><!--{t string="tpl_123" T_FIELD=$tpl_count_mtb_zip|h}--></p>
        <!--{if $tpl_count_mtb_zip == 0}-->
            <p class="attention"><!--{t string="tpl_124"}--></p>
        <!--{elseif $tpl_line <> $tpl_count_mtb_zip}-->
            <p class="attention"><!--{t string="tpl_125"}--></p>
        <!--{/if}-->

        <div class="basis-zip-item info">
            <p><!--{t string="tpl_126"}--></p>
        </div>

        <div class="basis-zip-item">
            <h2><!--{t string="tpl_127"}--></h2>
            <p>
                <!--{if !$tpl_skip_update_csv}-->
                    <!--{t string="tpl_128"}-->
                <!--{else}-->
                    <!--{t string="tpl_129"}-->
                <!--{/if}-->
            </p>
            <p><a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('auto', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_130"}--></span></a></p>
        </div>

        <div class="basis-zip-item">
            <h2><!--{t string="tpl_131"}--></h2>
            <p><!--{t string="tpl_132"}--></p>
            <p><a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('manual', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_133"}--></span></a> <!--{t string="tpl_134"}-->: <input type="text" name="startRowNum" value="<!--{$arrForm.startRowNum|default:$tpl_count_mtb_zip+1|h}-->" size="8"><span class="attention"><!--{$arrErr.startRowNum}--></span></p>
        </div>

        <div class="basis-zip-item">
            <h2><!--{t string="tpl_135"}--></h2>
            <!--{if $tpl_skip_update_csv}-->
                <!--{t string="tpl_136"}-->
                <!--{if $tpl_zip_download_url_empty}-->
                    <p class="attention"><!--{t string="tpl_137"}--></p>
                <!--{/if}-->
                <!--{if $tpl_zip_function_not_exists}-->
                    <p class="attention"><!--{t string="tpl_138"}--></p>
                <!--{/if}-->
            <!--{else}-->
                <p><!--{t string="tpl_139"}--></p>
                <p><a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('update_csv', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_140"}--></span></a><span class="attention"><!--{$arrErr.startRowNum}--></span></p>
            <!--{/if}-->
        </div>

        <div class="basis-zip-item end">
            <h2><!--{t string="tpl_004"}--></h2>
            <p><!--{t string="tpl_141"}--></p>
            <p><a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('delete', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_004"}--></span></a></p>
        </div>
    </form>
<!--{else}-->
    <iframe src="?mode=<!--{$tpl_mode|h}-->&amp;exec=yes&amp;startRowNum=<!--{$arrForm.startRowNum|h}-->" name="progress" height="200" width="750" frameborder="0"></iframe>
<!--{/if}-->
