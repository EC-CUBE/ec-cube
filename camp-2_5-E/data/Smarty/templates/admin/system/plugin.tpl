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
      <th>名前</th>
      <th>パス</th>
      <th>有効/無効</th>
      <th>設定</th>
    </tr>
    <!--{section name=data loop=$plugins}-->
    <!--▼メンバー<!--{$smarty.section.data.iteration}-->-->
    <tr>
      <td><!--{$plugins[data].plugin_name|escape}--></td>
      <td><!--{$plugins[data].plugin_name|escape}--></td>
      <td>
        <!--{if $plugins[data].create_date == null }-->
          <input type="button" name="install" value="install" />
        <!--{else}-->
          <!--{if $plugins[data].enable == 1}-->
          <input type="button" name="disable" value="disable" />
          <!--{else}-->
          <input type="button" name="enable" value="enable" /> 
          <!--{endif}-->
        <!--{endif}-->
      </td>
      <td></td>
      
      <td align="center"><!--{if $plugins[data].work eq 1}--><input type="radio" name="radio<!--{$smarty.section.data.iteration}-->" value="稼動" onclick="fnChangeRadio(this.name, 1, <!--{$plugins[data].member_id}-->, <!--{$tpl_disppage}-->);" checked /><!--{else}--><input type="radio" name="radio<!--{$smarty.section.data.iteration}-->" value="稼動" onclick="fnChangeRadio(this.name, 1, <!--{$plugins[data].member_id}-->, <!--{$tpl_disppage}-->);"/><!--{/if}--></td>
      <td align="center"><!--{if $plugins[data].work eq 0}--><input type="radio" name="radio<!--{$smarty.section.data.iteration}-->" value="非稼動"  onclick="fnChangeRadio(this.name, 0, <!--{$plugins[data].member_id}-->, <!--{$tpl_disppage}-->);" checked /><!--{else}--><input type="radio" name="radio<!--{$smarty.section.data.iteration}-->" value="非稼動" onclick="fnChangeRadio(this.name, 0, <!--{$plugins[data].member_id}-->, <!--{$tpl_disppage}-->);" <!--{if $workmax <= 1 }-->disabled<!--{/if}-->  /><!--{/if}--></td>
      <td align="center"><a href="#" onClick="win01('./input.php?id=<!--{$plugins[data].member_id}-->&amp;pageno=<!--{$tpl_disppage}-->','member_edit','500','420'); return false;">編集</a></td>
      <td align="center"><!--{if $workmax > 1 }--><a href="#" onClick="fnDeleteMember(<!--{$plugins[data].member_id}-->,<!--{$tpl_disppage}-->); return false;">削除</a><!--{else}-->-<!--{/if}--></td>
      <td align="center">
      <!--{$tpl_nomove}-->
      <!--{if !($smarty.section.data.first && $tpl_disppage eq 1) }--><a href="./rank.php?id=<!--{$plugins[data].member_id}-->&move=up&pageno=<!--{$tpl_disppage}-->">上へ</a><!--{/if}-->
      <!--{if !($smarty.section.data.last && $tpl_disppage eq $tpl_pagemax) }--><a href="./rank.php?id=<!--{$plugins[data].member_id}-->&move=down&pageno=<!--{$tpl_disppage}-->">下へ</a><!--{/if}-->
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

</div>
</form>
