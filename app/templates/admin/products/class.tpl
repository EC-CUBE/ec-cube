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

<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="edit" />
    <input type="hidden" name="class_id" value="<!--{$tpl_class_id|h}-->" />
    <div id="products" class="contents-main">

        <table>
            <tr>
                <th>規格名<span class="attention"> *</span></th>
                <td>
                    <!--{if $arrErr.name}-->
                        <span class="attention"><!--{$arrErr.name}--></span>
                    <!--{/if}-->
                    <input type="text" name="name" value="<!--{$arrForm.name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name|sfGetErrorColor}-->" size="30" class="box30" />
                    <span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
                </td>
            </tr>
        </table>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="eccube.fnFormModeSubmit('form1', 'edit', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
            </ul>
        </div>

        <table class="list">
            <col />
            <col width="15%" />
            <col width="10%" />
            <col width="10%" />
            <col width="15%" />
            <tr>
                <th>規格名 (登録数)</th>
                <th>分類登録</th>
                <th class="edit">編集</th>
                <th class="delete">削除</th>
                <th>移動</th>
            </tr>
            <!--{section name=cnt loop=$arrClass}-->
                <tr style="background:<!--{if $tpl_class_id != $arrClass[cnt].class_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->;">
                    <!--{assign var=class_id value=$arrClass[cnt].class_id}-->
                    <td><!--{* 規格名 *}--><!--{$arrClass[cnt].name|h}--> (<!--{$arrClassCatCount[$class_id]|default:0}-->)</td>
                    <td align="center"><a href="javascript:;" onclick="eccube.moveClassCatPage(<!--{$arrClass[cnt].class_id}-->); return false;">分類登録</a></td>
                    <td align="center">
                        <!--{if $tpl_class_id != $arrClass[cnt].class_id}-->
                            <a href="?" onclick="eccube.setModeAndSubmit('pre_edit', 'class_id', <!--{$arrClass[cnt].class_id}-->); return false;">編集</a>
                        <!--{else}-->
                            編集中
                        <!--{/if}-->
                    </td>
                    <td align="center">
                        <!--{if $arrClassCatCount[$class_id] > 0}-->
                            -
                        <!--{else}-->
                            <a href="?" onclick="eccube.setModeAndSubmit('delete', 'class_id', <!--{$arrClass[cnt].class_id}-->); return false;">削除</a>
                        <!--{/if}-->
                    </td>
                    <td align="center">
                        <!--{if $smarty.section.cnt.iteration != 1}-->
                            <a href="?" onclick="eccube.setModeAndSubmit('up', 'class_id', <!--{$arrClass[cnt].class_id}-->); return false;">上へ</a>
                        <!--{/if}-->
                        <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
                            <a href="?" onclick="eccube.setModeAndSubmit('down', 'class_id', <!--{$arrClass[cnt].class_id}-->); return false;">下へ</a>
                        <!--{/if}-->
                    </td>
                </tr>
            <!--{/section}-->
        </table>

    </div>
</form>
