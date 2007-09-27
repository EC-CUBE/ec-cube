<!--{*
 * Copyright (c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<script type="text/javascript">//<![CDATA[

function fnCheckAfterOpenWin(){
    if (<!--{$tpl_linemax}--> >= <!--{$smarty.const.DELIV_ADDR_MAX}-->){
        alert('最大登録数を超えています');
        return false;
    }else{
        win02('./delivery_addr.php','new_deiv','600','640');
    }
}
//]]>
</script>

<!--▼CONTENTS-->
<div id="mypagecolumn">
  <h2 class="title"><img src="<!--{$TPL_DIR}-->img/mypage/title.jpg" width="700" height="40" alt="MYページ" /></h2>
<!--{include file=$tpl_navi}-->
  <div id="mycontentsarea">
    <h3><img src="<!--{$TPL_DIR}-->img/mypage/subtitle03.gif" width="515" height="32" alt="お届け先追加・変更" /></h3>
    <p>登録住所以外へのご住所へ送付される場合等にご利用いただくことができます。<br />
     ※最大<!--{$smarty.const.DELIV_ADDR_MAX}-->件まで登録できます。</p>

    <p class="addbtn">
    <!--{if $tpl_linemax < 20}-->
      <a href="<!--{$smarty.const.URL_DIR}-->mypage/delivery_addr.php" onclick="win03('./delivery_addr.php','delivadd','600','640'); return false;" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/newadress_on.gif','newadress');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/newadress.gif','newadress');" target="_blank"><img src="<!--{$TPL_DIR}-->img/common/newadress.gif" width="160" height="22" alt="新しいお届け先を追加" border="0" name="newadress" /></a>
    <!--{/if}-->
    </p>

    <!--{if $tpl_linemax > 0}-->
    <form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" >
      <input type="hidden" name="mode" value="" />
      <input type="hidden" name="other_deliv_id" value="" />
      <input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->" />

      <table summary="お届け先">
        <tr>
          <th colspan="5">▼お届け先</th>
        </tr>
       <!--{section name=cnt loop=$arrOtherDeliv}-->
       <!--{assign var=OtherPref value="`$arrOtherDeliv[cnt].pref`"}-->
        <tr>
          <td class="centertd"><!--{$smarty.section.cnt.iteration}--></td>
          <td><label for="add<!--{$smarty.section.cnt.iteration}-->">お届け先住所</label></td>
          <td>
            〒<!--{$arrOtherDeliv[cnt].zip01}-->-<!--{$arrOtherDeliv[cnt].zip02}--><br />
            <!--{$arrPref[$OtherPref]|escape}--><!--{$arrOtherDeliv[cnt].addr01|escape}--><!--{$arrOtherDeliv[cnt].addr02|escape}--><br />
        <!--{$arrOtherDeliv[cnt].name01|escape}-->&nbsp;<!--{$arrOtherDeliv[cnt].name02|escape}-->
          </td>
          <td class="centertd">
            <a href="./delivery_addr.php" onclick="win02('./delivery_addr.php?other_deliv_id=<!--{$arrOtherDeliv[cnt].other_deliv_id}-->','deliv_disp','600','640'); return false;">変更</a>
          </td>
          <td class="centertd">
            <a href="<!--{$smarty.server.PHP_SELF}-->" onclick="fnModeSubmit('delete','other_deliv_id','<!--{$arrOtherDeliv[cnt].other_deliv_id}-->');">削除</a>
          </td>
        </tr>
        <!--{/section}-->
      </table>
    </form>
    <!--{else}-->
    <p class="delivempty"><strong>新しいお届け先はありません。</strong></p>
    <!--{/if}-->
  </div>
</div>
<!--▲CONTENTS-->
