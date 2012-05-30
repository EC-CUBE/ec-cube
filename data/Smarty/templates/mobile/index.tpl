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

<!--{strip}-->
    <!-- ▼メニュー ここから -->
    <a href="products/search.php" accesskey="2"><!--{2|numeric_emoji}-->商品検索</a><br>
    <!--{if $isLogin eq true}-->
        <a href="<!--{$smarty.const.HTTPS_URL}-->mypage/refusal.php?<!--{$smarty.const.SID}-->" accesskey="3"><!--{3|numeric_emoji}-->会員退会</a><br>
    <!--{else}-->
        <a href="<!--{$smarty.const.HTTPS_URL}-->entry/kiyaku.php?<!--{$smarty.const.SID}-->" accesskey="3"><!--{3|numeric_emoji}-->新規会員登録</a><br>
    <!--{/if}-->
    <a href="guide/<!--{$smarty.const.DIR_INDEX_PATH}-->" accesskey="4"><!--{4|numeric_emoji}-->ご利用ガイド</a><br>
    <a href="contact/<!--{$smarty.const.DIR_INDEX_PATH}-->" accesskey="5"><!--{5|numeric_emoji}-->お問い合わせ</a><br>
    <a href="order/<!--{$smarty.const.DIR_INDEX_PATH}-->" accesskey="6"><!--{6|numeric_emoji}-->特定商取引に関する表記</a>
    <!-- ▲メニュー ここまで -->
<!--{/strip}-->
