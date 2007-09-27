<!--{*
 * Copyright (c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<div id="mynavarea">
  <ul>
  <!--{if $tpl_mypageno == 'index'}-->
    <li><a href="./index.php"><img src="<!--{$TPL_DIR}-->img/mypage/navi01_on.jpg" width="170" height="30" alt="購入履歴一覧" border="0" name="m_navi01" /></a></li>
  <!--{else}-->
    <li><a href="./index.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/mypage/navi01_on.jpg','m_navi01');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/mypage/navi01.jpg','m_navi01');"><img src="<!--{$TPL_DIR}-->img/mypage/navi01.jpg" width="170" height="30" alt="購入履歴一覧" border="0" name="m_navi01" /></a></li>
  <!--{/if}-->
  <!--{if $tpl_mypageno == 'change'}-->
    <li><a href="./change.php"><img src="<!--{$TPL_DIR}-->img/mypage/navi02_on.jpg" width="170" height="30" alt="会員登録内容変更" border="0" name="m_navi02" /></a></li>
  <!--{else}-->
    <li><a href="./change.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/mypage/navi02_on.jpg','m_navi02');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/mypage/navi02.jpg','m_navi02');"><img src="<!--{$TPL_DIR}-->img/mypage/navi02.jpg" width="170" height="30" alt="会員登録内容変更" border="0" name="m_navi02" /></a></li>
  <!--{/if}-->
  <!--{if $tpl_mypageno == 'delivery'}-->
    <li><a href="./delivery.php"><img src="<!--{$TPL_DIR}-->img/mypage/navi03_on.jpg" width="170" height="30" alt="お届け先追加・変更" border="0" name="m_navi03" /></a></li>
  <!--{else}-->
    <li><a href="./delivery.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/mypage/navi03_on.jpg','m_navi03');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/mypage/navi03.jpg','m_navi03');"><img src="<!--{$TPL_DIR}-->img/mypage/navi03.jpg" width="170" height="30" alt="お届け先追加・変更" border="0" name="m_navi03" /></a></li>
  <!--{/if}-->
  <!--{if $tpl_mypageno == 'refusal'}-->
    <li><a href="./refusal.php"><img src="<!--{$TPL_DIR}-->img/mypage/navi04_on.jpg" width="170" height="30" alt="退会手続き" border="0" name="m_navi04" /></a></li>
  <!--{else}-->
    <li><a href="./refusal.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/mypage/navi04_on.jpg','m_navi04');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/mypage/navi04.jpg','m_navi04');"><img src="<!--{$TPL_DIR}-->img/mypage/navi04.jpg" width="170" height="30" alt="退会手続き" border="0" name="m_navi04" /></a></li>
  <!--{/if}-->
  <!-- 現在のポイント ここから -->
  <!--{if $point_disp !== false}-->
     <li>ようこそ <br />
       <!--{$CustomerName1|escape}--> <!--{$CustomerName2|escape}-->様<br />
       現在の所持ポイントは<em><!--{$CustomerPoint|number_format|escape|default:"0"}-->pt</em>です。</li>
<!--{/if}-->
<!-- 現在のポイント ここまで -->
  </ul>
</div>
<!--▲NAVI-->
