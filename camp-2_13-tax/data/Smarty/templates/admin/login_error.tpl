<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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

<div id="outside">
    <div id="out-wrap">
            <div class="logo">
                <img src="<!--{$TPL_URLPATH}-->img/contents/logo_resize.jpg" width="99" height="15" alt="EC-CUBE" />
            </div>
        <div id="error">
            <div class="out-top"></div>
            <div class="contents">
                <div class="message">
                    <!--{$tpl_error}-->
                </div>
            </div>
            <div class="btn-area-top"></div>
            <div class="btn-area">
                <ul>
                    <li>
                        <a class="btn-action" href="<!--{$smarty.const.ADMIN_LOGIN_URLPATH}-->"><span class="btn-prev">ログインページに戻る</span></a>
                    </li>
                </ul>
            </div>
            <div class="btn-area-bottom"></div>
        </div>
    </div>
</div>

