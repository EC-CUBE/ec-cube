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
<!--▼おすすめ情報ここから-->
<!--{if count($arrBestProducts) > 0}-->
<div id="recomendarea">
  <h2>
    <img src="<!--{$TPL_DIR}-->img/top/osusume.jpg" width="400" height="29" alt="おすすめ情報" />
  </h2>
  <!--{section name=cnt loop=$arrBestProducts step=2}-->
  <div class="recomendblock">
    <div class="recomendleft">
    <!--{if $arrBestProducts[cnt].main_list_image != ""}-->
      <!--{assign var=image_path value="`$arrBestProducts[cnt].main_list_image`"}-->
    <!--{else}-->
      <!--{assign var=image_path value="`$smarty.const.NO_IMAGE_DIR`"}-->
    <!--{/if}-->

      <a href="<!--{$smarty.const.URL_DIR}-->products/detail.php?product_id=<!--{$arrBestProducts[cnt].product_id}-->">
        <img src="<!--{$smarty.const.SITE_URL}-->resize_image.php?image=<!--{$image_path|sfRmDupSlash}-->&amp;width=48&amp;height=48" alt="<!--{$arrBestProducts[cnt].name|escape}-->" />
      </a>

      <h3>
        <a href="<!--{$smarty.const.URL_DIR}-->products/detail.php?product_id=<!--{$arrBestProducts[cnt].product_id}-->"><!--{$arrBestProducts[cnt].name|escape}--></a>
      </h3>

<!--{assign var=price01 value=`$arrBestProducts[cnt].price01_min`}-->
<!--{assign var=price02 value=`$arrBestProducts[cnt].price02_min`}-->

      <p>価格<span class="mini">(税込)</span>：<span class="price">
<!--{if $price02 == ""}-->
  <!--{$price01|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
<!--{else}-->
  <!--{$price02|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
<!--{/if}-->円
        </span>
      </p>
      <p class="mini"><!--{$arrBestProducts[cnt].comment|escape|nl2br}--></p>
    </div>

    <div class="recomendright">
      <!--{assign var=cnt2 value=`$smarty.section.cnt.iteration*$smarty.section.cnt.step-1` }-->
      <!--{if $arrBestProducts[$cnt2]|count > 0}-->
        <!--{if $arrBestProducts[$cnt2].main_list_image != ""}-->
          <!--{assign var=image_path value="`$arrBestProducts[$cnt2].main_list_image`"}-->
        <!--{else}-->
          <!--{assign var=image_path value="`$smarty.const.NO_IMAGE_DIR`"}-->
        <!--{/if}-->

      <a href="<!--{$smarty.const.URL_DIR}-->products/detail.php?product_id=<!--{$arrBestProducts[$cnt2].product_id}-->">
        <img src="<!--{$smarty.const.SITE_URL}-->resize_image.php?image=<!--{$image_path|sfRmDupSlash}-->&amp;width=48&amp;height=48" alt="<!--{$arrBestProducts[$cnt2].name|escape}-->" />
      </a>

      <h3>
      <a href="<!--{$smarty.const.URL_DIR}-->products/detail.php?product_id=<!--{$arrBestProducts[$cnt2].product_id}-->"><!--{$arrBestProducts[$cnt2].name|escape}--></a>
      </h3>

<!--{assign var=price01 value=`$arrBestProducts[$cnt2].price01_min`}-->
<!--{assign var=price02 value=`$arrBestProducts[$cnt2].price02_min`}-->

      <p>価格<span class="mini">(税込)</span>：<span class="price">
<!--{if $price02 == ""}-->
  <!--{$price01|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
<!--{else}-->
  <!--{$price02|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
<!--{/if}-->円</span>
      </p>

      <p class="mini"><!--{$arrBestProducts[$cnt2].comment|escape|nl2br}--></p>
      <!--{/if}-->
    </div>
  </div>
  <!--{/section}-->
</div>
<!--{/if}-->
<!--▲おすすめ情報ここまで-->
