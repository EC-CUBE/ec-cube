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
<input type="hidden" name="kiyaku_id" value="<!--{$tpl_kiyaku_id}-->" />
<div id="basis" class="contents-main">
    <table class="form">
        <tr>
            <th><!--{t string="tpl_Agreement title<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.kiyaku_title}--></span>
                <span class="attention"><!--{$arrErr.name}--></span>
                <input type="text" name="kiyaku_title" value="<!--{$arrForm.kiyaku_title|h}-->" maxlength="<!--{$smarty.const.SMTEXT_LEN}-->" style="<!--{if $arrErr.kiyaku_title != "" || $arrErr.name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60"/>
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.SMTEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Agreement contents<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
            <span class="attention"><!--{$arrErr.kiyaku_text}--></span>
            <textarea name="kiyaku_text" maxlength="<!--{$smarty.const.MLTEXT_LEN}-->" cols="60" rows="8" class="area60" style="<!--{if $arrErr.kiyaku_text != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" ><!--{"\n"}--><!--{$arrForm.kiyaku_text|h}--></textarea>
            <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.MLTEXT_LEN}--></span>
            </td>
        </tr>
    </table>
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'confirm', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
        </ul>
    </div>

    <table class="list">
        <col width="65%" />
        <col width="10%" />
        <col width="10%" />
        <col width="15%" />
        <tr>
            <th><!--{t string="tpl_Agreement title_01"}--></th>
            <th><!--{t string="tpl_Edit_01"}--></th>
            <th><!--{t string="tpl_Remove_01"}--></th>
            <th><!--{t string="tpl_Move_01"}--></th>
        </tr>
        <!--{section name=cnt loop=$arrKiyaku}-->
            <tr style="background:<!--{if $tpl_kiyaku_id != $arrKiyaku[cnt].kiyaku_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->;">
            <!--{assign var=kiyaku_id value=$arrKiyaku[cnt].kiyaku_id}-->
                <td><!--{* 規格名 *}--><!--{$arrKiyaku[cnt].kiyaku_title|h}--></td>
                <td align="center">
                    <!--{if $tpl_kiyaku_id != $arrKiyaku[cnt].kiyaku_id}-->
                    <a href="?" onclick="fnSetFormSubmit('form1', 'kiyaku_id', <!--{$arrKiyaku[cnt].kiyaku_id}-->); return false;"><!--{t string="tpl_Edit_01"}--></a>
                    <!--{else}-->
                    <!--{t string="tpl_being edited_01"}-->
                    <!--{/if}-->
                </td>
                <td align="center">
                    <!--{if $arrClassCatCount[$class_id] > 0}-->
                    -
                    <!--{else}-->
                    <a href="?" onclick="fnModeSubmit('delete', 'kiyaku_id', <!--{$arrKiyaku[cnt].kiyaku_id}-->); return false;"><!--{t string="tpl_Remove_01"}--></a>
                    <!--{/if}-->
                </td>
                <td align="center">
                    <!--{if $smarty.section.cnt.iteration != 1}-->
                    <a href="?" onclick="fnModeSubmit('up', 'kiyaku_id', <!--{$arrKiyaku[cnt].kiyaku_id}-->); return false;"><!--{t string="tpl_To top_01"}--></a>
                    <!--{/if}-->
                    <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
                    <a href="?" onclick="fnModeSubmit('down', 'kiyaku_id', <!--{$arrKiyaku[cnt].kiyaku_id}-->); return false;"><!--{t string="tpl_To bottom_01"}--></a>
                    <!--{/if}-->
                </td>
            </tr>
        <!--{/section}-->
    </table>

</div>
</form>
