<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->">
<input type="hidden" name="step" value="0">

<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->

<div class="contents">
  <div class="message">

<h2>ECサイトの設定</h2>
	</div>
<div class="block">
    <table>
<colgroup width="30%">
<colgroup width="70%">
        <tr>
            <th>店名<span class="attention">※</span></th>
            <td>
            <!--{assign var=key value="shop_name"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50"  /><br>
            <span class="ex-text">あなたの店名をご記入ください。</span>
            </td>
        </tr>
        <tr>
            <th>メールアドレス<span class="attention">※</span></th>
            <td>
            <!--{assign var=key value="admin_mail"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50"  /><br>
            <span class="ex-text">受注メールなどの宛先になります。<br>
            (例) eccube@example.com</span>
            </td>
        </tr>
        <tr>
            <th>ログインID<span class="attention">※</span><br/>半角英数字<!--{$smarty.const.ID_MIN_LEN}-->～<!--{$smarty.const.ID_MAX_LEN}-->文字</th>
            <td>
            <!--{assign var=key value="login_id"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50"  /><br>
            <span class="ex-text">管理機能にログインするためのIDです。</span><br />
            </td>
        </tr>
        <tr>
            <th>パスワード<span class="attention">※</span><br/>半角英数字<!--{$smarty.const.ID_MIN_LEN}-->～<!--{$smarty.const.ID_MAX_LEN}-->文字</th>
            <td>
            <!--{assign var=key value="login_pass"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="password" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$smarty.const.ID_MAX_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->;" size="50"  /><br>
            <span class="ex-text">管理機能にログインするためのパスワードです。</sapn><br />
            </td>
        </tr>
    </table>



<h2>管理機能の設定</h2>
<table>
<colgroup width="30%">
<colgroup width="70%">
        <tr>
            <th>ディレクトリ<br/>半角英数字<!--{$smarty.const.ID_MIN_LEN}-->～<!--{$smarty.const.ID_MAX_LEN}-->文字</th>
            <td>
            <!--{assign var=key value="admin_dir"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape|default:admin}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->;" size="50" /><br>
            <span class="ex-text">管理機能のディレクトリ名です。<br>
             下記で管理機能にアクセスする場合の[管理機能]の部分です。<br>
             https://[ホスト名].[ドメイン名]/[ショップディレクトリ]/<span class="bold">[ディレクトリ]</span>/</span><br />
            </td>
        </tr>
        <tr>
            <th>SSL制限<br/></th>
            <td>
            <!--{assign var=key value="admin_force_ssl"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="checkbox" name="<!--{$key}-->" id="<!--{$key}-->" value="1" <!--{if $arrForm[$key].value == 1}-->checked="checked"<!--{/if}--> /><label for="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->;">SSLを強制する。</label><br>
            <span class="ex-text">管理機能へのアクセスをSSL経由(https)の接続に制限します。</span><br />
            </td>
        </tr>
        <tr>
            <th>IP制限<br/></th>
            <td>
            <!--{assign var=key value="admin_allow_hosts"}-->
            <span class="ex-text">管理機能へのアクセスを特定のIPアドレスからの接続のみに制限します。<br />
            アクセスを許可するIPアドレスを1行づつ入力してください。<br />
            何も入力しない場合は全てを許可します。</span><br />
            <textarea name="<!--{$key}-->" class="box280"><!--{$arrForm[$key].value|escape}--></textarea>
            </td>
        </tr>
    </table>

<h2>WEBサーバの設定</h2>
    <table>
    <colgroup width="30%">
    <colgroup width="70%">
        <tr>
            <th>URL(通常)<span class="attention">※</span></th>
            <td>
            <!--{assign var=key value="normal_url"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50"  />
            </td>
        </tr>
        <tr>
            <th>URL(セキュア)<span class="attention">※</span></th>
            <td>
            <!--{assign var=key value="secure_url"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50"  />
            </td>
        </tr>
        <tr>
            <th>共通ドメイン</th>
            <td>
            <!--{assign var=key value="domain"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50"  /><br>
            <span class="ex-text">通常URLとセキュアURLでサブドメインが異なる場合に指定します。</span>
            </td>
        </tr>
    </table>
</block>
</div>
<div class="btn-area-top"></div>
  <div class="btn-area">
    <ul>
        <li><a class="btn-action" href="javascript:;" onclick="document.form1['mode'].value='return_step0';document.form1.submit();return false;" /><span class="btn-prev">前へ戻る</span></a></li>
    <li><a class="btn-action"href="javascript:;" onclick="document.form1.submit(); return false;" /><span class="btn-next">次へ進む</span></a></li>
    </ul>
  </div>
  <div class="btn-area-bottom"></div>
</from>
