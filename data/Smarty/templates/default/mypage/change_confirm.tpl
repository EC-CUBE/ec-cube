<!--{*
/*
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
 */
*}-->

<div id="mypagecolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <!--{include file=$tpl_navi}-->
    <div id="mycontents_area">
        <h3><!--{$tpl_subtitle|h}--></h3>
        <p>下記の内容で送信してもよろしいでしょうか？<br />
            よろしければ、一番下の「完了ページへ」ボタンをクリックしてください。</p>

        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="complete" />
            <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id.value|h}-->" />
            <!--{foreach from=$arrForm key=key item=item}-->
                <!--{if $key ne "mode" && $key ne "subm"}-->
                <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item.value|h}-->" />
                <!--{/if}-->
            <!--{/foreach}-->
            <table summary=" " class="delivname">
                <col width="30%" />
                <col width="70%" />
                <tr>
                    <th>お名前</th>
                    <td><!--{$arrForm.name01.value|h}-->　<!--{$arrForm.name02.value|h}--></td>
                </tr>
                <tr>
                    <th>お名前(フリガナ)</th>
                    <td><!--{$arrForm.kana01.value|h}-->　<!--{$arrForm.kana02.value|h}--></td>
                </tr>
                <tr>
                    <th>会社名</th>
                    <td>
                        <!--{$arrForm.company_name.value|h}-->
                    </td>
                </tr>
                <!--{if $smarty.const.FORM_COUNTRY_ENABLE}-->
                <tr>
                    <th>国</th>
                    <td>
                        <!--{$arrCountry[$arrForm.country_id.value]|h}-->
                    </td>
                </tr>
                <tr>
                    <th>ZIP CODE</th>
                    <td>
                        <!--{$arrForm.zipcode.value|h}-->
                    </td>
                </tr>
                <!--{/if}-->
                <tr>
                    <th>郵便番号</th>
                    <td><!--{$arrForm.zip01.value}-->-<!--{$arrForm.zip02.value}--></td>
                </tr>
                <tr>
                    <th>住所</th>
                    <td><!--{$arrPref[$arrForm.pref.value]}--><!--{$arrForm.addr01.value|h}--><!--{$arrForm.addr02.value|h}--></td>
                </tr>
                <tr>
                    <th>電話番号</th>
                    <td><!--{$arrForm.tel01.value|h}-->-<!--{$arrForm.tel02.value}-->-<!--{$arrForm.tel03.value}--></td>
                </tr>
                <tr>
                    <th>FAX</th>
                    <td><!--{if strlen($arrForm.fax01.value) > 0}--><!--{$arrForm.fax01.value}-->-<!--{$arrForm.fax02.value}-->-<!--{$arrForm.fax03.value}--><!--{else}-->未登録<!--{/if}--></td>
                </tr>
                <tr>
                    <th>メールアドレス</th>
                    <td><a href="mailto:<!--{$arrForm.email.value|escape:'hex'}-->"><!--{$arrForm.email.value|escape:'hexentity'}--></a></td>
                </tr>
                <tr>
                    <th>携帯メールアドレス</th>
                    <td>
                        <!--{if strlen($arrForm.email_mobile.value) > 0}-->
                        <a href="mailto:<!--{$arrForm.email_mobile.value|escape:'hex'}-->"><!--{$arrForm.email_mobile.value|escape:'hexentity'}--></a>
                        <!--{else}-->
                        未登録
                        <!--{/if}-->
                    </td>
                </tr>
                <tr>
                    <th>性別</th>
                    <td><!--{$arrSex[$arrForm.sex.value]}--></td>
                </tr>
                <tr>
                    <th>職業</th>
                    <td><!--{$arrJob[$arrForm.job.value]|default:"未登録"|h}--></td>
                </tr>
                <tr>
                    <th>生年月日</th>
                    <td><!--{if strlen($arrForm.year.value) > 0 && strlen($arrForm.month.value) > 0 && strlen($arrForm.day.value) > 0}--><!--{$arrForm.year.value|h}-->年<!--{$arrForm.month.value|h}-->月<!--{$arrForm.day.value|h}-->日<!--{else}-->未登録<!--{/if}--></td>
                </tr>
                <tr>
                    <th>希望するパスワード<br />
                    </th>
                    <td><!--{$passlen}--></td>
                </tr>
                <tr>
                    <th>パスワードを忘れた時のヒント</th>
                    <td>質問：&nbsp;<!--{$arrReminder[$arrForm.reminder.value]|h}--><br />
                            答え：&nbsp;<!--{$arrForm.reminder_answer.value|h}--></td>
                </tr>
                <tr>
                    <th>メールマガジン送付について</th>
                    <td><!--{$arrMAILMAGATYPE[$arrForm.mailmaga_flg.value]}--></td>
                </tr>
            </table>

            <div class="btn_area">
                <ul>
                    <li>
                        <a href="?" onclick="eccube.setModeAndSubmit('return', '', ''); return false;">
                            <img class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_back.jpg" alt="戻る" /></a>
                    </li>
                    <li>
                        <input type="image" class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_complete.jpg" alt="送信" name="complete" id="complete" />
                    </li>
                </ul>
            </div>
        </form>
    </div>
</div>
