<!--{*
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
 *}-->

<section id="undercolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>

    <!--★インフォメーション★-->
    <div class="information end">
        <p>入力内容をご確認ください。</p>
    </div>

    <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="complete">
        <!--{foreach from=$arrForm key=key item=item}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item.value|h}-->" />
        <!--{/foreach}-->

        <dl class="form_entry">

            <dt>お名前</dt>
            <dd><!--{$arrForm.name01.value|h}-->&nbsp;<!--{$arrForm.name02.value|h}--></dd>

            <dt>お名前(フリガナ)</dt>
            <dd><!--{$arrForm.kana01.value|h}-->&nbsp;<!--{$arrForm.kana02.value|h}--></dd>

            <dt>住所</dt>
            <dd>
                〒<!--{$arrForm.zip01.value|h}--> - <!--{$arrForm.zip02.value|h}--><br />
                <!--{$arrPref[$arrForm.pref.value]|h}--><!--{$arrForm.addr01.value|h}--><!--{$arrForm.addr02.value|h}-->
            </dd>

            <dt>電話番号</dt>
            <dd><!--{$arrForm.tel01.value|h}--> - <!--{$arrForm.tel02.value|h}--> - <!--{$arrForm.tel03.value|h}--></dd>

            <dt>FAX</dt>
            <dd>
                <!--{if strlen($arrForm.fax01.value) > 0 && strlen($arrForm.fax02.value) > 0 && strlen($arrForm.fax03.value) > 0}-->
                    <!--{$arrForm.fax01.value|h}--> - <!--{$arrForm.fax02.value|h}--> - <!--{$arrForm.fax03.value|h}-->
                <!--{else}-->
                    未登録
                <!--{/if}-->
            </dd>

            <dt>メールアドレス</dt>
            <dd><a href="mailto:<!--{$arrForm.email.value|escape:'hex'}-->" rel="external"><!--{$arrForm.email.value|escape:'hexentity'}--></a></dd>

            <dt>性別</dt>
            <dd>
                <!--{if $arrForm.sex.value eq 1}-->
                    男性
                <!--{else}-->
                    女性
                <!--{/if}-->
            </dd>

            <dt>職業</dt>
            <dd><!--{$arrJob[$arrForm.job.value]|default:"未登録"|h}--></dd>

            <dt>生年月日</dt>
            <dd>
                <!--{if strlen($arrForm.year.value) > 0 && strlen($arrForm.month.value) > 0 && strlen($arrForm.day.value) > 0}-->
                    <!--{$arrForm.year.value|h}-->年<!--{$arrForm.month.value|h}-->月<!--{$arrForm.day.value|h}-->日
                <!--{else}-->
                    未登録
                <!--{/if}-->
            </dd>

            <dt>希望するパスワード</dt>
            <dd><!--{$passlen}--></dd>

            <dt>パスワードを忘れた時のヒント</dt>
            <dd>
                質問：<!--{$arrReminder[$arrForm.reminder.value]|h}--><br />
                答え：<!--{$arrForm.reminder_answer.value|h}-->
            </dd>

            <dt>メールマガジン送付について</dt>
            <dd>
                <!--{if $arrForm.mailmaga_flg.value eq 1}-->
                    HTMLメール＋テキストメールを受け取る
                <!--{elseif $arrForm.mailmaga_flg.value eq 2}-->
                    テキストメールを受け取る
                <!--{else}-->
                    受け取らない
                <!--{/if}-->
            </dd>
        </dl>

        <!--★ボタン★-->
        <div class="btn_area">
            <ul class="btn_btm">
                <li><input type="submit" value="完了ページへ" class="btn data-role-none" name="send" id="send" /></li>
                <li><a href="#" onclick="fnModeSubmit('return', '', ''); return false;" class="btn_back">戻る</a></li>
            </ul>
        </div>
    </form>
</section>

<!--{include file= 'frontparts/search_area.tpl'}-->

