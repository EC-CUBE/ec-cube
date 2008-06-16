<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2008 LOCKON CO.,LTD. All Rights Reserved.
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
<ul id="navi-total" class="level1">
<li<!--{if ($arrForm.page.value == 'term' || $arrForm.page.value == '')}--> class="on"<!--{/if}--> id="navi-total-term"><a href="<!--{$smarty.const.URL_DIR}-->admin/total/index.php?page=term"><span>期間別集計</span></a></li>
<li<!--{if ($arrForm.page.value == 'products')}--> class="on"<!--{/if}--> id="navi-total-products"><a href="<!--{$smarty.const.URL_DIR}-->admin/total/index.php?page=products"><span>商品別集計</span></a></li>
<li<!--{if ($arrForm.page.value == 'age')}--> class="on"<!--{/if}--> id="navi-total-age"><a href="<!--{$smarty.const.URL_DIR}-->admin/total/index.php?page=age"><span>年代別集計</span></a></li>
<li<!--{if ($arrForm.page.value == 'job')}--> class="on"<!--{/if}--> id="navi-total-job"><a href="<!--{$smarty.const.URL_DIR}-->admin/total/index.php?page=job"><span>職業別集計</span></a></li>
<li<!--{if ($arrForm.page.value == 'member')}--> class="on"<!--{/if}--> id="navi-total-member"><a href="<!--{$smarty.const.URL_DIR}-->admin/total/index.php?page=member"><span>会員別集計</span></a></li>
</ul>