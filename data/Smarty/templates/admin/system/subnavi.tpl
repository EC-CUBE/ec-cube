<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
<li<!--{if $tpl_subno == 'index'}--> class="on"<!--{/if}--> id="navi-system-index"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>メンバー管理</span></a></li>
<li<!--{if $tpl_subno == 'bkup'}--> class="on"<!--{/if}--> id="navi-system-bkup"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/bkup.php"><span>バックアップ管理</span></a></li>
<li<!--{if $tpl_subno == 'parameter'}--> class="on"<!--{/if}--> id="navi-system-parameter"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/parameter.php"><span>パラメーター設定</span></a></li>
<li<!--{if $tpl_subno == 'masterdata'}--> class="on"<!--{/if}--> id="navi-system-masterdata"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/masterdata.php"><span>マスターデータ管理</span></a></li>
<li<!--{if $tpl_subno == 'masterdata'}--> class="on"<!--{/if}--> id="navi-system-adminarea"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/adminarea.php"><span>管理画面設定</span></a></li>
<li<!--{if $tpl_subno == 'system'}--> class="on"<!--{/if}--> id="navi-system-system"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/system.php"><span>システム情報</span></a></li>
<li<!--{if $tpl_mainno == 'system' && $tpl_subno == 'log'}--> class="on"<!--{/if}--> id="navi-system-log"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/log.php"><span>EC-CUBE ログ表示</span></a></li>
<li<!--{if $tpl_mainno == 'system' && $tpl_subno == 'editdb'}--> class="on"<!--{/if}--> id="navi-system-editdb"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/editdb.php"><span>高度なデータベース管理</span></a></li>
</ul>
