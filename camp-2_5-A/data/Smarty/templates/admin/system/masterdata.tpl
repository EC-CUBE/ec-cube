<!--{*
/*
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
 */
*}-->
<div id="basis" class="contents-main">
  <form name="form1" id="form1" method="post" action="?">
  <input type="hidden" name="mode" value="show" />
  <div id="basis-masterdata-select">
    <select name="master_data_name" id="master_data_name">
    <!--{html_options output=$arrMasterDataName values=$arrMasterDataName selected=$masterDataName}-->
    </select>
    <button type="submit"><span>選択</span></button>
  </div>
  </form>

  <!--{if $smarty.post.mode == 'show'}-->

  <form name="form2" id="form2" method="post" action="?">
  <input type="hidden" name="mode" value="edit" />
  <input type="hidden" name="master_data_name" value="<!--{$masterDataName}-->" />
  <h2>マスタデータ編集</h2>
  <ul class="attention">
    <li>マスタデータの値を設定できます。</li>
    <li>重複したIDを登録することはできません。</li>
    <li>空のIDを登録すると、値は削除されます。</li>
    <li>設定値によってはサイトが機能しなくなる場合もありますので、十分ご注意下さい。</li>
  </ul>
  <!--{if $errorMessage != ""}-->
  <div class="message">
    <span class="attention"><!--{$errorMessage}--></span>
  </div>
  <!--{/if}-->

  <table class="form">
    <!--{foreach from=$arrMasterData item=val key=key}-->
    <tr>
      <th>ID：<input type="text" name="id[]" value="<!--{$key|escape}-->" size="6" /></th>
      <td>値：<input type="text" name="name[]" value="<!--{$val|escape}-->" style="" size="60" class="box60" /></td>
    </tr>
    <!--{/foreach}-->
  </table>

  <h2>追加のデータ</h2>
  <table class="form">
    <tr>
      <th>ID：<input type="text" name="id[]" size="6" /></th>
      <td>値：<input type="text" name="name[]" style="" size="60" class="box60" /></td>
    </tr>
  </table>
  <div class="btn"><button type="submit" onClick="return document.form2.submit()"><span>この内容で登録する</span></button></div>

  </form>
  <!--{/if}-->

</div>
