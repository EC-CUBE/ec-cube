<!--{printXMLDeclaration}--><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}--><!--{$smarty.const.USER_DIR}-->css/common.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/site.js"></script>
<title><!--{$arrSiteInfo.shop_name}-->/<!--{$tpl_title|escape}--></title>
</head>

<body onload="preLoadImg('<!--{$smarty.const.URL_DIR}-->'); <!--{$tpl_onload}-->">
<noscript>
  <p><em>JavaScriptを有効にしてご利用下さい.</em></p>
</noscript>

<a name="top" id="top"></a>

<!--▼CONTENTS-->
<div id="windowcolumn">
  <div id="windowarea">
    <h2>
      <img src="<!--{$TPL_DIR}-->img/shopping/delivadd_title.jpg" width="500" height="40" alt="新しいお届け先の追加・変更" />
    </h2>
    <p class="windowtext">下記項目にご入力ください。「<span class="attention">※</span>」印は入力必須項目です。<br />
      入力後、一番下の「確認ページへ」ボタンをクリックしてください。</p>

    <form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
    <input type="hidden" name="mode" value="edit" />
    <input type="hidden" name="other_deliv_id" value="<!--{$smarty.session.other_deliv_id}-->" />
    <input type="hidden" name="ParentPage" value="<!--{$ParentPage}-->" />

      <table summary="お届け先登録">
        <tr>
          <th>お名前<span class="attention">※</span></th>
          <td>
            <span class="attention"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
            姓&nbsp;<input type="text" name="name01" value="<!--{if $name01 == ""}--><!--{$arrOtherDeliv.name01|escape}--><!--{else}--><!--{$name01|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name01|sfGetErrorColor}-->" size="15" class="box120" />&nbsp;
            名&nbsp;<input type="text" name="name02" value="<!--{if $name02 == ""}--><!--{$arrOtherDeliv.name02|escape}--><!--{else}--><!--{$name02|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name02|sfGetErrorColor}-->" size="15" class="box120" />
          </td>
        </tr>
        <tr>
          <th>お名前（フリガナ）<span class="attention">※</span></th>
          <td>
            <span class="attention"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>
            セイ&nbsp;<input type="text" name="kana01" value="<!--{if $kana01 == ""}--><!--{$arrOtherDeliv.kana01|escape}--><!--{else}--><!--{$kana01|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana01|sfGetErrorColor}-->" size="15" class="box120" />&nbsp;
            メイ&nbsp;<input type="text" name="kana02" value="<!--{if $kana02 == ""}--><!--{$arrOtherDeliv.kana02|escape}--><!--{else}--><!--{$kana02|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana02|sfGetErrorColor}-->" size="15" class="box120" />
          </td>
        </tr>
        <tr>
          <th>郵便番号<span class="attention">※</span></th>
          <td>
            <!--{assign var=key1 value="zip01"}-->
            <!--{assign var=key2 value="zip02"}-->
            <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
            <p>〒&nbsp;<input type="text" name="zip01" value="<!--{if $zip01 == ""}--><!--{$arrOtherDeliv.zip01|escape}--><!--{else}--><!--{$zip01|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" size="6" class="box60" />&nbsp;-&nbsp;<input type="text" name="zip02" value="<!--{if $zip02 == ""}--><!--{$arrOtherDeliv.zip02|escape}--><!--{else}--><!--{$zip02|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" size="6" class="box60" />&nbsp;
               <a href="http://search.post.japanpost.jp/7zip/" target="_blank"><span class="fs10">郵便番号検索</span></a></p>
            <p class="zipimg"><a href="<!--{$smarty.const.URL_INPUT_ZIP}-->" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'zip01', 'zip02', 'pref', 'addr01'); return false;" target="_blank"><img src="<!--{$TPL_DIR}-->img/common/address.gif" width="86" height="20" alt="住所自動入力" border="0" /></a>
              <span class="mini">&nbsp;郵便番号を入力後、クリックしてください。</span></p>
          </td>
        </tr>
        <tr>
          <th>住所<span class="attention">※</span></th>
          <td>
           <span class="attention"><!--{$arrErr.pref}--></span>
           <select name="pref" style="<!--{$arrErr.pref|sfGetErrorColor}-->">
             <option value="" selected="selected">選択してください</option>
             <!--{if $pref == ""}-->
               <!--{html_options options=$arrPref selected=$arrOtherDeliv.pref|escape}-->
             <!--{else}-->
               <!--{html_options options=$arrPref selected=$pref|escape}-->
            <!--{/if}-->
            </select>
            <p class="mini">
              <span class="attention"><!--{$arrErr.addr01}--></span>
              <input type="text" name="addr01" value="<!--{if $addr01 == ""}--><!--{$arrOtherDeliv.addr01|escape}--><!--{else}--><!--{$addr01|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" style="<!--{$arrErr.addr01|sfGetErrorColor}-->" size="40" class="box300" /><br />
              <!--{$smarty.const.SAMPLE_ADDRESS1}--></p>

            <p class="mini">
              <span class="attention"><!--{$arrErr.addr02}--></span>
              <input type="text" name="addr02" value="<!--{if $addr02 == ""}--><!--{$arrOtherDeliv.addr02|escape}--><!--{else}--><!--{$addr02|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" style="<!--{$arrErr.addr02|sfGetErrorColor}-->" size="40" class="box300" /><br />
             <!--{$smarty.const.SAMPLE_ADDRESS2}--></p>
            <p class="mini"><em>住所は2つに分けてご記入いただけます。マンション名は必ず記入してください。</em></p></td>
        </tr>
        <tr>
          <th>電話番号<span class="attention">※</span></th>
          <td>
            <span class="attention"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span>
            <input type="text" name="tel01" value="<!--{if $tel01 == ""}--><!--{$arrOtherDeliv.tel01|escape}--><!--{else}--><!--{$tel01|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel01|sfGetErrorColor}-->" size="6" class="box60" />&nbsp;-&nbsp;<input type="text" name="tel02" value="<!--{if $tel02 == ""}--><!--{$arrOtherDeliv.tel02|escape}--><!--{else}--><!--{$tel02|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel02|sfGetErrorColor}-->" size="6" class="box60" />&nbsp;-&nbsp;<input type="text" name="tel03" value="<!--{if $tel03 == ""}--><!--{$arrOtherDeliv.tel03|escape}--><!--{else}--><!--{$tel03|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel03|sfGetErrorColor}-->" size="6" class="box60" />
          </td>
        </tr>
      </table>

      <div class="btn">
        <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_entry_on.gif',this);" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_entry.gif',this);" src="<!--{$TPL_DIR}-->img/common/b_entry.gif" class="box150" alt="登録する" name="register" id="register" />
      </div>
    </form>
  </div>
</div>
</body>
</html>
