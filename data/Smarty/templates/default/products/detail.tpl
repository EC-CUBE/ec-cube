<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/products.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/facebox.js"></script>
<link rel="stylesheet" type="text/css" href="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/facebox.css" media="screen" />
<script type="text/javascript">//<![CDATA[
// 規格2に選択肢を割り当てる。
function fnSetClassCategories(form, classcat_id2_selected) {
    var $form = $(form);
    var product_id = $form.find('input[name=product_id]').val();
    var $sele1 = $form.find('select[name=classcategory_id1]');
    var $sele2 = $form.find('select[name=classcategory_id2]');
    setClassCategories($form, product_id, $sele1, $sele2, classcat_id2_selected);
}
$(document).ready(function() {
    $('a.expansion').facebox({
        loadingImage : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/loading.gif',
        closeImage   : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/closelabel.png'
    });
});
//]]>
</script>

<!--▼CONTENTS-->
<div id="undercolumn">
    <form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <div id="detailarea" class="clearfix">
        <div id="detailphotobloc">
            <div class="photo">
                <!--{assign var=key value="main_image"}-->
                <!--★画像★-->
                <!--{if $arrProduct.main_large_image|strlen >= 1}-->
                    <a
                        href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct.main_large_image|h}-->"
                        class="expansion"
                        target="_blank"
                    >
                <!--{/if}-->
                    <img src="<!--{$arrFile[$key].filepath|h}-->" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].height}-->" alt="<!--{$arrProduct.name|h}-->" class="picture" />
                <!--{if $arrProduct.main_large_image|strlen >= 1}-->
                    </a>
                <!--{/if}-->
            </div>
            <!--{if $arrProduct.main_large_image|strlen >= 1}-->
                <span class="mini">
                        <!--★拡大する★-->
                        <a
                            href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct.main_large_image|h}-->"
                            class="expansion"
                            target="_blank"
                        >
                            画像を拡大する</a>
                </span>
            <!--{/if}-->
        </div>

        <div id="detailrightbloc">
            <!--▼商品ステータス-->
            <!--{assign var=ps value=$productStatus[$smarty.get.product_id]}-->
            <!--{if count($ps) > 0}-->
                <ul class="status_icon clearfix">
                    <!--{foreach from=$ps item=status}-->
                    <li>
                        <img src="<!--{$TPL_URLPATH}--><!--{$arrSTATUS_IMAGE[$status]}-->" width="60" height="17" alt="<!--{$arrSTATUS[$status]}-->" id="icon<!--{$status}-->" />
                    </li>
                    <!--{/foreach}-->
                </ul>
            <!--{/if}-->
            <!--▲商品ステータス-->

            <!--★商品コード★-->
            <div>商品コード：
                <span id="product_code_default">
                    <!--{if $arrProduct.product_code_min == $arrProduct.product_code_max}-->
                        <!--{$arrProduct.product_code_min|h}-->
                    <!--{else}-->
                        <!--{$arrProduct.product_code_min|h}-->～<!--{$arrProduct.product_code_max|h}-->
                    <!--{/if}-->
                </span><span id="product_code_dynamic"></span>
            </div>

            <!--★商品名★-->
            <h2><!--{$arrProduct.name|h}--></h2>

            <!--★通常価格★-->
            <!--{if $arrProduct.price01_max > 0}-->
                <div class="normal_price">
                    <!--{$smarty.const.NORMAL_PRICE_TITLE}-->(税込)：
                    <span class="price">
                        <span id="price01_default">
                            <!--{if $arrProduct.price01_min == $arrProduct.price01_max}-->
                                <!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                            <!--{else}-->
                                <!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$arrProduct.price01_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                            <!--{/if}-->
                        </span><span id="price01_dynamic"></span>
                        円
                    </span>
                </div>
            <!--{/if}-->

            <!--★販売価格★-->
            <div class="sale_price"><!--{$smarty.const.SALE_PRICE_TITLE}-->(税込)：
                <span class="price">
                    <span id="price02_default">
                        <!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
                            <!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                        <!--{else}-->
                            <!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$arrProduct.price02_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                        <!--{/if}-->
                    </span><span id="price02_dynamic"></span>
                    円
                </span>
            </div>

            <!--★ポイント★-->
            <!--{if $smarty.const.USE_POINT !== false}-->
                <div class="point">ポイント：
                    <span id="point_default">
                        <!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
                            <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
                        <!--{else}-->
                            <!--{if $arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id == $arrProduct.price02_max|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id}-->
                                <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
                            <!--{else}-->
                                <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->～<!--{$arrProduct.price02_max|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
                            <!--{/if}-->
                        <!--{/if}-->
                        </span><span id="point_dynamic"></span>
                        Pt
                </div>
            <!--{/if}-->

            <!--▼メーカーURL-->
            <!--{if $arrProduct.comment1|strlen >= 1}-->
                <div><span class="comment1">メーカーURL：
                    <a href="<!--{$arrProduct.comment1|h}-->"><!--{$arrProduct.comment1|h}--></a>
                </div>
            <!--{/if}-->
            <!--▼メーカーURL-->

            <!--★関連カテゴリ★-->
            <div class="relative_cat">関連カテゴリ：
                <!--{section name=r loop=$arrRelativeCat}-->
                <p>
                    <!--{section name=s loop=$arrRelativeCat[r]}-->
                    <a href="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php?category_id=<!--{$arrRelativeCat[r][s].category_id}-->"><!--{$arrRelativeCat[r][s].category_name}--></a>
                    <!--{if !$smarty.section.s.last}--><!--{$smarty.const.SEPA_CATNAVI}--><!--{/if}-->
                    <!--{/section}-->
                </p>
                <!--{/section}-->
            </div>

            <!--★詳細メインコメント★-->
            <div class="main_comment"><!--{$arrProduct.main_comment|nl2br_html}--></div>

            <!--▼買い物かご-->

            <div class="cart_area clearfix">
            <input type="hidden" name="mode" value="cart" />
            <input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->" />
            <input type="hidden" name="product_class_id" value="<!--{$tpl_product_class_id}-->" id="product_class_id" />
            <input type="hidden" name="favorite_product_id" value="" />

            <!--{if $tpl_stock_find}-->
                <!--{if $tpl_classcat_find1}-->
                    <div class="classlist">
                        <!--▼規格1-->
                        <ul class="clearfix">
                            <li><!--{$tpl_class_name1|h}-->：</li>
                            <li>
                                <select name="classcategory_id1" style="<!--{$arrErr.classcategory_id1|sfGetErrorColor}-->">
                                <!--{html_options options=$arrClassCat1 selected=$arrForm.classcategory_id1.value}-->
                                </select>
                                <!--{if $arrErr.classcategory_id1 != ""}-->
                                <br /><span class="attention">※ <!--{$tpl_class_name1}-->を入力して下さい。</span>
                                <!--{/if}-->
                            </li>
                        </ul>
                        <!--▲規格1-->
                        <!--{if $tpl_classcat_find2}-->
                        <!--▼規格2-->
                        <ul class="clearfix">
                            <li><!--{$tpl_class_name2|h}-->：</li>
                            <li>
                                <select name="classcategory_id2" style="<!--{$arrErr.classcategory_id2|sfGetErrorColor}-->">
                                </select>
                                <!--{if $arrErr.classcategory_id2 != ""}-->
                                <br /><span class="attention">※ <!--{$tpl_class_name2}-->を入力して下さい。</span>
                                <!--{/if}-->
                            </li>
                        </ul>
                        <!--▲規格2-->
                        <!--{/if}-->
                    </div>
                <!--{/if}-->

                <!--★数量★-->
                <div class="quantity">
                    <ul class="clearfix">
                        <li>数量：</li>
                        <li><input type="text" class="box60" name="quantity" value="<!--{$arrForm.quantity.value|default:1|h}-->" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{$arrErr.quantity|sfGetErrorColor}-->" />
                            <!--{if $arrErr.quantity != ""}-->
                                <br /><span class="attention"><!--{$arrErr.quantity}--></span>
                            <!--{/if}-->
                        </li>
                    </ul>
                </div>

                <div class="cartin">
                    <div class="cartin_btn">
                        <div id="cartbtn_default">
                            <!--★カゴに入れる★-->
                            <a href="javascript:void(document.form1.submit())" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_cartin_on.jpg','cart');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_cartin.jpg','cart');">
                                <img src="<!--{$TPL_URLPATH}-->img/button/btn_cartin.jpg" alt="カゴに入れる" name="cart" id="cart" /></a>
                        </div>
                    </div>
                </div>
                <div class="attention" id="cartbtn_dynamic"></div>
            <!--{else}-->
                <div class="attention">申し訳ございませんが、只今品切れ中です。</div>
            <!--{/if}-->

            <!--★お気に入り登録★-->
            <!--{if $smarty.const.OPTION_FAVOFITE_PRODUCT == 1 && $tpl_login === true}-->
                <div class="favorite_btn">
                    <!--{assign var=add_favorite value="add_favorite`$product_id`"}-->
                    <!--{if $arrErr[$add_favorite]}-->
                        <div class="attention"><!--{$arrErr[$add_favorite]}--></div>
                    <!--{/if}-->
                    <!--{if !$is_favorite}-->
                        <a href="javascript:fnChangeAction('?product_id=<!--{$arrProduct.product_id|h}-->'); fnModeSubmit('add_favorite','favorite_product_id','<!--{$arrProduct.product_id|h}-->');" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_add_favorite_on.jpg','add_favorite_product');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_add_favorite.jpg','add_favorite_product');"><img src="<!--{$TPL_URLPATH}-->img/button/btn_add_favorite.jpg" alt="お気に入りに追加" name="add_favorite_product" id="add_favorite_product" /></a>
                    <!--{else}-->
                        <img src="<!--{$TPL_URLPATH}-->img/button/btn_add_favorite_on.jpg" alt="お気に入り登録済" name="add_favorite_product" id="add_favorite_product" />
                        <script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.tipsy.js"></script>
                        <script type="text/javascript">
                            var favoriteButton = $("#add_favorite_product");
                            favoriteButton.tipsy({gravity: $.fn.tipsy.autoNS, fallback: "お気に入りに登録済み", fade: true });
                            
                            <!--{if $just_added_favorite == true}-->
                            favoriteButton.load(function(){$(this).tipsy("show")});
                            $(function(){
                                var tid = setTimeout('favoriteButton.tipsy("hide")',5000);
                            });
                            <!--{/if}-->
                        </script>
                    <!--{/if}-->
                </div>
            <!--{/if}-->
        </div>
    </div>
    <!--▲買い物かご-->
</div>
</form>

    <!--詳細ここまで-->

    <!--▼サブコメント-->
    <!--{section name=cnt loop=$smarty.const.PRODUCTSUB_MAX}-->
        <!--{assign var=key value="sub_title`$smarty.section.cnt.index+1`"}-->
        <!--{if $arrProduct[$key] != ""}-->
            <div class="sub_area clearfix">
                <h3><!--★サブタイトル★--><!--{$arrProduct[$key]|h}--></h3>
                <!--{assign var=ckey value="sub_comment`$smarty.section.cnt.index+1`"}-->
                <!--▼サブ画像-->
                <!--{assign var=key value="sub_image`$smarty.section.cnt.index+1`"}-->
                <!--{assign var=lkey value="sub_large_image`$smarty.section.cnt.index+1`"}-->
                <!--{if $arrProduct[$key]|strlen >= 1}-->
                    <div class="subtext"><!--★サブテキスト★--><!--{$arrProduct[$ckey]|nl2br_html}--></div>
                    <div class="subphotoimg">
                        <!--{if $arrProduct[$lkey]|strlen >= 1}-->
                            <a href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct[$lkey]|h}-->" class="expansion" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_expansion_on.gif', 'expansion_<!--{$lkey|h}-->');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_expansion.gif', 'expansion_<!--{$lkey|h}-->');" target="_blank" >
                        <!--{/if}-->
                        <img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrProduct.name|h}-->" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].height}-->" />
                        <!--{if $arrProduct[$lkey]|strlen >= 1}--></a>
                            <span class="mini">
                                <a href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct[$lkey]|h}-->" class="expansion" target="_blank">
                                    画像を拡大する</a>
                            </span>
                        <!--{/if}-->
                    </div>
                <!--{else}-->
                    <p class="subtext"><!--★サブテキスト★--><!--{$arrProduct[$ckey]|nl2br_html}--></p>
                <!--{/if}-->
                <!--▲サブ画像-->
            </div>
        <!--{/if}-->
    <!--{/section}-->
    <!--▲サブコメント-->

    <!--この商品に対するお客様の声-->
    <div id="customervoice_area">
        <h2><img src="<!--{$TPL_URLPATH}-->img/title/tit_product_voice.jpg" alt="この商品に対するお客様の声" /></h2>

        <div class="review_bloc clearfix">
            <p>この商品に対するご感想をぜひお寄せください。</p>
            <div class="review_btn">
                <!--{if count($arrReview) < $smarty.const.REVIEW_REGIST_MAX}-->
                    <!--★新規コメントを書き込む★-->
                    <a href="./review.php"
                        onclick="win02('./review.php?product_id=<!--{$arrProduct.product_id}-->','review','600','640'); return false;"
                        onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_comment_on.jpg','review');"
                        onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_comment.jpg','review');" target="_blank">
                        <img src="<!--{$TPL_URLPATH}-->img/button/btn_comment.jpg" alt="新規コメントを書き込む" name="review" id="review" /></a>
                <!--{/if}-->
            </div>
        </div>

        <!--{if count($arrReview) > 0}-->
            <ul>
                <!--{section name=cnt loop=$arrReview}-->
                    <li>
                        <p class="voicetitle"><!--{$arrReview[cnt].title|h}--></p>
                        <p class="voicedate"><!--{$arrReview[cnt].create_date|sfDispDBDate:false}-->　投稿者：<!--{if $arrReview[cnt].reviewer_url}--><a href="<!--{$arrReview[cnt].reviewer_url}-->" target="_blank"><!--{$arrReview[cnt].reviewer_name|h}--></a><!--{else}--><!--{$arrReview[cnt].reviewer_name|h}--><!--{/if}-->　おすすめレベル：<span class="recommend_level"><!--{assign var=level value=$arrReview[cnt].recommend_level}--><!--{$arrRECOMMEND[$level]|h}--></span></p>
                        <p class="voicecomment"><!--{$arrReview[cnt].comment|h|nl2br}--></p>
                    </li>
                <!--{/section}-->
            </ul>
        <!--{/if}-->
    </div>
    <!--お客様の声ここまで-->

    <!--▼関連商品-->
    <!--{if $arrRecommend}-->
        <div id="whobought_area">
            <h2><img src="<!--{$TPL_URLPATH}-->img/title/tit_product_recommend.jpg" alt="その他のオススメ商品" /></h2>

            <!--{section name=cnt loop=$arrRecommend step=2}-->
            <div class="whobought_bloc clearfix">
                <!--{if $arrRecommend[cnt]}-->
                    <!-- 左列 -->
                    <div class="whobought_left">
                        <div class="productImage">
                            <a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrRecommend[cnt].product_id|u}-->">
                                <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrRecommend[cnt].main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$arrRecommend[cnt].name|h}-->" /></a>
                        </div>
                        <!--{assign var=price02_min value=`$arrRecommend[cnt].price02_min`}-->
                        <!--{assign var=price02_max value=`$arrRecommend[cnt].price02_max`}-->
                        <div class="productContents">
                            <h3><a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrRecommend[cnt].product_id|u}-->"><!--{$arrRecommend[cnt].name|h}--></a></h3>
                            <p class="sale_price"><!--{$smarty.const.SALE_PRICE_TITLE}-->(税込)：<span class="price">
                                <!--{if $price02_min == $price02_max}-->
                                    <!--{$price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                                <!--{else}-->
                                    <!--{$price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$price02_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                                <!--{/if}-->円</span></p>
                            <p class="mini"><!--{$arrRecommend[cnt].comment|h|nl2br}--></p>
                        </div>
                    </div>
                    <!-- 左列 -->
                <!--{/if}-->

                <!--{assign var=cnt2 value=`$smarty.section.cnt.iteration*$smarty.section.cnt.step-1`}-->
                <!--{if $arrRecommend[$cnt2]}-->
                    <!-- 右列 -->
                    <div class="whobought_right clearfix">
                        <div class="productImage">
                            <a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrRecommend[$cnt2].product_id|u}-->">
                                <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrRecommend[$cnt2].main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$arrRecommend[$cnt2].name|h}-->" /></a>
                            <!--{assign var=price02_min value=`$arrRecommend[$cnt2].price02_min`}-->
                            <!--{assign var=price02_max value=`$arrRecommend[$cnt2].price02_max`}-->
                        </div>
                        <div class="productContents">
                            <h3><a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrRecommend[$cnt2].product_id|u}-->"><!--{$arrRecommend[$cnt2].name|h}--></a></h3>
                            <p class="sale_price"><!--{$smarty.const.SALE_PRICE_TITLE}-->(税込)：<span class="price">
                                <!--{if $price02_min == $price02_max}-->
                                    <!--{$price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                                <!--{else}-->
                                    <!--{$price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$price02_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                                <!--{/if}-->円</span></p>
                            <p class="mini"><!--{$arrRecommend[$cnt2].comment|h|nl2br}--></p>
                        </div>
                    </div>
                    <!-- 右列 -->
                <!--{/if}-->
            </div>
            <!--{if $smarty.section.cnt.last}-->
                </div>
            <!--{/if}-->
        <!--{/section}-->
    <!--{/if}-->
    <!--▲関連商品-->

</div>
<!--▲CONTENTS-->
