<!--{*
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
 *}-->
<!--▼CONTENTS-->
<div id="undercolumn">
  <div id="undercolumn_contact">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    
    <p>お問い合わせはメールにて承っています。<br />
    内容によっては回答をさしあげるのにお時間をいただくこともございます。また、土日、祝祭日、年末年始、夏期休暇期間は翌営業日以降の対応となりますのでご了承ください。</p>
    
    <p class="mini"><em>※ご注文に関するお問い合わせには、必ず「ご注文番号」と「お名前」をご記入の上、メールくださいますようお願いいたします。</em></p>
    
    <form name="form1" method="post" action="?">
    <input type="hidden" name="mode" value="confirm" />
    
    <table summary="お問い合わせ">
      <tr>
        <th>お名前<span class="attention">※</span></th>
        <td>
          <span class="attention"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
          姓&nbsp;<input type="text"
                         class="box120"
                         name="name01"
                         value="<!--{$arrForm.name01|default:$arrData.name01|h}-->"
                         maxlength="<!--{$smarty.const.STEXT_LEN}-->"
                         style="<!--{$arrErr.name01|sfGetErrorColor}-->; ime-mode: active;" />　
          名&nbsp;<input type="text" class="box120" name="name02"
                         value="<!--{$arrForm.name02|default:$arrData.name02|h}-->"
                         maxlength="<!--{$smarty.const.STEXT_LEN}-->"
                         style="<!--{$arrErr.name02|sfGetErrorColor}-->; ime-mode: active;" />
        </td>
      </tr>
      <tr>
        <th>お名前(フリガナ)<span class="attention">※</span></th>
        <td>
          <span class="attention"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>
          セイ&nbsp;<input type="text"
                           class="box120"
                           name="kana01"
                           value="<!--{$arrForm.kana01|default:$arrData.kana01|h}-->"
                           maxlength="<!--{$smarty.const.STEXT_LEN}-->"
                           style="<!--{$arrErr.kana01|sfGetErrorColor}-->; ime-mode: active;" />　
          メイ&nbsp;<input type="text"
                           class="box120"
                           name="kana02"
                           value="<!--{$arrForm.kana02|default:$arrData.kana02|h}-->"
                           maxlength="<!--{$smarty.const.STEXT_LEN}-->"
                           style="<!--{$arrErr.kana02|sfGetErrorColor}-->; ime-mode: active;" />
        </td>
      </tr>
      <tr>
        <th>郵便番号</th>
        <td>
          <span class="attention"><!--{$arrErr.zip01}--><!--{$arrErr.zip02}--></span>
          <p>
            〒&nbsp;
            <input type="text"
                   name="zip01"
                   class="box60"
                   value="<!--{$arrForm.zip01|default:$arrData.zip01|h}-->"
                   maxlength="<!--{$smarty.const.ZIP01_LEN}-->"
                   style="<!--{$arrErr.zip01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;
            <input type="text"
                   name="zip02"
                   class="box60"
                   value="<!--{$arrForm.zip02|default:$arrData.zip02|h}-->"
                   maxlength="<!--{$smarty.const.ZIP02_LEN}-->"
                   style="<!--{$arrErr.zip02|sfGetErrorColor}-->; ime-mode: disabled;" />　
            <a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="fs12">郵便番号検索</span></a>
          </p>
          <p class="zipimg">
            <a href="javascript:fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'zip01', 'zip02', 'pref', 'addr01');">
              <img src="<!--{$TPL_DIR}-->img/button/btn_address_input.gif" width="86" height="20" alt="住所自動入力" /></a>
            <span class="mini">&nbsp;郵便番号を入力後、クリックしてください。</span>
          </p>
        </td>
      </tr>
      <tr>
        <th>住所</th>
        <td>
          <span class="attention"><!--{$arrErr.pref}--><!--{$arrErr.addr01}--><!--{$arrErr.addr02}--></span>
          
          <select name="pref" style="<!--{$arrErr.pref|sfGetErrorColor}-->">
          <option value="">都道府県を選択</option>
          <!--{html_options options=$arrPref selected=$arrForm.pref|default:$arrData.pref|h}-->
          </select>
          
          <p class="mini">
            <input type="text"
                   class="box380"
                   name="addr01"
                   value="<!--{$arrForm.addr01|default:$arrData.addr01|h}-->"
                   style="<!--{$arrErr.addr01|sfGetErrorColor}-->; ime-mode: active;" /><br />
            <!--{$smarty.const.SAMPLE_ADDRESS1}-->
          </p>
          
          <p class="mini">
            <input type="text"
                   class="box380"
                   name="addr02"
                   value="<!--{$arrForm.addr02|default:$arrData.addr02|h}-->"
                   style="<!--{$arrErr.addr02|sfGetErrorColor}-->; ime-mode: active;" /><br />
            <!--{$smarty.const.SAMPLE_ADDRESS2}-->
          </p>
          
          <p class="mini"><em>住所は2つに分けてご記入ください。マンション名は必ず記入してください。</em></p>
        </td>
      </tr>
      <tr>
        <th>電話番号</th>
        <td>
          <span class="attention"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span>
          <input type="text" 
                 class="box60"
                 name="tel01"
                 value="<!--{$arrForm.tel01|default:$arrData.tel01|h}-->"
                 maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->"
                 style="<!--{$arrErr.tel01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;
          <input type="text" 
                 class="box60"
                 name="tel02"
                 value="<!--{$arrForm.tel02|default:$arrData.tel02|h}-->"
                 maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->"
                 style="<!--{$arrErr.tel02|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;
          <input type="text" 
                 class="box60"
                 name="tel03"
                 value="<!--{$arrForm.tel03|default:$arrData.tel03|h}-->"
                 maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->"
                 style="<!--{$arrErr.tel03|sfGetErrorColor}-->; ime-mode: disabled;" />
        </td>
      </tr>
      <tr>
        <th>メールアドレス<span class="attention">※</span></th>
        <td>
          <span class="attention"><!--{$arrErr.email}--><!--{$arrErr.email02}--></span>
          <input type="text"
                 class="box380"
                 name="email"
                 value="<!--{$arrForm.email|default:$arrData.email|h}-->"
                 maxlength="<!--{$smarty.const.MTEXT_LEN}-->"
                 style="<!--{$arrErr.email|sfGetErrorColor}-->; ime-mode: disabled;" /><br />
          <!--{* ログインしていれば入力済みにする *}-->
          <!--{if $smarty.server.REQUEST_METHOD != 'POST' && $smarty.session.customer}-->
          <!--{assign var=email02 value=$arrData.email}-->
          <!--{/if}-->
          <input type="text"
                 class="box380"
                 name="email02"
                 value="<!--{$arrForm.email02|h}-->"
                 maxlength="<!--{$smarty.const.MTEXT_LEN}-->"
                 style="<!--{$arrErr.email02|sfGetErrorColor}-->; ime-mode: disabled;" /><br />
          <p class="mini"><em>確認のため2度入力してください。</em></p>
        </td>
      </tr>        
      <tr>
        <th>お問い合わせ内容<span class="attention">※</span><br />
        <span class="mini">（全角<!--{$smarty.const.MLTEXT_LEN}-->字以下）</span></th>
        <td>
          <span class="attention"><!--{$arrErr.contents}--></span>
          <textarea name="contents"
                    class="area380"
                    cols="60"
                    rows="20"
                    style="<!--{$arrErr.contents|sfGetErrorColor}-->; ime-mode: active;"><!--{$arrForm.contents|h}--></textarea>
        </td>
      </tr>
    </table>
    
    <div class="tblareabtn">
      <input type="image"
             onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_confirm_on.gif', this)"
             onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/button/btn_confirm.gif', this)"
             src="<!--{$TPL_DIR}-->img/button/btn_confirm.gif"
             style="width:150px; height=30px;"
             alt="確認ページへ"
             name="confirm" />
    </div>
    </form>
  </div>
</div>
<!--▲CONTENTS-->
