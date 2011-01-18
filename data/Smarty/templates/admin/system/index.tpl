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
<form name="form1" id="form1" method="post" action="">
<div id="system" class="contents-main">
  <div class="paging">
    <!--▼ページ送り-->
    <!--{$tpl_strnavi}-->
    <!--▲ページ送り-->
  </div>
  
  <!--▼メンバー一覧ここから-->
  <table class="list">
    <tr>
      <th>権限</th>
      <th>名前</th>
      <th>所属</th>
      <th>稼動</th>
      <th>非稼動</th>
      <th class="edit">編集</th>
      <th class="delete">削除</th>
      <th>移動</th>
    </tr>
    <!--{section name=data loop=$list_data}--><!--▼メンバー<!--{$smarty.section.data.iteration}-->-->
    <tr>
      <!--{assign var="auth" value=$list_data[data].authority}--><td><!--{$arrAUTHORITY[$auth]|h}--></td>
      <td><!--{$list_data[data].name|h}--></td>
      <td><!--{$list_data[data].department|h}--></td>
      <td align="center"><!--{if $list_data[data].work eq 1}--><input type="radio" name="radio<!--{$smarty.section.data.iteration}-->" value="稼動" onclick="fnChangeRadio(this.name, 1, <!--{$list_data[data].member_id}-->, <!--{$tpl_disppage}-->);" checked /><!--{else}--><input type="radio" name="radio<!--{$smarty.section.data.iteration}-->" value="稼動" onclick="fnChangeRadio(this.name, 1, <!--{$list_data[data].member_id}-->, <!--{$tpl_disppage}-->);"/><!--{/if}--></td>
      <td align="center"><!--{if $list_data[data].work eq 0}--><input type="radio" name="radio<!--{$smarty.section.data.iteration}-->" value="非稼動"  onclick="fnChangeRadio(this.name, 0, <!--{$list_data[data].member_id}-->, <!--{$tpl_disppage}-->);" checked /><!--{else}--><input type="radio" name="radio<!--{$smarty.section.data.iteration}-->" value="非稼動" onclick="fnChangeRadio(this.name, 0, <!--{$list_data[data].member_id}-->, <!--{$tpl_disppage}-->);" <!--{if $workmax <= 1 }-->disabled<!--{/if}-->  /><!--{/if}--></td>
      <td align="center"><a href="#" onClick="win01('./input.php?id=<!--{$list_data[data].member_id}-->&amp;pageno=<!--{$tpl_disppage}-->','member_edit','600','420'); return false;">編集</a></td>
      <td align="center"><!--{if $workmax > 1 }--><a href="#" onClick="fnDeleteMember(<!--{$list_data[data].member_id}-->,<!--{$tpl_disppage}-->); return false;">削除</a><!--{else}-->-<!--{/if}--></td>
      <td align="center">
      <!--{$tpl_nomove}-->
      <!--{if !($smarty.section.data.first && $tpl_disppage eq 1) }--><a href="./rank.php?id=<!--{$list_data[data].member_id}-->&move=up&pageno=<!--{$tpl_disppage}-->">上へ</a><!--{/if}-->
      <!--{if !($smarty.section.data.last && $tpl_disppage eq $tpl_pagemax) }--><a href="./rank.php?id=<!--{$list_data[data].member_id}-->&move=down&pageno=<!--{$tpl_disppage}-->">下へ</a><!--{/if}-->
      </td>
    </tr>
    <!--▲メンバー<!--{$smarty.section.data.iteration}-->-->
    <!--{/section}-->
  </table>

  <div class="paging">
    <!--▼ページ送り-->
    <!--{$tpl_strnavi}-->
    <!--▲ページ送り-->
  </div>

  <div class="btn addnew">
    <a class="btn-normal" href="javascript:;" onclick="win01('./input.php','input','600','420'); return false;"><span>メンバーを新規入力</span></a>
  </div>
</div>
</form>
