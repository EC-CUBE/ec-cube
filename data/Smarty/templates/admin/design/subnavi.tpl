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
    <li class="on_level2"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/<!--{$smarty.const.DIR_INDEX_PATH}-->?device_type_id=<!--{$smarty.const.DEVICE_TYPE_PC}-->"><span><!--{t string="tpl_PC_01"}--></span></a>
        <ul class="level2">
            <li<!--{if $tpl_subno == 'layout'}--> class="on"<!--{/if}--> id="navi-design-layout-<!--{$smarty.const.DEVICE_TYPE_PC}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/<!--{$smarty.const.DIR_INDEX_PATH}-->?device_type_id=<!--{$smarty.const.DEVICE_TYPE_PC}-->"><span><!--{t string="tpl_Layout settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'main_edit'}--> class="on"<!--{/if}--> id="navi-design-main-<!--{$smarty.const.DEVICE_TYPE_PC}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/main_edit.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_PC}-->"><span><!--{t string="tpl_Page detail settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'bloc'}--> class="on"<!--{/if}--> id="navi-design-bloc-<!--{$smarty.const.DEVICE_TYPE_PC}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/bloc.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_PC}-->"><span><!--{t string="tpl_Block settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'header'}--> class="on"<!--{/if}--> id="navi-design-header-<!--{$smarty.const.DEVICE_TYPE_PC}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/header.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_PC}-->"><span><!--{t string="tpl_Header/footer settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'css'}--> class="on"<!--{/if}--> id="navi-design-css-<!--{$smarty.const.DEVICE_TYPE_PC}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/css.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_PC}-->"><span><!--{t string="tpl_CSS settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'template'}--> class="on"<!--{/if}--> id="navi-design-template-<!--{$smarty.const.DEVICE_TYPE_PC}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/template.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_PC}-->"><span><!--{t string="tpl_Template settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'up_down'}--> class="on"<!--{/if}--> id="navi-design-add-<!--{$smarty.const.DEVICE_TYPE_PC}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/up_down.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_PC}-->"><span><!--{t string="tpl_Template addition_01"}--></span></a></li>
        </ul>
    </li>
<!--{if $smarty.const.USE_MOBILE !== false}-->
    <li class="on_level2"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/<!--{$smarty.const.DIR_INDEX_PATH}-->?device_type_id=<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><span><!--{t string="tpl_Mobile_01"}--></span></a>
        <ul class="level2">
            <li<!--{if $tpl_subno == 'layout'}--> class="on"<!--{/if}--> id="navi-design-layout-<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/<!--{$smarty.const.DIR_INDEX_PATH}-->?device_type_id=<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><span><!--{t string="tpl_Layout settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'main_edit'}--> class="on"<!--{/if}--> id="navi-design-main-<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/main_edit.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><span><!--{t string="tpl_Page detail settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'bloc'}--> class="on"<!--{/if}--> id="navi-design-bloc-<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/bloc.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><span><!--{t string="tpl_Block settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'header'}--> class="on"<!--{/if}--> id="navi-design-header-<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/header.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><span><!--{t string="tpl_Header/footer settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'css'}--> class="on"<!--{/if}--> id="navi-design-css-<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/css.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><span><!--{t string="tpl_CSS settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'template'}--> class="on"<!--{/if}--> id="navi-design-template-<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/template.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><span><!--{t string="tpl_Template settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'up_down'}--> class="on"<!--{/if}--> id="navi-design-add-<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/up_down.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"><span><!--{t string="tpl_Template addition_01"}--></span></a></li>
        </ul>
    </li>
<!--{/if}-->
    <li class="on_level2"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/<!--{$smarty.const.DIR_INDEX_PATH}-->?device_type_id=<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><span><!--{t string="tpl_Smartphone_01"}--></span></a>
        <ul class="level2">
            <li<!--{if $tpl_subno == 'layout'}--> class="on"<!--{/if}--> id="navi-design-layout-<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/<!--{$smarty.const.DIR_INDEX_PATH}-->?device_type_id=<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><span><!--{t string="tpl_Layout settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'main_edit'}--> class="on"<!--{/if}--> id="navi-design-main-<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/main_edit.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><span><!--{t string="tpl_Page detail settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'bloc'}--> class="on"<!--{/if}--> id="navi-design-bloc-<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/bloc.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><span><!--{t string="tpl_Block settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'header'}--> class="on"<!--{/if}--> id="navi-design-header-<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/header.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><span><!--{t string="tpl_Header/footer settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'css'}--> class="on"<!--{/if}--> id="navi-design-css-<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/css.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><span><!--{t string="tpl_CSS settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'template'}--> class="on"<!--{/if}--> id="navi-design-template-<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/template.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><span><!--{t string="tpl_Template settings_01"}--></span></a></li>
            <li<!--{if $tpl_subno == 'up_down'}--> class="on"<!--{/if}--> id="navi-design-add-<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/up_down.php?device_type_id=<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"><span><!--{t string="tpl_Template addition_01"}--></span></a></li>
        </ul>
    </li>
</ul>
