<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
            <!--{section name=cnt loop=$arrBestProducts}-->
                <li id="mainImage<!--{$smarty.section.cnt.index}-->">
                    <div class="recommendblock clearfix">
                        <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrBestProducts[cnt].main_list_image|sfNoImageMainList|h}-->" style="max-width: 80px;max-height: 80px;" alt="<!--{$arrBestProducts[cnt].name|h}-->" />
                        <div class="productContents">
                            <h3><a rel="external" href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrBestProducts[cnt].product_id|u}-->"><!--{$arrBestProducts[cnt].name|h}--></a></h3>
                            <p class="mini comment"><!--{$arrBestProducts[cnt].comment|h|nl2br}--></p>
                            <p class="sale_price">
                                <span class="mini"><!--{$smarty.const.SALE_PRICE_TITLE|h}-->(税込):</span><span class="price"><!--{$arrBestProducts[cnt].price02_min_inctax|n2s}--> 円</span>
                            </p>
                        </div>
                    </div>
                </li>
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
