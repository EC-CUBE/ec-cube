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

<script>
    $(function() {
        $('#contents')
            .css('font-size', '100%')
            .autoResizeTextAreaQ({
                'max_rows': 50,
                'extra_rows': 0
            });
    });
</script>
<section id="undercolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <div class="intro">
        <p>ご意見やご質問をお受けしております。<br />
            休業日は翌営業日以降のご返信となりますのでご了承ください。</p>
    </div>

    <form name="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />

        <dl class="form_entry">
            <dt>お名前&nbsp;<span class="attention">※</span></dt>
            <dd>
                <span class="attention"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
                <input type="text" name="name01"
                    value="<!--{$arrForm.name01.value|default:$arrData.name01|h}-->"
                    maxlength="<!--{$smarty.const.STEXT_LEN}-->"
                    style="<!--{$arrErr.name01|sfGetErrorColor}-->" class="boxHarf text data-role-none" placeholder="姓" />&nbsp;&nbsp;
                <input type="text" name="name02"
                    value="<!--{$arrForm.name02.value|default:$arrData.name02|h}-->"
                    maxlength="<!--{$smarty.const.STEXT_LEN}-->"
                    style="<!--{$arrErr.name02|sfGetErrorColor}-->" class="boxHarf text data-role-none" placeholder="名" />
            </dd>

            <dt>お名前(フリガナ)&nbsp;<span class="attention">※</span></dt>
            <dd>
                <span class="attention"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>
                <input type="text" name="kana01"
                    value="<!--{$arrForm.kana01.value|default:$arrData.kana01|h}-->"
                    maxlength="<!--{$smarty.const.STEXT_LEN}-->"
                    style="<!--{$arrErr.kana01|sfGetErrorColor}-->"class="boxHarf text data-role-none" placeholder="セイ"/>&nbsp;&nbsp;
                <input type="text" name="kana02"
                    value="<!--{$arrForm.kana02.value|default:$arrData.kana02|h}-->"
                    maxlength="<!--{$smarty.const.STEXT_LEN}-->"
                    style="<!--{$arrErr.kana02|sfGetErrorColor}-->" class="boxHarf text data-role-none" placeholder="メイ"/>
            </dd>

            <dt>郵便番号</dt>
            <dd>
                <!--{* <!--{assign var=key1 value="`$prefix`zip01"}--> *}-->
                <!--{* <!--{assign var=key2 value="`$prefix`zip02"}--> *}-->
                <!--{assign var=key1 value="`$prefix`zipcode"}-->
                <!--{assign var=key3 value="`$prefix`pref"}-->
                <!--{assign var=key4 value="`$prefix`addr01"}-->
                <!--{* <span class="attention"><!--{$arrErr.zip01}--><!--{$arrErr.zip02}--></span> *}-->
                <span class="attention"><!--{$arrErr.zipcode}--></span>
                <p>
                    <!--{*
                    <input type="tel" name="zip01"
                        value="<!--{$arrForm.zip01.value|default:$arrData.zip01|h}-->"
                        max="<!--{$smarty.const.ZIP01_LEN}-->"
                        style="<!--{$arrErr.zip01|sfGetErrorColor}-->; ime-mode: disabled;" class="boxShort text data-role-none" />&nbsp;－&nbsp;<input type="tel" name="zip02"
                        value="<!--{$arrForm.zip02.value|default:$arrData.zip02|h}-->"
                        max="<!--{$smarty.const.ZIP02_LEN}-->"
                        style="<!--{$arrErr.zip02|sfGetErrorColor}-->; ime-mode: disabled;" class="boxShort text data-role-none" />&nbsp;&nbsp;<a href="http://search.post.japanpost.jp/zipcode/" target="_blank" rel="external"><span class="fn">郵便番号検索</span></a>
                    *}-->
                    <input type="tel" name="zipcode"
                        value="<!--{$arrForm.zipcode.value|default:$arrData.zipcode|h}-->"
                        max="<!--{$smarty.const.ZIPCODE_LEN}-->"
                        style="<!--{$arrErr.zipcode|sfGetErrorColor}-->; ime-mode: disabled;" class="boxShort text data-role-none" />
                        &nbsp;&nbsp;<a href="http://search.post.japanpost.jp/zipcode/" target="_blank" rel="external"><span class="fn">郵便番号検索</span></a>
                </p>

                <!--{* <a href="javascript:fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', '<!--{$key1}-->', '<!--{$key2}-->', '<!--{$key3}-->', '<!--{$key4}-->');" class="btn_sub btn_inputzip" rel="external">郵便番号から住所自動入力</a> *}-->
            </dd>

            <dt>住所</dt>
            <dd>
                <span class="attention"><!--{$arrErr.pref}--><!--{$arrErr.addr01}--><!--{$arrErr.addr02}--></span>
                <select name="pref" style="<!--{$arrErr.pref|sfGetErrorColor}-->" class="boxHarf top data-role-none">
                    <option value="" selected="selected">都道府県</option>
                    <!--{html_options options=$arrPref selected=$arrForm.pref.value|default:$arrData.pref|h}-->
                </select>

                <input type="text" name="addr01"
                    value="<!--{$arrForm.addr01.value|default:$arrData.addr01|h}-->"
                    class="boxLong top text data-role-none"
                    style="<!--{$arrErr.addr01|sfGetErrorColor}-->" placeholder="市区町村名" />
                <input type="text" name="addr02"
                    value="<!--{$arrForm.addr02.value|default:$arrData.addr02|h}-->"
                    class="boxLong text data-role-none"
                    style="<!--{$arrErr.addr02|sfGetErrorColor}-->" placeholder="番地・ビル名" />
            </dd>

            <dt>電話番号</dt>
            <dd>
                <span class="attention"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span>
                <input type="tel" name="tel01"
                    value="<!--{$arrForm.tel01.value|default:$arrData.tel01|h}-->"
                    maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->"
                    style="<!--{$arrErr.tel01|sfGetErrorColor}-->"
                    class="boxShort text data-role-none" />&nbsp;－&nbsp;<input type="tel" name="tel02" value="<!--{$arrForm.tel02.value|default:$arrData.tel02|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel02|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;－&nbsp;<input type="text" name="tel03" value="<!--{$arrForm.tel03.value|default:$arrData.tel03|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel03|sfGetErrorColor}-->" class="boxShort text data-role-none" />
            </dd>

            <dt>メールアドレス&nbsp;<span class="attention">※</span></dt>
            <dd>
                <span class="attention"><!--{$arrErr.email}--><!--{$arrErr.email02}--></span>
                <input type="email" name="email"
                    value="<!--{$arrForm.email.value|default:$arrData.email|h}-->"
                    style="<!--{$arrErr.email|sfGetErrorColor}-->"
                    maxlength="<!--{$smarty.const.MTEXT_LEN}-->" class="boxLong top text data-role-none" />

                <!--{* ログインしていれば入力済みにする *}-->
                <!--{if $smarty.server.REQUEST_METHOD != 'POST' && $smarty.session.customer}-->
                    <!--{assign var=email02 value=$arrData.email}-->
                <!--{/if}-->

                <input type="email" name="email02"
                    value="<!--{$arrForm.email02.value|default:$email02|h}-->"
                    style="<!--{$arrErr.email02|sfGetErrorColor}-->"
                    maxlength="<!--{$smarty.const.MTEXT_LEN}-->" class="boxLong text data-role-none" placeholder="確認のため2回入力してください" />
            </dd>

            <dt>お問い合わせ内容&nbsp;<span class="attention">※</span>
                <span class="mini">（全角<!--{$smarty.const.MLTEXT_LEN}-->字以下）</span></dt>
            <dd><span class="attention"><!--{$arrErr.contents}--></span>
                <textarea name="contents" id="contents" class="textarea data-role-none" rows="4" cols="42" style="<!--{$arrErr.contents|sfGetErrorColor}-->"><!--{"\n"}--><!--{$arrForm.contents.value|h}--></textarea>
            </dd>

        </dl>

        <div class="btn_area">
            <input type="submit" value="確認ページへ" class="btn data-role-none" name="confirm" id="confirm" />
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
