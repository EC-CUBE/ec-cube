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
<li<!--{if $tpl_subno == 'index'}--> class="on"<!--{/if}--> id="navi-contents-index"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->contents/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span><!--{t string="tpl_198"}--></span></a></li>
<li<!--{if $tpl_subno == 'recommend'}--> class="on"<!--{/if}--> id="navi-contents-recommend"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->contents/recommend.php"><span><!--{t string="tpl_199"}--></span></a></li>
<li<!--{if $tpl_subno == 'file'}--> class="on"<!--{/if}--> id="navi-contents-file"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->contents/file_manager.php"><span><!--{t string="tpl_200"}--></span></a></li>
<li<!--{if $tpl_subno == 'csv'}--> class="on"<!--{/if}--> class="on_level2" id="navi-contents-csv"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->contents/csv.php"><span><!--{t string="tpl_201"}--></span></a>
    <ul id="navi-csv-sub" class="level2">
    <li<!--{if $tpl_subno_csv == 'product'}--> class="on"<!--{/if}--> id="navi-csv-product"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->contents/csv.php?tpl_subno_csv=product"><span><!--{t string="tpl_202"}--></span></a></li>
    <li<!--{if $tpl_subno_csv == 'customer'}--> class="on"<!--{/if}--> id="navi-csv-customer"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->contents/csv.php?tpl_subno_csv=customer"><span><!--{t string="tpl_203"}--></span></a></li>
    <li<!--{if $tpl_subno_csv == 'order'}--> class="on"<!--{/if}--> id="navi-csv-order"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->contents/csv.php?tpl_subno_csv=order"><span><!--{t string="tpl_204"}--></span></a></li>
    <li<!--{if $tpl_subno_csv == 'category'}--> class="on"<!--{/if}--> id="navi-csv-category"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->contents/csv.php?tpl_subno_csv=category"><span><!--{t string="tpl_191"}--></span></a></li>
    <li<!--{if $tpl_subno_csv == 'review'}--> class="on"<!--{/if}--> id="navi-csv-review"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->contents/csv.php?tpl_subno_csv=review"><span><!--{t string="tpl_205"}--></span></a></li>
    <li<!--{if $tpl_subno_csv == 'csv_sql'}--> class="on"<!--{/if}--> id="navi-csv-sql"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->contents/csv_sql.php"><span><!--{t string="tpl_206"}--></span></a></li>
    </ul>
</li>
</ul>
