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

function func_submit(product_id, class_name1, class_name2, product_class_id) {
  var err_text = '';
  var fm = window.opener.document.form1;
  var fm1 = window.opener.document;
  var class1 = "classcategory_id" + product_id + "_1";
  var class2 = "classcategory_id" + product_id + "_2";

  var class1_id = document.getElementById(class1).value;
  var class2_id = document.getElementById(class2).value;

  <!--{if $tpl_no != ''}-->
  var opner_product_id = 'edit_product_id';
  var opner_product_class_id = 'edit_product_class_id';
  fm1.getElementById("no").value = <!--{$tpl_no}-->;
  <!--{else}-->
  var opner_product_id = 'add_product_id';
  var opner_product_class_id = 'add_product_class_id';
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
  fm1.getElementById(opner_product_class_id).value = product_class_id;

  fm.mode.value = 'select_product_detail';
  fm.anchor_key.value = 'order_products';
  fm.submit();
  window.close();

  return true;
}
//-->
</script>


<script type="text/javascript">//<![CDATA[
// 規格2に選択肢を割り当てる。
function fnSetClassCategories(form, classcat_id2_selected) {
    sele1 = form.classcategory_id1;
    sele2 = form.classcategory_id2;
    product_id = form.product_id.value;

    if (sele1) {
        if (sele2) {
            // 規格2の選択肢をクリア
            count = sele2.options.length;
            for(i = count; i >= 0; i--) {
                sele2.options[i] = null;
            }

            // 規格2に選択肢を割り当てる
            classcats = productsClassCategories[product_id][sele1.value];
            i = 0;
            for (var classcat_id2_key in classcats) {
                sele2.options[i] = new Option(classcats[classcat_id2_key].name, classcat_id2_key);
                if (classcat_id2_key == classcat_id2_selected) {
                    sele2.options[i].selected = true;
                }
                i++;
            }
        }
        fnCheckStock(form);
    }
}

function fnCheckStock(form) {
    product_id = form.product_id.value;
    classcat_id1 = form.classcategory_id1.value;
    classcat_id2 = form.classcategory_id2 ? form.classcategory_id2.value : 0;
    classcat2 = productsClassCategories[product_id][classcat_id1][classcat_id2];
    // 商品規格
    eleDynamic = document.getElementById('product_class_id' + product_id);
    if (
           classcat2
        && typeof classcat2.product_class_id != 'undefined'
        && String(classcat2.product_class_id).length >= 1
    ) {
        eleDynamic.value = classcat2.product_class_id;
    } else {
        eleDynamic.value = ''
    }
}
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
</form>
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
  <form name="product_form<!--{$id|escape}-->" action="?" onsubmit="return false;">
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
        <select name="classcategory_id1" id="<!--{$class1}-->" style="<!--{$arrErr[$class1]|sfGetErrorColor}-->"  onchange="fnSetClassCategories(this.form);">
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
        <select name="classcategory_id2" id="<!--{$class2}-->" style="<!--{$arrErr[$class2]|sfGetErrorColor}-->" onchange="fnCheckStock(this.form);"></select>
        <!--{if $arrErr[$class2] != ""}-->
        <br /><span class="attention">※ <!--{$tpl_class_name2[$id]}-->を入力して下さい。</span>
        <!--{/if}-->
      </dd>
      <!--{else}-->
      <input type="hidden" name="<!--{$class2}-->" id="<!--{$class2}-->" value="" />
      <!--{/if}-->
      <input type="hidden" name="product_id" value="<!--{$id|escape}-->" />
      <input type="hidden" name="product_class_id<!--{$id|escape}-->" id="product_class_id<!--{$id|escape}-->" value="<!--{$tpl_product_class_id[$id]}-->" />
      <input type="hidden" name="product_type" id="product_type<!--{$id|escape}-->" value="<!--{$tpl_product_type[$id]}-->" />
    </td>
    <td class="center"><a href="javascript:;" onclick="return func_submit('<!--{$arrProducts[cnt].product_id}-->', '<!--{$tpl_class_name1[$id]}-->', '<!--{$tpl_class_name2[$id]}-->', '<!--{$tpl_product_class_id[$id]}-->')">決定</a></td>
  </tr>
  <!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
</form>
  <!--{sectionelse}-->
  <tr>
    <td colspan="4">商品が登録されていません</td>
  </tr>  
  <!--{/section}-->
  </table>
<!--{/if}-->
<!--▲検索結果表示-->


<!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`admin_popup_footer.tpl"}-->
