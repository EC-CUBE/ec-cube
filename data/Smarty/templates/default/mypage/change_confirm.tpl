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
        <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|h}-->" />
        <!--{foreach from=$arrForm key=key item=item}-->
            <!--{if $key ne "mode" && $key ne "subm"}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
            <!--{/if}-->
        <!--{/foreach}-->
        <table summary=" " class="delivname">
            <col width="30%" />
            <col width="70%" />
            <tr>
                <th>お名前<span class="attention">※</span></th>
                <td><!--{$arrForm.name01|h}-->　<!--{$arrForm.name02|h}--></td>
            </tr>
            <tr>
                <th>お名前(フリガナ)<span class="attention">※</span></th>
                <td><!--{$arrForm.kana01|h}-->　<!--{$arrForm.kana02|h}--></td>
            </tr>
            <tr>
                <th>郵便番号<span class="attention">※</span></th>
                <td><!--{$arrForm.zip01}-->-<!--{$arrForm.zip02}--></td>
            </tr>
            <tr>
                <th>住所<span class="attention">※</span></th>
                <td><!--{$arrPref[$arrForm.pref]}--><!--{$arrForm.addr01|h}--><!--{$arrForm.addr02|h}--></td>
            </tr>
            <tr>
                <th>電話番号<span class="attention">※</span></th>
                <td><!--{$arrForm.tel01|h}-->-<!--{$arrForm.tel02}-->-<!--{$arrForm.tel03}--></td>
            </tr>
            <tr>
                <th>FAX</th>
                <td><!--{if strlen($arrForm.fax01) > 0}--><!--{$arrForm.fax01}-->-<!--{$arrForm.fax02}-->-<!--{$arrForm.fax03}--><!--{else}-->未登録<!--{/if}--></td>
            </tr>
            <tr>
                <th>メールアドレス<span class="attention">※</span></th>
                <td><a href="<!--{$arrForm.email|escape:'hex'}-->"><!--{$arrForm.email|escape:'hexentity'}--></a></td>
            </tr>
            <tr>
                <th>携帯メールアドレス</th>
                <td>
                    <!--{if strlen($arrForm.email_mobile) > 0}-->
                    <a href="<!--{$arrForm.email_mobile|escape:'hex'}-->"><!--{$arrForm.email_mobile|escape:'hexentity'}--></a>
                    <!--{else}-->
                    未登録
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>性別<span class="attention">※</span></th>
                <td><!--{$arrSex[$arrForm.sex]}--></td>
            </tr>
            <tr>
                <th>職業</th>
                <td><!--{$arrJob[$arrForm.job]|default:"未登録"|h}--></td>
            </tr>
            <tr>
                <th>生年月日</th>
                <td><!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}--><!--{$arrForm.year|h}-->年<!--{$arrForm.month|h}-->月<!--{$arrForm.day|h}-->日<!--{else}-->未登録<!--{/if}--></td>
            </tr>
            <tr>
                <th>希望するパスワード<span class="attention">※</span><br />
                </th>
                <td><!--{$passlen}--></td>
            </tr>
            <tr>
                <th>パスワードを忘れた時のヒント<span class="attention">※</span></th>
                <td>質問：&nbsp;<!--{$arrReminder[$arrForm.reminder]|h}--><br />
                        答え：&nbsp;<!--{$arrForm.reminder_answer|h}--></td>
            </tr>
            <tr>
                <th>メールマガジン送付について<span class="attention">※</span></th>
                <td><!--{$arrMAILMAGATYPE[$arrForm.mailmaga_flg]}--></td>
            </tr>
        </table>

        <div class="btn_area">
            <ul>
                <li>
                    <a href="?" onclick="fnModeSubmit('return', '', ''); return false;" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_back_on.jpg','back');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_back.jpg','back');">
                        <img src="<!--{$TPL_URLPATH}-->img/button/btn_back.jpg" alt="戻る" name="back" id="back" /></a>
                </li>
                <li>
                    <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_complete_on.jpg',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_complete.jpg',this)" src="<!--{$TPL_URLPATH}-->img/button/btn_complete.jpg" alt="送信" name="complete" id="complete" />
                </li>
            </ul>
        </div>
        </form>
    </div>
</div>
