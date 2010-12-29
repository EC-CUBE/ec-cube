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
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`admin_popup_header.tpl"}-->

<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();
//-->
</script>
</head>


<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="search" />
<div id="mail" class="contents-main">
  <h2>配信条件</h2>

  <table class="form">
    <tr>
      <th>顧客名</th>
      <td><!--{$list_data.name|default:"(未指定)"|h}--></td>
    </tr>
    <tr>
      <th>顧客名(カナ)</th>
      <td width="249"><!--{$list_data.kana|default:"(未指定)"|h}--></td>
    </tr>
    <tr>
      <th>都道府県</th>
      <td><!--{$list_data.pref_disp|default:"(未指定)"}--></td>
    </tr>
    <tr>
      <th>TEL</th>
      <td width="249"><!--{$list_data.tel|default:"(未指定)"|h}--></td>
    </tr>
    <tr>
      <th>性別</th>
      <td><!--{$list_data.sex_disp|default:"(未指定)"}--></td>
    </tr>
    <tr>
      <th>誕生月</th>
      <td width="249"><!--{if $list_data.birth_month}--><!--{$list_data.birth_month|h}-->月<!--{else}-->(未指定)<!--{/if}--></td>        
    </tr>
    <tr>
      <th>配信形式</th>
      <td><!--{$list_data.htmlmail_disp|default:"(未指定)"|h}--></td>
    </tr>
    <tr>
      <th>購入回数</th>
      <td>
        <!--{if $list_data.buy_times_from == null}-->(未指定)<!--{else}--><!--{$list_data.buy_times_from|h}-->回<!--{/if}--> ～ 
        <!--{if $list_data.buy_times_to == null}-->(未指定)<!--{else}--><!--{$list_data.buy_times_to|h}-->回<!--{/if}-->
      </td>
    </tr>
    <!--{*非会員は選択できない
    <tr>
      <th>種別</th>
      <td>
      <!--{$list_data.customer|default:"すべて"|h}-->
      </td>
    </tr>
    *}-->
    <tr>
      <th>購入商品コード</th>
      <td><!--{$list_data.buy_product_code|default:"(未指定)"|h}--></td>
    </tr>
    <tr>
      <th>購入金額</th>
      <td>
        <!--{if $list_data.buy_total_from == null}-->(未指定)<!--{else}--><!--{$list_data.buy_total_from|h}-->円<!--{/if}--> ～ 
        <!--{if $list_data.buy_total_to == null}-->(未指定)<!--{else}--><!--{$list_data.buy_total_to|h}-->円<!--{/if}-->
      </td>
    </tr>
    <tr>
      <th>メールアドレス</th>
      <td><!--{$list_data.email|default:"(未指定)"|h}--></td>
    </tr>
    <tr>
      <th>職業</th>
      <td><!--{$list_data.job_disp|default:"(未指定)"|h}--></td>
    </tr>
    <tr>
      <th>生年月日</th>
      <td>
      <!--{if $list_data.b_start_year}-->
        <!--{$list_data.b_start_year}-->年<!--{$list_data.b_start_month}-->月<!--{$list_data.b_start_day}-->日&nbsp;?&nbsp;<!--{$list_data.b_end_year}-->年<!--{$list_data.b_end_month}-->月<!--{$list_data.b_end_day}-->日
      <!--{else}-->(未指定)<!--{/if}-->
      </td>
    </tr>  
    <tr>
      <th>登録日</th>
      <td>
      <!--{if $list_data.start_year}-->
        <!--{$list_data.start_year}-->年<!--{$list_data.start_month}-->月<!--{$list_data.start_day}-->日&nbsp;?&nbsp;<!--{$list_data.end_year}-->年<!--{$list_data.end_month}-->月<!--{$list_data.end_day}-->日
      <!--{else}-->(未指定)<!--{/if}-->
      </td>
    </tr>      
    <tr>
      <th>最終購入日</th>
      <td>
      <!--{if $list_data.buy_start_year}-->
        <!--{$list_data.buy_start_year}-->年<!--{$list_data.buy_start_month}-->月<!--{$list_data.buy_start_day}-->日&nbsp;?&nbsp;<!--{$list_data.buy_end_year}-->年<!--{$list_data.buy_end_month}-->月<!--{$list_data.buy_end_day}-->日
      <!--{else}-->(未指定)<!--{/if}-->  
      </td>
    </tr>
    <tr>
      <th>購入商品名</th>
      <td><!--{$list_data.buy_product_name|default:"(未指定)"|h}--></td>
    </tr>
    <tr>
      <th>カテゴリ</th>
      <td><!--{$list_data.category_name|default:"(未指定)"|h}--></td>
    </tr>
  </table>

  <div class="btn">
    <a class="btn-normal" href="javascript:;" onclick="window.close();"><span>ウインドウを閉じる</span></a>
  </div>
</div>
</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`admin_popup_footer.tpl"}-->
