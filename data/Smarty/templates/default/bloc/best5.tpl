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
<!--▼おすすめ商品-->
<!--{if count($arrBestProducts) > 0}-->
<div id="recomendarea">
  <h2>
    <img src="<!--{$TPL_DIR}-->img/top/osusume.jpg" width="400" height="29" alt="おすすめ商品" />
  </h2>
  <!--{section name=cnt loop=$arrBestProducts step=2}-->
  <div class="recomendblock">
    <div class="recomendleft">

      <a href="<!--{$smarty.const.URL_DIR}-->products/detail.php?product_id=<!--{$arrBestProducts[cnt].product_id}-->">
        <img src="<!--{$smarty.const.SITE_URL}-->resize_image.php?image=<!--{$arrBestProducts[cnt].main_list_image|sfNoImageMainList|escape}-->&amp;width=48&amp;height=48" alt="<!--{$arrBestProducts[cnt].name|escape}-->" /></a>

      <h3>
        <a href="<!--{$smarty.const.URL_DIR}-->products/detail.php?product_id=<!--{$arrBestProducts[cnt].product_id}-->"><!--{$arrBestProducts[cnt].name|escape}--></a>
      </h3>

<!--{assign var=price01 value=`$arrBestProducts[cnt].price01_min`}-->
<!--{assign var=price02 value=`$arrBestProducts[cnt].price02_min`}-->

      <p class="sale_price"><!--{$smarty.const.SALE_PRICE_TITLE}--><span class="mini">(税込)</span>：
        <span class="price"><!--{$price02|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}--> 円</span>
      </p>

      <p class="mini comment"><!--{$arrBestProducts[cnt].comment|escape|nl2br}--></p>
    </div>

    <div class="recomendright">
      <!--{assign var=cnt2 value=`$smarty.section.cnt.iteration*$smarty.section.cnt.step-1` }-->
      <!--{if $arrBestProducts[$cnt2]|count > 0}-->

      <a href="<!--{$smarty.const.URL_DIR}-->products/detail.php?product_id=<!--{$arrBestProducts[$cnt2].product_id}-->">
        <img src="<!--{$smarty.const.SITE_URL}-->resize_image.php?image=<!--{$arrBestProducts[$cnt2].main_list_image|sfNoImageMainList|escape}-->&amp;width=48&amp;height=48" alt="<!--{$arrBestProducts[$cnt2].name|escape}-->" /></a>

      <h3>
      <a href="<!--{$smarty.const.URL_DIR}-->products/detail.php?product_id=<!--{$arrBestProducts[$cnt2].product_id}-->"><!--{$arrBestProducts[$cnt2].name|escape}--></a>
      </h3>

<!--{assign var=price01 value=`$arrBestProducts[$cnt2].price01_min`}-->
<!--{assign var=price02 value=`$arrBestProducts[$cnt2].price02_min`}-->

      <p class="sale_price"><!--{$smarty.const.SALE_PRICE_TITLE}--><span class="mini">(税込)</span>：
        <span class="price"><!--{$price02|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}--> 円</span>
      </p>

      <p class="mini comment"><!--{$arrBestProducts[$cnt2].comment|escape|nl2br}--></p>
      <!--{/if}-->
    </div>
  </div>
  <!--{/section}-->
</div>
<!--{/if}-->
<!--▲おすすめ商品-->
