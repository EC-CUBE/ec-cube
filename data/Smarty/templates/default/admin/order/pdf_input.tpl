<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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

<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_header.tpl" subtitle="PDF入力"}-->

<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="order_id" value="<!--{$arrForm.order_id}-->">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">

<table width="440" border="0" cellspacing="0" cellpadding="0" summary=" ">
  <tr>
    <td bgcolor="#f3f3f3" width="400" class="fs14n"><span class="white"><!--コンテンツタイトル-->帳票の作成</span></td>
  </tr>
</table>
                  
<table width="440" border="0" cellspacing="1" cellpadding="8" summary=" ">
  <tr class="fs12n">
    <td width="120" bgcolor="#f3f3f3">受注番号</td>
    <td width="307" bgcolor="#ffffff"><!--{$arrForm.order_id}--></td>
  </tr>
  <tr class="fs12n">
    <td width="120" bgcolor="#f3f3f3">発行日</td>
    <td width="307" bgcolor="#ffffff"><!--{if $arrErr.year}--><span class="red"><!--{$arrErr.year}--></span><!--{/if}-->
      <select name="year">
      <!--{html_options options=$arrYear selected=$arrForm.year}-->
      </select>年
      <select name="month">
      <!--{html_options options=$arrMonth selected=$arrForm.month}-->
      </select>月
      <select name="day">
      <!--{html_options options=$arrDay selected=$arrForm.day}-->
      </select>日　
      <span class="red">※必須入力</span>
    </td>
  </tr>
  <tr class="fs12n">
    <td width="120" bgcolor="#f3f3f3">帳票の種類</td>
    <td width="307" bgcolor="#ffffff"><!--{if $arrErr.download}--><span class="red"><!--{$arrErr.download}--></span><!--{/if}-->
      <select name="type">
      <!--{html_options options=$arrType selected=$arrForm.type}-->
      </select>
    </td>
  </tr>
  <tr class="fs12n">
    <td width="120" bgcolor="#f3f3f3">ダウンロード方法</td>
    <td width="307" bgcolor="#ffffff"><!--{if $arrErr.download}--><span class="red"><!--{$arrErr.download}--></span><!--{/if}-->
      <select name="download">
      <!--{html_options options=$arrDownload selected=$arrForm.download}-->
      </select>
    </td>
  </tr>
  <tr class="fs12">
    <td width="120" bgcolor="#f3f3f3">帳票タイトル</td>
    <td width="307" bgcolor="#ffffff"><!--{if $arrErr.title}--><span class="red"><!--{$arrErr.title}--></span><!--{/if}-->
      <input type="text" name="title" size="40" value="<!--{$arrForm.title}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
      <span class="red">※未入力時は、デフォルトのタイトルが表示されます。</span><br />
    </td>
  </tr>
  <tr class="fs12">
    <td width="120" bgcolor="#f3f3f3">帳票メッセージ</td>
    <td width="307" bgcolor="#ffffff"><!--{if $arrErr.msg1}--><span class="red"><!--{$arrErr.msg1}--></span><!--{/if}-->
      1行目：<input type="text" name="msg1" size="40" value="<!--{$arrForm.msg1}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
      <!--{if $arrErr.msg2}--><span class="red"><!--{$arrErr.msg1}--></span><!--{/if}-->
      2行目：<input type="text" name="msg2" size="40" value="<!--{$arrForm.msg2}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
      <!--{if $arrErr.msg3}--><span class="red"><!--{$arrErr.msg3}--></span><!--{/if}-->
      3行目：<input type="text" name="msg3" size="40" value="<!--{$arrForm.msg3}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
      <span class="red">※未入力時は、デフォルトのメッセージが表示されます。</span><br />
    </td>
  </tr>
  <tr class="fs12">
    <td width="120" bgcolor="#f3f3f3">備考</td>
    <td width="307" bgcolor="#ffffff">
      1行目：<input type="text" name="etc1" size="40" value="" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
      <!--{if $arrErr.etc2}--><span class="red"><!--{$arrErr.msg1}--></span><!--{/if}-->
      2行目：<input type="text" name="etc2" size="40" value="" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
      <!--{if $arrErr.etc3}--><span class="red"><!--{$arrErr.msg3}--></span><!--{/if}-->
      3行目：<input type="text" name="etc3" size="40" value="" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
      <span class="red">※未入力時は、表示されません。</span><br />
    </td>
  </tr>

  <tr class="fs12">
    <td width="120" bgcolor="#f3f3f3">ポイント表記</td>
    <td width="307" bgcolor="#ffffff">
      <input type="radio" name="disp_point" value="1" checked="checked" />する　<input type="radio" name="disp_point" value="0" />しない<br />
      <span style="font-size: 80%;">※「する」を選択されても、お客様が非会員の場合は表示されません。</span>
    </td>
  </tr>
</table>

<table width="440" border="0" cellspacing="0" cellpadding="0" summary=" ">
  <tr>
    <td align="center" bgcolor="#f3f3f3">
      <div class="btn">
      <input type="image" onMouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$TPL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" >
      <a href="javascript:window.close()" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/b_close_on.gif','b_close');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/b_close.gif','b_close');"><img src="<!--{$TPL_DIR}-->img/common/b_close.gif" width="140" height="30" alt="閉じる" border="0" name="b_close" /></a>
      </div>
    </td>
  </tr>
</table>

</form>

<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_footer.tpl"}-->

