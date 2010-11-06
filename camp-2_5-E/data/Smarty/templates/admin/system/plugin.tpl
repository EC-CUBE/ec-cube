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
          <!--{/if}-->
          <input type="button" name="uninstall" value="uninstall">
        <!--{/if}-->
      </td>
      <td>
        <input type="button" name="preference" value="preference" />
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
