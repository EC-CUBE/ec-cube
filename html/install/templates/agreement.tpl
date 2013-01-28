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
<tr><td align="left" class="fs12st"><!--{t string="tpl_*Agreement to the license agreement_01"}--></td></tr>
<tr><td align="left" class="fs12"><!--{t string="tpl_Please read the license agreement below.<br />It is necessary to agree to the agreement in order to continue with installation_01" escape="none"}--></td></tr>
<tr><td height="10"></td></tr>
<tr>
    <td bgcolor="#cccccc" class="fs12">
    <table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
        <tr>
            <td bgcolor="#ffffff" class="fs12">
            <div id="agreement">
                <!--{t string="tpl_Please agree to the software license agreement<br /><br />Lockon Co., Ltd. (hereinafter referred to as 'our company') requires customers to agree to the contents of the 'Software License Agreement' below to be able to use this software. You will be deemed as having agreed to the 'Software License Agreement' below at the point in time that you install, copy, or use this software.<br /><br />--------------------- Software License Agreement ---------------------<br /><br />1. License<br /><br />EC-CUBE adopts a 'dual license method' in which you can select either a free GPL license or paid commercial license for using the EC-CUBE product. The major characteristics of each license are as follows.<br /><br />1-1. GPL license<br /><br />Although it is possible to use EC-CUBE for free, and carry out duplication, modification, and distribution, if distributing an application that uses EC-CUBE, you must publish the source code for the application and make it utilizable.<br /><br />* When modifying, you may modify everything except for the copyright notice in the header section of the program file (PHP file, etc.) <br /><br />* With regard to the official conditions of the GPL license (GNU General Public License), please refer to http://www.fsf.org/licenses/ (Japanese translation: http://www.opensource.jp/gpl/gpl.ja.html).<br /><br />1-2. Commercial license <br /><br />The EC-CUBE commercial license is a license for parties who do not want to comply with the GPL license. <br />When you purchase an EC-CUBE commercial license, it is not necessary to make your own application an open source, within the scope of the commercial license. <br /><br />* A commercial license is necessary for all uses that do not comply with the GPL license. <br /><br />* For details regarding the commercial license, refer to http://www.ec-cube.net/license/business.php.<br /><br />2. Exclusion of liability<br /><br />2-1. Users shall confirm and agree that all direct and indirect damages (data loss, server trouble, halting of operations, claims from a third party, etc.) and dangers arising from use of this software are entirely the responsibility of the user.<br />2-2. In any case, and even in cases of illegal behavior, agreement, or any other legal basis, the suppliers, resellers, and various information contents providers for this software are not responsible for any direct, indirect, specific, incidental, or consequential loss or damage of the customer or other third party, including the loss of sales value, halting of operations, damage resulting from computer malfunctions, and any other commercial loss or damages, etc. Furthermore, our company is not responsible for any claims by a third party. <br /><br />3. Gathering of site information <br /><br />3-1 When installing EC-CUBE, the customer confirms and agrees that our company gathers information such as the site URL, store name, EC-CUBE version, PHP information, DB information, etc.<br />_01" escape="none"}-->
            </div>
            </td>
        </tr>
    </table>
    </td>
</tr>
<tr><td height="10"></td></tr>
<!--{assign var=key value="agreement"}-->
<tr><td align="left" class="fs12"><input type="radio" id="agreement_yes" name="<!--{$key}-->" value=true onclick="fnChangeVisible('agreement_yes', 'next');" <!--{if $arrHidden[$key]}-->checked<!--{/if}-->><label for="agreement_yes"><!--{t string="tpl_Agree_01"}--></label> <input type="radio" id="agreement_no" name="<!--{$key}-->" value=false onclick="fnChangeVisible('agreement_yes', 'next');" <!--{if !$arrHidden[$key]|h}-->checked<!--{/if}-->><label for="agreement_no"><!--{t string="tpl_Do not agree_01"}--></label></td></tr>
</table>

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
    <tr><td height="20"></td></tr>
    <tr>
        <td align="center">
        <a href="#" onmouseover="chgImg('../img/install/back_on.jpg','back')" onmouseout="chgImg('../img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_welcome';document.form1.submit();" /><img  width="105" src="../img/install/back.jpg"  height="24" alt="<!--{t string="tpl_Go back_01"}-->" border="0" name="back"></a>
        <a href="#" onclick="document.form1.submit();"><input type='image' onMouseover="chgImgImageSubmit('../img/install/next_on.jpg',this)" onMouseout="chgImgImageSubmit('../img/install/next.jpg',this)" src="../img/install/next.jpg" width="105" height="24" alt="<!--{t string="tpl_Next_01"}-->" border="0" name="next" id="next"></a>
        </td>
    </tr>
    <tr><td height="30"></td></tr>
</form>
</table>
