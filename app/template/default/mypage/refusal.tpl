<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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
<article id="article_mypage" class="undercolumn">
    <h1 class="title"><!--{$tpl_title|h}--></h1>
    <!--{include file=$tpl_navi}-->
    <div id="mycontents_area">
        <h2><!--{$tpl_subtitle|h}--></h2>
        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="confirm" />
            <div id="complete_area">
                <div class="message">会員を退会された場合には、現在保存されている購入履歴や、<br />
                お届け先などの情報は、全て削除されますがよろしいでしょうか？</div>
                <div class="message_area">
                    <p>退会手続きが完了した時点で、現在保存されている購入履歴や、<br />
                    お届け先等の情報は全てなくなりますのでご注意ください。</p>
                    <div class="btn_area">
                        <ul>
                            <li>
                                <input type="submit" value="会員退会を行う" class="btn btn-success" name="refusal" id="refusal" />
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</article>
