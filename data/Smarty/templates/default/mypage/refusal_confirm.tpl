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

<div id="mypagecolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <!--{include file=$tpl_navi}-->
    <form name="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="complete" />

    <div id="mycontents_area">
        <h3><!--{$tpl_subtitle|h}--></h3>
        <div id="complete_area">
            <div class="message">退会手続きを実行してもよろしいでしょうか？</div>
            <div class="message_area">
                <div class="btn_area">
                    <p>退会手続きが完了した時点で、現在保存されている購入履歴や、<br />
                    お届け先等の情報はすべてなくなりますのでご注意ください。</p>
                    <ul>
                        <li>
                            <a href="./refusal.php" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_refuse_not_on.jpg','refuse_not');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_refuse_not.jpg','refuse_not');"><img src="<!--{$TPL_URLPATH}-->img/button/btn_refuse_not.jpg" alt="いいえ、退会しません" name="refuse_not" id="refuse_not" /></a>
                        </li>
                        <li>
                            <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_refuse_do_on.jpg',this);" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_refuse_do.jpg',this);" src="<!--{$TPL_URLPATH}-->img/button/btn_refuse_do.jpg" alt="はい、退会します" name="refuse_do" id="refuse_do" />
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>
