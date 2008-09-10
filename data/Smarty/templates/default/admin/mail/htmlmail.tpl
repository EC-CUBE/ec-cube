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
<script language="JavaScript">
<!--
function lfc_del_product( pname ){
  fm = document.form1;
  fm[pname].value = '';
  fm['photo_' + pname].src = '<!--{$smarty.const.NO_IMAGE_URL}-->';
  fm['name_' + pname].value = '';
  
  return false;
}
//-->
</script>


<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="template_id" value="<!--{$arrForm.template_id|escape}-->" />
<input type="hidden" name="mail_method" value="3" />
<input type="hidden" name="image_key" value="" />
<input type="hidden" name="product_key" value="" />
<input type="hidden" name="sub_product_num" value="" />
<input type="hidden" name="mode" value="confirm" />
<!--{foreach key=key item=item from=$arrHidden}-->
<!--{if $key ne "mode"}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->" />
<!--{/if}-->
<!--{/foreach}-->
<div id="mail" class="contents-main">
  <h2>HTMLメール作成</h2>
  <table>
    <tr>
      <th>Subject<span class="attention"> *</span></th>
      <td>
        <input type="text" name="subject" size="65" class="box65" <!--{if $arrErr.subject}--><!--{sfSetErrorStyle}--><!--{/if}--> value="<!--{$arrForm.subject|escape}-->" />
        <!--{if $arrErr.subject}--><br /><span class="attention"><!--{$arrErr.subject}--></span><!--{/if}-->
      </td>
    </tr>
    <tr>
      <th>メール担当写真<span class="attention"> *</span><br />[130×130]</th>
      <!--{assign var=key value="charge_image"}-->
      <td>
        <!--{if $arrFile[$key].filepath != ""}-->
        <img src="<!--{$arrFile[$key].filepath}-->" alt="メール担当写真" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].height}-->" /><br /><br />
        <!--{/if}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <input type="file" name="<!--{$key}-->" size="45" class="box45"　style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
        <button type="button" onclick="fnModeSubmit('upload_image','image_key','<!--{$key}-->');"><span>アップロード</span></button>
      </td>
    </tr>
    <tr>
      <th>ヘッダーテキスト<span class="attention"> *</span></th>
      <td>
        <textarea name="header" cols="70" rows="8" class="area70" <!--{if $arrErr.header}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{$arrForm.header|escape}--></textarea>
        <!--{if $arrErr.header}--><br /><span class="attention"><!--{$arrErr.header}--></span><!--{/if}-->
      </td>
    </tr>
    <tr>
      <th>メイン商品タイトル<span class="attention"> *</span></th>
      <td>
        <input type="text" name="main_title" size="65" class="box65"  <!--{if $arrErr.main_title}--><!--{sfSetErrorStyle}--><!--{/if}--> value="<!--{$arrForm.main_title|escape}-->" />
        <!--{if $arrErr.main_title}--><br /><span class="attention"><!--{$arrErr.main_title}--></span><!--{/if}-->
      </td>
    </tr>
    <tr>
      <th>メイン商品コメント<span class="attention"> *</span></th>
      <td>
        <textarea name="main_comment" cols="70" rows="8" class="area70" <!--{if $arrErr.main_comment}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{$arrForm.main_comment|escape}--></textarea>
        <!--{if $arrErr.main_comment}--><br /><span class="attention"><!--{$arrErr.main_comment}--></span><!--{/if}-->
      </td>
    </tr>
    <tr>
      <th>メイン商品選択<span class="attention"> *</span><br />[110×120]</th>
      <td>
        <!--{if is_numeric($arrForm.template_id)}-->
          <!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrFileName[0].main_image`"}-->
        <!--{elseif $arrFileName[0].main_image != ""}-->
          <!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrFileName[0].main_image`"}-->
        <!--{else}-->
          <!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
        <!--{/if}-->
        <img src="<!--{$image_path}-->" width="<!--{$smarty.const.SMALL_IMAGE_WIDTH}-->" height="<!--{$smarty.const.SMALL_IMAGE_HEIGHT}-->" alt="商品画像main" />
        <input type="hidden" name="main_product_id" value="<!--{$arrForm.main_product_id}-->" />
      　<a href="#" onclick="win03('./htmlmail_select.php?name=main_product_id','select','450','300'); return false;" target="_blank">商品選択</a><br />
        <!--{if $arrErr.main_product_id}--><br /><span class="attention"><!--{$arrErr.main_product_id}--></span><!--{/if}-->
        <input type="text" name="name_main_product_id" value="<!--{$arrFileName[0].name|escape}-->" disabled="disabled" size="65" class="box65" style="background:#FFF;border-style:solid;border-color:#FFFFFF;" />
      </td>
    </tr>
    <tr>
      <th>サブ商品群タイトル<span class="attention"> *</span></th>
      <td>
        <input type="text" name="sub_title" size="65" class="box65" <!--{if $arrErr.sub_title}--><!--{sfSetErrorStyle}--><!--{/if}--> value="<!--{$arrForm.sub_title|escape}-->" />
        <!--{if $arrErr.sub_title}--><br /><span class="attention"><!--{$arrErr.sub_title}--></span><!--{/if}-->
      </td>
    </tr>
    <tr>
      <th>サブ商品群コメント<span class="attention"> *</span></th>
      <td>
        <textarea name="sub_comment" cols="70" rows="8" class="area70" <!--{if $arrErr.sub_comment}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{$arrForm.sub_comment|escape}--></textarea>
        <!--{if $arrErr.sub_comment}--><br /><span class="attention"><!--{$arrErr.sub_comment}--></span><!--{/if}-->
      </td>
    </tr>
    <!--{section name=cnt loop=$smarty.const.HTML_TEMPLATE_SUB_MAX}-->
    <!--{assign var=subProductNum value=`$smarty.section.cnt.iteration`}-->
    <tr>
      <th>商品画像（<!--{$subProductNum}-->）</th>
      <td>
        <!--{if is_numeric($arrForm.template_id)}-->
          <!--{if strlen($arrFileName[$smarty.section.cnt.iteration].main_image) > 0}--><!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrFileName[$smarty.section.cnt.iteration].main_image`"}--><!--{else}--><!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}--><!--{/if}-->
        <!--{elseif $arrFileName[$smarty.section.cnt.iteration].main_image != ""}-->
          <!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrFileName[$smarty.section.cnt.iteration].main_image`"}-->
        <!--{else}-->
          <!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
        <!--{/if}-->
        <!--{if $smarty.section.cnt.iteration <= 9}-->
        <!--{assign var=sub_product_id value="sub_product_id0`$smarty.section.cnt.iteration`"}-->
        <!--{else}-->
        <!--{assign var=sub_product_id value="sub_product_id`$smarty.section.cnt.iteration`"}-->
        <!--{/if}-->
        <img src="<!--{$image_path}-->" width="<!--{$smarty.const.SMALL_IMAGE_WIDTH}-->" height="<!--{$smarty.const.SMALL_IMAGE_HEIGHT}-->" alt="商品画像<!--{$subProductNum}-->" />
        <input type="hidden" name="<!--{$sub_product_id}-->" value="<!--{$arrFileName[$smarty.section.cnt.iteration].product_id|escape}-->" />
      　<a href="#" onclick="win03('./htmlmail_select.php?name=<!--{$sub_product_id}-->' ,'select','450','300'); return false;" target="_blank">商品選択</a>
        <!--{assign var=sub_box value="delete_sub`$smarty.section.cnt.iteration`"}-->
      　<!--{if $arrForm[$sub_product_id]}--><input type="checkbox" name="delete_sub<!--{$smarty.section.cnt.iteration}-->" value="1" <!--{if $arrForm[$sub_box] == '1'}-->checked<!--{/if}--> />商品削除<br /><!--{/if}-->
        <input type="text" name="name_sub_product" value="<!--{$arrFileName[$smarty.section.cnt.iteration].name|escape}-->" disabled="disabled"  size="65" class="box65" style="background:#FFF;border-style:solid;border-color:#FFF;" />
      </td>
    </tr>
    <!--{/section}-->
  </table>

  <div class="btn">
    <button type="submit"><span>確認ページへ</span></button>
  </div>
</div>
</form>
