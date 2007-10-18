<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼まだ会員登録されていないお客様-->
<form name="member_form2" id="member_form2" method="post" action="./index.php">
      <input type="hidden" name="mode" value="nonmember" />
      <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <div class="loginarea">
    <p>
      <img src="<!--{$TPL_DIR}-->img/login/guest.gif" width="247" height="16" alt="まだ会員登録されていないお客様" />
    </p>
    <p class="inputtext">お申込を行う為には、会員登録が必要です。
      会員登録をするボタンをクリックして会員登録を行ってください。
    </p>
    <div class="inputbox02">

      <a href="<!--{$smarty.const.CAMPAIGN_URL}--><!--{$dir_name}-->/entry.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/login/b_gotoentry_on.gif','b_gotoentry');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/login/b_gotoentry.gif','b_gotoentry');">
        <img src="<!--{$TPL_DIR}-->img/login/b_gotoentry.gif" width="130" height="30" alt="会員登録をする" border="0" name="b_gotoentry" />
      </a>
    </div>
  </div>
</form>
</div>
</div>
<!--▲まだ会員登録されていないお客様-->
