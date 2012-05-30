<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can attentionistribute it and/or
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
<script type="text/javascript">
<!--
    // モードとキーを指定してSUBMITを行う。
    function fnModeSubmit(mode) {
        switch(mode) {
        case 'drop':
            if(!window.confirm('一度削除したデータは、元に戻せません。\n削除しても宜しいですか？')){
                return;
            }
            break;
        default:
            break;
        }
        document.form1['mode'].value = mode;
        document.form1.submit();
    }
//-->
</script>

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->" />
<input type="hidden" name="step" value="0" />

<!--{foreach key=key item=item from=$arrHidden}-->
    <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->

<div class="contents">
    <div class="message">
        <h2>データベースの初期化</h2>
    </div>
    <div class="result-info02">
    <p class="action-message">
        <!--{if $tpl_db_version != ""}--><span class="bold">接続情報：</span><br />
            <!--{$tpl_db_version}-->
        <!--{/if}-->
        データベースの初期化を開始します。<br />
        ※すでにテーブル等が作成されている場合は中断されます。</P>
        <!--{if $tpl_mode != 'complete'}-->
            <input type="checkbox" id="skip" name="db_skip" <!--{if $tpl_db_skip == "on"}-->checked="checked"<!--{/if}--> /> <label for="skip">データベースの初期化処理を行わない</label>
        <!--{/if}-->
    </div>
    <div class="result-info02">
        <!--{if count($arrErr) > 0 || $tpl_message != ""}-->
            <!--{$tpl_message}--><br />
            <span class="attention top"><!--{$arrErr.all}--></span>
            <!--{if $arrErr.all != ""}-->
                <ul class="btn-area">
                    <li><a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('drop'); return false;">既存データをすべて削除する</a></li>
                </ul>
            <!--{/if}-->
        <!--{/if}-->
    </div>
</div>

<div class="btn-area-top"></div>
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="document.form1['mode'].value='return_step2';document.form1.submit();return false;"><span class="btn-prev">前へ戻る</span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="document.body.style.cursor='wait'; document.form1.submit(); return false;"><span class="btn-next">次へ進む</span></a></li>
        </ul>
    </div>
    <div class="btn-area-bottom"></div>
</form>
