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
<!--▼ログインここから-->
<div id="block-login" class="block-side">
  <div class="create-box">
    <!--{if $tpl_login}-->
      <a href="<!--{$smarty.const.SMARTPHONE_SSL_URL|sfTrimURL}-->/mypage/login.php" class="spbtn">マイページ</a>
    <!--{else}-->
      <a href="<!--{$smarty.const.SMARTPHONE_SSL_URL|sfTrimURL}-->/entry/kiyaku.php" class="spbtn">会員登録</a>
      <a href="<!--{$smarty.const.SMARTPHONE_SSL_URL|sfTrimURL}-->/mypage/login.php" class="spbtn">ログイン</a>
    <!--{/if}-->
    <!--ログインフォーム-->
  </div>
</div>
<!--▲ログインここまで-->
