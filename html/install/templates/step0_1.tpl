<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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

<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<tr><td height="20"></td></tr>
<tr><td align="left" class="fs12st">■必要なファイルのコピー</td></tr>
<tr>
    <td bgcolor="#cccccc">
    <table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
        <tr>
            <td bgcolor="#ffffff" class="fs12">
            <textarea name="disp_area" cols="70" rows="20" class="area70"><!--{$copy_mess}--></textarea></td>
            </td>
        </tr>
    </table>
    </td>
</tr>
</table>

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->">
<input type="hidden" name="step" value="0">

<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->
    <tr><td height="20"></td></tr>
    <tr>
        <td align="center">
        <a href="#" onmouseover="chgImg('img/back_on.jpg','back')" onmouseout="chgImg('img/back.jpg','back')" onclick="document.form1['mode'].value='return_step0';document.form1.submit();return false;" /><img  width="105" src="img/back.jpg"  height="24" alt="前へ戻る" border="0" name="back"></a>
        <input type="image" onMouseover="chgImgImageSubmit('img/next_on.jpg',this)" onMouseout="chgImgImageSubmit('img/next.jpg',this)" src="img/next.jpg" width="105" height="24" alt="次へ進む" border="0" name="next">
        </td>
    </tr>
    <tr><td height="30"></td></tr>
</from>
</table>
