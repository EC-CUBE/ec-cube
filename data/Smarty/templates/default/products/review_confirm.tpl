<!--{printXMLDeclaration}--><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--{*
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
 *}-->
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}--><!--{$smarty.const.USER_DIR}-->css/common.css" type="text/css" media="all" />
<link rel="alternate" type="application/rss+xml" title="RSS" href="<!--{$smarty.const.SITE_URL}-->/rss/index.php" />
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/site.js"></script>
<meta name="author" content="<!--{$arrPageLayout.author|escape}-->" />
<meta name="description" content="<!--{$arrPageLayout.description|escape}-->" />
<meta name="keywords" content="<!--{$arrPageLayout.keyword|escape}-->" />
<title><!--{$arrSiteInfo.shop_name}-->/お客様の声書き込み（確認ページ）</title>
</head>
<body onload="preLoadImg('<!--{$TPL_DIR}-->')">
<a name="top" id="top"></a>
<div id="windowcolumn">
  <div id="windowarea">
    <h2><img src="<!--{$TPL_DIR}-->img/products/review_title.jpg" width="500" height="40" alt="お客様の声書き込み" /></h2>
    <form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
      <input type="hidden" name="mode" value="complete" />
        <!--{foreach from=$arrForm key=key item=item}-->
        <!--{if $key ne "mode"}-->
        <input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->" /><!--{/if}-->
        <!--{/foreach}-->
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

        <table summary="お客様の声書き込み">
          <tr>
            <th>商品名</th>
            <td><!--{$arrForm.name|escape}--></td>
          </tr>
          <tr>
            <th>投稿者名<span class="attention">※</span></th>
            <td><!--{$arrForm.reviewer_name|escape}--></td>
          </tr>
          <tr>
            <th>メールアドレス</th>
            <td><!--{$arrForm.reviewer_url|escape}--></td>
          </tr>
          <tr>
            <th>性別</th>
            <td><!--{if $arrForm.sex eq 1 }-->男性<!--{elseif $arrForm.sex eq 2 }-->女性<!--{/if}--></td>
          </tr>
          <tr>
            <th>おすすめレベル<span class="attention">※</span></th>
            <td><span class="attention"><!--{$arrRECOMMEND[$arrForm.recommend_level]}--></span></td>
          </tr>
          <tr>
            <th>タイトル<span class="attention">※</span></th>
            <td><!--{$arrForm.title|escape}--></td>
          </tr>
          <tr>
            <th>コメント<span class="attention">※</span></th>
            <td><!--{$arrForm.comment|escape|nl2br}--></td>
          </tr>
        </table>
        <div class="btn">
          <input type="image" onclick=" mode.value='return';" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_back_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_back.gif',this)" src="<!--{$TPL_DIR}-->img/common/b_back.gif" class="box150" alt="戻る"  name="back" id="back" />
          <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_send_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_send.gif',this)" src="<!--{$TPL_DIR}-->img/common/b_send.gif" class="box150" alt="送信"  name="send" id="send" />
        </div>
      </form>
    </div>
  </div>
</body>
</html>
