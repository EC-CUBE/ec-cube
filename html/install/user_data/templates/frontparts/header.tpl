<!--{*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
  <tr>
    <td bgcolor="#cccccc"><img src="/user_data/topimg/_.gif" width="1" height="10" alt="" /></td>
    <td align="center" background="/user_data/topimg/header/bg.jpg">
      <table width="778" border="0" cellspacing="0" cellpadding="0" summary=" ">
        <tr>
          <td bgcolor="#9f0000" height="3"></td>
        </tr>
        <tr>
          <td bgcolor="#cc0000" height="5"></td>
        </tr>
      </table>
      <table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
        <tr>
          <td height="3"></td>
        </tr>
        <tr>
          <td width="179" valign="top"><a href="<!--{$smarty.const.SITE_URL}-->"><img src="/user_data/topimg/header/logos.jpg" width="179" height="85" /></a></td>
          <td width="276">

<!--▼ログインフォームここから（編集しないで下さい）-->
				<!--{include_php file=$tpl_login_php}-->
<!--▲ログインフォームここまで（編集しないで下さい）-->

          </td>
          <td width="305">
            <table width="304" border="0" cellspacing="0" cellpadding="0" summary=" ">
              <tr>
                <td align="right" colspan="5" height="38"><img src="/user_data/topimg/header/info.gif" width="300" height="50" /> </td>
              </tr>
              <tr align="right">
                <td><a href="/entry/kiyaku.php" onmouseover="chgImg('/user_data/topimg/header/entry_on.gif','entry');" onmouseout="chgImg('/user_data/topimg/header/entry.gif','entry');"><img src="/user_data/topimg/header/entry.gif" width="95" height="20" alt="会員登録" border="0" name="entry" id="entry" /></a>
                <a href="/contact/index.php" onmouseover="chgImg('/user_data/topimg/header/contact_on.gif','contact');" onmouseout="chgImg('/user_data/topimg/header/contact.gif','contact');"><img src="/user_data/topimg/header/contact.gif" width="95" height="20" alt="お問い合わせ" border="0" name="contact" id="contact" /></a>
                <a href="/cart/index.php" onmouseover="chgImg('/user_data/topimg/header/cartin_on.gif','cartin');" onmouseout="chgImg('/user_data/topimg/header/cartin.gif','cartin');"><img src="/user_data/topimg/header/cartin.gif" width="95" height="20" alt="カゴの中を見る" border="0" name="cartin" id="cartin" /></a></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      <table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
      	 <tr>
      	 <!--▼NAVI-->
		 <!--{include file=$tpl_mainnavi}-->
		 <!--▲NAVI-->
		 </tr>
	  </table>
	
	<table width="778" cellspacing="0" cellpadding="0" summary=" ">
		<tr><td bgcolor="#666666" height="1"></td></tr>
		<tr><td bgcolor="#cccccc" height="4"></td></tr>
	</table>
	
	<!--{if $smarty.server.PHP_SELF == '/index.php'}-->
	<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<tr>
			<td><img src="/user_data/topimg/space.gif" width="758" height="5" /></td>
		</tr>
		<tr>
		<!--▼TOPバナー-->
			<td><img src="/user_data/topimg/banner/head.jpg" width="758" height="40" /></td>
		<!--▲TOPバナー-->
		</tr>
	</table>
	<!--{/if}-->	  
    
    </td>
    <td bgcolor="#cccccc"><img src="/user_data/topimg/_.gif" width="1" height="10" alt="" /></td>
  </tr>
</table>