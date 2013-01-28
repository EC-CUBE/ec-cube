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

<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/breadcrumbs.js"></script>
<script type="text/javascript">//<![CDATA[
    $(function() {
        $('h2').breadcrumbs({
            'bread_crumbs': <!--{$tpl_bread_crumbs}-->
        });
    });
//]]></script>
<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="parent_category_id" value="<!--{$arrForm.parent_category_id|h}-->">
<input type="hidden" name="category_id" value="<!--{$arrForm.category_id|h}-->">
<input type="hidden" name="keySet" value="">
<div id="products" class="contents-main">
    <div class="btn">
        <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('csv','',''); return false;"><!--{t string="tpl_CSV download_01"}--></a>
        <a class="btn-normal" href='../contents/csv.php?tpl_subno_csv=category'><!--{t string="tpl_CSV output settings_01"}--></a>
    </div>

    <!--{* ▼画面左 *}-->
    <div id="products-category-left">
        <a href="?"><img src="<!--{$TPL_URLPATH}-->img/contents/folder_close.gif" alt="Folder">&nbsp;<!--{t string="tpl_Home_01"}--></a><br />
        <!--{section name=cnt loop=$arrTree}-->
            <!--{assign var=level value="`$arrTree[cnt].level`}-->

            <!--{* 上の階層表示の時にdivを閉じる *}-->
            <!--{assign var=close_cnt value="`$before_level-$level+1`"}-->
            <!--{if $close_cnt > 0}-->
                <!--{section name=n loop=$close_cnt}--></div><!--{/section}-->
            <!--{/if}-->

            <!--{* スペース繰り返し *}-->
            <!--{section name=n loop=$level}-->&nbsp;&nbsp;<!--{/section}-->

            <!--{* カテゴリ名表示 *}-->
            <!--{assign var=disp_name value="`$arrTree[cnt].category_id`.`$arrTree[cnt].category_name`"}-->
            <!--{if $arrTree[cnt].level != $smarty.const.LEVEL_MAX}-->
                <a href="?" onclick="fnModeSubmit('tree', 'parent_category_id', <!--{$arrTree[cnt].category_id}-->); return false;">
                <!--{if $arrForm.parent_category_id == $arrTree[cnt].category_id}-->
                    <img src="<!--{$TPL_URLPATH}-->img/contents/folder_open.gif" alt="Folder">
                <!--{else}-->
                    <img src="<!--{$TPL_URLPATH}-->img/contents/folder_close.gif" alt="Folder">
                <!--{/if}-->
                <!--{$disp_name|sfCutString:10:false|h}--></a><br />
            <!--{else}-->
                <img src="<!--{$TPL_URLPATH}-->img/contents/folder_close.gif" alt="Folder">
                <!--{$disp_name|sfCutString:10:false|h}--></a><br />
            <!--{/if}-->

            <!--{if $arrTree[cnt].display == true}-->
                <div id="f<!--{$arrTree[cnt].category_id}-->">
            <!--{else}-->
                <div id="f<!--{$arrTree[cnt].category_id}-->" style="display:none">
            <!--{/if}-->

            <!--{if $smarty.section.cnt.last}-->
                <!--{section name=n loop=$level}--></div><!--{/section}-->
            <!--{/if}-->

            <!--{assign var=before_level value="`$arrTree[cnt].level`}-->
        <!--{/section}-->
    </div>
    <!--{* ▲画面左 *}-->

    <!--{* ▼画面右 *}-->
    <div id="products-category-right">


        <div class="now_dir">
                <!--{if $arrErr.category_name}-->
                <span class="attention"><!--{$arrErr.category_name}--></span>
                <!--{/if}-->
                <input type="text" name="category_name" value="<!--{$arrForm.category_name|h}-->" size="30" class="box30" maxlength="<!--{$smarty.const.STEXT_LEN}-->" />
                <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('edit','',''); return false;"><span class="btn-next"><!--{t string="tpl_Register_02"}--></span></a><span class="attention">&nbsp;<!--{t string="tpl_(max T_ARG1 characters)_01" T_ARG1=$smarty.const.STEXT_LEN}--></span>
        </div>

        <h2><!--{* jQuery で挿入される *}--></h2>
        <!--{if count($arrList) > 0}-->

        <table class="list" id="categoryTable">
            <col width="5%" />
            <col width="60%" />
            <col width="10%" />
            <col width="10%" />
            <col width="25%" />
            <tr class="nodrop nodrag">
                <th><!--{t string="tpl_ID_01"}--></th>
                <th><!--{t string="tpl_Category name_01"}--></th>
                <th class="edit"><!--{t string="tpl_Edit_01"}--></th>
                <th class="delete"><!--{t string="tpl_Remove_01"}--></th>
                <th><!--{t string="tpl_Move_01"}--></th>
            </tr>

            <!--{section name=cnt loop=$arrList}-->
            <tr id="<!--{$arrList[cnt].category_id}-->" style="background:<!--{if $arrForm.category_id != $arrList[cnt].category_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->;" align="left">
                <td class="center"><!--{$arrList[cnt].category_id}--></td>
                <td>
                <!--{if $arrList[cnt].level != $smarty.const.LEVEL_MAX}-->
                    <a href="?" onclick="fnModeSubmit('tree', 'parent_category_id', <!--{$arrList[cnt].category_id}-->); return false"><!--{$arrList[cnt].category_name|h}--></a>
                <!--{else}-->
                    <!--{$arrList[cnt].category_name|h}-->
                <!--{/if}-->
                </td>
                <td class="center">
                    <!--{if $arrForm.category_id != $arrList[cnt].category_id}-->
                    <a href="?" onclick="fnModeSubmit('pre_edit', 'category_id', <!--{$arrList[cnt].category_id}-->); return false;"><!--{t string="tpl_Edit_01"}--></a>
                    <!--{else}-->
                    <!--{t string="tpl_being edited_01"}-->
                    <!--{/if}-->
                </td>
                <td class="center">
                    <a href="?" onclick="fnModeSubmit('delete', 'category_id', <!--{$arrList[cnt].category_id}-->); return false;"><!--{t string="tpl_Remove_01"}--></a>
                </td>
                <td class="center">
                <!--{* 移動 *}-->
                <!--{if $smarty.section.cnt.iteration != 1}-->
                <a href="?" onclick="fnModeSubmit('up','category_id', <!--{$arrList[cnt].category_id}-->); return false;"><!--{t string="tpl_To top_01"}--></a>
                <!--{/if}-->
                <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
                <a href="?" onclick="fnModeSubmit('down','category_id', <!--{$arrList[cnt].category_id}-->); return false;"><!--{t string="tpl_To bottom_01"}--></a>
                <!--{/if}-->
                </td>

            </tr>
            <!--{/section}-->
        </table>
        <!--{else}-->
        <p><!--{t string="tpl_No categories are registered for this hierarchy._01"}--></p>
        <!--{/if}-->
    </div>
    <!--{* ▲画面右 *}-->

</div>
</form>
