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
    <input type="hidden" name="keySet" value="" />
    <div id="products" class="contents-main">
        <div class="btn">
            <a class="btn-normal" href="javascript:;" onclick="eccube.setModeAndSubmit('csv','',''); return false;">CSV ダウンロード</a>
            <a class="btn-normal" href='../contents/csv.php?tpl_subno_csv=category'>CSV 出力項目設定</a>
        </div>

        <!--{* ▼画面左 *}-->
        <div id="products-category-left">
            <ul>
                <li>
                    <a href="?"><img src="<!--{$TPL_URLPATH}-->img/contents/folder_close.gif" alt="フォルダ" />&nbsp;ホーム</a>
                    <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`products/category_tree_fork.tpl" children=$arrTree treeID="f0" display=1}-->
                </li>
            </ul>
        </div>
        <!--{* ▲画面左 *}-->

        <!--{* ▼画面右 *}-->
        <div id="products-category-right">


            <div class="now_dir">
                    <!--{if $arrErr.category_name}-->
                    <span class="attention"><!--{$arrErr.category_name}--></span>
                    <!--{/if}-->
                    <input type="text" name="category_name" value="<!--{$arrForm.category_name|h}-->" size="30" class="box30" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.category_name|sfGetErrorColor}-->" />
                    <a class="btn-normal" href="javascript:;" onclick="eccube.setModeAndSubmit('edit','',''); return false;"><span class="btn-next">登録</span></a><span class="attention">&nbsp;（上限<!--{$smarty.const.STEXT_LEN}-->文字）</span>
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
                    <th>ID</th>
                    <th>カテゴリ名</th>
                    <th class="edit">編集</th>
                    <th class="delete">削除</th>
                    <th>移動</th>
                </tr>

                <!--{section name=cnt loop=$arrList}-->
                <tr id="<!--{$arrList[cnt].category_id}-->" style="background:<!--{if $arrForm.category_id != $arrList[cnt].category_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->;" align="left">
                    <td class="center"><!--{$arrList[cnt].category_id}--></td>
                    <td>
                    <!--{if $arrList[cnt].level != $smarty.const.LEVEL_MAX}-->
                        <a href="?" onclick="eccube.setModeAndSubmit('tree', 'parent_category_id', <!--{$arrList[cnt].category_id}-->); return false"><!--{$arrList[cnt].category_name|h}--></a>
                    <!--{else}-->
                        <!--{$arrList[cnt].category_name|h}-->
                    <!--{/if}-->
                    </td>
                    <td class="center">
                        <!--{if $arrForm.category_id != $arrList[cnt].category_id}-->
                        <a href="?" onclick="eccube.setModeAndSubmit('pre_edit', 'category_id', <!--{$arrList[cnt].category_id}-->); return false;">編集</a>
                        <!--{else}-->
                        編集中
                        <!--{/if}-->
                    </td>
                    <td class="center">
                        <a href="?" onclick="eccube.setModeAndSubmit('delete', 'category_id', <!--{$arrList[cnt].category_id}-->); return false;">削除</a>
                    </td>
                    <td class="center">
                    <!--{* 移動 *}-->
                    <!--{if $smarty.section.cnt.iteration != 1}-->
                    <a href="?" onclick="eccube.setModeAndSubmit('up','category_id', <!--{$arrList[cnt].category_id}-->); return false;">上へ</a>
                    <!--{/if}-->
                    <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
                    <a href="?" onclick="eccube.setModeAndSubmit('down','category_id', <!--{$arrList[cnt].category_id}-->); return false;">下へ</a>
                    <!--{/if}-->
                    </td>

                </tr>
                <!--{/section}-->
            </table>
            <!--{else}-->
            <p>この階層には、カテゴリが登録されていません。</p>
            <!--{/if}-->
        </div>
        <!--{* ▲画面右 *}-->

    </div>
</form>
