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

<!-- ▼おすすめ商品 -->
<!--{if count($arrBestProducts) > 0}-->
    <section id="recommend_area" class="mainImageInit">
        <h2>おすすめ商品</h2>
        <ul>
            <!--{section name=cnt loop=$arrBestProducts step=2}-->
                <li id="mainImage<!--{$smarty.section.cnt.index}-->">
                    <div class="recommendblock clearfix">
                        <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrBestProducts[cnt].main_list_image|sfNoImageMainList|h}-->&amp;width=80&amp;height=80" alt="<!--{$arrBestProducts[cnt].name|h}-->" />
                        <div class="productContents">
                            <h3><a rel="external" href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrBestProducts[cnt].product_id|u}-->"><!--{$arrBestProducts[cnt].name|h}--></a></h3>
                            <p class="mini comment"><!--{$arrBestProducts[cnt].comment|h|nl2br}--></p>
                            <!--{assign var=price01 value=`$arrBestProducts[cnt].price01_min`}-->
                            <!--{assign var=price02 value=`$arrBestProducts[cnt].price02_min`}-->
                            <p class="sale_price"><span class="mini"><!--<span class="mini">-->販売価格(税込):<!--</span>--></span><span class="price"><!--{$price02|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}--> 円</span></p>
                        </div>
                    </div>
                </li>
                <!--{assign var=cnt2 value=`$smarty.section.cnt.iteration*$smarty.section.cnt.step-1`}-->
                <!--{if $arrBestProducts[$cnt2]|count > 0}-->
                    <li id="mainImage<!--{$cnt2}-->">
                        <div class="recommendblock clearfix">
                            <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrBestProducts[$cnt2].main_list_image|sfNoImageMainList|h}-->&amp;width=80&amp;height=80" alt="<!--{$arrBestProducts[$cnt2].name|h}-->" />
                            <div class="productContents">
                                <h3><a rel="external" href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrBestProducts[$cnt2].product_id|u}-->"><!--{$arrBestProducts[$cnt2].name|h}--></a></h3>
                                <p class="mini comment"><!--{$arrBestProducts[$cnt2].comment|h|nl2br}--></p>
                                <!--{assign var=price01 value=`$arrBestProducts[$cnt2].price01_min`}-->
                                <!--{assign var=price02 value=`$arrBestProducts[$cnt2].price02_min`}-->
                                <p class="sale_price"><span class="mini"><!--<span class="mini">-->販売価格(税込):</span><!--</span>--><span class="price"><!--{$price02|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}--> 円</span></p>
                            </div>
                        </div>
                    </li>
                <!--{/if}-->
            <!--{/section}-->
        </ul>
    </section>
<!--{/if}-->
<!-- ▲おすすめ商品 -->

<script type="application/javascript">
    <!--//
    $(function(){
        $('#recommend_area ul li').flickSlide({target:'#recommend_area>ul', duration:5000});
    });
    //-->
</script>
