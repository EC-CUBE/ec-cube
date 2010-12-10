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
<ul class="level1">
  <li><a><span>PC</span></a>
    <ul class="level2">
      <li<!--{if $tpl_subno == 'layout'}--> class="on"<!--{/if}--> id="navi-design-layout"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/<!--{$smarty.const.DIR_INDEX_URL}-->?device_type_id=<!--{$smarty.const.DEVICE_TYPE_PC}-->"><span>レイアウト設定</span></a></li>
      <li<!--{if $tpl_subno == 'main_edit'}--> class="on"<!--{/if}--> id="navi-design-main"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/main_edit.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_PC}-->"><span>ページ詳細設定</span></a></li>
      <li<!--{if $tpl_subno == 'bloc'}--> class="on"<!--{/if}--> id="navi-design-bloc"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/bloc.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_PC}-->"><span>ブロック編集</span></a></li>
      <li<!--{if $tpl_subno == 'header'}--> class="on"<!--{/if}--> id="navi-design-header"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/header.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_PC}-->"><span>ヘッダー/フッター設定</span></a></li>
      <li<!--{if $tpl_subno == 'css'}--> class="on"<!--{/if}--> id="navi-design-css"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/css.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_PC}-->"><span>CSS編集</span></a></li>
      <li<!--{if $tpl_subno == 'template'}--> class="on"<!--{/if}--> id="navi-design-template"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/template.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_PC}-->"><span>テンプレート設定</span></a></li>
      <li<!--{if $tpl_subno == 'up_down'}--> class="on"<!--{/if}--> id="navi-design-add"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/up_down.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_PC}-->"><span>テンプレート追加</span></a></li>
    </ul>
  </li>
  <li><a><span>モバイル</span></a>
    <ul class="level2">
      <li<!--{if $tpl_subno == 'layout'}--> class="on"<!--{/if}--> id="navi-design-layout"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/<!--{$smarty.const.DIR_INDEX_URL}-->?device_type_id=<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><span>レイアウト設定</span></a></li>
      <li<!--{if $tpl_subno == 'main_edit'}--> class="on"<!--{/if}--> id="navi-design-main"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/main_edit.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><span>ページ詳細設定</span></a></li>
      <li<!--{if $tpl_subno == 'bloc'}--> class="on"<!--{/if}--> id="navi-design-bloc"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/bloc.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><span>ブロック編集</span></a></li>
      <li<!--{if $tpl_subno == 'header'}--> class="on"<!--{/if}--> id="navi-design-header"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/header.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><span>ヘッダー/フッター設定</span></a></li>
      <li<!--{if $tpl_subno == 'css'}--> class="on"<!--{/if}--> id="navi-design-css"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/css.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><span>CSS編集</span></a></li>
      <li<!--{if $tpl_subno == 'template'}--> class="on"<!--{/if}--> id="navi-design-template"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/template.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><span>テンプレート設定</span></a></li>
      <li<!--{if $tpl_subno == 'up_down'}--> class="on"<!--{/if}--> id="navi-design-add"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/up_down.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><span>テンプレート追加</span></a></li>
    </ul>
  </li>
  <li><a><span>スマートフォン</span></a>
    <ul class="level2">
      <li<!--{if $tpl_subno == 'layout'}--> class="on"<!--{/if}--> id="navi-design-layout"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/<!--{$smarty.const.DIR_INDEX_URL}-->?device_type_id=<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><span>レイアウト設定</span></a></li>
      <li<!--{if $tpl_subno == 'main_edit'}--> class="on"<!--{/if}--> id="navi-design-main"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/main_edit.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><span>ページ詳細設定</span></a></li>
      <li<!--{if $tpl_subno == 'bloc'}--> class="on"<!--{/if}--> id="navi-design-bloc"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/bloc.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><span>ブロック設定</span></a></li>
      <li<!--{if $tpl_subno == 'header'}--> class="on"<!--{/if}--> id="navi-design-header"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/header.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><span>ヘッダー/フッター設定</span></a></li>
      <li<!--{if $tpl_subno == 'css'}--> class="on"<!--{/if}--> id="navi-design-css"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/css.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><span>CSS設定</span></a></li>
      <li<!--{if $tpl_subno == 'template'}--> class="on"<!--{/if}--> id="navi-design-template"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/template.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><span>テンプレート設定</span></a></li>
      <li<!--{if $tpl_subno == 'up_down'}--> class="on"<!--{/if}--> id="navi-design-add"><a href="<!--{$smarty.const.URL_DIR}-->admin/design/up_down.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><span>テンプレート追加</span></a></li>
    </ul>
  </li>
</ul>
