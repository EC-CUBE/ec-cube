<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
<!--{if $tpl_mode|strlen == 0 || $arrErr|@count >= 1}-->
    <style type="text/css">
        .item {
            margin-top: 1em;
        }
    </style>
    <form name="form1" id="form1" method="get" action="?" onsubmit="return false;">
        <input type="hidden" name="mode" value="">
        <p>郵便番号CSVには <!--{$tpl_line|h}--> 行のデータがあります。</p>
        <p>郵便番号DBには <!--{$tpl_count_mtb_zip|h}--> 行のデータがあります。</p>
        <!--{if $tpl_count_mtb_zip == 0}-->
            <p class="attention">登録を行なってください。</p>
        <!--{elseif $tpl_line <> $tpl_count_mtb_zip}-->
            <p class="attention">行数に差異があります。登録に異常がある恐れがあります。</p>
        <!--{/if}-->

        <div style="margin: 1em 0;">
            <p>通常は、[自動登録] を利用してください。<br />
                ただし、タイムアウトしてしまう環境では、タイムアウトした後に [手動登録] を正常に完了するまで繰り返してください。</p>
        </div>

        <div class="item">
            <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('auto', '', '');">自動登録</a><br />
            全ての郵便番号を削除してから、登録しなおします。タイムアウトした場合、元の状態に戻ります。
        </div>
        <div class="item">
            <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('delete', '', '');">手動削除</a><br />
            全ての郵便番号を削除します。再登録するまで、住所自動入力は機能しなくなります。
        </div>
        <div class="item">
            <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('manual', '', '');">手動登録</a>
            開始行: <input type="text" name="startRowNum" value="<!--{$arrForm.startRowNum|default:$tpl_count_mtb_zip+1|h}-->" size="8"><span class="attention"><!--{$arrErr.startRowNum}--></span><br />
            指定した行数から郵便番号を登録します。タイムアウトした場合、直前まで登録されます。
        </div>
    </form>
<!--{else}-->
    <iframe src="?mode=<!--{$tpl_mode|h}-->&amp;exec=yes&amp;startRowNum=<!--{$arrForm.startRowNum|h}-->" name="progress" height="200" width="750" frameborder="0"></iframe>
<!--{/if}-->
