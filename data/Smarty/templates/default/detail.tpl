<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<script type="text/javascript">//<![CDATA[
// セレクトボックスに項目を割り当てる。
function lnSetSelect(form, name1, name2, val) {

        sele11 = document[form][name1];
        sele12 = document[form][name2];

        if(sele11 && sele12) {
                index = sele11.selectedIndex;

                // セレクトボックスのクリア
                count = sele12.options.length;
                for(i = count; i >= 0; i--) {
                        sele12.options[i] = null;
                }

                // セレクトボックスに値を割り当てる
                len = lists[index].length;
                for(i = 0; i < len; i++) {
                        sele12.options[i] = new Option(lists[index][i], vals[index][i]);
                        if(val != "" && vals[index][i] == val) {
                                sele12.options[i].selected = true;
                        }
                }
        }
}
//]]>
</script>

<!--▼CONTENTS-->
<div id="undercolumn">
  <div id="detailtitle"><h2><!--★タイトル★--><!--{$tpl_subtitle|escape}--></h2></div>
  <p><!--★詳細メインコメント★--><!--{$arrProduct.main_comment|nl2br}--></p>

  <div id="detailarea">
    <div id="detailphotoblock">

    <!--{assign var=key value="main_image"}-->
    <!--{if $arrProduct.main_large_image != ""}-->
    <!--★画像★-->
      <a href="javascript:void(win01('./detail_image.php?product_id=<!--{$arrProduct.product_id}-->&amp;image=main_large_image<!--{if $smarty.get.admin == 'on'}-->&amp;admin=on<!--{/if}-->','detail_image','<!--{$arrFile.main_large_image.width+60}-->', '<!--{$arrFile.main_large_image.height+80}-->'))">
        <img src="<!--{$arrFile[$key].filepath}-->" width="<!--{$smarty.const.NORMAL_IMAGE_WIDTH}-->" height="<!--{$smarty.const.NORMAL_IMAGE_HEIGHT}-->" alt="<!--{$arrProduct.name|escape}-->" class="picture" />
      </a>
      <p>
      <!--★拡大する★-->
        <a href="javascript:void(opWin02('./detail_image.php','detail_image','560','580'))" onmouseover="chgImg('<!--{$TPL_DIR}-->img/products/b_expansion_on.gif','expansion01');" onMouseOut="chgImg('<!--{$TPL_DIR}-->img/products/b_expansion.gif','expansion01');" target="_blank">
          <img src="<!--{$TPL_DIR}-->img/products/b_expansion.gif" width="85" height="13" alt="画像を拡大する" name="expansion01" id="expansion01" />
       </a>
      </p>
      <!--{else}-->
      <img src="<!--{$arrFile[$key].filepath}-->" width="<!--{$smarty.const.NORMAL_IMAGE_WIDTH}-->" height="<!--{$smarty.const.NORMAL_IMAGE_HEIGHT}-->" alt="<!--{$arrProduct.name|escape}-->" class="picture" />
      <!--{/if}-->
    </div>

    <div id="detailrightblock">
      <!--アイコン-->
      <!--{if count($arrProduct.product_flag) > 0}-->
      <ul>
        <!--{section name=flg loop=$arrProduct.product_flag|count_characters}-->
        <!--{if $arrProduct.product_flag[flg] == "1"}-->
        <li>
          <!--{assign var=key value="`$smarty.section.flg.iteration`"}-->
          <img src="<!--{$TPL_DIR}--><!--{$arrSTATUS_IMAGE[$key]}-->" width="65" height="17" alt="<!--{$arrSTATUS[$key]}-->" id="icon<!--{$key}-->" />
        </li>
        <!--{/if}-->
        <!--{/section}-->
      </ul>
      <!--{/if}-->

      <!--★商品コード★-->
      <!--{assign var=codecnt value=$arrProductCode|@count}-->
      <!--{assign var=codemax value=`$codecnt-1`}-->
      <div>商品コード：
        <!--{if $codecnt > 1}-->
          <!--{$arrProductCode.0}-->〜<!--{$arrProductCode[$codemax]}-->
        <!--{else}-->
          <!--{$arrProductCode.0}-->
        <!--{/if}-->
      </div>
      <h2><!--★商品名★--><!--{$arrProduct.name|escape}--></h2>
      <!--★価格★-->
      <div><!--{$smarty.const.SALE_PRICE_TITLE}--><span class="mini">(税込)</span>：
        <span class="price">
          <!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
            <!--{$arrProduct.price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
          <!--{else}-->
            <!--{$arrProduct.price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->〜<!--{$arrProduct.price02_max|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
          <!--{/if}-->円</span></div>
      <div>
        <!--{if $arrProduct.price01_max > 0}-->
        <span class="price"><!--{$smarty.const.NORMAL_PRICE_TITLE}-->：
          <!--{if $arrProduct.price01_min == $arrProduct.price01_max}-->
            <!--{$arrProduct.price01_min|number_format}-->
          <!--{else}-->
            <!--{$arrProduct.price01_min|number_format}-->〜<!--{$arrProduct.price01_max|number_format}-->
          <!--{/if}-->円</span>
        <!--{/if}-->
      </div>

      <!--★ポイント★-->
      <div><span class="price">ポイント：
        <!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
          <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id}-->
        <!--{else}-->
          <!--{if $arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id == $arrProduct.price02_max|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id}-->
            <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id}-->
          <!--{else}-->
            <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id}-->〜<!--{$arrProduct.price02_max|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id}-->
          <!--{/if}-->
        <!--{/if}-->Pt</span></div>


      <form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
        <input type="hidden" name="mode" value="cart" />
        <input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->" />
        <!--{if $tpl_classcat_find1}-->
        <dl>
          <dt>
            <!--{$tpl_class_name1}-->
          </dt>
          <dd>
            <select name="classcategory_id1"
                    style="<!--{$arrErr.classcategory_id1|sfGetErrorColor}-->"
                    onchange="lnSetSelect('form1', 'classcategory_id1', 'classcategory_id2', ''); ">
              <option value="">選択してください</option>
              <!--{html_options options=$arrClassCat1 selected=$arrForm.classcategory_id1.value}-->
            </select>
            <!--{if $arrErr.classcategory_id1 != ""}-->
            <br /><span class="attention">※ <!--{$tpl_class_name1}-->を入力して下さい。</span>
          <!--{/if}-->
          </dd>
        </dl>
        <!--{/if}-->

        <!--{if $tpl_stock_find}-->
          <!--{if $tpl_classcat_find2}-->
        <dl>
          <dt><!--{$tpl_class_name2}--></dt>
          <dd>
            <select name="classcategory_id2"
                    style="<!--{$arrErr.classcategory_id2|sfGetErrorColor}-->">
              <option value="">選択してください</option>
            </select>
            <!--{if $arrErr.classcategory_id2 != ""}-->
            <br /><span class="attention">※ <!--{$tpl_class_name2}-->を入力して下さい。</span>
            <!--{/if}-->
          </dd>
        </dl>
          <!--{/if}-->

        <dl>
          <dt>個&nbsp;&nbsp;数</dt>
          <dd><input type="text" name="quantity" class="box54" value="<!--{$arrForm.quantity.value|default:1}-->" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{$arrErr.quantity|sfGetErrorColor}-->" />
           <!--{if $arrErr.quantity != ""}-->
           <br /><span class="attention"><!--{$arrErr.quantity}--></span>
           <!--{/if}-->
          </dd>
        </dl>
        <!--{/if}-->

        <!--{if $tpl_stock_find}-->
        <p class="btn">
          <!--★カゴに入れる★-->
          <a href="javascript:void(document.form1.submit())" onmouseover="chgImg('<!--{$TPL_DIR}-->img/products/b_cartin_on.gif','cart');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/products/b_cartin.gif','cart');">
            <img src="<!--{$TPL_DIR}-->img/products/b_cartin.gif" width="115" height="25" alt="カゴに入れる" name="cart" id="cart" />
          </a>
        </p>
        <!--{else}-->
        <div class="attention">申し訳ございませんが、只今品切れ中です。</div>
        <!--{/if}-->
      </form>

    </div>
  </div>
  <!--詳細ここまで-->

  <!--▼サブコメントここから-->
  <!--{section name=cnt loop=$smarty.const.PRODUCTSUB_MAX}-->
  <!--{assign var=key value="sub_title`$smarty.section.cnt.iteration`"}-->
    <!--{if $arrProduct[$key] != ""}-->
  <div class="subarea">
    <h3><!--★サブタイトル★--><!--{$arrProduct[$key]|escape}--></h3>
    <!--{assign var=ckey value="sub_comment`$smarty.section.cnt.iteration`"}-->

    <!--拡大写真がある場合ここから-->
    <!--{assign var=key value="sub_image`$smarty.section.cnt.iteration`"}-->
    <!--{assign var=lkey value="sub_large_image`$smarty.section.cnt.iteration`"}-->
    <!--{if $arrFile[$key].filepath != ""}-->
    <div class="subtext"><!--★サブテキスト★--><!--{$arrProduct[$ckey]|nl2br}--></div>
      <div class="subphotoimg">
      <!--{if $arrFile[$lkey].filepath != ""}-->
        <a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="win01('./detail_image.php?product_id=<!--{$arrProduct.product_id}-->&amp;image=<!--{$lkey}--><!--{if $smarty.get.admin == 'on'}-->&amp;admin=on<!--{/if}-->','detail_image','<!--{$arrFile[$lkey].width+60}-->','<!--{$arrFile[$lkey].height+80}-->'); return false;" target="_blank">
      <!--{/if}-->
      <!--サブ画像-->
        <img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrProduct.name|escape}-->" width="<!--{$smarty.const.NORMAL_SUBIMAGE_WIDTH}-->" height="<!--{$smarty.const.NORMAL_SUBIMAGE_WIDTH}-->" />
      <!--{if $arrFile[$lkey].filepath != ""}-->
        </a>
        <p>
          <a href="<!--{$smarty.server.PHP_SELF|escape}-->"
             onclick="win01('./detail_image.php?product_id=<!--{$arrProduct.product_id}-->&amp;image=<!--{$lkey}--><!--{if $smarty.get.admin == 'on'}-->&amp;admin=on<!--{/if}-->','detail_image','<!--{$arrFile[$lkey].width+60}-->','<!--{$arrFile[$lkey].height+80}-->'); return false;"
             onmouseover="chgImg('<!--{$TPL_DIR}-->img/products/b_expansion_on.gif','expansion02');"
             onmouseout="chgImg('<!--{$TPL_DIR}-->img/products/b_expansion.gif','expansion02');" target="_blank">
            <img src="<!--{$TPL_DIR}-->img/products/b_expansion.gif" width="85" height="13" alt="画像を拡大する" />
          </a>
        </p>
      <!--{/if}-->
      </div>
      <!--拡大写真がある場合ここまで-->
    <!--{else}-->
    <p><!--★サブテキスト★--><!--{$arrProduct[$ckey]|nl2br}--></p>
    <!--{/if}-->

  </div>
  <!--{/if}-->
  <!--{/section}-->
  <!--▲サブコメントここまで-->



  <!--この商品に対するお客様の声-->
  <div id="customervoicearea">
    <h2><img src="<!--{$TPL_DIR}-->img/products/title_voice.jpg" width="580" height="30" alt="この商品に対するお客様の声" /></h2>

    <!--{if count($arrReview) < $smarty.const.REVIEW_REGIST_MAX}-->
    <!--★新規コメントを書き込む★-->
      <a href="./review.php"
         onclick="win02('./review.php?product_id=<!--{$arrProduct.product_id}-->','review','580','580'); return false;"
         onmouseover="chgImg('<!--{$TPL_DIR}-->img/products/b_comment_on.gif','review');"
         onmouseout="chgImg('<!--{$TPL_DIR}-->img/products/b_comment.gif','review');" target="_blank">
        <img src="<!--{$TPL_DIR}-->img/products/b_comment.gif" width="150" height="22" alt="新規コメントを書き込む" name="review" id="review" />
      </a>
    <!--{/if}-->

    <!--{if count($arrReview) > 0}-->
    <ul>
    <!--{section name=cnt loop=$arrReview}-->
      <li>
        <p class="voicedate"><!--{$arrReview[cnt].create_date|sfDispDBDate:false}-->　投稿者：<!--{if $arrReview[cnt].reviewer_url}--><a href="<!--{$arrReview[cnt].reviewer_url}-->" target="_blank"><!--{$arrReview[cnt].reviewer_name|escape}--></a><!--{else}--><!--{$arrReview[cnt].reviewer_name|escape}--><!--{/if}-->　おすすめレベル：<span class="price"><!--{assign var=level value=$arrReview[cnt].recommend_level}--><!--{$arrRECOMMEND[$level]|escape}--></span></p>
        <p class="voicetitle"><!--{$arrReview[cnt].title|escape}--></p>
        <p class="voicecomment"><!--{$arrReview[cnt].comment|escape|nl2br}--></p>
      </li>
    <!--{/section}-->
    </ul>
    <!--{/if}-->
  </div>
  <!--お客様の声ここまで-->


  <!--{if $arrTrackbackView == "ON"}-->
  <!--▼トラックバックここから-->
  <div id="trackbackarea">
    <h2><img src="<!--{$TPL_DIR}-->img/products/title_tb.jpg" width="580" height="30" alt="この商品に対するトラックバック" /></h2>
    <h3>この商品のトラックバック先URL</h3>
    <input type="text" name="trackback" value="<!--{$trackback_url}-->" size="100" class="box500" />

    <!--{if $arrTrackback}-->
      <ul>
      <!--{section name=cnt loop=$arrTrackback}-->
        <li><strong><!--{$arrTrackback[cnt].create_date|sfDispDBDate:false}-->　<a href="<!--{$arrTrackback[cnt].url}-->" target="_blank"><!--{$arrTrackback[cnt].title|escape}--></a> from <!--{$arrTrackback[cnt].blog_name|escape}--></strong>
          <p><!--{$arrTrackback[cnt].excerpt|escape|mb_strimwidth:0:200:"..."}--></p></li>
      <!--{/section}-->
      </ul>
    <!--{/if}-->
  <!--▲トラックバックここまで-->
  </div>
  <!--{/if}-->


  <!--▼オススメ商品ここから-->
  <!--{if $arrRecommend}-->
  <div id="whoboughtarea">
    <h2><img src="<!--{$TPL_DIR}-->img/products/title_recommend.jpg" width="580" height="30" alt="オススメ商品" /></h2>
    <!--{section name=cnt loop=$arrRecommend}-->
    <!--{if ($smarty.section.cnt.index % 2) == 0}-->
    <div class="whoboughtblock">
    <!--{/if}-->
      <!-- 左列 -->
      <div class="whoboughtleft">
      <!--{if $arrRecommend[cnt].main_list_image != ""}-->
        <!--{assign var=image_path value="`$arrRecommend[cnt].main_list_image`"}-->
      <!--{else}-->
        <!--{assign var=image_path value="`$smarty.const.NO_IMAGE_DIR`"}-->
      <!--{/if}-->

        <a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrRecommend[cnt].product_id}-->">
         <img src="<!--{$smarty.const.SITE_URL}-->resize_image.php?image=<!--{$image_path|sfRmDupSlash}-->&amp;width=65&amp;height=65" alt="<!--{$arrRecommend[cnt].name|escape}-->" />
        </a>

        <!--{assign var=price02_min value=`$arrRecommend[cnt].price02_min`}-->
        <!--{assign var=price02_max value=`$arrRecommend[cnt].price02_max`}-->
        <h3><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrRecommend[cnt].product_id}-->"><!--{$arrRecommend[cnt].name|escape}--></a></h3>

        <p>価格<span class="mini">(税込)</span>：<span class="price">
        <!--{if $price02_min == $price02_max}-->
          <!--{$price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
        <!--{else}-->
          <!--{$price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->〜<!--{$price02_max|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
        <!--{/if}-->円</span></p>
        <p class="mini"><!--{$arrRecommend[cnt].comment|escape|nl2br}--></p>
      </div>
      <!-- 左列 -->

      <!--{assign var=nextCnt value=$smarty.section.cnt.index+1}-->
      <!--{if $arrRecommend[$nextCnt].product_id}-->
      <!-- 右列 -->
      <div class="whoboughtright">
        <a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrRecommend[$nextCnt].product_id}-->">
        <!--{if $arrRecommend[$nextCnt].main_list_image != ""}-->
          <!--{assign var=image_path value="`$arrRecommend[$nextCnt].main_list_image`"}-->
        <!--{else}-->
          <!--{assign var=image_path value="`$smarty.const.NO_IMAGE_DIR`"}-->
        <!--{/if}-->
          <img src="<!--{$smarty.const.SITE_URL}-->resize_image.php?image=<!--{$image_path|sfRmDupSlash}-->&amp;width=65&amp;height=65" alt="<!--{$arrRecommend[$nextCnt].name|escape}-->" />
        </a>
        <!--{assign var=price02_min value=`$arrRecommend[$nextCnt].price02_min`}-->
        <!--{assign var=price02_max value=`$arrRecommend[$nextCnt].price02_max`}-->
        <h3><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrRecommend[$nextCnt].product_id}-->"><!--{$arrRecommend[$nextCnt].name|escape}--></a></h3>

        <p>価格<span class="mini">(税込)</span>：<span class="price">

        <!--{if $price02_min == $price02_max}-->
          <!--{$price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
        <!--{else}-->
          <!--{$price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->〜<!--{$price02_max|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
        <!--{/if}-->円</span></p>
        <p class="mini"><!--{$arrRecommend[$nextCnt].comment|escape|nl2br}--></p>
      </div>
      <!-- 右列 -->
    <!--{/if}-->
    <!--{if ($smarty.section.cnt.index % 2) == 0 || $smarty.section.cnt.last}-->
    </div>
    <!--{/if}-->
  <!--{/section}-->
<!--{/if}-->
</div>
<!--▲CONTENTS-->
