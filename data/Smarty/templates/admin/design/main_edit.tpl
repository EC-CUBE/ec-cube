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

<script type="text/javascript">
<!--
function fnTargetSelf(){
    document.form_edit.target = "_self";
}
//-->
</script>


<form name="form_edit" id="form_edit" method="post" action="?" >
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="page_id" value="<!--{$page_id|h}-->" />
<input type="hidden" name="device_type_id" value="<!--{$device_type_id|h}-->" />

    <!--{if $arrErr.err != ""}-->
        <div class="message">
            <span class="attention"><!--{$arrErr.err}--></span>
        </div>
    <!--{/if}-->
    <table>
        <tr>
            <th><!--{t string="tpl_Name_01"}--></th>
            <td>
                <!--{assign var=key value="page_name"}-->
                <!--{if $arrForm.edit_flg.value == 2}-->
                    <!--{$arrForm[$key].value|h}--><input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" />
                <!--{else}-->
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length|h}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" /><span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$arrForm[$key].length|h}--></span>
                <!--{/if}-->
                <!--{if $arrErr[$key] != ""}-->
                    <div class="message">
                        <span class="attention"><!--{$arrErr[$key]}--></span>
                    </div>
                <!--{/if}-->
            </td>
        </tr>
        <tr>
        <th><!--{t string="tpl_URL_01"}--></th>
            <td>
                <!--{assign var=key value="filename"}-->
                <!--{if $arrForm.edit_flg.value == 2}-->
                    <!--{$smarty.const.HTTP_URL|h}--><!--{$arrForm[$key].value|h}-->.php
                    <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" />
                <!--{else}-->
                    <!--{$smarty.const.USER_URL|h}--><input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length|h}-->" style="ime-mode: disabled;<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />.php<span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$arrForm[$key].length|h}--></span>
                <!--{/if}-->
                <!--{if $arrErr[$key] != ""}-->
                    <div class="attention">
                        <span class="attention"><!--{$arrErr[$key]}--></span>
                    </div>
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <label for="header-chk"><input type="checkbox" name="header_chk" id="header-chk" value="1" <!--{if $arrForm.header_chk.value == "1"}-->checked="checked"<!--{/if}--> /><!--{t string="tpl_Use a common header_01"}--></label>&nbsp;
                <label for="footer-chk"><input type="checkbox" name="footer_chk" id="footer-chk" value="1" <!--{if $arrForm.footer_chk.value == "1"}-->checked="checked"<!--{/if}--> /><!--{t string="tpl_Use a common footer_01"}--></label>
                <div>
                    <textarea id="tpl_data" class="top" name="tpl_data" rows=<!--{$text_row}--> style="width: 98%;"><!--{"\n"}--><!--{$arrForm.tpl_data.value|h|smarty:nodefaults}--></textarea>
                    <input type="hidden" name="html_area_row" value="<!--{$text_row}-->" /><br />
                    <a id="resize-btn" class="btn-normal" href="javascript:;" onclick="ChangeSize('#resize-btn', '#tpl_data', 50, 13); return false;"><span><!--{t string="tpl_Enlarge the image_01"}--></span></a>
                </div>
            </td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form_edit','confirm','',''); return false;"><span class="btn-next"><!--{t string="tpl_Register_01"}--></span></a></li>
        </ul>
    </div>

    <h2><!--{t string="tpl_List of pages that can be edited_01"}--></h2>
    <div class="btn addnew">
        <a class="btn-normal" href="?device_type_id=<!--{$device_type_id|u}-->"><span><!--{t string="tpl_New input of page_01"}--></span></a>
    </div>
    <table class="list">
        <col width="70%" />
        <col width="10%" />
        <col width="10%" />
        <col width="10%" />
        <tr>
            <th><!--{t string="tpl_Name_01"}--></th>
            <th><!--{t string="tpl_Layout_01"}--></th>
            <th><!--{t string="tpl_Page details_01"}--></th>
            <th><!--{t string="tpl_Remove_01"}--></th>
        </tr>
        <!--{foreach key=key item=item from=$arrPageList}-->
            <tr style="<!--{if $item.page_id == $page_id}-->background-color: <!--{$smarty.const.SELECT_RGB}-->;<!--{/if}-->">
                <td>
                    <!--{$item.page_name}-->
                </td>
                <td class="center">
                    <a href="./<!--{$smarty.const.DIR_INDEX_PATH}-->?page_id=<!--{$item.page_id}-->&amp;device_type_id=<!--{$item.device_type_id}-->" ><!--{t string="tpl_Edit_01"}--></a>
                </td>
                <td class="center">
                    <!--{if $item.filename|strlen >= 1}-->
                        <a href="?page_id=<!--{$item.page_id}-->&amp;device_type_id=<!--{$item.device_type_id}-->"><!--{t string="tpl_Edit_01"}--></a>
                    <!--{/if}-->
                </td>
                <td class="center">
                    <!--{if $item.edit_flg == 1}-->
                        <a href="javascript:;" onclick="fnTargetSelf(); fnFormModeSubmit('form_edit','delete','page_id','<!--{$item.page_id|escape:'javascript'|h}-->'); return false;"><!--{t string="tpl_Remove_01"}--></a>
                    <!--{/if}-->
                </td>
            </tr>
        <!--{/foreach}-->
    </table>
</form>
