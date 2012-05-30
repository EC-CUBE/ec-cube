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
    <br>
    <br>
    <div align="right"><a href="#top">↑このページのトップへ</a></div>

    <hr>

    <a href="<!--{$smarty.const.HTTPS_URL}-->mypage/login.php?<!--{$smarty.const.SID}-->" accesskey="8" utn><!--{8|numeric_emoji}-->MYページ</a><br>
    <a href="<!--{$smarty.const.MOBILE_CART_URLPATH}-->" accesskey="9"><!--{9|numeric_emoji}-->かごの中を見る</a><br>
    <a href="<!--{$smarty.const.MOBILE_TOP_URLPATH}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>
    <br>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" bgcolor="#666666">
                <font color="#ffffff" size="-2">Copyright &copy;&nbsp;
                    <!--{if $smarty.const.RELEASE_YEAR !=  $smarty.now|date_format:"%Y"}-->
                        <!--{$smarty.const.RELEASE_YEAR}-->-
                    <!--{/if}-->
                    <!--{$smarty.now|date_format:"%Y"}--> <!--{$arrSiteInfo.shop_name_eng|default:$arrSiteInfo.shop_name|h}-->&nbsp;
                    All rights reserved.</font>
            </td>
        </tr>
    </table>
<!--{/strip}-->
