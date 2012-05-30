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
    <li id="navi-total-term"
        class="<!--{if ($tpl_mainno == 'total' && ($arrForm.page.value == 'term' || $arrForm.page.value == ''))}-->on<!--{/if}-->"
    ><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->total/<!--{$smarty.const.DIR_INDEX_PATH}-->?page=term"><span>期間別集計</span></a></li>
    <li id="navi-total-products"
        class="<!--{if ($tpl_mainno == 'total' && $arrForm.page.value == 'products')}-->on<!--{/if}-->"
    ><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->total/<!--{$smarty.const.DIR_INDEX_PATH}-->?page=products"><span>商品別集計</span></a></li>
    <li id="navi-total-age"
        class="<!--{if ($tpl_mainno == 'total' && $arrForm.page.value == 'age')}-->on<!--{/if}-->"
    ><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->total/<!--{$smarty.const.DIR_INDEX_PATH}-->?page=age"><span>年代別集計</span></a></li>
    <li id="navi-total-job"
        class="<!--{if ($tpl_mainno == 'total' && $arrForm.page.value == 'job')}-->on<!--{/if}-->"
    ><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->total/<!--{$smarty.const.DIR_INDEX_PATH}-->?page=job"><span>職業別集計</span></a></li>
    <li id="navi-total-member"
        class="<!--{if ($tpl_mainno == 'total' && $arrForm.page.value == 'member')}-->on<!--{/if}-->"
    ><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->total/<!--{$smarty.const.DIR_INDEX_PATH}-->?page=member"><span>会員別集計</span></a></li>
</ul>
