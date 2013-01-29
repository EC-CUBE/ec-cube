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
<input type="hidden" name="parent_category_id" value="<!--{$arrForm.parent_category_id|h}-->" />
<input type="hidden" name="category_id" value="<!--{$arrForm.category_id|h}-->" />
<input type="hidden" name="product_id" value="" />
<input type="hidden" name="pageno" value="<!--{$tpl_pageno|h}-->" />
<div id="products" class="contents-main">

    <!--{* ▼画面左 *}-->
    <div id="products-rank-left">
        <a href="?"><img src="<!--{$TPL_URLPATH}-->img/contents/folder_close.gif" alt="Folder">&nbsp;<!--{t string="tpl_Home_01"}--></a><br />
        <!--{section name=cnt loop=$arrTree}-->
            <!--{assign var=level value="`$arrTree[cnt].level`}-->

            <!--{* 上の階層表示の時にdivを閉じる *}-->
            <!--{assign var=close_cnt value="`$before_level-$level+1`}-->
            <!--{if $close_cnt > 0}-->
                <!--{section name=n loop=$close_cnt}--></div><!--{/section}-->
            <!--{/if}-->

            <!--{* スペース繰り返し *}-->
            <!--{section name=n loop=$level}-->&nbsp;&nbsp;<!--{/section}-->

            <!--{* カテゴリ名表示 *}-->
            <!--{assign var=disp_name value="`$arrTree[cnt].category_id`.`$arrTree[cnt].category_name`"}-->
            <a href="?" onclick="fnModeSubmit('tree', 'parent_category_id', <!--{$arrTree[cnt].category_id}-->); return false">
            <!--{if $arrForm.parent_category_id == $arrTree[cnt].category_id}-->
                <img src="<!--{$TPL_URLPATH}-->img/contents/folder_open.gif" alt="Folder">
            <!--{else}-->
                <img src="<!--{$TPL_URLPATH}-->img/contents/folder_close.gif" alt="Folder">
            <!--{/if}-->
            <!--{$disp_name|sfCutString:10:false|h}-->(<!--{$arrTree[cnt].product_count|default:0}-->)</a>
            <br />
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

    <!--▼画面右-->
    <div id="products-rank-right">
        <h2><!--{* jQuery で挿入される *}--></h2>
        <!--{if count($arrProductsList) > 0}-->

            <p class="remark"><!--{t string="tpl_<span class='attention'>T_ARG1 items</span> were found._01" escape="none" T_ARG1=$tpl_linemax}--></p>
            <div class="pager">
                <!--{$tpl_strnavi}-->
            </div>

            <!--{if $smarty.const.ADMIN_MODE == '1'}-->
                <p class="right"><a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('renumber', '', ''); return false;"><!--{t string="tpl_Reallocation of internal ranking_01"}--></a></p>
            <!--{/if}-->

            <table class="list" id="categoryTable">
                <col width="20%" />
                <col width="47.5%" />
                <col width="10%" />
                <col width="7.5%" />
                <col width="15%" />
                <tr class="nodrop nodrag">
                    <th><!--{t string="tpl_Product code_01"}--></th>
                    <th><!--{t string="tpl_Product name_01"}--></th>
                    <th><!--{t string="tpl_Product image_01"}--></th>
                    <th><!--{t string="tpl_Ranking_01"}--></th>
                    <th><!--{t string="tpl_Move_01"}--></th>
                </tr>

                <!--{assign var=rank value=$tpl_start_row}-->
                <!--{section name=cnt loop=$arrProductsList}-->
                    <tr>
                        <td>
                            <!--{$arrProductsList[cnt].product_code_min|h}-->
                            <!--{if $arrProductsList[cnt].product_code_min != $arrProductsList[cnt].product_code_max}-->
                                <br /><!--{t string="-"}--> <!--{$arrProductsList[cnt].product_code_max|h}-->
                            <!--{/if}-->
                        </td>
                        <td>
                            <!--{$arrProductsList[cnt].name|h}-->
                        </td>
                        <td align="center">
                            <!--{* 商品画像 *}-->
                            <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrProductsList[cnt].main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$arrProducts[cnt].name|h}-->">
                        </td>
                        <!--{assign var=rank value=`$rank+1`}-->
                        <td align="center">
                            <!--{$rank}-->
                            <!--{if $arrProductsList[cnt].status == "2"}--><br /><!--{t string="tpl_(Not disclosed)_01"}--><!--{/if}-->
                        </td>
                        <td align="center">
                        <!--{* 移動 *}-->
                        <!--{if !(count($arrProductsList) == 1 && $rank == 1)}-->
                        <input type="text" name="pos-<!--{$arrProductsList[cnt].product_id}-->" size="3" class="box3" /><!--{t string="tpl_Line(s)_01"}--><a href="?" onclick="fnModeSubmit('move','product_id', '<!--{$arrProductsList[cnt].product_id}-->'); return false;"><!--{t string="tpl_Move_01"}--></a><br />
                        <!--{/if}-->
                        <!--{if !($smarty.section.cnt.first && $tpl_disppage eq 1)}-->
                        <a href="?" onclick="fnModeSubmit('up','product_id', '<!--{$arrProductsList[cnt].product_id}-->'); return false;"><!--{t string="tpl_To top_01"}--></a>
                        <!--{/if}-->
                        <!--{if !($smarty.section.cnt.last && $tpl_disppage eq $tpl_pagemax)}-->
                        <a href="?" onclick="fnModeSubmit('down','product_id', '<!--{$arrProductsList[cnt].product_id}-->'); return false;"><!--{t string="tpl_To bottom_01"}--></a>
                        <!--{/if}-->
                        </td>
                    </tr>
                <!--{/section}-->
            </table>
        <!--{else}-->
            <p><!--{t string="tpl_Select a category._01"}--></p>
        <!--{/if}-->
    </div>
    <!--▲画面右-->

</div>
</form>
