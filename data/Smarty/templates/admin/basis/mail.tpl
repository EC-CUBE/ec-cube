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
var flag = 0;

function setFlag(){
    flag = 1;
}
function checkFlagAndSubmit(){
    if ( flag == 1 ){
        if( confirm('<!--{t string="tpl_The contents are revised. _01"}-->') ){
            fnSetvalAndSubmit( 'form1', 'mode', 'id_set' );
        } else {
            return false;
        }
    } else {
        fnSetvalAndSubmit( 'form1', 'mode', 'id_set' );
    }
}

//-->
</script>


<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="regist" />
<div id="basis" class="contents-main">
    <table>
        <tr>
            <th><!--{t string="tpl_Template<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
            <!--{assign var=key value="template_id"}-->
            <!--{if $arrErr[$key]}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{/if}-->
            <select name="template_id" onChange="return checkFlagAndSubmit();" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
            <option value="" selected="selected"><!--{t string="tpl_Please make a selection_01"}--></option>
            <!--{html_options options=$arrMailTEMPLATE selected=$arrForm[$key]}-->
            </select>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_E-mail title<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
            <!--{assign var=key value="subject"}-->
            <!--{if $arrErr[$key]}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{/if}-->
            <input type="text" name="subject" value="<!--{$arrForm[$key]|h}-->" onChange="setFlag();" size="30" class="box30" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Header_01"}--></th>
            <td>
            <!--{assign var=key value="header"}-->
            <!--{if $arrErr[$key]}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{/if}-->
            <textarea name="header" cols="75" rows="12" class="area75" onChange="setFlag();" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{"\n"}--><!--{$arrForm[$key]|h}--></textarea><br />
            <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.LTEXT_LEN}-->
            </span>
            <div>
                <a class="btn-normal" href="javascript:;" onclick="fnCharCount('form1','header','cnt_header'); return false;"><span><!--{t string="tpl_Text count_01"}--></span></a>
                <!--{t string="tpl_Character count_01"}-->
                <input type="text" name="cnt_header" size="4" class="box4" readonly = true style="text-align:right" />
            </div>
            </td>
        </tr>
        <tr>
            <th colspan="2" align="center"><!--{t string="tpl_Dynamic data insertion section_01"}--></th>
        </tr>
        <tr>
            <th><!--{t string="tpl_Footer_01"}--></th>
            <td>
            <!--{assign var=key value="footer"}-->
            <!--{if $arrErr[$key]}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{/if}-->
            <textarea name="footer" cols="75" rows="12" class="area75" onChange="setFlag();" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{"\n"}--><!--{$arrForm[$key]|h}--></textarea><br />
            <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.LTEXT_LEN}--></span>
            <div>
                <a class="btn-normal" href="javascript:;" onclick="fnCharCount('form1','footer','cnt_footer'); return false;"><span><!--{t string="tpl_Text count_01"}--></span></a>
                <!--{t string="tpl_Character count_01"}-->
                <input type="text" name="cnt_footer" size="4" class="box4" readonly = true style="text-align:right" />
            </div>
            </td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'regist', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
        </ul>
    </div>
</div>
</form>
