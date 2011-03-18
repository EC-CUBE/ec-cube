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



<!--▼ BEGIN FOOTER-->
<div id="footer">
<div id="footer-info">
<ul id="footer-menu">
<li><a href="<!--{$smarty.const.CART_URLPATH|h}-->">カゴの中を見る</a></li>
<li><a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/login.php">MYページ</a></li>
<li><a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/entry/kiyaku.php">新規会員登録</a></li>

<!--{php}-->
$tmp = $this->get_template_vars('tpl_mainpage');
if(preg_match("/index\.tpl$/", $tmp))
$this->assign('isTop', 1);
<!--{/php}-->

</ul>

<span class="footB">
<a href="<!--{$smarty.const.HTTP_URL}-->abouts/<!--{$smarty.const.DIR_INDEX_PATH|h}-->">当サイトについて</a>│
<a href="<!--{$smarty.const.HTTPS_URL}-->contact/<!--{$smarty.const.DIR_INDEX_PATH|h}-->">お問い合わせ</a><br>
<a href="<!--{$smarty.const.HTTP_URL}-->order/<!--{$smarty.const.DIR_INDEX_PATH|h}-->">特定商取引に関する表記</a>│
<a href="<!--{$smarty.const.HTTP_URL}-->guide/privacy.php">プライバシーポリシー</a>
</span>

<div id="copyright">(C) <!--{$arrSiteInfo.shop_name|h}-->.</div>

</div>
</div>
<!--▲ END FOOTER-->
