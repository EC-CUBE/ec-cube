<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
            <ul>
                <li>
                    <a href="?"><img src="<!--{$TPL_URLPATH}-->img/contents/folder_close.gif" alt="フォルダ" />&nbsp;ホーム</a>
                    <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`products/product_rank_tree_fork.tpl" children=$arrTree treeID="f0" display=1}-->
                </li>
            </ul>
        </div>
        <!--{* ▲画面左 *}-->

        <!--▼画面右-->
        <div id="products-rank-right">
            <h2><!--{* jQuery で挿入される *}--></h2>
            <!--{if count($arrProductsList) > 0}-->

                <p class="remark"><span class="attention"><!--{$tpl_linemax}-->件</span>が該当しました。</p>
                <div class="pager">
                    <!--{$tpl_strnavi}-->
                </div>

                <!--{if $smarty.const.ADMIN_MODE == '1'}-->
                    <p class="right"><a class="btn-normal" href="javascript:;" onclick="eccube.setModeAndSubmit('renumber', '', ''); return false;">内部順位再割り当て</a></p>
                <!--{/if}-->

                <table class="list" id="categoryTable">
                    <col width="20%" />
                    <col width="47.5%" />
                    <col width="10%" />
                    <col width="7.5%" />
                    <col width="15%" />
                    <tr class="nodrop nodrag">
                        <th>商品コード</th>
                        <th>商品名</th>
                        <th>商品画像</th>
                        <th>順位</th>
                        <th>移動</th>
                    </tr>

                    <!--{assign var=rank value=$tpl_start_row}-->
                    <!--{section name=cnt loop=$arrProductsList}-->
                        <tr>
                            <td><!--{from_to from=$arrProductsList[cnt].product_code_min to=$arrProductsList[cnt].product_code_max separator="～<br />"}--></td>
                            <td>
                                <!--{$arrProductsList[cnt].name|h}-->
                            </td>
                            <td align="center">
                                <!--{* 商品画像 *}-->
                                <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProductsList[cnt].main_list_image|sfNoImageMainList|h}-->" style="max-width: 65px;max-height: 65;" alt="<!--{$arrProducts[cnt].name|h}-->" />
                            </td>
                            <!--{assign var=rank value=`$rank+1`}-->
                            <td align="center">
                                <!--{$rank}-->
                                <!--{if $arrProductsList[cnt].status == "2"}--><br />(非公開)<!--{/if}-->
                            </td>
                            <td align="center">
                            <!--{* 移動 *}-->
                            <!--{if !(count($arrProductsList) == 1 && $rank == 1)}-->
                            <input type="text" name="pos-<!--{$arrProductsList[cnt].product_id}-->" size="3" class="box3" />番目へ<a href="?" onclick="eccube.setModeAndSubmit('move','product_id', '<!--{$arrProductsList[cnt].product_id}-->'); return false;">移動</a><br />
                            <!--{/if}-->
                            <!--{if !($smarty.section.cnt.first && $tpl_disppage eq 1)}-->
                            <a href="?" onclick="eccube.setModeAndSubmit('up','product_id', '<!--{$arrProductsList[cnt].product_id}-->'); return false;">上へ</a>
                            <!--{/if}-->
                            <!--{if !($smarty.section.cnt.last && $tpl_disppage eq $tpl_pagemax)}-->
                            <a href="?" onclick="eccube.setModeAndSubmit('down','product_id', '<!--{$arrProductsList[cnt].product_id}-->'); return false;">下へ</a>
                            <!--{/if}-->
                            </td>
                        </tr>
                    <!--{/section}-->
                </table>
            <!--{else}-->
                <p>カテゴリを選択してください。</p>
            <!--{/if}-->
        </div>
        <!--▲画面右-->

    </div>
</form>
