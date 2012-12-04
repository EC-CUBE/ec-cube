0<!--{*
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

<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data"">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="csv_upload" />
<div id="products" class="contents-main">
    <!--{if $tpl_errtitle != ""}-->
        <div class="message">
            <span class="attention"><!--{$tpl_errtitle}--></span><br />
            <!--{foreach key=key item=item from=$arrCSVErr}-->
                <span class="attention"><!--{$item}-->
                <!--{if $key != 'blank'}-->
                    <!--{t string="tpl_631" T_FIELD=$arrParam[$key]}-->
                <!--{/if}-->
                </span><br />
            <!--{/foreach}-->
        </div>
    <!--{/if}-->

    <!--▼登録テーブルここから-->
    <table>
        <tr>
            <th><!--{t string="tpl_632"}--></th>
            <td>
                <!--{if $arrErr.csv_file}-->
                    <span class="attention"><!--{$arrErr.csv_file}--></span>
                <!--{/if}-->
                <input type="file" name="csv_file" size="40" /><span class="attention"><!--{t string="tpl_633"}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_634"}--></th>
            <td>
                <!--{foreach name=title key=key item=item from=$arrTitle}-->
                    <!--{t string="tpl_635" T_FIELD1=$smarty.foreach.title.iteration T_FIELD2=$item}--><br />
                <!--{/foreach}-->
            </td>
        </tr>
    </table>
    <!--▲登録テーブルここまで-->
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'csv_upload', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_636"}--></span></a></li>
        </ul>
    </div>
</div>
</form>
