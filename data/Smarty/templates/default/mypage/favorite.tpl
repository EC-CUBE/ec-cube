<div id="mypage" class="page-contents">
  <h2 class="title">マイページ</h2>
  <!--{if $tpl_navi != ""}-->
    <!--{include file=$tpl_navi}-->
  <!--{else}-->
    <!--{include file=`$smarty.const.TEMPLATE_DIR`mypage/navi.tpl}-->
  <!--{/if}-->

  <div id="mypage-contents">
    <form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
    <input type="hidden" name="order_id" value="" />
    <input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->" />
    <h3>お気に入り一覧</h3>

<!--{if $tpl_linemax > 0}-->

    <p><!--{$tpl_linemax}-->件のお気に入りがあります。</p>
    <div class="paging">
      <!--▼ページナビ-->
      <!--{$tpl_strnavi}-->
      <!--▲ページナビ-->
    </div>
    <p>※最大20件まで表示します。</p>

    <form name="form1" id="form1" method="post" action="<!--{$smarty.const.SITE_URL}-->products/detail.php">
    <input type="hidden" name="mode" value="cart" />
    <input type="hidden" name="product_id" value="" />
    <table summary="お気に入り" id="mypage-history-list" class="list">
      <tr>
        <th width="40">削除</th>
        <th width="60">商品画像</th>
        <th width="200">商品名</th>
        <th width="200"><!--{$smarty.const.SALE_PRICE_TITLE}--></th>
      </tr>
      <!--{section name=cnt loop=$arrFavorite}-->
      <!--{if $arrFavorite[cnt].main_list_image != ""}-->
        <!--{assign var=image_path value="`$arrFavorite[cnt].main_list_image`"}-->
      <!--{else}-->
        <!--{assign var=image_path value="`$smarty.const.NO_IMAGE_DIR`"}-->
      <!--{/if}-->
      <!--{assign var=product_id value="`$arrFavorite[cnt].product_id`"}-->
      <tr>
       <td><a href="javascript:fnModeSubmit('delete_favorite','product_id','<!--{$product_id}-->');">削除</a></td>
       <td><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$product_id}-->"><img src="<!--{$smarty.const.SITE_URL}-->resize_image.php?image=<!--{$image_path|sfRmDupSlash}-->&width=65&height=65"></a></td>
       <td><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$product_id}-->"><!--{$arrFavorite[cnt].name}--></a></td>
       <td class="right">
        <div><!--{$smarty.const.SALE_PRICE_TITLE}--><span class="mini">(税込)</span>：
        <span class="price">
          <!--{if $arrFavorite[cnt].price02_min == $arrFavorite[cnt].price02_max}-->
            <!--{$arrFavorite[cnt].price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
          <!--{else}-->
            <!--{$arrFavorite[cnt].price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->〜<!--{$arrFavorite[cnt].price02_max|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
          <!--{/if}-->円</span></div>
      <div>
        <!--{if $arrFavorite[cnt].price01_max > 0}-->
        <span class="price"><!--{$smarty.const.NORMAL_PRICE_TITLE}-->：
          <!--{if $arrFavorite[cnt].price01_min == $arrFavorite[cnt].price01_max}-->
            <!--{$arrFavorite[cnt].price01_min|number_format}-->
          <!--{else}-->
            <!--{$arrFavorite[cnt].price01_min|number_format}-->〜<!--{$arrFavorite[cnt].price01_max|number_format}-->
          <!--{/if}-->円</span>
        <!--{/if}-->
        </div>
       </td>
     </tr>
     <!--{/section}-->
    </table>
    <br />
    <!--{if $stock_find_count > 0 && $customer_rank < 51}-->
    <div class="product-btn">
      <a href="javascript:void(document.form1.submit())" class="btn-cart">カートに入れる</a>
    </div>
    <!--{/if}-->
    </form>

    <!--{else}-->
    <p>お気に入りが登録されておりません。</p>
    <!--{/if}-->
    </form>
  </div>
</div>
