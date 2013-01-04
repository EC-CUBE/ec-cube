<!--{*
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
 *}-->
<script type="text/javascript">
<!--
// ラジオボタンによる表示・非表示
function fnChangeVisible(check_id, mod_id){

    if (document.getElementById(check_id).checked){
        document.getElementById(mod_id).onclick = false;
        document.getElementById(mod_id).src = '../img/install/next.jpg';
    } else {
        document.getElementById(mod_id).disabled = true;
        document.getElementById(mod_id).src = '../img/install/next_off.jpg';
    }
}
//-->
</script>

<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->" />
<input type="hidden" name="step" value="0" />

<!--{foreach key=key item=item from=$arrHidden}-->
    <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->

<tr><td height="30"></td></tr>
<tr><td align="left" class="fs12st"><!--{t string="tpl_731"}--></td></tr>
<tr><td align="left" class="fs12"><!--{t string="tpl_732"}--></td></tr>
<tr><td height="10"></td></tr>
<tr>
    <td bgcolor="#cccccc" class="fs12">
    <table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
        <tr>
            <td bgcolor="#ffffff" class="fs12">
            <div id="agreement">
                <!--{t string="tpl_733"}-->
            </div>
            </td>
        </tr>
    </table>
    </td>
</tr>
<tr><td height="10"></td></tr>
<!--{assign var=key value="agreement"}-->
<tr><td align="left" class="fs12"><input type="radio" id="agreement_yes" name="<!--{$key}-->" value=true onclick="fnChangeVisible('agreement_yes', 'next');" <!--{if $arrHidden[$key]}-->checked<!--{/if}-->><label for="agreement_yes"><!--{t string="tpl_734"}--></label> <input type="radio" id="agreement_no" name="<!--{$key}-->" value=false onclick="fnChangeVisible('agreement_yes', 'next');" <!--{if !$arrHidden[$key]|h}-->checked<!--{/if}-->><label for="agreement_no"><!--{t string="tpl_735"}--></label></td></tr>
</table>

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
    <tr><td height="20"></td></tr>
    <tr>
        <td align="center">
        <a href="#" onmouseover="chgImg('../img/install/back_on.jpg','back')" onmouseout="chgImg('../img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_welcome';document.form1.submit();" /><img  width="105" src="../img/install/back.jpg"  height="24" alt="<!--{t string="tpl_610"}-->" border="0" name="back"></a>
        <a href="#" onclick="document.form1.submit();"><input type='image' onMouseover="chgImgImageSubmit('../img/install/next_on.jpg',this)" onMouseout="chgImgImageSubmit('../img/install/next.jpg',this)" src="../img/install/next.jpg" width="105" height="24" alt="<!--{t string="tpl_736"}-->" border="0" name="next" id="next"></a>
        </td>
    </tr>
    <tr><td height="30"></td></tr>
</form>
</table>
