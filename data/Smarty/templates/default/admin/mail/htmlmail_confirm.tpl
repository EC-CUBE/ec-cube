<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2008 LOCKON CO.,LTD. All Rights Reserved.
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
<input type="hidden" name="mode" value="complete" />
<!--$arrForm-->
<!--{foreach key=key item=item from=$arrForm}-->
<!--{if $key != "mode"}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->" />
<!--{/if}-->
<!--{/foreach}-->

<!--$arrHidden-->
<!--{foreach key=key item=item from=$arrHidden}-->
  <input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->" />
<!--{/foreach}-->

<div id="main" class="contents-main">
  <h2>HTMLメール作成</h2>
  <table>
    <tr>
      <th>Subject<span class="attention"> *</span></th>
      <td><!--{$arrForm.subject|escape}--></td>
    </tr>
    <tr>
      <th>メール担当写真<span class="attention"> *</span></th>
      <td>
      <!--{assign var=key value="charge_image"}-->
      <!--{if $arrFile[$key].filepath != ""}-->
      <img src="<!--{$arrFile[$key].filepath}-->" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].height}-->" />
      <!--{/if}-->
      </td>
    </tr>
    <tr>
      <th>ヘッダーテキスト<span class="attention"> *</span></th>
      <td><!--{$arrForm.header|escape|nl2br}--></td>
    </tr>
    <tr>
      <th>メイン商品タイトル<span class="attention"> *</span></th>
      <td><!--{$arrForm.main_title|escape}--></td>
    </tr>
    <tr>
      <th>メイン商品コメント<span class="attention"> *</span></th>
      <td><!--{$arrForm.main_comment|escape}--></td>
    </tr>
    <tr>
      <th>メイン商品選択<span class="attention"> *</span></th>
      <td>
      <img src="<!--{$smarty.const.IMAGE_SAVE_URL|escape}--><!--{$arrFileName[0].main_image}-->" alt="メイン商品画像" /><br />
      <input type="text" name="name_main_product" value="<!--{$arrFileName[0].name|escape}-->" disabled="disabled"  size="65" class="box65" style="background:#FFF;border-style:solid;border-color:#FFF;" />
      </td>
    </tr>
    <tr>
      <th>サブ商品群タイトル<span class="attention"> *</span></th>
      <td><!--{$arrForm.sub_title|escape}--></td>
    </tr>
    <tr>
      <th>サブ商品群コメント<span class="attention"> *</span></th>
      <td><!--{$arrForm.sub_comment|escape|nl2br}--></td>
    </tr>
    <!--{foreach key=key item=item from=$arrSub.delete}-->
    <tr>
      <th>商品画像（<!--{$key}-->）</th>
      <td>
        <!--{if $arrFileName[$key].main_image != "" && $item != 'on'}-->
        <!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrFileName[$key].main_image`"}-->
        <img src="<!--{$image_path}-->" width="<!--{$smarty.const.SMALL_IMAGE_WIDTH}-->" height="<!--{$smarty.const.SMALL_IMAGE_HEIGHT}-->" alt="商品画像<!--{$smarty.section.cnt.iteration}-->" /><br />
        <input type="text" name="name_sub_product" value="<!--{$arrFileName[$key].name|escape}-->" disabled="disabled"  size="65" class="box65" style="background:#FFF;border-style:solid;border-color:#FFF;" />
        <!--{else}-->未登録<!--{/if}-->
      </td>
    </tr>
    <!--{/foreach}-->
  </table>

  <div class="btn">
    <button type="button" onclick="fnModeSubmit('return', '', ''); return false;"><span>前のページに戻る</span></button>
    <button type="submit"><span>この内容で登録する</span></button>
  </div>
</div>
</form>
