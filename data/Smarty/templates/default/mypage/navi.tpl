<!--{*
/*
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA    02111-1307, USA.
 */
*}-->

<div id="mynavarea">
    <!--{strip}-->
    <ul class="button_like">
        <li>
            <!--{if $tpl_mypageno == 'index'}-->
                <a href="./index.php"><img src="<!--{$TPL_DIR}-->img/mypage/navi01_on.jpg" width="170" height="30" alt="購入履歴一覧" border="0" name="m_navi01" /></a>
            <!--{else}-->
                <a href="./index.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/mypage/navi01_on.jpg','m_navi01');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/mypage/navi01.jpg','m_navi01');"><img src="<!--{$TPL_DIR}-->img/mypage/navi01.jpg" width="170" height="30" alt="購入履歴一覧" border="0" name="m_navi01" /></a>
            <!--{/if}-->
        </li>
        <!--{if $smarty.const.OPTION_FAVOFITE_PRODUCT == 1}-->
            <li>
                <!--{if $tpl_mypageno == 'favorite'}-->
                    <a href="./favorite.php"><img src="<!--{$TPL_DIR}-->img/mypage/navi05_on.jpg" width="170" height="30" alt="お気に入り商品一覧" border="0" name="m_navi05" /></a>
                <!--{else}-->
                    <a href="./favorite.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/mypage/navi05_on.jpg','m_navi05');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/mypage/navi05.jpg','m_navi05');"><img src="<!--{$TPL_DIR}-->img/mypage/navi05.jpg" width="170" height="30" alt="お気に入り商品一覧" border="0" name="m_navi05" /></a>
                <!--{/if}-->
            </li>
        <!--{/if}-->
        <li>
            <!--{if $tpl_mypageno == 'change'}-->
                <a href="./change.php"><img src="<!--{$TPL_DIR}-->img/mypage/navi02_on.jpg" width="170" height="30" alt="会員登録内容変更" border="0" name="m_navi02" /></a>
            <!--{else}-->
                <a href="./change.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/mypage/navi02_on.jpg','m_navi02');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/mypage/navi02.jpg','m_navi02');"><img src="<!--{$TPL_DIR}-->img/mypage/navi02.jpg" width="170" height="30" alt="会員登録内容変更" border="0" name="m_navi02" /></a>
            <!--{/if}-->
        </li>
        <li>
            <!--{if $tpl_mypageno == 'delivery'}-->
                <a href="./delivery.php"><img src="<!--{$TPL_DIR}-->img/mypage/navi03_on.jpg" width="170" height="30" alt="お届け先追加・変更" border="0" name="m_navi03" /></a>
            <!--{else}-->
                <a href="./delivery.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/mypage/navi03_on.jpg','m_navi03');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/mypage/navi03.jpg','m_navi03');"><img src="<!--{$TPL_DIR}-->img/mypage/navi03.jpg" width="170" height="30" alt="お届け先追加・変更" border="0" name="m_navi03" /></a>
            <!--{/if}-->
        </li>
        <li>
            <!--{if $tpl_mypageno == 'refusal'}-->
                <a href="./refusal.php"><img src="<!--{$TPL_DIR}-->img/mypage/navi04_on.jpg" width="170" height="30" alt="退会手続き" border="0" name="m_navi04" /></a>
            <!--{else}-->
                <a href="./refusal.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/mypage/navi04_on.jpg','m_navi04');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/mypage/navi04.jpg','m_navi04');"><img src="<!--{$TPL_DIR}-->img/mypage/navi04.jpg" width="170" height="30" alt="退会手続き" border="0" name="m_navi04" /></a>
            <!--{/if}-->
        </li>
    </ul>
    <!--{/strip}-->
    <!--▼現在のポイント-->
    <!--{if $point_disp !== false}-->
        <ul>
             <li>ようこそ <br />
                 <!--{$CustomerName1|escape}--> <!--{$CustomerName2|escape}-->様
                 <!--{if $smarty.const.USE_POINT !== false}-->
                     <br />現在の所持ポイントは<em><!--{$CustomerPoint|number_format|escape|default:"0"}-->pt</em> です。
                 <!--{/if}-->
             </li>
        </ul>
    <!--{/if}-->
    <!--▲現在のポイント-->
</div>
<!--▲NAVI-->
