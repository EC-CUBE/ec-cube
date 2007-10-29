<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
 *}-->
<!--▼リンクここから-->
<div id="guidearea">
  <ul>
    <!--{if $tpl_page_category != "abouts"}-->
    <li><a href="<!--{$smarty.const.URL_DIR}-->abouts/index.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/side/about_on.jpg','about');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/side/about.jpg','about');"><img src="<!--{$TPL_DIR}-->img/side/about.jpg" width="166" height="30" alt="当サイトについて" style="border: none" name="about" id="about" /></a></li>
    <!--{else}-->
    <li><a href="<!--{$smarty.const.URL_DIR}-->abouts/index.php"><img src="<!--{$TPL_DIR}-->img/side/about_on.jpg" width="166" height="30" alt="当サイトについて"  style="border: none" name="about" id="about" /></a></li>
    <!--{/if}-->

    <!--{if $tpl_page_category != "contact"}-->
    <li><a href="<!--{$smarty.const.URL_DIR}-->contact/index.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/side/contact_on.jpg','contact');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/side/contact.jpg','contact');"><img src="<!--{$TPL_DIR}-->img/side/contact.jpg" width="166" height="30" alt="お問い合わせ"  style="border: none" name="contact" id="contact" /></a></li>
    <!--{else}-->
    <li><a href="<!--{$smarty.const.URL_DIR}-->contact/index.php"><img src="<!--{$TPL_DIR}-->img/side/contact_on.jpg" width="166" height="30" alt="お問い合わせ" style="border: none" name="contact" id="contact" /></a></li>
    <!--{/if}-->

    <!--{if $tpl_page_category != "order"}-->
    <li><a href="<!--{$smarty.const.URL_DIR}-->order/index.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/side/low_on.jpg','low');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/side/low.jpg','low');"><img src="<!--{$TPL_DIR}-->img/side/low.jpg" width="166" height="30" alt="特定商取引に関する法律" style="border: none" name="low" id="low" /></a></li>
    <!--{else}-->
    <li><a href="<!--{$smarty.const.URL_DIR}-->order/index.php"><img src="<!--{$TPL_DIR}-->img/side/low_on.jpg" width="166" height="30" alt="特定商取引に関する法律" style="border: none" name="low" id="low" /></a></li>
    <!--{/if}-->
  </ul>
</div>
<!--▲リンクここまで-->
