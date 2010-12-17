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
    <li<!--{if $tpl_subno == 'index'}--> class="on"<!--{/if}--> id="navi-plugin-index"><a href="<!--{$smarty.const.URL_DIR}--><!--{$smarty.const.ADMIN_DIR}-->plugin/<!--{$smarty.const.DIR_INDEX_URL}-->"><span>プラグイン管理</span></a></li>
    <!--{foreach from=$smarty.env.pluginsXml->plugin item="plugin"}-->
        <li<!--{if $tpl_subno == $plugin->path}--> class="on"<!--{/if}--> id="navi-plugin-<!--{$plugin->path|escape}-->"><a href="<!--{$smarty.const.PLUGIN_URL}--><!--{$plugin->path|escape}-->/<!--{$smarty.const.ADMIN_DIR}--><!--{$smarty.const.DIR_INDEX_URL}-->"><span><!--{$plugin->name|escape}--></span></a></li>
    <!--{/foreach}-->
</ul>
