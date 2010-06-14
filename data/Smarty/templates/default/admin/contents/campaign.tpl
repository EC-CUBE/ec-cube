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
<!--
// カートに商品を入れるにチェックが入っているかチェック
function fnIsCartOn(){
    if (document.form1.cart_flg.checked <!--{if $is_update}-->|| <!--{$arrForm.cart_flg}--><!--{/if}-->){
    document.form1.deliv_free_flg.disabled = false;
    } else {
    document.form1.deliv_free_flg.disabled = true;    
    }
}
//-->
</script>

<div id="admin-contents" class="contents-main">
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="campaign_id" value="<!--{$campaign_id}-->" />
<input type="hidden" name="is_update" value="<!--{$is_update}-->" />
<!--{if $is_update}-->
<input type="hidden" name="cart_flg" value="<!--{$arrForm.cart_flg}-->" />
<!--{/if}-->
  <h2>キャンペーンページ登録</h2>
  <!--{if $arrErr.campaign_template_path || $arrErr.campaign_path}-->
  <div class="message">
    <span class="attention"><!--{$arrErr.campaign_template_path}--><!--{$arrErr.campaign_path}--></span>
  </div>
  <!--{/if}-->

  <table class="form">
    <tr>
      <th>キャンペーン名<span class="attention"> *</span></td>
      <td>
        <!--{if $arrErr.campaign_name}--><span class="attention"><!--{$arrErr.campaign_name}--></span><!--{/if}-->
        <input type="text" name="campaign_name" size="60" class="box60" value="<!--{$arrForm.campaign_name|escape}-->" <!--{if $arrErr.campaign_name}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}--> maxlength="<!--{$smarty.const.STEXT_LEN}-->" />
      </td>
    </tr>
    <tr>
      <th>キャンペーン期間<span class="attention"> *</span></td>
      <td>
        <!--{if $arrErr.start_year || $arrErr.start_month || $arrErr.start_day}--><span class="attention"><!--{$arrErr.start_year}--><!--{$arrErr.start_month}--><!--{$arrErr.start_day}--></span><!--{/if}-->
        開始日時：
        <select name="start_year" <!--{if $arrErr.start_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
          <option value="" selected>----</option>
          <!--{html_options options=$arrYear selected=$arrForm.start_year}-->
        </select>年
        <select name="start_month" <!--{if $arrErr.start_month || $arrErr.start_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
          <option value="" selected>--</option>
          <!--{html_options options=$arrMonth selected=$arrForm.start_month}-->
        </select>月
        <select name="start_day" <!--{if $arrErr.start_day || $arrErr.start_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
          <option value="" selected>--</option>
          <!--{html_options options=$arrDay selected=$arrForm.start_day}-->
        </select>日
        <select name="start_hour" <!--{if $arrErr.start_hour || $arrErr.start_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
          <option value="" selected>--</option>
          <!--{html_options options=$arrHour selected=$arrForm.start_hour}-->
        </select>時
        <select name="start_minute" <!--{if $arrErr.start_minute || $arrErr.start_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
          <option value="" selected>--</option>
          <!--{html_options options=$arrMinutes selected=$arrForm.start_minute}-->
        </select>分<br />
        <span class="attention"><!--{$arrErr.end_year}--><!--{$arrErr.end_month}--><!--{$arrErr.end_day}--></span>  
        停止日時：
        <select name="end_year" <!--{if $arrErr.end_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
          <option value="" selected>----</option>
          <!--{html_options options=$arrYear selected=$arrForm.end_year}-->
        </select>年
        <select name="end_month" <!--{if $arrErr.end_month || $arrErr.end_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
          <option value="" selected>--</option>
          <!--{html_options options=$arrMonth selected=$arrForm.end_month}-->
        </select>月
        <select name="end_day" <!--{if $arrErr.end_day || $arrErr.end_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
          <option value="" selected>--</option>
          <!--{html_options options=$arrDay selected=$arrForm.end_day}-->
        </select>日
        <select name="end_hour" <!--{if $arrErr.end_hour || $arrErr.end_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
          <option value="" selected>--</option>
          <!--{html_options options=$arrHour selected=$arrForm.end_hour}-->
        </select>時
        <select name="end_minute" <!--{if $arrErr.end_minute || $arrErr.end_year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
          <option value="" selected>--</option>
          <!--{html_options options=$arrMinutes selected=$arrForm.end_minute}-->
        </select>分
      </td>
    </tr>
    <tr>
      <th>ディレクトリ名<span class="attention"> *</span></td>
      <td>
        <!--{if $arrErr.directory_name}--><span class="attention"><!--{$arrErr.directory_name}--></span><!--{/if}-->
        <input type="text" name="directory_name" size="60" class="box60"  value="<!--{$arrForm.directory_name|escape}-->" <!--{if $arrErr.directory_name}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}--> maxlength="<!--{$smarty.const.STEXT_LEN}-->"/></span><br/>
        <span>※<!--{$smarty.const.SITE_URL}--><!--{$smarty.const.CAMPAIGN_DIR}-->入力したディレクリ名/ でアクセス出来るようになります。</span>
      </td>
    </tr>
    <tr>
      <th>申込数制御</td>
      <td>
        <!--{if $arrErr.limit_count}--><span class="attention"><!--{$arrErr.limit_count}--></span><!--{/if}-->
        <input type="text" name="limit_count" size="54" class="box6"  value="<!--{$arrForm.limit_count|escape}-->" <!--{if $arrErr.limit_count}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}--> maxlength="<!--{$smarty.const.STEXT_LEN}-->"/>&nbsp;件で終了ページに切り替え
      </td>
    </tr>
    <tr>
      <th>重複申込制御</td>
      <td><input type="checkbox" name="orverlapping_flg" id="orverlapping_flg" value="1" <!--{if $arrForm.orverlapping_flg eq 1}--> checked <!--{/if}--> ><label for="orverlapping_flg">重複申込を制御する</label></td>
    </tr>
    <tr>
      <th>カートに商品を入れる</td>
      <td><input type="checkbox" onclick="fnIsCartOn()" name="cart_flg" id="cart_flg" value="1" <!--{if $arrForm.cart_flg eq 1}--> checked <!--{/if}--> <!--{if $is_update}-->disabled<!--{/if}-->><label for="cart_flg">カートに商品を入れるようにする</label></td>
    </tr>
    <tr>
      <th>送料無料設定</td>
      <td><input type="checkbox" name="deliv_free_flg" id="deliv_free_flg" value="1" <!--{if $arrForm.deliv_free_flg eq 1}--> checked <!--{/if}--> ><label for="deliv_free_flg">送料無料</label></td>
    </tr>
  </table>
  <!--▲登録テーブルここまで-->

  <div class="btn"><button type="button" onclick="fnFormModeSubmit('form1', 'regist', '', '');"><span>この内容で登録する</span></button></div>


  <h2>キャンペーン一覧</h2>

  <table class="list">
    <tr>
      <th rowspan="2">キャンペーン名</th>
      <th rowspan="2">申込人数</th>
      <th colspan="2">デザイン設定</th>
      <th rowspan="2">編集</th>
      <th rowspan="2">削除</th>
      <th rowspan="2">CSV</th>
    </tr>
    <tr>
      <th>キャンペーン中</th>
      <th>キャンペーン終了</th>
    </tr>
    <!--{section name=cnt loop=$arrCampaign}-->
    <tr class="center">
      <td><!--{$arrCampaign[cnt].campaign_name}--></td>
      <td><!--{$arrCampaign[cnt].total_count}--></td>
      <td><a href="<!--{$smarty.const.URL_CAMPAIGN_DESIGN}-->?campaign_id=<!--{$arrCampaign[cnt].campaign_id}-->&status=active">設定</a></td>
      <td><a href="<!--{$smarty.const.URL_CAMPAIGN_DESIGN}-->?campaign_id=<!--{$arrCampaign[cnt].campaign_id}-->&status=end">設定</a></td>
      <!--{if $arrCampaign[cnt].campaign_id != $arrForm.campaign_id}-->
      <td><a href="javascript:fnFormModeSubmit('form1', 'update', 'campaign_id', '<!--{$arrCampaign[cnt].campaign_id}-->')">編集</a></td>
      <!--{else}-->
      <td>編集</td>
      <!--{/if}-->
      <td><a href="javascript:fnFormModeSubmit('form1', 'delete', 'campaign_id', '<!--{$arrCampaign[cnt].campaign_id}-->')">削除</a></td>
      <td><a href="javascript:fnFormModeSubmit('form1', 'csv', 'campaign_id', '<!--{$arrCampaign[cnt].campaign_id}-->')">CSV</a></td>
    </tr>
    <!--{/section}-->
  </table>

</div>
</form>
