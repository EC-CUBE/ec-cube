<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
 */
*}-->
<script type="text/javascript">
// URLの表示非表示切り替え
function lfnDispChange(){
  inner_id = 'switch';

  cnt = document.form1.item_cnt.value;
  
  if($('#disp_url1').css("display") == 'none'){
    for (i = 1; i <= cnt; i++) {
      disp_id = 'disp_url'+i;
      $('#' + disp_id).css("display", "");
  
      disp_id = 'disp_cat'+i;
      $('#' + disp_id).css("display", "none");
      
      $('#' + inner_id).html('  URL <a href="#" onClick="lfnDispChange();"> &gt;&gt; カテゴリ表示<\/a>');
    }
  }else{
    for (i = 1; i <= cnt; i++) {
      disp_id = 'disp_url'+i;
      $('#' + disp_id).css("display", "none");
  
      disp_id = 'disp_cat'+i;
      $('#' + disp_id).css("display", "");
      
      $('#' + inner_id).html('  カテゴリ <a href="#" onClick="lfnDispChange();"> &gt;&gt; URL表示<\/a>');
    }
  }

}

</script>


<div id="products" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
  <input type="hidden" name="mode" value="search" />
  <!--{foreach key=key item=item from=$arrHidden}-->
  <!--{if $key == 'campaign_id' || $key == 'search_mode'}-->
  <input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->" />
  <!--{/if}-->
  <!--{/foreach}-->
  <h2>検索条件設定</h2>

  <!--検索条件設定テーブルここから-->
  <table>
    <tr>
      <th>商品ID</th>
      <td>
        <!--{if $arrErr.search_product_id}-->
        <span class="attention"><!--{$arrErr.search_product_id}--></span>
        <!--{/if}-->
        <input type="text" name="search_product_id" value="<!--{$arrForm.search_product_id|escape}-->" size="30" class="box30" style="<!--{$arrErr.search_product_id|sfGetErrorColor}-->"/>
      </td>
    </tr>
    <tr>
      <th>商品コード</th>
      <td><input type="text" name="search_product_code" value="<!--{$arrForm.search_product_code|escape}-->" size="30" class="box30" /></td>
    </tr>
    <tr>
      <th>カテゴリ</th>
      <td>
        <select name="search_category_id" style="<!--{if $arrErr.search_category_id != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->">
        <option value="">選択してください</option>
        <!--{html_options options=$arrCatList selected=$arrForm.search_category_id}-->
        </select>
      </td>
    </tr>
    <tr>
      <th>規格名称</th>
      <td>
        <!--{if $arrErr.search_product_class_name}-->
        <span class="attention"><!--{$arrErr.search_product_class_name}--></span>
        <!--{/if}-->
        <input type="text" name="search_product_class_name" value="<!--{$arrForm.search_product_class_name|escape}-->" size="30" class="box30"style="<!--{$arrErr.search_product_class_name|sfGetErrorColor}-->" />
      </td>
    </tr>
    <tr>
      <th>商品名</th>
      <td><input type="text" name="search_name" value="<!--{$arrForm.search_name|escape}-->" size="30" class="box30" /></td>
    </tr>
    <tr>
      <th>種別</th>
      <td><!--{html_checkboxes name="search_status" options=$arrDISP selected=$arrForm.search_status}--></td>
    </tr>
    <tr>
      <th>登録・更新日</th>
      <td>
        <!--{if $arrErr.search_startyear || $arrErr.search_endyear}-->
        <span class="attention"><!--{$arrErr.search_startyear}--></span>
        <span class="attention"><!--{$arrErr.search_endyear}--></span>
        <!--{/if}-->
        <select name="search_startyear" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
        <option value="">----</option>
        <!--{html_options options=$arrStartYear selected=$arrForm.search_startyear}-->
        </select>年
        <select name="search_startmonth" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
        <option value="">--</option>
        <!--{html_options options=$arrStartMonth selected=$arrForm.search_startmonth}-->
        </select>月
        <select name="search_startday" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
        <option value="">--</option>
        <!--{html_options options=$arrStartDay selected=$arrForm.search_startday}-->
        </select>日～
        <select name="search_endyear" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
        <option value="">----</option>
        <!--{html_options options=$arrEndYear selected=$arrForm.search_endyear}-->
        </select>年
        <select name="search_endmonth" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
        <option value="">--</option>
        <!--{html_options options=$arrEndMonth selected=$arrForm.search_endmonth}-->
        </select>月
        <select name="search_endday" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
        <option value="">--</option>
        <!--{html_options options=$arrEndDay selected=$arrForm.search_endday}-->
        </select>日
      </td>
    </tr>
    <tr>
      <th>ステータス</th>
      <td>
      <!--{html_checkboxes name="search_product_flag" options=$arrSTATUS selected=$arrForm.search_product_flag}-->
      </td>
    </tr>
  </table>
  <div class="btn">
    検索結果表示件数
    <!--{assign var=key value="search_page_max"}-->
    <!--{if $arrErr[$key]}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <!--{/if}-->
    <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
    <!--{html_options options=$arrPageMax selected=$arrForm.search_page_max}-->
    </select> 件
    <button type="submit"><span>この条件で検索する</span></button>
  </div>
  <!--検索条件設定テーブルここまで-->
</form>  


<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete')}-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
  <input type="hidden" name="mode" value="search" />
  <input type="hidden" name="product_id" value="" />
  <input type="hidden" name="category_id" value="" />
  <!--{foreach key=key item=item from=$arrHidden}-->
  <input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->" />
  <!--{/foreach}-->  
  <h2>検索結果一覧</h2>
  <p>
    <span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
    <!--検索結果-->
    <!--{if $smarty.const.ADMIN_MODE == '1'}-->
    <button type="button" onclick="fnModeSubmit('delete_all','','');">検索結果をすべて削除</button>
    <!--{/if}-->
    <button type="button" onclick="fnModeSubmit('csv','','');">CSV DOWNLOAD</button>
    <a href="../contents/csv.php?tpl_subno_csv=product"> &gt;&gt; CSV出力項目設定</a>
  </p>
  <!--{include file=$tpl_pager}-->

  <!--{if count($arrProducts) > 0}-->
  <!--検索結果表示テーブル-->
  <table class="list" id="products-search-result">
    <tr>
      <th rowspan="2">商品ID</th>
      <th rowspan="2">商品画像</th>
      <th rowspan="2">商品コード</th>
      <th rowspan="2">価格(円)</th>
      <th>商品名</th>
      <th rowspan="2">在庫</th>
      <th rowspan="2">種別</th>
      <th rowspan="2">編集</th>
      <th rowspan="2">確認</th>
      <!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
      <th rowspan="2">規格</th>
      <!--{/if}-->
      <th rowspan="2">削除</th>
      <th rowspan="2">複製</th>
    </tr>
    <tr>
      <th><a href="#" onClick="lfnDispChange(); return false;">カテゴリ ⇔ URL</a></th>
    </tr>

    <!--{section name=cnt loop=$arrProducts}-->
    <!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
    <!--{assign var=status value="`$arrProducts[cnt].status`"}-->
    <tr style="background:<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->;">
      <td rowspan="2"><!--{$arrProducts[cnt].product_id}--></td>
      <td rowspan="2">
      <img src="<!--{$smarty.const.URL_DIR}-->resize_image.php?image=<!--{$arrProducts[cnt].main_list_image|sfNoImageMainList|escape}-->&amp;width=65&amp;height=65">
      </td>
      <td rowspan="2"><!--{$arrProducts[cnt].product_code_min|escape}-->
        <!--{if $arrProducts[cnt].product_code_min != $arrProducts[cnt].product_code_max}-->
          <br />～ <!--{$arrProducts[cnt].product_code_max|escape}-->
        <!--{/if}-->
      </td>
      <!--{* 価格 *}-->
      <td rowspan="2" class="right">
        <!--{$arrProducts[cnt].price02_min|number_format}-->
        <!--{if $arrProducts[cnt].price02_min != $arrProducts[cnt].price02_max}-->
          <br />～ <!--{$arrProducts[cnt].price02_max|number_format}-->
        <!--{/if}-->
      </td>
      <td><!--{$arrProducts[cnt].name|escape}--></td>
      <!--{* 在庫 *}-->
      <!--{* XXX 複数規格でかつ、全ての在庫数量が等しい場合は先頭に「各」と入れたれたら良いと思う。 *}-->
      <td rowspan="2">
        <!--{if $arrProducts[cnt].stock_unlimited_min}-->無制限<!--{else}--><!--{$arrProducts[cnt].stock_min|number_format}--><!--{/if}-->
        <!--{if $arrProducts[cnt].stock_unlimited_min != $arrProducts[cnt].stock_unlimited_max || $arrProducts[cnt].stock_min != $arrProducts[cnt].stock_max}-->
          <br />～ <!--{if $arrProducts[cnt].stock_unlimited_max}-->無制限<!--{else}--><!--{$arrProducts[cnt].stock_max|number_format}--><!--{/if}-->
        <!--{/if}-->
      </td>
      <!--{* 表示 *}-->
      <!--{assign var=key value=$arrProducts[cnt].status}-->
      <td rowspan="2"><!--{$arrDISP[$key]}--></td>
      <td rowspan="2"><span class="icon_edit"><a href="<!--{$smarty.const.URL_DIR}-->" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" >編集</a></span></td>
      <td rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts[cnt].product_id}-->&amp;admin=on" target="_blank">確認</a></span></td>
      <!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
      <td rowspan="2"><span class="icon_class"><a href="<!--{$smarty.const.URL_DIR}-->" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" >規格</a></span></td>
      <!--{/if}-->
      <td rowspan="2"><span class="icon_delete"><a href="<!--{$smarty.const.URL_DIR}-->" onclick="fnSetFormValue('category_id', '<!--{$arrProducts[cnt].category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;">削除</a></span></td>
      <td rowspan="2"><span class="icon_copy"><a href="<!--{$smarty.const.URL_DIR}-->" onclick="fnChangeAction('./product.php'); fnModeSubmit('copy', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" >複製</a></span></td>
    </tr>
    <tr style="background:<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->;">
      <td>
        <!--{* カテゴリ名 *}-->
        <div id="disp_cat<!--{$smarty.section.cnt.iteration}-->" style="display:<!--{$cat_flg}-->">
          <!--{foreach from=$arrProducts[cnt].categories item=category_id name=categories}-->
            <!--{$arrCatList[$category_id]|sfTrim}-->
            <!--{if !$smarty.foreach.categories.last}--><br /><!--{/if}-->
          <!--{/foreach}-->
        </div>

        <!--{* URL *}-->
        <div id="disp_url<!--{$smarty.section.cnt.iteration}-->" style="display:none">
        <!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts[cnt].product_id}-->
        </div>
      </td>
    </tr>
    <!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
    <!--{/section}-->
  </table>
  <input type="hidden" name="item_cnt" value="<!--{$arrProducts|@count}-->" />
  <!--検索結果表示テーブル-->
  <!--{/if}-->

</form>

<!--★★検索結果一覧★★-->    
<!--{/if}-->
</div>
