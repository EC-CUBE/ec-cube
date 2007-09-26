<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<div id="under02column">
  <div id="under02column_shopping">
    <p class="flowarea">
      <img src="<!--{$TPL_DIR}-->img/shopping/flow01.gif" width="700" height="36" alt="購入手続きの流れ" />
    </p>
    <h2 class="title">
      <img src="<!--{$TPL_DIR}-->img/shopping/deliv_title.jpg" width="700" height="40" alt="お届け先の指定" />
    </h2>

    <p>下記一覧よりお届け先住所を選択して、「選択したお届け先に送る」ボタンをクリックしてください。<br />
      一覧にご希望の住所が無い場合は、「新しいお届け先を追加する」より追加登録してください。<br />
      ※最大20件まで登録できます。</p>
    <!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
    <p class="addbtn">
      <a href="<!--{$smarty.const.URL_DIR}-->mypage/delivery_addr.php" onclick="win02('<!--{$smarty.const.URL_DIR}-->mypage/delivery_addr.php?page=<!--{$smarty.server.PHP_SELF|escape}-->','new_deiv','600','640'); return false;" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/newadress_on.gif','addition');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/newadress.gif','addition');"><img src="<!--{$TPL_DIR}-->img/common/newadress.gif" width="160" height="22" alt="新しいお届け先を追加する" name="addition" id="addition" /></a>
    </p>
    <!--{/if}-->
    <form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
      <input type="hidden" name="mode" value="customer_addr" />
      <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
      <input type="hidden" name="other_deliv_id" value="" />
      <!--{if $arrErr.deli != ""}-->
      <p class="attention"><!--{$arrErr.deli}--></p>
      <!--{/if}-->
      <table summary="お届け先の指定">
        <tr>
          <th>選択</th>
          <th>住所種類</th>
          <th>お届け先</th>
          <th>変更</th>
          <th>削除</th>
        </tr>
       <!--{section name=cnt loop=$arrAddr}-->
       <tr>
         <td class="centertd">
           <!--{if $smarty.section.cnt.first}-->
           <input type="radio" name="deliv_check" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="-1" <!--{if $arrForm.deliv_check.value == "" || $arrForm.deliv_check.value == -1}--> checked="checked"<!--{/if}--> />
           <!--{else}-->
           <input type="radio" name="deliv_check" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrAddr[cnt].other_deliv_id}-->" <!--{if $arrForm.deliv_check.value == $arrAddr[cnt].other_deliv_id}--> checked="checked"<!--{/if}--> />
           <!--{/if}-->
        </td>
        <td>
          <label for="chk_id_<!--{$smarty.section.cnt.iteration}-->">
            <!--{if $smarty.section.cnt.first}-->
            会員登録住所
            <!--{else}-->
            追加登録住所
            <!--{/if}-->
          </label>
        </td>
        <td>
          <!--{assign var=key value=$arrAddr[cnt].pref}-->
          <!--{$arrPref[$key]}--><!--{$arrAddr[cnt].addr01|escape}--><!--{$arrAddr[cnt].addr02|escape}--><br />
          <!--{$arrAddr[cnt].name01|escape}--> <!--{$arrAddr[cnt].name02|escape}-->
        </td>
        <td class="centertd">
        <!--{if !$smarty.section.cnt.first}-->
          <a href="<!--{$smarty.const.URL_DIR}-->mypage/delivery_addr.php" onclick="win02('<!--{$smarty.const.URL_DIR}-->mypage/delivery_addr.php?page=<!--{$smarty.server.PHP_SELF|escape}-->&amp;other_deliv_id=<!--{$arrAddr[cnt].other_deliv_id}-->','new_deiv','600','640'); return false;">変更</a>
        <!--{/if}-->
        </td>
        <td class="centertd">
        <!--{if !$smarty.section.cnt.first}-->
          <a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('delete', 'other_deliv_id', '<!--{$arrAddr[cnt].other_deliv_id}-->'); return false">削除</a>
        <!--{/if}-->
        </td>
      </tr>
      <!--{/section}-->
    </table>

      <div class="tblareabtn">
        <a href="<!--{$smarty.const.URL_DIR}-->cart/index.php" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_back_on.gif',back03)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_back.gif',back03)">
          <img src="<!--{$TPL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="戻る" border="0" name="back03" id="back03" />
        </a>
        <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/shopping/b_select_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/shopping/b_select.gif',this)" src="<!--{$TPL_DIR}-->img/shopping/b_select.gif" alt="選択したお届け先に送る" class="box190" name="send_button" id="send_button" />
      </div>
    </form>
  </div>
</div>
<!--▲CONTENTS-->
