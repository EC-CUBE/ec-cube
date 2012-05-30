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

<div id="design" class="contents-main">

    <!--{if $arrErr.err != ""}-->
        <div class="message">
            <span class="attention"><!--{$arrErr.err}--></span>
        </div>
    <!--{/if}-->

    <!--{* ▼ヘッダー編集ここから *}-->
    <h2>ヘッダー編集</h2>
    <form name="form_header" id="form_header" method="post" action="?" >
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="" />
    <input type="hidden" name="division" value="header" />
    <input type="hidden" name="header_row" value="<!--{$header_row}-->" />
    <input type="hidden" name="device_type_id" value="<!--{$device_type_id|h}-->" />

        <textarea id="header-area" class="top" name="header" rows="<!--{$header_row}-->" style="width: 100%;"><!--{$header_data|h|smarty:nodefaults}--></textarea>
        <div class="btn">
            <a id="header-area-resize-btn" class="btn-normal" href="javascript:;" onclick="ChangeSize('#header-area-resize-btn', '#header-area', 50, 13); $('input[name=header_row]').val($('#header-area').attr('rows'));return false;"><span>拡大</span></a>
        </div>

        <div class="btn-area">
                <ul>
                    <li><a class="btn-action" href="javascript:;" name='subm' onclick="fnFormModeSubmit('form_header','regist','',''); return false;"><span class="btn-next">登録する</span></a></li>
                </ul>
        </div>

    </form>
    <!--{* ▲ヘッダー編集ここまで *}-->

    <!--{* ▼フッター編集ここから *}-->
    <h2>フッター編集</h2>
    <form name="form_footer" id="form_footer" method="post" action="?" >
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="" />
    <input type="hidden" name="division" value="footer" />
    <input type="hidden" name="footer_row" value=<!--{$footer_row}--> />
    <input type="hidden" name="device_type_id" value="<!--{$device_type_id|h}-->" />

        <textarea id="footer-area" class="top" name="footer" rows="<!--{$footer_row}-->" style="width: 100%;"><!--{$footer_data|h|smarty:nodefaults}--></textarea>
        <div class="btn">
            <a id="footer-area-resize-btn" class="btn-normal" href="javascript:;" onclick="ChangeSize('#footer-area-resize-btn', '#footer-area', 50, 13); $('input[name=footer_row]').val($('#footer-area').attr('rows'));return false;"><span>拡大</span></a>
        </div>

        <div class="btn-area">
                <ul>
                    <li><a class="btn-action" href="javascript:;" name='subm' onclick="fnFormModeSubmit('form_footer','regist','',''); return false;"><span class="btn-next">登録する</span></a></li>
                </ul>
        </div>

    </form>
    <!--{* ▲フッター編集ここまで *}-->
</div>
