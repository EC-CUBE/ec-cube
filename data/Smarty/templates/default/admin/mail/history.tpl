<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2008 LOCKON CO.,LTD. All Rights Reserved.
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
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="search_pageno" value="" />
<input type="hidden" name="mode" value="" />
<div id="mail" class="contents-main">
  <table class="list center">
    <tr>
      <th>配信開始時刻</th>
      <th>配信終了時刻</th>
      <th>Subject</th>
      <th>プレビュー</th>
      <th>配信条件</th>
      <th>配信予定件数</th>
      <th>配信件数</th>
      <th>削除</th>
    </tr>
    <!--{section name=cnt loop=$arrDataList}-->
    <tr>
      <td><!--{$arrDataList[cnt].start_date|sfDispDBDate|escape}--></td>
      <td><!--{$arrDataList[cnt].end_date|sfDispDBDate|escape}--></td>
      <td class="left"><!--{$arrDataList[cnt].subject|escape}--></td>
      <td><a href="./preview.php?send_id=<!--{$arrDataList[cnt].send_id|escape}-->" target="_blank">確認</a></td>
      <td><a href="#" onclick="win03('./<!--{$smarty.const.DIR_INDEX_URL}-->?mode=query&amp;send_id=<!--{$arrDataList[cnt].send_id|escape}-->','query','720','420'); return false;">確認</a></td>
      <td><!--{$arrDataList[cnt].send_count|escape}--></td>
      <td><!--{$arrDataList[cnt].complete_count|escape}--></td>
      <td><a href="<!--{$smarty.server.PHP_SELF|escape}-->?mode=delete&send_id=<!--{$arrDataList[cnt].send_id|escape}-->" onclick="return window.confirm('配信履歴を削除しても宜しいでしょうか');">削除</a></td>
    </tr>
    <!--{/section}-->
  </table>
</div>
</form>
