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
?<form name="form1" id="form1" method="post" action="?" >
<input type="hidden" name="mode" value="complete" />
<!--{foreach key=key item=item from=$arrTrackback}-->
<!--{if $key ne "mode"}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/if}-->
<!--{/foreach}-->
<!--{foreach key=key item=item from=$arrSearchHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->

<div id="products" class="contents-main">
  <h2>トラックバック設定</h2>

<!--▼編集テーブルここから-->
<table>
  <tr>
    <th>商品名</th>
    <td><!--{$arrTrackback.name|h}--></td>
  </tr>
  <tr>
    <th>ブログ名</th>
    <td><!--{$arrTrackback.blog_name|h}--></td>
  </tr>
  <tr>
    <th>ブログ記事タイトル</th>
    <td><!--{$arrTrackback.title|h}--></td>
  </tr>
  <tr>
    <th>ブログ記事内容</th>
    <td><!--{$arrTrackback.excerpt|h}--></td>
  </tr>
  <tr>
    <th>ブログURL</th>
    <td><!--{$arrTrackback.url|h}--></td>
  </tr>
  <tr>
    <th>投稿日</th>
    <td><!--{$arrTrackback.create_date|sfDispDBDate}--></td>
  </tr>
  <tr>
    <th>状態</th>
    <td>
    <!--{assign var=key value="status"}-->
    <span class="attention"><!--{$arrErr.status}--></span>
    <select name="<!--{$key}-->" style="<!--{$arrErr.status|sfGetErrorColor}-->" >
    <option value="">選択してください</option>
    <!--{html_options options=$arrTrackBackStatus selected=$arrTrackback[$key]}-->
    </select>
    </td>
  </tr>
</table>
<!--▲編集テーブルここまで-->
  <div class="btn">
    <a class="btn-normal" href="javascript:;" onclick="document.form1.action='./trackback.php'; fnModeSubmit('search','','');"><span>検索画面に戻る</span></a>
    <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('complete','','');"><span>この内容で登録する</span></a>
  </div>
</div>
</form>
