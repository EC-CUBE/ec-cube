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
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="class_id" value="<!--{$tpl_class_id|h}-->" />
<div id="products" class="contents-main">

    <table>
        <tr>
            <th><!--{t string="tpl_Standard name<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <!--{if $arrErr.name}-->
                    <span class="attention"><!--{$arrErr.name}--></span>
                <!--{/if}-->
                <input type="text" name="name" value="<!--{$arrForm.name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="" size="30" class="box30" />
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.STEXT_LEN}--></span>
            </td>
        </tr>
    </table>
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'edit', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
        </ul>
    </div>

    <table class="list">
        <col />
        <col width="15%" />
        <col width="10%" />
        <col width="10%" />
        <col width="15%" />
        <tr>
            <th><!--{t string="tpl_Standard name (registered number)_01"}--></th>
            <th><!--{t string="tpl_Category registration_01"}--></th>
            <th class="edit"><!--{t string="tpl_Edit_01"}--></th>
            <th class="delete"><!--{t string="tpl_Remove_01"}--></th>
            <th><!--{t string="tpl_Move_01"}--></th>
        </tr>
        <!--{section name=cnt loop=$arrClass}-->
            <tr style="background:<!--{if $tpl_class_id != $arrClass[cnt].class_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->;">
                <!--{assign var=class_id value=$arrClass[cnt].class_id}-->
                <td><!--{* 規格名 *}--><!--{$arrClass[cnt].name|h}--> (<!--{$arrClassCatCount[$class_id]|default:0}-->)</td>
                <td align="center"><a href="<!--{$smarty.const.ROOT_URLPATH}-->" onclick="fnClassCatPage(<!--{$arrClass[cnt].class_id}-->); return false;"><!--{t string="tpl_Category registration_01"}--></a></td>
                <td align="center">
                    <!--{if $tpl_class_id != $arrClass[cnt].class_id}-->
                        <a href="?" onclick="fnModeSubmit('pre_edit', 'class_id', <!--{$arrClass[cnt].class_id}-->); return false;"><!--{t string="tpl_Edit_01"}--></a>
                    <!--{else}-->
                        <!--{t string="tpl_being edited_01"}-->
                    <!--{/if}-->
                </td>
                <td align="center">
                    <!--{if $arrClassCatCount[$class_id] > 0}-->
                        -
                    <!--{else}-->
                        <a href="?" onclick="fnModeSubmit('delete', 'class_id', <!--{$arrClass[cnt].class_id}-->); return false;"><!--{t string="tpl_Remove_01"}--></a>
                    <!--{/if}-->
                </td>
                <td align="center">
                    <!--{if $smarty.section.cnt.iteration != 1}-->
                        <a href="?" onclick="fnModeSubmit('up', 'class_id', <!--{$arrClass[cnt].class_id}-->); return false;"><!--{t string="tpl_To top_01"}--></a>
                    <!--{/if}-->
                    <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
                        <a href="?" onclick="fnModeSubmit('down', 'class_id', <!--{$arrClass[cnt].class_id}-->); return false;"><!--{t string="tpl_To bottom_01"}--></a>
                    <!--{/if}-->
                </td>
            </tr>
        <!--{/section}-->
    </table>

</div>
</form>
