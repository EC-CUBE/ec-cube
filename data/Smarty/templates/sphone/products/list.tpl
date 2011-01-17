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
// 並び順を変更
function fnChangeOrderby(orderby) {
    fnSetVal('orderby', orderby);
    fnSetVal('pageno', 1);
    fnSubmit();
}
// 表示件数を変更
function fnChangeDispNumber(dispNumber) {
    fnSetVal('disp_number', dispNumber);
    fnSetVal('pageno', 1);
    fnSubmit();
}
// カゴに入れる
function fnInCart(productForm) {
    var product_id = productForm["product_id"].value;
    fnChangeAction("?#product" + product_id);
    if (productForm["classcategory_id1"]) {
        fnSetVal("classcategory_id1", productForm["classcategory_id1"].value);
    }
    if (productForm["classcategory_id2"]) {
        fnSetVal("classcategory_id2", productForm["classcategory_id2"].value);
    }
    fnSetVal("quantity", productForm["quantity"].value);
    fnSetVal("product_id", productForm["product_id"].value);
    fnSetVal("product_class_id", productForm["product_class_id"].value);
    fnSetVal("product_type", productForm["product_type"].value);
    fnSubmit();
}
function fnCheckStock(form) {
    product_id = form.product_id.value;
    classcat_id1 = form.classcategory_id1.value;
    classcat_id2 = form.classcategory_id2 ? form.classcategory_id2.value : 0;
    classcat2 = productsClassCategories[product_id][classcat_id1][classcat_id2];
    
    // 在庫(品切れ)
    eleDefault = document.getElementById('cartbtn_default_' + product_id);
    eleDynamic = document.getElementById('cartbtn_dynamic_' + product_id);
    if (
           classcat2
        && classcat2.stock_find === false
    ) {
        eleDefault.style.display = 'none';
        eleDynamic.innerHTML = '申し訳ございませんが、只今品切れ中です。';
    } else {
        eleDefault.style.display = '';
        eleDynamic.innerHTML = '';
    }
    
    // 販売価格
    eleDefault = document.getElementById('price02_default_' + product_id);
    eleDynamic = document.getElementById('price02_dynamic_' + product_id);
    if (
           classcat2
        && typeof classcat2.price02 != 'undefined'
        && String(classcat2.price02).length >= 1
    ) {
        eleDefault.style.display = 'none';
        eleDynamic.innerHTML = classcat2.price02;
    } else {
        eleDefault.style.display = '';
        eleDynamic.innerHTML = '';
    }
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
    // 商品種別
    eleDynamic = document.getElementById('product_type' + product_id);
    if (
           classcat2
        && typeof classcat2.product_type != 'undefined'
        && String(classcat2.product_type).length >= 1
    ) {
        eleDynamic.value = classcat2.product_type;
    } else {
        eleDynamic.value = ''
    }
}
//]]>
</script>

<!--▼CONTENTS-->
<div id="undercolumn" class="product product_list">
    <form name="form1" id="form1" method="get" action="?">
        <input type="hidden" name="mode" value="<!--{$mode|h}-->" />
        <!--{* ▼検索条件 *}-->
        <input type="hidden" name="category_id" value="<!--{$arrSearchData.category_id|h}-->" />
        <input type="hidden" name="maker_id" value="<!--{$arrSearchData.maker_id|h}-->" />
        <input type="hidden" name="name" value="<!--{$arrSearchData.name|h}-->" />
        <!--{* ▲検索条件 *}-->
        <!--{* ▼ページナビ関連 *}-->
        <input type="hidden" name="orderby" value="<!--{$orderby|h}-->" />
        <input type="hidden" name="disp_number" value="<!--{$disp_number|h}-->" />
        <input type="hidden" name="pageno" value="<!--{$tpl_pageno|h}-->" />
        <!--{* ▲ページナビ関連 *}-->
        <!--{* ▼注文関連 *}-->
        <input type="hidden" name="product_id" value="" />
        <input type="hidden" name="classcategory_id1" value="" />
        <input type="hidden" name="classcategory_id2" value="" />
        <input type="hidden" name="product_class_id" value="" />
        <input type="hidden" name="product_type" value="" />
        <input type="hidden" name="quantity" value="" />
        <!--{* ▲注文関連 *}-->
        <input type="hidden" name="rnd" value="<!--{$tpl_rnd|h}-->" />
    </form>
    
    <!--★タイトル★-->
    <h2 class="title"><!--{$tpl_subtitle|h}--></h2>
    
    <!--▼検索条件-->
    <!--{if $tpl_subtitle == "検索結果"}-->
        <ul class="pagecondarea">
            <li><strong>商品カテゴリ：</strong><!--{$arrSearch.category|h}--></li>
        <!--{if $arrSearch.maker|strlen >= 1}--><li><strong>メーカー：</strong><!--{$arrSearch.maker|h}--></li><!--{/if}-->
            <li><strong>商品名：</strong><!--{$arrSearch.name|h}--></li>
        </ul>
    <!--{/if}-->
    <!--▲検索条件-->

    <!--▼ページナビ(本文)-->
    <!--{capture name=page_navi_body}-->
        <div class="pagenumberarea">
            <div class="change">
                <!--{if $orderby != 'price'}-->
                    <a href="javascript:fnChangeOrderby('price');">価格順</a>
                <!--{else}-->
                    <strong>価格順</strong>
                <!--{/if}-->
                <!--{if $orderby != "date"}-->
                        <a href="javascript:fnChangeOrderby('date');">新着順</a>
                <!--{else}-->
                    <strong>新着順</strong>
                <!--{/if}-->
            </div>
            <div class="navi"><!--{$tpl_strnavi}--></div>
        </div>
    <!--{/capture}-->
    <!--▲ページナビ(本文)-->

    <!--{foreach from=$arrProducts item=arrProduct name=arrProducts}-->

        <!--{if $smarty.foreach.arrProducts.first}-->
            <!--▼件数-->
            <div>
                <span class="pagenumber"><!--{$tpl_linemax}--></span>件の商品
                
                <select name="disp_number" onchange="javascript:fnChangeDispNumber(this.value);">
                    <!--{foreach from=$arrPRODUCTLISTMAX item="dispnum" key="num"}-->
                        <!--{if $num == $disp_number}-->
                        <option value="<!--{$num}-->" selected="selected" ><!--{$dispnum}--></option>
                        <!--{else}-->
                        <option value="<!--{$num}-->" ><!--{$dispnum}--></option>
                        <!--{/if}-->
                    <!--{/foreach}-->
                </select>
            </div>
            <!--▲件数-->
            
            <!--▼ページナビ(上部)-->
            <form name="page_navi_top" id="page_navi_top" action="?">
                <!--{if $tpl_linemax > 0}--><!--{$smarty.capture.page_navi_body|smarty:nodefaults}--><!--{/if}-->
            </form>
            <!--▲ページナビ(上部)-->
        <!--{/if}-->

        <!--{assign var=id value=$arrProduct.product_id}-->
        <!--{assign var=arrErr value=$arrProduct.arrErr}-->
        <!--▼商品-->
        <div class="listarea">
        <a name="product<!--{$id|h}-->" />
            <div class="listphoto">
                <!--★画像★-->
                <a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrProduct.product_id|u}-->" class="over"><!--商品写真--><img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH|sfTrimURL}-->/<!--{$arrProduct.main_list_image|sfNoImageMainList|h}-->" alt="<!--{$arrProduct.name|h}-->" class="picture" /></a>
            </div>
            
            <div class="listrightblock">
                <!--▼商品ステータス-->
                <!--{if count($productStatus[$id]) > 0}-->
                    <ul class="status_icon">
                        <!--{foreach from=$productStatus[$id] item=status}--> 
                            <li>
                                <img src="<!--{$TPL_DIR}--><!--{$arrSTATUS_IMAGE[$status]}-->" width="65" height="17" alt="<!--{$arrSTATUS[$status]}-->"/>
                            </li>
                        <!--{/foreach}-->
                    </ul>
                <!--{/if}-->
                <!--▲商品ステータス-->
                
                <!--★商品名★-->
                <h3>
                    <a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrProduct.product_id|u}-->" name="product<!--{$arrProduct.product_id}-->"><!--{$arrProduct.name|h}--></a>
                </h3>
                
                <!--★コメント★-->
                <p class="listcomment"><!--{$arrProduct.main_list_comment|h|nl2br}--></p>
                
                <p>
                    <span class="pricebox sale_price">
                        <span class="mini">税込</span>：
                        <span class="price">
                            <span id="price02_default_<!--{$id}-->">
                                <!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
                                    <!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                                <!--{else}-->
                                    <!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$arrProduct.price02_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                                <!--{/if}-->
                            </span><span id="price02_dynamic_<!--{$id}-->"></span>
                            円</span>
                    </span>
                </p>

            </div>
        </div>
        <!--▲商品-->

        <!--{if $smarty.foreach.arrProducts.last}-->
            <!--▼ページナビ(下部)-->
            <form name="page_navi_bottom" id="page_navi_bottom" action="?">
                <!--{if $tpl_linemax > 0}--><!--{$smarty.capture.page_navi_body|smarty:nodefaults}--><!--{/if}-->
            </form>
            <!--▲ページナビ(下部)-->
        <!--{/if}-->

    <!--{foreachelse}-->
        <!--{include file="frontparts/search_zero.tpl"}-->
    <!--{/foreach}-->

</div>
<!--▲CONTENTS-->
