<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
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

<!--▼CONTENTS-->
<div id="undercolumn">
  <form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
    <input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->" />
    <input type="hidden" name="mode" value="" />
    <input type="hidden" name="orderby" value="<!--{$orderby}-->" />
    <input type="hidden" name="product_id" value="" />
  <div id="listtitle"><h2><!--★タイトル★--><!--{$tpl_subtitle}--></h2></div>
  <!--検索条件ここから-->
  <!--{if $tpl_subtitle == "検索結果"}-->
    <ul class="pagecondarea">
      <li><strong>商品カテゴリ：</strong><!--{$arrSearch.category|escape}--></li>
      <li><strong>商品名：</strong><!--{$arrSearch.name|escape}--></li>
    </ul>
  <!--{/if}-->
  <!--検索条件ここまで-->

 <!--件数ここから-->
  <!--{if $tpl_linemax > 0}-->
  <ul class="pagenumberarea">
    <li class="left"><span class="pagenumber"><!--{$tpl_linemax}--></span>件の商品がございます。</li>
    <li class="center"><!--{$tpl_strnavi}--></li>
    <li class="right"><!--{if $orderby != 'price'}-->
        <a href="javascript:fnModeSubmit('', 'orderby', 'price')">価格順</a>
    <!--{else}-->
        <strong>価格順</strong>
    <!--{/if}-->&nbsp;
    <!--{if $orderby != "date"}-->
        <a href="javascript:fnModeSubmit('', 'orderby', 'date')">新着順</a>
    <!--{else}-->
        <strong>新着順</strong>
    <!--{/if}-->
    </li>
  </ul><!--件数ここまで-->
  <!--{else}-->
    <!--{include file="frontparts/search_zero.tpl"}-->
  <!--{/if}-->

  <!--{section name=cnt loop=$arrProducts}-->
    <!--{assign var=id value=$arrProducts[cnt].product_id}-->
    <!--▼商品ここから-->
    <div class="listarea">
      <div class="listphoto">
        <!--★画像★-->
        <a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProducts[cnt].product_id}-->" class="over"><!--商品写真--><img src="<!--{$smarty.const.IMAGE_SAVE_URL|sfTrimURL}-->/<!--{$arrProducts[cnt].main_list_image}-->" alt="<!--{$arrProducts[cnt].name|escape}-->" class="picture" /></a>
     </div>
     <div class="listrightblock">
       <!--アイコン-->
       <!--商品ステータス-->
       <!--{if count($arrProducts[cnt].product_flag) > 0}-->
       <ul>
         <!--{section name=flg loop=$arrProducts[cnt].product_flag|count_characters}-->
           <!--{if $arrProducts[cnt].product_flag[flg] == "1"}-->
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
         <a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProducts[cnt].product_id}-->"><!--{$arrProducts[cnt].name|escape}--></a>
       </h3>
       <p class="listcomment"><!--★コメント★--><!--{$arrProducts[cnt].main_list_comment|escape|nl2br}--></p>
       <p>
         <span class="pricebox">価格<span class="mini">(税込)</span>：
         <span class="price">
         <!--{if $arrProducts[cnt].price02_min == $arrProducts[cnt].price02_max}-->
           <!--{$arrProducts[cnt].price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
         <!--{else}-->
           <!--{$arrProducts[cnt].price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->〜<!--{$arrProducts[cnt].price02_max|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
         <!--{/if}-->円</span></span>
         <span class="btnbox"><!--★詳細ボタン★-->
         <!--{assign var=name value="detail`$smarty.section.cnt.iteration`"}-->
           <a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProducts[cnt].product_id}-->" onmouseover="chgImg('<!--{$TPL_DIR}-->img/products/b_detail_on.gif','<!--{$name}-->');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/products/b_detail.gif','<!--{$name}-->');">
            <img src="<!--{$TPL_DIR}-->img/products/b_detail.gif" width="115" height="25" alt="詳しくはこちら" name="<!--{$name}-->" id="<!--{$name}-->" />
           </a>
         </span>
       </p>

         <!--{if $arrProducts[cnt].stock_max == 0 && $arrProducts[cnt].stock_unlimited_max != 1}-->
           <p class="soldout"><em>申し訳ございませんが、只今品切れ中です。</em></p>
         <!--{else}-->
           <!--▼買い物かご-->
           <div class="in_cart">
             <dl>
         <!--{if $tpl_classcat_find1[$id]}-->
           <!--{assign var=class1 value=classcategory_id`$id`_1}-->
           <!--{assign var=class2 value=classcategory_id`$id`_2}-->
           <dt><!--{$tpl_class_name1[$id]|escape}-->：</dt>
           <dd><select name="<!--{$class1}-->" style="<!--{$arrErr[$class1]|sfGetErrorColor}-->" onchange="lnSetSelect('<!--{$class1}-->', '<!--{$class2}-->', '<!--{$id}-->','');">
             <option value="">選択してください</option>
             <!--{html_options options=$arrClassCat1[$id] selected=$arrForm[$class1]}-->
           </select>
             <!--{if $arrErr[$class1] != ""}-->
             <br /><span class="attention">※ <!--{$tpl_class_name1[$id]}-->を入力して下さい。</span>
             <!--{/if}-->
           </dd>

           <!--{/if}-->
           <!--{if $tpl_classcat_find2[$id]}-->
             <dt><!--{$tpl_class_name2[$id]|escape}-->：</dt>
             <dd><select name="<!--{$class2}-->" style="<!--{$arrErr[$class2]|sfGetErrorColor}-->">
               <option value="">選択してください</option>
             </select>

             <!--{if $arrErr[$class2] != ""}-->
             <br /><span class="attention">※ <!--{$tpl_class_name2[$id]}-->を入力して下さい。</span>
             <!--{/if}-->
             </dd>

           <!--{/if}-->
           <!--{assign var=quantity value=quantity`$id`}-->

           <dt>個数：</dt>
           <dd><input type="text" name="<!--{$quantity}-->" size="3" class="box54" value="<!--{$arrForm[$quantity]|default:1}-->" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{$arrErr[$quantity]|sfGetErrorColor}-->" />
             <!--{if $arrErr[$quantity] != ""}-->
             <br /><span class="attention"><!--{$arrErr[$quantity]}--></span>
             <!--{/if}-->
           </dd>
         </dl>
             <div class="cartbtn">
             <a href="<!--{$smarty.server.REQUEST_URI|escape}-->#product<!--{$id}-->" onclick="fnChangeAction('<!--{$smarty.server.REQUEST_URI|escape}-->#product<!--{$id}-->'); fnModeSubmit('cart','product_id','<!--{$id}-->'); return false;" onmouseover="chgImg('<!--{$TPL_DIR}-->img/products/b_cartin_on.gif','cart<!--{$id}-->');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/products/b_cartin.gif','cart<!--{$id}-->');">
               <img src="<!--{$TPL_DIR}-->img/products/b_cartin.gif" width="115" height="25" alt="カゴに入れる" name="cart<!--{$id}-->" id="cart<!--{$id}-->" />
             </a>
             </div>
           </div>
             <!--▲買い物かご-->
           <!--{/if}-->
          </div>
       </div>
         <!--{/section}-->

  <!--件数ここから-->
  <!--{if $tpl_linemax > 0}-->
  <ul class="pagenumberarea">
    <li class="left"><span class="pagenumber"><!--{$tpl_linemax}--></span>件の商品がございます。</li>
    <li class="center"><!--{$tpl_strnavi}--></li>
    <li class="right"><!--{if $orderby != 'price'}-->
        <a href="javascript:fnModeSubmit('', 'orderby', 'price')">価格順</a>
    <!--{else}-->
        <strong>価格順</strong>
    <!--{/if}-->&nbsp;
    <!--{if $orderby != "date"}-->
        <a href="javascript:fnModeSubmit('', 'orderby', 'date')">新着順</a>
    <!--{else}-->
        <strong>新着順</strong>
    <!--{/if}-->
    </li>
  </ul><!--件数ここまで-->
    <!--{/if}-->
  </form>
</div>
<!--▲CONTENTS-->
