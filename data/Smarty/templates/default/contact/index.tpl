<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<div id="undercolumn">
  
  <div id="undercolumn_contact">
    <h2 class="title"><img src="<!--{$TPL_DIR}-->img/contact/title.jpg" width="580" height="40" alt="お問い合わせ" /></h2>
    
    <p>お問い合わせはメールにて承っています。<br />
    内容によっては回答をさしあげるのにお時間をいただくこともございます。また、土日、祝祭日、年末年始、夏季期間は翌営業日以降の対応となりますのでご了承ください。</p>
    
    <p class="mini"><em>※ご注文に関するお問い合わせには、必ず「ご注文番号」と「お名前」をご記入の上、メールくださいますようお願いいたします。</em></p>
    
    <form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
    <input type="hidden" name="mode" value="confirm" />
    
    <table summary="お問い合わせ">
      <tr>
        <th>お名前<span class="attention">※</span></th>
        <td>
          <span class="attention"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
          姓&nbsp;<input type="text"
                         class="box120"
                         name="name01"
                         value="<!--{$name01|escape|default:$arrData.name01|escape}-->"
                         maxlength="<!--{$smarty.const.STEXT_LEN}-->"
                         style="<!--{$arrErr.name01|sfGetErrorColor}-->" />　
          名&nbsp;<input type="text" class="box120" name="name02"
                         value="<!--{$name02|escape|default:$arrData.name02|escape}-->"
                         maxlength="<!--{$smarty.const.STEXT_LEN}-->"
                         style="<!--{$arrErr.name02|sfGetErrorColor}-->" />
        </td>
      </tr>
      <tr>
        <th>お名前（フリガナ）<span class="attention">※</span></th>
        <td>
          <span class="attention"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>
          セイ&nbsp;<input type="text"
                           class="box120"
                           name="kana01"
                           value="<!--{$kana01|escape|default:$arrData.kana01|escape}-->"
                           maxlength="<!--{$smarty.const.STEXT_LEN}-->"
                           style="<!--{$arrErr.kana01|sfGetErrorColor}-->" />　
          メイ&nbsp;<input type="text"
                           class="box120"
                           name="kana02"
                           value="<!--{$kana02|escape|default:$arrData.kana02|escape}-->"
                           maxlength="<!--{$smarty.const.STEXT_LEN}-->"
                           style="<!--{$arrErr.kana02|sfGetErrorColor}-->" />
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
                   value="<!--{$zip01|escape|default:$arrData.zip01|escape}-->"
                   maxlength="<!--{$smarty.const.ZIP01_LEN}-->"
                   style="<!--{$arrErr.zip01|sfGetErrorColor}-->" />&nbsp;-&nbsp;
            <input type="text"
                   name="zip02"
                   class="box60"
                   value="<!--{$zip02|escape|default:$arrData.zip02|escape}-->"
                   maxlength="<!--{$smarty.const.ZIP02_LEN}-->"
                   style="<!--{$arrErr.zip02|sfGetErrorColor}-->" />　
            <a href="http://search.post.japanpost.jp/7zip/" target="_blank"><span class="fs10">郵便番号検索</span></a>
          </p>
          <p class="zipimg">
            <a href="javascript:fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'zip01', 'zip02', 'pref', 'addr01');">
              <img src="<!--{$TPL_DIR}-->img/common/address.gif" width="86" height="20" alt="住所自動入力" />
            </a>
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
          <!--{html_options options=$arrPref selected=$pref|escape|default:$arrData.pref|escape}-->
          </select>
          
          <p class="mini">
            <input type="text"
                   class="box380"
                   name="addr01"
                   value="<!--{$addr01|escape|default:$arrData.addr01|escape}-->"
                   style="<!--{$arrErr.addr01|sfGetErrorColor}-->" /><br />
            <!--{$smarty.const.SAMPLE_ADDRESS1}-->
          </p>
          
          <p class="mini">
            <input type="text"
                   class="box380"
                   name="addr02"
                   value="<!--{$addr02|escape|default:$arrData.addr02|escape}-->"
                   style="<!--{$arrErr.addr02|sfGetErrorColor}-->" /><br />
            <!--{$smarty.const.SAMPLE_ADDRESS1}-->
          </p>
          
          <p class="mini"><em>住所は2つに分けてご記入いただけます。マンション名は必ず記入してください。</em></p>
        </td>
      </tr>
      <tr>
        <th>電話番号</th>
        <td>
          <span class="attention"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span>
          <input type="text" 
                 class="box60"
                 name="tel01"
                 value="<!--{$tel01|escape|default:$arrData.tel01|escape}-->"
                 maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->"
                 style="<!--{$arrErr.tel01|sfGetErrorColor}-->" />&nbsp;-&nbsp;
          <input type="text" 
                 class="box60"
                 name="tel02"
                 value="<!--{$tel02|escape|default:$arrData.tel02|escape}-->"
                 maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->"
                 style="<!--{$arrErr.tel02|sfGetErrorColor}-->" />&nbsp;-&nbsp;
          <input type="text" 
                 class="box60"
                 name="tel03"
                 value="<!--{$tel03|escape|default:$arrData.tel03|escape}-->"
                 maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->"
                 style="<!--{$arrErr.tel03|sfGetErrorColor}-->" />
        </td>
      </tr>
      <tr>
        <th>メールアドレス<span class="attention">※</span></th>
        <td>
          <span class="attention"><!--{$arrErr.email}--><!--{$arrErr.email02}--></span>
          <input type="text"
                 class="box380"
                 name="email"
                 value="<!--{$email|escape|default:$arrData.email|escape}-->"
                 maxlength="<!--{$smarty.const.MTEXT_LEN}-->"
                 style="<!--{$arrErr.email|sfGetErrorColor}-->" /><br />
          <!--{* ログインしていれば入力済みにする *}-->
          <!--{if $smarty.server.REQUEST_METHOD != 'POST' && $smarty.session.customer}-->
          <!--{assign var=email02 value=$arrData.email}-->
          <!--{/if}-->
          <input type="text"
                 class="box380"
                 name="email02"
                 value="<!--{$email02|escape}-->"
                 maxlength="<!--{$smarty.const.MTEXT_LEN}-->"
                 style="<!--{$arrErr.email02|sfGetErrorColor}-->" /><br />
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
                    style="<!--{$arrErr.contents|sfGetErrorColor}-->"><!--{$contents|escape}--></textarea>
        </td>
      </tr>
    </table>
    
    <div class="tblareabtn">
      <input type="image"
             onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_confirm_on.gif', this)"
             onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_confirm.gif', this)"
             src="<!--{$TPL_DIR}-->img/common/b_confirm.gif"
             style="width:150px; height=30px;"
             alt="確認ページへ"
             name="confirm" />
    </div>
    </form>
  </div>
</div>
<!--▲CONTENTS-->
