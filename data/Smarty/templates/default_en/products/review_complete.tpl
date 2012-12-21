<!--{*
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
 *}-->

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="Form for customer's opinion (completion page)"}-->

<div id="window_area">
    <h2 class="title">Form for customer's opinion</h2>
    <div id="completebox">
        <p class="message">Registration is complete. Thank you.</p>
        <p>After confirming the registered contents at our company, we will then reflect the contents on our home page.<br />
           Please wait awhile.</p>
    </div>
    <div class="btn_area">
        <ul>
            <li><a href="javascript:window.close()" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_close_on.jpg','b_close');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_close.jpg','b_close');">
                <img src="<!--{$TPL_URLPATH}-->img/button/btn_close.jpg" alt="Close" border="0" name="b_close" /></a></li>
        </ul>
    </div>
</div>

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->
