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

<section id="mypagecolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <!--{include file=$tpl_navi}-->

    <h3 class="title_mypage"><!--{$tpl_subtitle|h}--></h3>

    <!--★インフォメーション★-->
    <div class="intro">
        <p>入力内容をご確認ください。</p>
    </div>

    <form name="form1" id="form1" method="post" action="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/change.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="complete" />
        <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|h}-->" />
        <!--{foreach from=$arrForm key=key item=item}-->
            <!--{if $key ne "mode" && $key ne "subm"}-->
                <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
            <!--{/if}-->
        <!--{/foreach}-->

        <dl class="form_entry">
            <dt>お名前</dt>
            <dd><!--{$arrForm.name01|h}-->&nbsp;<!--{$arrForm.name02|h}--></dd>

            <dt>お名前(フリガナ)</dt>
            <dd><!--{$arrForm.kana01|h}-->&nbsp;<!--{$arrForm.kana02|h}--></dd>

            <dt>住所</dt>
            <dd>
                〒<!--{$arrForm.zip01}-->-<!--{$arrForm.zip02}--><br />
                <!--{$arrPref[$arrForm.pref]}--><!--{$arrForm.addr01|h}--><!--{$arrForm.addr02|h}-->
            </dd>

            <dt>電話番号</dt>
            <dd><!--{$arrForm.tel01|h}-->-<!--{$arrForm.tel02}-->-<!--{$arrForm.tel03}--></dd>

            <dt>FAX</dt>
            <dd>
                <!--{if strlen($arrForm.fax01) > 0}-->
                    <!--{$arrForm.fax01}-->-<!--{$arrForm.fax02}-->-<!--{$arrForm.fax03}-->
                <!--{else}-->
                    未登録
                <!--{/if}-->
            </dd>

            <dt>メールアドレス</dt>
            <dd><a href="<!--{$arrForm.email|escape:'hex'}-->" rel="external"><!--{$arrForm.email|escape:'hexentity'}--></a></dd>

            <dt>携帯メールアドレス</dt>
            <dd>
                <!--{if strlen($arrForm.email_mobile) > 0}-->
                    <a href="<!--{$arrForm.email_mobile|escape:'hex'}-->" rel="external"><!--{$arrForm.email_mobile|escape:'hexentity'}--></a>
                <!--{else}-->
                    未登録
                <!--{/if}-->
            </dd>

            <dt>性別</dt>
            <dd><!--{$arrSex[$arrForm.sex]}--></dd>

            <dt>職業</dt>
            <dd><!--{$arrJob[$arrForm.job]|default:"未登録"|h}--></dd>

            <dt>生年月日</dt>
            <dd>
                <!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}-->
                    <!--{$arrForm.year|h}-->年<!--{$arrForm.month|h}-->月<!--{$arrForm.day|h}-->日
                <!--{else}-->
                    未登録
                <!--{/if}-->
            </dd>

            <dt>希望するパスワード</dt>
            <dd><!--{$passlen}--></dd>

            <dt>パスワードを忘れた時のヒント</dt>
            <dd>
                質問：&nbsp;<!--{$arrReminder[$arrForm.reminder]|h}--><br />
                答え：&nbsp;<!--{$arrForm.reminder_answer|h}-->
            </dd>

            <dt>メールマガジン送付について</dt>
            <dd><!--{$arrMAILMAGATYPE[$arrForm.mailmaga_flg]}--></dd>
        </dl>

        <div class="btn_area">
            <ul class="btn_btm">
                <li><input type="submit" value="完了ページへ" class="btn data-role-none" alt="完了ページへ" name="complete" id="complete" /></li>
                <li><a class="btn_back" href="Javascript:fnModeSubmit('return', '', '');" rel="external">戻る</a></li>
            </ul>
        </div>
    </form>
</section>

<!--▼検索バー -->
<section id="search_area">
    <form method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="search" />
        <input type="search" name="name" id="search" value="" placeholder="キーワードを入力" class="searchbox" >
    </form>
</section>
<!--▲検索バー -->
