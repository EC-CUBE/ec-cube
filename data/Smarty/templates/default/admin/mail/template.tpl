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
<form name="form1" id="form1" method="post" action="?">
<div id="mail" class="contents-main">
  <h2>テンプレート</h2>
  <table class="list center">
    <tr>
      <th>作成日</th>
      <th>subject</th>
      <th>メール形式</th>
      <th>編集</th>
      <th>削除</th>
      <th>プレビュー</th>
    </tr>
    <!--{section name=data loop=$list_data}-->
    <tr>
      <td><!--{$list_data[data].disp_date|escape|date_format:'%Y/%m/%d'}--></td>
      <td class="left"><!--{$list_data[data].subject|escape}--></td>
      <!--{assign var=type value=$list_data[data].mail_method|escape}-->
      <td><!--{$arrMagazineType[$type]}--></td>
      <td><!--{if $list_data[data].mail_method eq 3}--><a href="./htmlmail.php?mode=edit&template_id=<!--{$list_data[data].template_id}-->"><!--{else}--><a href="./template_input.php?mode=edit&template_id=<!--{$list_data[data].template_id}-->"><!--{/if}-->編集</a></td>
      <td><a href="" onclick="fnDelete('<!--{$smarty.server.PHP_SELF|escape}-->?mode=delete&id=<!--{$list_data[data].template_id}-->'); return false;">削除</a></td>
      <td><!--{if $list_data[data].mail_method eq 3}--><a href="" onclick="win03('./preview.php?method=template&id=<!--{$list_data[data].template_id}-->','preview','650','700'); return false;" target="_blank"><!--{else}--><a href="" onclick="win03('./preview.php?id=<!--{$list_data[data].template_id}-->','preview','650','700'); return false;" target="_blank"><!--{/if}-->プレビュー</a></td>
    </tr>
    <!--{/section}-->
  </table>

  <div class="btn addnew">
    <button type="button" onclick="location.href='./template_input.php'"><span>テンプレートを新規入力</span></button>
    <!-- ＨＴＭＬ作成ウィザードは保留 （次期開発）
    <button type="button" onclick="location.href='./htmlmail.php'"><span>HTMLテンプレート作成ウィザード</span></button>
    -->
  </div>
</div>
