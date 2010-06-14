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
<form name="search_form" method="post" action="?" >
<input type="hidden" name="mode" value="search" />
  <h2>検索条件設定</h2>

  <!--検索条件設定テーブルここから-->
  <table class="form">
    <tr>
      <th>ブログ名</th>
      <td><input type="text" name="search_blog_name" value="<!--{$arrForm.search_blog_name|escape}-->" size="30" class="box30" /></td>
    </tr>
    <tr>
      <th>ブログ記事タイトル</th>
      <td><input type="text" name="search_blog_title" value="<!--{$arrForm.search_blog_title|escape}-->" size="30" class="box30" /></td>
    </tr>
    <tr>
      <th>URL</th>
      <td><input type="text" name="search_blog_url" value="<!--{$arrForm.search_blog_url|escape}-->" size="30" class="box30" /></td>
    </tr>
    <tr>
      <th>状態</th>
      <td>
      <select name="search_status" style="<!--{$arrErr.search_status|sfGetErrorColor}-->">
      <option value="">----</option>
      <!--{html_options options=$arrTrackBackStatus selected=$arrForm.search_status}-->
      </select>
      </td>
    </tr>
    <tr>
      <th>商品名</th>
      <td><input type="text" name="search_name" value="<!--{$arrForm.search_name|escape}-->" size="30" class="box30" /></td>
    </tr>
    <tr>
      <th>商品コード</th>
      <td><input type="text" name="search_product_code" value="<!--{$arrForm.search_product_code|escape}-->" size="30" class="box30" /></td>
    </tr>
    <tr>
      <th>投稿日</th>
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
  </table>

  <div>
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


<!--{if $smarty.post.mode == 'search'}-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
  <input type="hidden" name="mode" value="search" />
  <input type="hidden" name="trackback_id" value="" />
  <input type="hidden" name="search_pageno" value="<!--{$tpl_pageno}-->" />
  <!--{foreach key=key item=item from=$arrHidden}-->
  <!--{if $key ne "search_pageno"}-->
  <input type="hidden" name="<!--{$key}-->" value="<!--{$item}-->" />
  <!--{/if}-->
  <!--{/foreach}-->

  <h2>検索結果一覧</h2>
  <p>
    <span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
    <!--{if $smarty.const.ADMIN_MODE == '1'}-->
    <button type="button" onclick="fnModeSubmit('delete_all','','');"><span>検索結果をすべて削除</span></button>
    <!--{/if}-->
  </p>
  <div class="btn">
    <button type="button" onclick="fnModeSubmit('csv','','');" ><span>CSV DOWNLOAD</span></button>
  </div>
  <!--{include file=$tpl_pager}-->
  
  <!--{ if $arrTrackback > 0 & $tpl_linemax > 0 }-->
  <!--{* 検索結果表示テーブル *}-->
  <table class="list" id="products-trackback-result">
    <tr>
      <th>投稿日</th>
      <th>商品名</th>
      <th>ブログ名</th>
      <th>ブログ記事タイトル</th>
      <th>状態</th>
      <th>編集</th>
      <th>削除</th>
    </tr>
    
    <!--{section name=cnt loop=$arrTrackback}-->
    <tr>
      <td><!--{$arrTrackback[cnt].create_date|sfDispDBDate}--></td>
      <td><!--{$arrTrackback[cnt].name|escape}--></td>
      <td><a href="<!--{$arrTrackback[cnt].url|escape}-->"><!--{$arrTrackback[cnt].blog_name|escape}--></a></td>
      <td><!--{$arrTrackback[cnt].title|escape}--></td>
      <td><!--{if $arrTrackback[cnt].status eq 1}-->表示<!--{elseif $arrTrackback[cnt].status eq 2}-->非表示<!--{elseif $arrTrackback[cnt].status eq 3}-->スパム<!--{/if}--></td>
      <td><button type="button" onclick="fnChangeAction('./trackback_edit.php'); fnModeSubmit('','trackback_id','<!--{$arrTrackback[cnt].trackback_id}-->');"><span>編集</span></button></td>
      <td><button type="button" onclick="fnModeSubmit('delete','trackback_id','<!--{$arrTrackback[cnt].trackback_id}-->'); return false;"><span>削除</span></button></td>
    </tr>
    <!--{/section}-->
  </table>
  <!--{* 検索結果表示テーブル *}-->
  <!--{ /if }-->
</form>
<!--{ /if }-->
<!--★★検索結果一覧★★-->    
