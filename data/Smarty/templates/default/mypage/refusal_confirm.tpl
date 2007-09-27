<!--{*
 * Copyright (c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<div id="mypagecolumn">
  <h2 class="title"><img src="<!--{$TPL_DIR}-->img/mypage/title.jpg" width="700" height="40" alt="MYページ" /></h2>
  <!--{include file=$tpl_navi}-->
  <form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
  <input type="hidden" name="mode" value="complete" />
  <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
  <div id="mycontentsarea">
    <h3><img src="<!--{$TPL_DIR}-->img/mypage/subtitle04.gif" width="515" height="32" alt="退会手続き" /></h3>
    <div id="completetext">
      <p>退会手続きを実行してもよろしいでしょうか？</p>
      <div class="tblareabtn">
        <a href="./refusal.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/mypage/b_no_on.gif','refusal_no');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/mypage/b_no.gif','refusal_no');"><img src="<!--{$TPL_DIR}-->img/mypage/b_no.gif" width="180" height="30" alt="いいえ、退会しません" name="refusal_no" id="refusal_no" /></a>&nbsp;
        <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/mypage/b_yes_on.gif',this);" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/mypage/b_yes.gif',this);" src="<!--{$TPL_DIR}-->img/mypage/b_yes.gif" class="box180" alt="はい、はい、退会します" name="refusal_yes" id="refusal_yes" />
      </div>

      <p class="mini"><em>※退会手続きが完了した時点で、現在保存されている購入履歴や、お届け先等の情報はすべてなくなりますのでご注意ください。</em></p>
    </div>
  </div>
  </form>
</div>
<!--▲CONTENTS-->
