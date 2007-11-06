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
<input type="hidden" name="db_skip" value=<!--{$tpl_db_skip}-->>
<input type="hidden" name="senddata_site_url" value="<!--{$tpl_site_url}-->">
<input type="hidden" name="senddata_shop_name" value="<!--{$tpl_shop_name}-->">
<input type="hidden" name="senddata_cube_ver" value="<!--{$tpl_cube_ver}-->">
<input type="hidden" name="senddata_php_ver" value="<!--{$tpl_php_ver}-->">
<input type="hidden" name="senddata_db_ver" value="<!--{$tpl_db_ver}-->">
<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->

<tr><td height="30"></td></tr>
<tr><td align="left" class="fs12st">■サイト情報について</td></tr>
<tr><td align="left" class="fs12">EC-CUBEのシステム向上及び、デバッグのため以下の情報のご提供をお願いいたします。</td></tr>
<tr>
    <td bgcolor="#cccccc" class="fs12">
    <table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
        <tr>
            <td bgcolor="#ffffff" class="fs12" height="50">
                - サイトURL：<!--{$tpl_site_url}--><br/>
                - 店舗名：<!--{$tpl_shop_name}--><br/>
                - EC-CUBEバージョン：<!--{$tpl_cube_ver}--><br/>
                - PHP情報：<!--{$tpl_php_ver}--><br/>
                - DB情報：<!--{$tpl_db_ver}--><br/>
            </td>
        </tr>
    </table>
    </td>
</tr>
<tr><td align="left" class="fs12"><input type="radio" id="ok" name="send_info" checked value=true><label for="ok">はい(推奨)</label>　<input type="radio" id="ng" name="send_info" value=false><label for="ng">いいえ</label></td></tr>
</table>

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
    <tr><td height="20"></td></tr>
    <tr>
        <td align="center">
        <a href="#" onmouseover="chgImg('../<!--{$default_dir}-->/img/install/back_on.jpg','back')" onmouseout="chgImg('../<!--{$default_dir}-->/img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_step3';document.form1.submit();return false;" /><img  width="105" src="../<!--{$default_dir}-->/img/install/back.jpg"  height="24" alt="前へ戻る" border="0" name="back"></a>
        <input type="image" onMouseover="chgImgImageSubmit('../<!--{$default_dir}-->/img/install/next_on.jpg',this)" onMouseout="chgImgImageSubmit('../<!--{$default_dir}-->/img/install/next.jpg',this)" src="../<!--{$default_dir}-->/img/install/next.jpg" width="105" height="24" alt="次へ進む" border="0" name="next">
        </td>
    </tr>
    <tr><td height="30"></td></tr>
</from>
</table>
