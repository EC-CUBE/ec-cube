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

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="update" />
<div id="basis" class="contents-main">
    <p class="remark attention">
        パラメーターの値がPHP定数として設定されます。<br />
        文字列は「&quot;」で囲んで下さい。<br />
        設定値によってはサイトが機能しなくなる場合もありますので、十分ご注意下さい。
    </p>

    <table class="list">
        <tr>
            <th>定数名</th>
            <th>パラメーター値</th>
        </tr>
        <!--{section name=cnt loop=$arrKeys}-->
            <tr>
                <th class="column">
                <!--{$arrKeys[cnt]|h}-->
                </th>
                <td>
                    <div style="font-size: 80%; color: #666666"><!--{$arrComments[cnt]|h}--></div>
                    <div>
                        <!--{assign var=key value=$arrKeys[cnt]}-->
                        <input type="text" name="<!--{$arrKeys[cnt]|h}-->" value="<!--{$arrValues[cnt]|h}-->" style="width: 370px; <!--{if $arrErr.$key != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
                        <!--{if $arrErr.$key}-->
                        <span class="attention"><!--{$arrErr.$key}--></span>
                        <!--{/if}-->
                    </div>
                </td>
            </tr>
        <!--{/section}-->
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'update', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
        </ul>
    </div>
</div>
</form>
