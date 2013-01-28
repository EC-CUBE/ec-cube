<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
    <li id="navi-ownersstore-index" class="<!--{if $tpl_subno == 'index'}-->on<!--{/if}-->">
        <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->ownersstore/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span><!--{t string="tpl_Plug-in management_01"}--></span></a></li>
    <li id="navi-ownersstore-module" class="<!--{if $tpl_subno == 'module'}-->on<!--{/if}-->">
        <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->ownersstore/module.php"><span><!--{t string="tpl_Module management_01"}--></span></a></li>
    <li id="navi-ownersstore-settings" class="<!--{if $tpl_subno == 'settings'}-->on<!--{/if}-->">
        <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->ownersstore/settings.php"><span><!--{t string="tpl_Authentication key settings_01"}--></span></a></li>
    <li id="navi-ownersstore-log" class="<!--{if $tpl_subno == 'log'}-->on<!--{/if}-->">
        <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->ownersstore/log.php"><span><!--{t string="tpl_Log management_01"}--></span></a></li>
</ul>
