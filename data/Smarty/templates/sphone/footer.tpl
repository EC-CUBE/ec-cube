<!--{*
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
 *}-->

<!--▼ BEGIN PAGETOP-->
<div id="pagetop">
<a href="#top">↑このページのトップへ</a>
</div>
<!--▲ END PAGETOP-->

<!--▼ BEGIN FOOTER-->
<div id="footer">
<div id="footer-info">
<ul id="footer-menu">
<li>
<a href="<!--{$smarty.const.SMARTPHONE_SSL_URL|sfTrimURL}-->/cart/index.php">かごを見る</a>
</li>
<li>
<a href="<!--{$smarty.const.SMARTPHONE_SSL_URL|sfTrimURL}-->/mypage/login.php">マイページ</a>
</li>
<li>
<a href="<!--{$smarty.const.SMARTPHONE_SSL_URL|sfTrimURL}-->/entry/kiyaku.php">会員登録</a>
</li>

<!--{php}-->
$tmp = $this->get_template_vars('tpl_mainpage');
if(preg_match("/top\.tpl$/", $tmp))
$this->assign('isTop', 1);
<!--{/php}-->
<!--{if $isTop ne 1}-->
<li>
<a href="<!--{$smarty.const.SMARTPHONE_SITE_URL|sfTrimURL}-->">TOPページへ</a>
</li>
<!--{/if}-->

</ul>
<ul id="footer-navi">
<li><a href="<!--{$smarty.const.SMARTPHONE_SITE_URL}-->contact/index.php">お問合せ</a></li>
<li><a href="<!--{$smarty.const.SMARTPHONE_SITE_URL}-->guide/privacy.php">プライバシーポリシー</a></li>
<li class="end"><a href="<!--{$smarty.const.SMARTPHONE_SITE_URL}-->abouts/index.php">店舗情報</a></li>
<li class="end"><a href="<!--{$smarty.const.SMARTPHONE_SITE_URL}-->order/index.php">特定商取引法に基づく表記</a></li>
</ul>

<div id="copyright">(C) <!--{$arrSiteInfo.shop_name|escape}-->.</div>

</div>
</div>
<!--▲ END FOOTER-->
