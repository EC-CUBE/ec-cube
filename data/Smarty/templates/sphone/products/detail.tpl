<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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

<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/products.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/jquery.facebox/facebox.js"></script>
<script>//<![CDATA[
    // 規格2に選択肢を割り当てる。
    function fnSetClassCategories(form, classcat_id2_selected) {
        var $form = $(form);
        var product_id = $form.find('input[name=product_id]').val();
        var $sele1 = $form.find('select[name=classcategory_id1]');
        var $sele2 = $form.find('select[name=classcategory_id2]');
        setClassCategories($form, product_id, $sele1, $sele2, classcat_id2_selected);
    }
    $(function(){
        $('#detailphotoblock ul li').flickSlide({target:'#detailphotoblock>ul', duration:5000, parentArea:'#detailphotoblock', height: 200});
        $('#whobought_area ul li').flickSlide({target:'#whobought_area>ul', duration:5000, parentArea:'#whobought_area', height: 80});

        //お勧め商品のリンクを張り直し(フリックスライドによるエレメント生成後)
        $('#whobought_area li').biggerlink();
        //商品画像の拡大
        $('a.expansion').facebox({
            loadingImage : '<!--{$TPL_URLPATH}-->js/jquery.facebox/loading.gif',
            closeImage   : '<!--{$TPL_URLPATH}-->js/jquery.facebox/closelabel.png'
        });
    });
    //サブエリアの表示/非表示
    var speed = 500;
    var stateSub = 0;
    function fnSubToggle(areaEl, imgEl) {
        areaEl.slideToggle(speed);
        if (stateSub == 0) {
            $(imgEl).attr("src", "<!--{$TPL_URLPATH}-->img/button/btn_plus.png");
            stateSub = 1;
        } else {
            $(imgEl).attr("src", "<!--{$TPL_URLPATH}-->img/button/btn_minus.png");
            stateSub = 0
        }
    }
    //この商品に対するお客様の声エリアの表示/非表示
    var stateReview = 0;
    function fnReviewToggle(areaEl, imgEl) {
        areaEl.slideToggle(speed);
        if (stateReview == 0) {
            $(imgEl).attr("src", "<!--{$TPL_URLPATH}-->img/button/btn_plus.png");
            stateReview = 1;
        } else {
            $(imgEl).attr("src", "<!--{$TPL_URLPATH}-->img/button/btn_minus.png");
            stateReview = 0
        }
    }
    //お勧めエリアの表示/非表示
    var statewhobought = 0;
    function fnWhoboughtToggle(areaEl, imgEl) {
        areaEl.slideToggle(speed);
        if (statewhobought == 0) {
            $(imgEl).attr("src", "<!--{$TPL_URLPATH}-->img/button/btn_plus.png");
            statewhobought = 1;
        } else {
            $(imgEl).attr("src", "<!--{$TPL_URLPATH}-->img/button/btn_minus.png");
            statewhobought = 0
        }
    }
//]]></script>

<section id="product_detail">

    <!--★タイトル★-->
    <h2 class="title"><!--{$tpl_subtitle|h}--></h2>
    <!--★画像★-->

    <div id="detailphotoblock" class="mainImageInit">
        <ul>
            <!--{assign var=key value="main_image"}-->
            <li id="mainImage0">

            <!--{* 画像の縦横倍率を算出 *}-->
            <!--{assign var=detail_image_size value=200}-->
            <!--{assign var=main_image_factor value=`$arrFile[$key].width/$detail_image_size`}-->
            <!--{if $arrProduct.main_large_image|strlen >= 1}-->
                <a rel="external" class="expansion" href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct.main_large_image|h}-->" target="_blank">
                    <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct.main_image|h}-->" alt="<!--{$arrProduct.name|h}-->" width="<!--{$arrFile.main_image.width/$main_image_factor}-->" height="<!--{$arrFile.main_image.height/$main_image_factor}-->" /></a>
            <!--{else}-->
                <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct.main_image|h}-->" alt="<!--{$arrProduct.name|h}-->" width="<!--{$arrFile.main_image.width/$main_image_factor}-->" height="<!--{$arrFile.main_image.height/$main_image_factor}-->" />
            <!--{/if}-->
            </li>
            <!--★サブ画像★-->
            <!--{section name=cnt loop=$smarty.const.PRODUCTSUB_MAX}-->
            <!--{assign var=key value="sub_image`$smarty.section.cnt.index+1`"}-->
            <!--{assign var=sub_image_factor value=`$arrFile[$key].width/$detail_image_size`}-->
            <!--{assign var=lkey value="sub_large_image`$smarty.section.cnt.index+1`"}-->
            <!--{if $arrFile[$key].filepath != ""}-->
                <li id="mainImage<!--{$smarty.section.cnt.index+1}-->">
                <!--{if $arrProduct[$lkey] != ""}-->
                    <a rel="external" class="expansion" href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct[$lkey]|h}-->" target="_blank">
                    <img src="<!--{$arrFile[$key].filepath|h}-->" alt="<!--{$arrProduct.name|h}-->" width="<!--{$arrFile[$key].width/$sub_image_factor}-->" height="<!--{$arrFile[$key].height/$sub_image_factor}-->" /></a>
                <!--{else}-->
                    <img src="<!--{$arrFile[$key].filepath|h}-->" alt="<!--{$arrProduct.name|h}-->" width="<!--{$arrFile[$key].width/$sub_image_factor}-->" height="<!--{$arrFile[$key].height/$sub_image_factor}-->" />
                <!--{/if}-->
                </li>
            <!--{/if}-->
            <!--{/section}-->
        </ul>
    </div>

    <section id="detailarea">

        <!--★詳細メインコメント★-->
        <p class="main_comment"><!--{$arrProduct.main_comment|nl2br_html}--></p>

        <form name="form1" id="form1" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->products/detail.php">
            <div id="detailrightblock">
                <!--▼商品ステータス-->
                <!--{assign var=ps value=$productStatus[$tpl_product_id]}-->
                <!--{if count($ps) > 0}-->
                    <ul class="status_icon">
                    <!--{foreach from=$ps item=status}-->
                        <li><!--{$arrSTATUS[$status]}--></li>
                    <!--{/foreach}-->
                    </ul>
                <!--{/if}-->
                <!--▲商品ステータス-->

                <div class="product_detail">

                    <!--★商品名★-->
                    <h3 class="product_name"><!--{$arrProduct.name|h}--></h3>

                    <p class="product_code">
                        <span class="mini">商品コード：</span>

                        <span id="product_code_default">
                            <!--{if $arrProduct.product_code_min == $arrProduct.product_code_max}-->
                                <!--{$arrProduct.product_code_min|h}-->
                            <!--{else}-->
                                <!--{$arrProduct.product_code_min|h}-->～<!--{$arrProduct.product_code_max|h}-->
                            <!--{/if}-->
                        </span><span id="product_code_dynamic"></span>
                    </p>

                    <!--★関連カテゴリ★-->
                    <p class="relative_cat"><span class="mini">関連カテゴリ：</span>
                        <!--{section name=r loop=$arrRelativeCat}-->
                            <!--{section name=s loop=$arrRelativeCat[r]}-->
                                <a rel="external" href="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php?category_id=<!--{$arrRelativeCat[r][s].category_id}-->"><!--{$arrRelativeCat[r][s].category_name}--></a>
                                <!--{if !$smarty.section.s.last}--><!--{$smarty.const.SEPA_CATNAVI}--><!--{/if}-->
                            <!--{/section}--><br />
                        <!--{/section}-->
                    </p>

                    <!--★通常価格★-->
                    <!--{if $arrProduct.price01_max_inctax > 0}-->
                        <p class="normal_price">
                            <span class="mini"><!--{$smarty.const.NORMAL_PRICE_TITLE}-->(税込)：</span>
                            <span id="price01_default">
                                <!--{if $arrProduct.price01_min_inctax == $arrProduct.price01_max_inctax}-->
                                    <!--{$arrProduct.price01_min_inctax|number_format}-->
                                <!--{else}-->
                                    <!--{$arrProduct.price01_min_inctax|number_format}-->～<!--{$arrProduct.price01_max_inctax|number_format}-->
                                <!--{/if}--></span>
                            <span id="price01_dynamic"></span>円
                        </p>
                    <!--{/if}-->

                    <!--★販売価格★-->
                    <p class="sale_price">
                        <span class="mini"><!--{$smarty.const.SALE_PRICE_TITLE}-->(税込)：</span>
                        <span class="price"><span id="price02_default">
                            <!--{if $arrProduct.price02_min_inctax == $arrProduct.price02_max_inctax}-->
                                <!--{$arrProduct.price02_min_inctax|number_format}-->
                            <!--{else}-->
                                <!--{$arrProduct.price02_min_inctax|number_format}-->～<!--{$arrProduct.price02_max_inctax|number_format}-->
                            <!--{/if}-->
                        </span><span id="price02_dynamic"></span>円</span>
                    </p>

                    <!--★ポイント★-->
                    <!--{if $smarty.const.USE_POINT !== false}-->
                        <p class="sale_price"><span class="mini">ポイント：</span><span id="point_default">
                            <!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
                                <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
                            <!--{else}-->
                                <!--{if $arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id == $arrProduct.price02_max|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id}-->
                                    <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
                                <!--{else}-->
                                    <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->～<!--{$arrProduct.price02_max|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
                                <!--{/if}-->
                            <!--{/if}-->
                            </span><span id="point_dynamic"></span>Pt
                        </p>
                    <!--{/if}-->

                    <!--▼メーカーURL-->
                    <!--{if $arrProduct.comment1|strlen >= 1}-->
                        <p class="sale_price">
                            <span class="mini">メーカーURL：</span><span>
                                <a rel="external" href="<!--{$arrProduct.comment1|h}-->" target="_blank">
                                    <!--{$arrProduct.comment1|h}--></a>
                            </span>
                        </p>
                    <!--{/if}-->
                    <!--▲メーカーURL-->
                </div><!-- /.product_detail -->

                <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
                <input type="hidden" name="mode" value="cart" />
                <input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->" />
                <input type="hidden" name="product_class_id" value="<!--{$tpl_product_class_id}-->" id="product_class_id" />
                <input type="hidden" name="favorite_product_id" value="" />

                <!--▼買い物かご-->
                <!--{if $tpl_stock_find}-->

                    <!--{if $tpl_classcat_find1}-->
                        <div class="cart_area">
                            <dl>
                                <!--▼規格1-->
                                <dt><!--{$tpl_class_name1|h}--></dt>
                                <dd>
                                    <select name="classcategory_id1"
                                        style="<!--{$arrErr.classcategory_id1|sfGetErrorColor}-->"
                                        class="data-role-none">
                                        <!--{html_options options=$arrClassCat1 selected=$arrForm.classcategory_id1.value}-->
                                    </select>
                                    <!--{if $arrErr.classcategory_id1 != ""}-->
                                        <br /><span class="attention">※ <!--{$tpl_class_name1}-->を入力して下さい。</span>
                                    <!--{/if}-->
                                </dd>
                                <!--▲規格1-->

                                <!--{if $tpl_classcat_find2}-->
                                    <!--▼規格2-->
                                    <dt><!--{$tpl_class_name2|h}--></dt>
                                    <dd>
                                        <select name="classcategory_id2"
                                            style="<!--{$arrErr.classcategory_id2|sfGetErrorColor}-->"
                                            class="data-role-none">
                                        </select>
                                        <!--{if $arrErr.classcategory_id2 != ""}-->
                                            <br /><span class="attention">※ <!--{$tpl_class_name2}-->を入力して下さい。</span>
                                        <!--{/if}-->
                                    </dd>
                                    <!--▲規格2-->
                                <!--{/if}-->
                            </dl>
                        </div>
                    <!--{/if}-->

                    <div class="cartin_btn">
                        <dl>
                            <dt>数量</dt>
                            <dd>
                                <input type="number" name="quantity" class="quantitybox" value="<!--{$arrForm.quantity.value|default:1|h}-->" max="<!--{$smarty.const.INT_LEN}-->" style="<!--{$arrErr.quantity|sfGetErrorColor}-->" />
                                <!--{if $arrErr.quantity != ""}-->
                                    <br /><span class="attention"><!--{$arrErr.quantity}--></span>
                                <!--{/if}-->
                            </dd>
                        </dl>

                        <!--★カートに入れる★-->
                        <div id="cartbtn_default">
                            <a rel="external" href="javascript:void(document.form1.submit());" class="btn cartbtn_default">カートに入れる</a>
                        </div>
                        <div class="attention" id="cartbtn_dynamic"></div>
                    </div>
                <!--{else}-->
                    <div class="cartin_btn">
                        <div class="attention">申し訳ございませんが、只今品切れ中です。</div>
                    </div>
                <!--{/if}-->
                <!--▲買い物かご-->

                <!--{if $tpl_login}-->
                    <!--{if !$is_favorite}-->
                        <div class="btn_favorite">
                            <p><a rel="external" href="javascript:void(0);" onclick="fnAddFavoriteSphone(<!--{$arrProduct.product_id|h}-->); return false;" class="btn_sub">お気に入りに追加</a></p>
                        </div>
                    <!--{else}-->
                        <div class="btn_favorite">
                            <p>お気に入り登録済み</p>
                        </div>
                    <!--{/if}-->
                <!--{/if}-->
            </div>
        </form>
    </section>
    <!--詳細ここまで-->

    <!--▼サブエリアここから-->
    <!--{if $arrProduct.sub_title1 != ""}-->
        <div class="title_box_sub clearfix">
            <h2>商品情報</h2>
            <!--{assign var=ckey value="sub_comment`$smarty.section.cnt.index+1`"}-->
            <span class="b_expand"><img src="<!--{$TPL_URLPATH}-->img/button/btn_minus.png" onclick="fnSubToggle($('#sub_area'), this);" alt=""></span>
        </div>
        <div id="sub_area">
            <!--{section name=cnt loop=$smarty.const.PRODUCTSUB_MAX}-->
                <!--{assign var=key value="sub_title`$smarty.section.cnt.index+1`"}-->
                <!--{if $arrProduct[$key] != ""}-->
                    <!--▼サブ情報-->
                    <div class="subarea clearfix">
                        <!--★サブタイトル★-->
                        <h3><!--{$arrProduct[$key]|h}--></h3>

                        <!--★サブ画像★-->
                        <!--{assign var=sub_image_size value=80}-->
                        <!--{assign var=key value="sub_image`$smarty.section.cnt.index+1`"}-->
                        <!--{assign var=lkey value="sub_large_image`$smarty.section.cnt.index+1`"}-->
                        <!--{assign var=ckey value="sub_comment`$smarty.section.cnt.index+1`"}-->
                        <!--{assign var=sub_image_factor value=`$arrFile[$key].width/$sub_image_size`}-->
                        <!--{if $arrProduct[$key]|strlen >= 1}-->
                            <p class="subphotoimg">
                                <!--{if $arrProduct[$lkey]|strlen >= 1}-->
                                    <a rel="external" class="expansion" href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct[$lkey]|h}-->" target="_blank">
                                        <img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrProduct.name|h}-->" width="<!--{$arrFile[$key].width/$sub_image_factor}-->" height="<!--{$arrFile[$key].height/$sub_image_factor}-->" />
                                    </a>
                                <!--{else}-->
                                    <img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrProduct.name|h}-->" width="<!--{$arrFile[$key].width/$sub_image_factor}-->" height="<!--{$arrFile[$key].height/$sub_image_factor}-->" />
                                <!--{/if}-->
                            </p>
                        <!--{/if}-->
                        <!--★サブテキスト★-->
                        <p class="subtext"><!--★サブテキスト★--><!--{$arrProduct[$ckey]|nl2br_html}--></p>
                    </div>
                <!--{/if}-->
            <!--{/section}-->
        </div>
    <!--{/if}-->
    <!--サブエリアここまで-->

    <!--この商品に対するお客様の声-->
    <div class="title_box_sub clearfix">
        <h2>この商品に対するお客様の声</h2>
            <span class="b_expand"><img src="<!--{$TPL_URLPATH}-->img/button/btn_minus.png" onclick="fnReviewToggle($('#review_bloc_area'), this);" alt=""></span>
        </div>

        <div id="review_bloc_area">
            <div class="review_bloc clearfix">
            <p>この商品に対するご感想をぜひお寄せください。</p>
            <div class="review_btn">
                <!--{if count($arrReview) < $smarty.const.REVIEW_REGIST_MAX}-->
                    <!--★新規コメントを書き込む★-->
                    <a href="./review.php?product_id=<!--{$arrProduct.product_id}-->" target="_blank" class="btn_sub" />新規コメントを書き込む</a>
                <!--{/if}-->
            </div>
            </div>

            <!--{if count($arrReview) > 0}-->
            <ul>
                <!--{section name=cnt loop=$arrReview}-->
                    <li>
                        <p class="voicetitle"><!--{$arrReview[cnt].title|h}--></p>
                        <p class="voicedate"><!--{$arrReview[cnt].create_date|sfDispDBDate:false}-->　投稿者：<!--{if $arrReview[cnt].reviewer_url}--><a href="<!--{$arrReview[cnt].reviewer_url}-->" target="_blank"><!--{$arrReview[cnt].reviewer_name|h}--></a><!--{else}--><!--{$arrReview[cnt].reviewer_name|h}--><!--{/if}--><br />おすすめレベル：<span class="recommend_level"><!--{assign var=level value=$arrReview[cnt].recommend_level}--><!--{$arrRECOMMEND[$level]|h}--></span></p>
                        <p class="voicecomment"><!--{$arrReview[cnt].comment|h|nl2br}--></p>
                    </li>
                <!--{/section}-->
            </ul>
            <!--{/if}-->
        </div>
    </div>
    <!--お客様の声ここまで-->


    <!--▼その他おすすめ商品-->
    <!--{if $arrRecommend}-->
        <div class="title_box_sub clearfix">
            <h2>その他のオススメ商品</h2>
            <span class="b_expand"><img src="<!--{$TPL_URLPATH}-->img/button/btn_minus.png" onclick="fnWhoboughtToggle($('#whobought_area'), this);" alt=""></span>
        </div>

        <div id="whobought_area" class="mainImageInit">
            <ul>
                <!--{section name=cnt loop=$arrRecommend}-->
                    <!--{if $arrRecommend[cnt].product_id}-->
                        <li id="mainImage1<!--{$smarty.section.cnt.index}-->">
                            <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrRecommend[cnt].main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$arrRecommend[cnt].name|h}-->" />
                            <!--{assign var=price02_min value=`$arrRecommend[cnt].price02_min_inctax`}-->
                            <!--{assign var=price02_max value=`$arrRecommend[cnt].price02_max_inctax`}-->
                            <h3><a rel="external" href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrRecommend[cnt].product_id|u}-->"><!--{$arrRecommend[cnt].name|h}--></a></h3>
                            <p class="sale_price"><span class="price">
                                <!--{if $price02_min == $price02_max}-->
                                    <!--{$price02_min|number_format}-->
                                <!--{else}-->
                                    <!--{$price02_min|number_format}-->～<!--{$price02_max|number_format}-->
                                <!--{/if}-->
                                円</span>
                            </p>
                        </li>
                    <!--{/if}-->
                <!--{/section}-->
            </ul>
        </div>
    <!--{/if}-->
    <!--▲その他おすすめ商品-->

    <div class="btn_area">
        <p><a href="javascript:void(0);" class="btn_more" data-rel="back">商品一覧に戻る</a></p>
    </div>
</section>

<!--▼検索バー -->
<section id="search_area">
    <form method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="search" />
        <input type="search" name="name" id="search" value="" placeholder="キーワードを入力" class="searchbox" >
    </form>
</section>
<!--▲検索バー -->
