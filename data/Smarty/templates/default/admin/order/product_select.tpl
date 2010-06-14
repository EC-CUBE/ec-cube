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
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`admin_popup_header.tpl"}-->

<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();

function func_submit(product_id, class_name1, class_name2) {
  var err_text = '';
  var fm = window.opener.document.form1;
  var fm1 = window.opener.document;
  var class1 = "classcategory_id" + product_id + "_1";
  var class2 = "classcategory_id" + product_id + "_2";

  var class1_id = document.getElementById(class1).value;
  var class2_id = document.getElementById(class2).value;

  <!--{if $tpl_no != ''}-->
  var opner_product_id = 'edit_product_id';
  var opner_classcategory_id1 = 'edit_classcategory_id1';
  var opner_classcategory_id2 = 'edit_classcategory_id2';
  fm1.getElementById("no").value = <!--{$tpl_no}-->;
  <!--{else}-->
  var opner_product_id = 'add_product_id';
  var opner_classcategory_id1 = 'add_classcategory_id1';
  var opner_classcategory_id2 = 'add_classcategory_id2';
  <!--{/if}-->

  if (document.getElementById(class1).type == 'select-one' && class1_id == '') {
    err_text = class_name1 + "を選択してください。\n";
  }
  if (document.getElementById(class2).type == 'select-one' && class2_id == '') {
    err_text = err_text + class_name2 + "を選択してください。\n";
  }
  if (err_text != '') {
    alert(err_text);
    return false;
  }

  fm1.getElementById(opner_product_id).value = product_id;
  if (class1_id != '') {
    fm1.getElementById(opner_classcategory_id1).value = class1_id;
  }
  if (class2_id != '') {
    fm1.getElementById(opner_classcategory_id2).value = class2_id;
  }

  fm.mode.value = 'select_product_detail';
  fm.anchor_key.value = 'order_products';
  fm.submit();
  window.close();

  return true;
}
//-->
</script>

<script type="text/javascript">//<![CDATA[
// セレクトボックスに項目を割り当てる。
function lnSetSelect(name1, name2, id, val) {
        sele1 = document.form1[name1];
        sele2 = document.form1[name2];
        lists = eval('lists' + id);
        vals = eval('vals' + id);

        if(sele1 && sele2) {
                index = sele1.selectedIndex;

                // セレクトボックスのクリア
                count = sele2.options.length;
                for(i = count; i >= 0; i--) {
                        sele2.options[i] = null;
                }

                // セレクトボックスに値を割り当てる
                len = lists[index].length;
                for(i = 0; i < len; i++) {
                        sele2.options[i] = new Option(lists[index][i], vals[index][i]);
                        if(val != "" && vals[index][i] == val) {
                                sele2.options[i].selected = true;
                        }
                }
        }
}
//]]>
</script>


<script type="text/javascript">//<![CDATA[
    <!--{$tpl_javascript}-->
//]]>
</script>

<!--▼検索フォーム-->
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
<input name="mode" type="hidden" value="search" />
<input name="anchor_key" type="hidden" value="" />
<input name="search_pageno" type="hidden" value="" />
<input name="no" type="hidden" value="<!--{$tpl_no}-->" />
<table class="form">
  <tr>
    <th>カテゴリ</th>
    <td>
      <select name="search_category_id">
        <option value="" selected="selected">選択してください</option>
        <!--{html_options options=$arrCatList selected=$arrForm.search_category_id}-->
      </select>
    </td>
  </tr>
  <tr>
    <th>商品名</th>
    <td><input type="text" name="search_name" value="<!--{$arrForm.search_name}-->" size="35" class="box35" /></td>
  </tr>
  <tr>
    <th>商品コード</th>
    <td><input type="text" name="search_product_code" value="<!--{$arrForm.search_product_code}-->" size="35" class="box35" /></td>
  </tr>
</table>
<div class="btn">
  <button type="submit"><span>検索を開始</span></button>
</div>

<!--▼検索結果表示-->
<!--{if $tpl_linemax}-->
<p>
  <!--{$tpl_linemax}-->件が該当しました。
  <!--{$tpl_strnavi}-->
</p>

<!--▼検索後表示部分-->
<table class="list">
  <tr>
    <th class="image">商品画像</th>
    <th class="id">商品コード</th>
    <th class="name">商品名</th>
    <th class="action">決定</th>
  </tr>
  <!--{section name=cnt loop=$arrProducts}-->
  <!--{assign var=id value=$arrProducts[cnt].product_id}-->
  <!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
  <tr>
    <td class="center">
      <img src="<!--{$smarty.const.URL_DIR}-->resize_image.php?image=<!--{$arrProducts[cnt].main_list_image|sfNoImageMainList|escape}-->&width=65&height=65" alt="<!--{$arrRecommend[$recommend_no].name|escape}-->" />
    </td>  
    <td>
      <!--{assign var=codemin value=`$arrProducts[cnt].product_code_min`}-->
      <!--{assign var=codemax value=`$arrProducts[cnt].product_code_max`}-->
      <!--{* 商品コード *}-->
      <!--{if $codemin != $codemax}-->
        <!--{$codemin|escape}-->～<!--{$codemax|escape}-->
      <!--{else}-->
        <!--{$codemin|escape}-->
      <!--{/if}-->
    </td>
    <td>
      <!--{$arrProducts[cnt].name|escape}-->

      <!--{assign var=class1 value=classcategory_id`$id`_1}-->
      <!--{assign var=class2 value=classcategory_id`$id`_2}-->
      <!--{if $tpl_classcat_find1[$id]}-->
      <dt><!--{$tpl_class_name1[$id]|escape}-->：</dt>
      <dd>
        <select name="<!--{$class1}-->" id="<!--{$class1}-->" style="<!--{$arrErr[$class1]|sfGetErrorColor}-->"  <!--{if $tpl_classcat_find2[$id]}--> onchange="lnSetSelect('<!--{$class1}-->', '<!--{$class2}-->', '<!--{$id}-->','');"<!--{/if}-->>
          <option value="">選択してください</option>
          <!--{html_options options=$arrClassCat1[$id] selected=$arrForm[$class1]}-->
        </select>
        <!--{if $arrErr[$class1] != ""}-->
        <br /><span class="attention">※ <!--{$tpl_class_name1[$id]}-->を入力して下さい。</span>
        <!--{/if}-->
      </dd>
      <!--{else}-->
      <input type="hidden" name="<!--{$class1}-->" id="<!--{$class1}-->" value="" />
      <!--{/if}-->
      <!--{if $tpl_classcat_find2[$id]}-->
      <dt><!--{$tpl_class_name2[$id]|escape}-->：</dt>
      <dd>
        <select name="<!--{$class2}-->" id="<!--{$class2}-->" style="<!--{$arrErr[$class2]|sfGetErrorColor}-->">
          <option value="">選択してください</option>
        </select>
        <!--{if $arrErr[$class2] != ""}-->
        <br /><span class="attention">※ <!--{$tpl_class_name2[$id]}-->を入力して下さい。</span>
        <!--{/if}-->
      </dd>
      <!--{else}-->
      <input type="hidden" name="<!--{$class2}-->" id="<!--{$class2}-->" value="" />
      <!--{/if}-->
    </td>
    <td class="center"><a href="" onclick="return func_submit('<!--{$arrProducts[cnt].product_id}-->', '<!--{$tpl_class_name1[$id]}-->', '<!--{$tpl_class_name2[$id]}-->')">決定</a></td>
  </tr>
  <!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
  <!--{sectionelse}-->
  <tr>
    <td colspan="4">商品が登録されていません</td>
  </tr>  
  <!--{/section}-->
  </table>
<!--{/if}-->
<!--▲検索結果表示-->

</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`admin_popup_footer.tpl"}-->
