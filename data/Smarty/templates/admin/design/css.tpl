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

<form name="form_css" method="post" action="?" >
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="area_row" value="<!--{$area_row|h}-->" />
<input type="hidden" name="old_css_name" value="<!--{$old_css_name|h}-->" />
<input type="hidden" name="device_type_id" value="<!--{$device_type_id|h}-->" />
<div id="design" class="contents-main">

    <!--{if $arrErr.err != ""}-->
        <div class="message">
            <span class="attention"><!--{$arrErr.err}--></span>
        </div>
    <!--{/if}-->

    <!--▼CSS設定ここから-->
    <table class="form">
        <tr>
            <th><!--{t string="tpl_CSS file name_01"}--></th>
            <td>
                <!--{assign var=key value="css_name"}-->
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" />.css
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$arrForm[$key].length}--></span>
                <!--{if $arrErr[$key] != ""}--> <div class="attention"><!--{$arrErr[$key]}--></div> <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_CSS contents_01"}--></th>
            <td>
                <!--{assign var=key value="css_data"}-->
                <textarea id="css" class="top" name="<!--{$key}-->" cols="90" rows=<!--{$area_row}--> align="left" style="width: 650px;"><!--{"\n"}--><!--{$arrForm[$key].value|h}--></textarea>
                <input type="hidden" name="area_row" value="<!--{$area_row}-->" />
                <div class="btn">
                    <a id="resize-btn" class="btn-normal" href="javascript:;" onclick="ChangeSize('#resize-btn', '#css', 50, 30); return false;"><!--{t string="tpl_Enlarge the image_01"}--></a>
                </div>
            </td>
        </tr>
    </table>
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form_css','confirm','',''); return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
        </ul>
    </div>
    <!--▲CSS設定　ここまで-->

    <!--▼CSSファイル一覧　ここから-->
    <h2><!--{t string="tpl_CSS file that can be edited_01"}--></h2>
    <div class="btn addnew">
        <a class="btn-normal" href="?device_type_id=<!--{$device_type_id|h}-->"><span><!--{t string="tpl_New CSS_01"}--></span></a>
    </div>
    <table class="list" id="design-css-list">
        <tr>
            <th class="name"><!--{t string="tpl_File name_01"}--></th>
            <th class="menu edit"><!--{t string="tpl_Edit_01"}--></th>
            <th class="action delete"><!--{t string="tpl_Remove_01"}--></th>
        </tr>
        <!--{if count($arrCSSList) > 0}-->
        <!--{foreach key=key item=item from=$arrCSSList}-->
        <tr>
            <td style="background:<!--{if $item.css_name == $css_name}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->;"><!--{$item.file_name|h}--></td>
            <td class="center" style="background:<!--{if $item.css_name == $css_name}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->;">
                <a href="?css_name=<!--{$item.css_name|h}-->&amp;device_type_id=<!--{$device_type_id|h}-->"><!--{t string="tpl_Edit_01"}--></a>
            </td>
            <td class="center" style="background:<!--{if $item.css_name == $css_name}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->;">
                <a href="javascript:;" onclick="fnFormModeSubmit('form_css','delete','css_name','<!--{$item.css_name|h}-->'); return false;"><!--{t string="tpl_Remove_01"}--></a>
            </td>
        </tr>
        <!--{/foreach}-->
        <!--{else}-->
        <tr>
            <td colspan="3"><!--{t string="tpl_The CSS file does not exist._01"}--></td>
        </tr>
        <!--{/if}-->
    </table>
    <!--▲CSSファイル一覧　ここまで-->

</div>
</form>
