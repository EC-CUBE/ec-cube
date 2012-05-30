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
<li<!--{if $tpl_subno == 'index'}--> class="on"<!--{/if}--> id="navi-basis-index"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>SHOPマスター</span></a></li>
<li<!--{if $tpl_subno == 'tradelaw'}--> class="on"<!--{/if}--> id="navi-basis-tradelaw"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/tradelaw.php"><span>特定商取引法</span></a></li>
<li<!--{if $tpl_subno == 'delivery'}--> class="on"<!--{/if}--> id="navi-basis-delivery"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/delivery.php"><span>配送方法設定</span></a></li>
<li<!--{if $tpl_subno == 'payment'}--> class="on"<!--{/if}--> id="navi-basis-payment"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/payment.php"><span>支払方法設定</span></a></li>
<li<!--{if $tpl_subno == 'point'}--> class="on"<!--{/if}--> id="navi-basis-point"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/point.php"><span>ポイント設定</span></a></li>
<li<!--{if $tpl_subno == 'mail'}--> class="on"<!--{/if}--> id="navi-basis-mail"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/mail.php"><span>メール設定</span></a></li>
<li<!--{if $tpl_subno == 'seo'}--> class="on"<!--{/if}--> id="navi-basis-seo"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/seo.php"><span>SEO管理</span></a></li>
<li<!--{if $tpl_subno == 'kiyaku'}--> class="on"<!--{/if}--> id="navi-basis-kiyaku"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/kiyaku.php"><span>会員規約設定</span></a></li>
<li<!--{if $tpl_subno == 'zip_install'}--> class="on"<!--{/if}--> id="navi-basis-zip"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/zip_install.php"><span>郵便番号DB登録</span></a></li>
<li<!--{if $tpl_subno == 'holiday'}--> class="on"<!--{/if}--> id="navi-basis-holiday"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/holiday.php"><span>定休日管理</span></a></li>
</ul>
