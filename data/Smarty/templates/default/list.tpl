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
<script type="text/javascript">//<![CDATA[
// セレクトボックスに項目を割り当てる。
function fnSetSelect(form, val) {
    sele1 = form['classcategory_id1'];
    sele2 = form['classcategory_id2'];
    id = form['product_id'].value;
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
// 並び順を変更
function fnChangeOrderby(orderby) {
    fnSetVal('orderby', orderby);
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
    fnSubmit();
}
//]]>
</script>

<!--▼CONTENTS-->
<div id="undercolumn" class="product product_list">
    <form name="form1" id="form1" method="get" action="?">
        <input type="hidden" name="mode" value="<!--{$mode|escape}-->" />
        <!--{* ▼検索条件 *}-->
        <input type="hidden" name="category_id" value="<!--{$arrSearchData.category_id|escape}-->" />
        <input type="hidden" name="maker_id" value="<!--{$arrSearchData.maker_id|escape}-->" />
        <input type="hidden" name="name" value="<!--{$arrSearchData.name|escape}-->" />
        <!--{* ▲検索条件 *}-->
        <!--{* ▼ページナビ関連 *}-->
        <input type="hidden" name="orderby" value="<!--{$orderby|escape}-->" />
        <input type="hidden" name="disp_number" value="<!--{$disp_number|escape}-->" />
        <input type="hidden" name="pageno" value="<!--{$tpl_pageno|escape}-->" />
        <!--{* ▲ページナビ関連 *}-->
        <!--{* ▼注文関連 *}-->
        <input type="hidden" name="product_id" value="" />
        <input type="hidden" name="classcategory_id1" value="" />
        <input type="hidden" name="classcategory_id2" value="" />
        <input type="hidden" name="quantity" value="" />
        <!--{* ▲注文関連 *}-->
    </form>
    
    <!--★タイトル★-->
    <h2 class="title"><!--{$tpl_subtitle|escape}--></h2>
    
    <!--▼検索条件-->
    <!--{if $tpl_subtitle == "検索結果"}-->
        <ul class="pagecondarea">
            <li><strong>商品カテゴリ：</strong><!--{$arrSearch.category|escape}--></li>
            <!--{if $arrSearch.maker|strlen >= 1}--><li><strong>メーカー：</strong><!--{$arrSearch.maker|escape}--></li><!--{/if}-->
            <li><strong>商品名：</strong><!--{$arrSearch.name|escape}--></li>
        </ul>
    <!--{/if}-->
    <!--▲検索条件-->

    <!--▼ページナビ(上部)-->
    <form name="page_navi_top" id="page_navi_top" action="?">
        <!--{if $tpl_linemax > 0}-->
            <ul class="pagenumberarea">
                <li class="left"><span class="pagenumber"><!--{$tpl_linemax}--></span>件の商品がございます。</li>
                <li class="center"><!--{$tpl_strnavi}--></li>
                <li class="right">
                    <!--{if $orderby != 'price'}-->
                        <a href="javascript:fnChangeOrderby('price');">価格順</a>
                    <!--{else}-->
                        <strong>価格順</strong>
                    <!--{/if}-->&nbsp;
                    <!--{if $orderby != "date"}-->
                        <a href="javascript:fnChangeOrderby('date');">新着順</a>
                    <!--{else}-->
                        <strong>新着順</strong>
                    <!--{/if}-->
                    表示件数
                    <select name="disp_number" onchange="javascript:fnModeSubmit('','disp_number',this.value);">
                        <!--{foreach from=$arrPRODUCTLISTMAX item="dispnum" key="num"}-->
                            <!--{if $num == $disp_number}-->
                                <option value="<!--{$num}-->" selected="selected" ><!--{$dispnum}--></option>
                            <!--{else}-->
                                <option value="<!--{$num}-->" ><!--{$dispnum}--></option>
                            <!--{/if}-->
                        <!--{/foreach}-->
                    </select>
                </li>
            </ul>
        <!--{else}-->
            <!--{include file="frontparts/search_zero.tpl"}-->
        <!--{/if}-->
    </form>
    <!--▲ページナビ(上部)-->

    <!--{foreach from=$arrProducts item=arrProduct}-->
        <!--{assign var=id value=$arrProduct.product_id}-->
        <!--▼商品-->
        <div class="listarea">
            <a name="product<!--{$id|escape}-->" />
            <div class="listphoto">
                <!--★画像★-->
                <a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProduct.product_id}-->" class="over"><!--商品写真--><img src="<!--{$smarty.const.IMAGE_SAVE_URL|sfTrimURL}-->/<!--{$arrProduct.main_list_image|sfNoImageMainList|escape}-->" alt="<!--{$arrProduct.name|escape}-->" class="picture" /></a>
            </div>
            
            <div class="listrightblock">
                <!--アイコン-->
                <!--商品ステータス-->
                <!--{if count($arrProduct.product_flag) > 0}-->
                    <ul class="status_icon">
                        <!--{section name=flg loop=$arrProduct.product_flag|count_characters}-->
                            <!--{if $arrProduct.product_flag[flg] == "1"}-->
                                <!--{assign var=key value="`$smarty.section.flg.iteration`"}-->
                                <li><img src="<!--{$TPL_DIR}--><!--{$arrSTATUS_IMAGE[$key]}-->" width="65" height="17" alt="<!--{$arrSTATUS[$key]}-->"/></li>
                                <!--{assign var=sts_cnt value=$sts_cnt+1}-->
                            <!--{/if}-->
                        <!--{/section}-->
                    </ul>
                <!--{/if}-->
                <!--商品ステータス-->
                <!--アイコン-->
                
                <!--★商品名★-->
                <h3>
                    <a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProduct.product_id}-->"><!--{$arrProduct.name|escape}--></a>
                </h3>
                
                <!--★コメント★-->
                <p class="listcomment"><!--{$arrProduct.main_list_comment|escape|nl2br}--></p>
                
                <p>
                    <span class="pricebox sale_price">
                        <!--{$smarty.const.SALE_PRICE_TITLE}--><span class="mini">(税込)</span>：
                        <span class="price">
                        <!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
                            <!--{$arrProduct.price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                        <!--{else}-->
                            <!--{$arrProduct.price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$arrProduct.price02_max|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                        <!--{/if}-->円</span>
                    </span>
                    
                    <!--★詳細ボタン★-->
                    <span class="btnbox">
                        <!--{assign var=name value="detail`$id`"}-->
                        <a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProduct.product_id}-->" onmouseover="chgImg('<!--{$TPL_DIR}-->img/products/b_detail_on.gif','<!--{$name}-->');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/products/b_detail.gif','<!--{$name}-->');">
                            <img src="<!--{$TPL_DIR}-->img/products/b_detail.gif" width="115" height="25" alt="詳しくはこちら" name="<!--{$name}-->" id="<!--{$name}-->" /></a>
                    </span>
                </p>

                <!--{if $arrProduct.stock_max == 0 && $arrProduct.stock_unlimited_max != 1}-->
                    <p class="soldout"><em>申し訳ございませんが、只今品切れ中です。</em></p>
                <!--{else}-->
                    <!--▼買い物かご-->
                    <form name="product_form<!--{$id|escape}-->" action="?">
                        <input type="hidden" name="product_id" value="<!--{$id|escape}-->" />
                        <div class="in_cart">
                            <dl>
                                <!--{if $tpl_classcat_find1[$id]}-->
                                    <dt><!--{$tpl_class_name1[$id]|escape}-->：</dt>
                                    <dd>
                                        <select name="classcategory_id1" style="<!--{$arrProduct.arrErr.classcategory_id1|sfGetErrorColor}-->" onchange="fnSetSelect(this.form);">
                                        <option value="">選択してください</option>
                                        <!--{html_options options=$arrClassCat1[$id] selected=$arrProduct.classcategory_id1}-->
                                        </select>
                                        <!--{if $arrProduct.arrErr.classcategory_id1 != ""}-->
                                            <br /><span class="attention">※ <!--{$tpl_class_name1[$id]}-->を入力して下さい。</span>
                                        <!--{/if}-->
                                    </dd>
                                <!--{/if}-->
                                <!--{if $tpl_classcat_find2[$id]}-->
                                    <dt><!--{$tpl_class_name2[$id]|escape}-->：</dt>
                                    <dd>
                                        <select name="classcategory_id2" style="<!--{$arrProduct.arrErr.classcategory_id2|sfGetErrorColor}-->">
                                        <option value="">選択してください</option>
                                        </select>
                                        <!--{if $arrProduct.arrErr.classcategory_id2 != ""}-->
                                            <br /><span class="attention">※ <!--{$tpl_class_name2[$id]}-->を入力して下さい。</span>
                                        <!--{/if}-->
                                    </dd>
                                <!--{/if}-->

                                <dt>数量：</dt>
                                <dd>
                                    <input type="text" name="quantity" size="3" class="box54" value="<!--{$arrProduct.quantity|default:1|escape}-->" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{$arrProduct.arrErr.quantity|sfGetErrorColor}-->" />
                                    <!--{if $arrProduct.arrErr.quantity != ""}-->
                                        <br /><span class="attention"><!--{$arrProduct.arrErr.quantity}--></span>
                                    <!--{/if}-->
                                </dd>
                            </dl>
                            <div class="cartbtn">
                                <input
                                    type="image"
                                    id="cart<!--{$id}-->"
                                    src="<!--{$TPL_DIR}-->img/products/b_cartin.gif"
                                    alt="カゴに入れる"
                                    onclick="fnInCart(this.form); return false;"
                                    onmouseover="chgImg('<!--{$TPL_DIR}-->img/products/b_cartin_on.gif', this);"
                                    onmouseout="chgImg('<!--{$TPL_DIR}-->img/products/b_cartin.gif', this);"
                                />
                            </div>
                        </div>
                    </form>
                    <!--▲買い物かご-->
                <!--{/if}-->
                
            </div>
        </div>
        <!--▲商品-->
    <!--{/foreach}-->

    <!--▼ページナビ(下部)-->
    <form name="page_navi_bottom" id="page_navi_bottom" action="?">
        <!--{if $tpl_linemax > 0}-->
            <ul class="pagenumberarea">
                <li class="left"><span class="pagenumber"><!--{$tpl_linemax}--></span>件の商品がございます。</li>
                <li class="center"><!--{$tpl_strnavi}--></li>
                <li class="right">
                    <!--{if $orderby != 'price'}-->
                        <a href="javascript:fnChangeOrderby('price');">価格順</a>
                    <!--{else}-->
                        <strong>価格順</strong>
                    <!--{/if}-->&nbsp;
                    <!--{if $orderby != "date"}-->
                        <a href="javascript:fnChangeOrderby('date');">新着順</a>
                    <!--{else}-->
                        <strong>新着順</strong>
                    <!--{/if}-->
                     表示件数
                    <select name="disp_number" onchange="javascript:fnModeSubmit('','disp_number',this.value);">
                        <!--{foreach from=$arrPRODUCTLISTMAX item="dispnum" key="num"}-->
                            <!--{if $num == $disp_number}-->
                                <option value="<!--{$num}-->" selected="selected" ><!--{$dispnum}--></option>
                            <!--{else}-->
                                <option value="<!--{$num}-->" ><!--{$dispnum}--></option>
                            <!--{/if}-->
                        <!--{/foreach}-->
                    </select>
                </li>
            </ul>
        <!--{/if}-->
    </form>
    <!--▲ページナビ(下部)-->
</div>
<!--▲CONTENTS-->
