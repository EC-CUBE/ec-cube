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

<div class="contents">
    <div class="message">
        <h2>必要なファイルのコピー</h2>
    </div>
    <div class="result-info01">
        <textarea name="disp_area" cols="50" rows="20" class="box470"><!--{$copy_mess}--></textarea>
    </div>
    <form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="mode" value="<!--{$tpl_mode}-->" />
    <input type="hidden" name="step" value="0" />

    <!--{foreach key=key item=item from=$arrHidden}-->
        <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
    <!--{/foreach}-->
    <div class="result-info02">
        <!--{if $hasErr}-->
            <p class="action-message">[次へ進む] をクリックすると、チェックを再実行します。</p>
            <div><input type="checkbox" name="mode_overwrite" value="step0" id="mode_overwrite" /> <label for="mode_overwrite">問題点を無視して次へ進む (上級者向け)</label></div>
            <div class="attention">※ 問題点を解決せずに無視して進めると、トラブルの原因となる場合があります。</div>
        <!--{else}-->
            <p class="action-message">必要なファイルのコピーを開始します。</p>
        <!--{/if}-->
    </div>

    <div class="btn-area-top"></div>
    <div class="btn-area">
        <ul>
            <li><a href="#" class="btn-action" onclick="document.form1['mode'].value='return_step0';document.form1.submit();return false;"><span class="btn-prev">前へ戻る</span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="document.form1.submit(); return false;"><span class="btn-next">次へ進む</span></a></li>
        </ul>
    </div>
    <div class="btn-area-bottom"></div>
    </form>
</div>
