<!--▼こんな商品も買っています-->
<!--{foreach from="$arrRecommendProducts" item="arrProduct" name="arrProduct"}-->
    <!--{if $smarty.foreach.arrProduct.first}-->
        <div id="undercolumn" class="product product_detail" style="margin-top: 0;"><!--{* FIXME detail.tpl と id 重複 *}-->
            <div id="whoboughtarea">
                <h2><img src="<!--{$smarty.const.PLUGIN_URL|h}--><!--{$arrPluginInfo.path}-->/img/title_recommend.png" width="580" height="30" alt="この商品を買った人はこんな商品も買っています" /></h2>
                <div class="whoboughtblock">
    <!--{/if}-->


    <!--{if ($smarty.section.cnt.index % 2) == 0}-->
        <!--{if $arrProduct.product_id}-->
            <div class="whoboughtleft"><!--{* XXX whoboughtleft は本来左列用なので、動作に不具合があるかもしれない。 *}-->
                
                <a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrProduct.product_id}-->">
                    <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrProduct.main_list_image|sfNoImageMainList|u}-->&amp;width=65&amp;height=65" alt="<!--{$arrProduct.name|h}-->" /></a>

                <!--{assign var=price02_min value=`$arrProduct.price02_min`}-->
                <!--{assign var=price02_max value=`$arrProduct.price02_max`}-->
                <h3><a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrProduct.product_id}-->"><!--{$arrProduct.name|h}--></a></h3>

                <p class="sale_price"><!--{$smarty.const.SALE_PRICE_TITLE}--><span class="mini">(税込)</span>：<span class="price">
                    <!--{if $price02_min == $price02_max}-->
                        <!--{$price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                    <!--{else}-->
                        <!--{$price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$price02_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                    <!--{/if}-->円</span></p>
                <p class="mini"><!--{$arrProduct.comment|h|nl2br}--></p>
            </div>
        <!--{/if}-->
    <!--{/if}-->


    <!--{if $smarty.foreach.arrProduct.last}-->
                </div>
            </div>
        </div>
    <!--{/if}-->
<!--{/foreach}-->
<!--▲こんな商品も買っています-->
