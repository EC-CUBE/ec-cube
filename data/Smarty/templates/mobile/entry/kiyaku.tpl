<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
    <!--{if $offset eq 1}-->
        ご注文に際して必要な内容（ご自宅住所等）をご登録していただきます。<br>
        ご利用の規約をよくお読みの上、ご登録ください。<br>
        <br>
        <hr>
    <!--{/if}-->
    <!--{$tpl_kiyaku_text|h|nl2br}--><br>

    <!--{if $max >= $offset+1}-->
        <a href="kiyaku.php?offset=<!--{$offset+1}-->">次へ→</a><br><br>
    <!--{/if}-->

    <a href="<!--{$smarty.const.HTTPS_URL}-->entry/<!--{$smarty.const.DIR_INDEX_PATH}-->?<!--{$smarty.const.SID}-->" accesskey="1"><!--{1|numeric_emoji}-->同意して登録へ</a><br>
    <a href="<!--{$smarty.const.TOP_URL}-->?<!--{$smarty.const.SID|h}-->" accesskey="2"><!--{2|numeric_emoji}-->同意しない</a>
<!--{/strip}-->
