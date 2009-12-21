<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->">
<input type="hidden" name="step" value="0">

<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->

<tr><td height="30"></td></tr>
<tr><td align="left" class="fs12st">■アクセス権限のチェック</td></tr>
<tr>
    <td bgcolor="#cccccc">
    <table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
        <tr>
            <td bgcolor="#ffffff" class="fs12">
            <!--{$mess}-->
            </td>
        </tr>
    </table>
    </td>
</tr>
</table>

<!--{if !$err_file}-->
<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<tr><td height="15"></td></tr>
<tr><td align="left" class="fs12">必要なファイルのコピーを開始します。</td></tr>
</table>
<!--{/if}-->

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
    <tr><td height="20"></td></tr>
    <tr>
        <td align="center">
        <!--{if !$err_file}-->
        <a href="#" onmouseover="chgImg('../<!--{$default_dir}-->/img/install/back_on.jpg','back')" onmouseout="chgImg('../<!--{$default_dir}-->/img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_welcome';document.form1.submit();" /><img  width="105" src="../<!--{$default_dir}-->/img/install/back.jpg"  height="24" alt="前へ戻る" border="0" name="back"></a>
        <input type="image" onMouseover="chgImgImageSubmit('../<!--{$default_dir}-->/img/install/next_on.jpg',this)" onMouseout="chgImgImageSubmit('../<!--{$default_dir}-->/img/install/next.jpg',this)" src="../<!--{$default_dir}-->/img/install/next.jpg" width="105" height="24" alt="次へ進む" border="0" name="next">
        <!--{else}-->
        <a href="#" onmouseover="chgImg('../<!--{$default_dir}-->/img/install/back_on.jpg','back')" onmouseout="chgImg('../<!--{$default_dir}-->/img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_welcome';document.form1.submit();" /><img  width="105" src="../<!--{$default_dir}-->/img/install/back.jpg"  height="24" alt="前へ戻る" border="0" name="back"></a>
        <img src="../<!--{$default_dir}-->/img/install/next_off.jpg" width="105" height="24" alt="次へ進む" border="0" name="next">
        <!--{/if}-->
        </td>
    </tr>
    <tr><td height="30"></td></tr>
</from>
</table>
