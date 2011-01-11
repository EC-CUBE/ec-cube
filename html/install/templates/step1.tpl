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
<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->">
<input type="hidden" name="step" value="0">

<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->

<tr><td height="30"></td></tr>
<tr><td align="left" class="fs12st">■ECサイトの設定</td></tr>
<tr>
    <td bgcolor="#cccccc">
    <table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
        <tr>
            <td bgcolor="#f2f1ec" width="150" class="fs12n">店名<span class="red">※</span></td>
            <td bgcolor="#ffffff" width="332">
            <!--{assign var=key value="shop_name"}-->
            <span class="red"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
            <br><span class="fs10">※あなたの店名をご記入ください。</span>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f2f1ec" width="150" class="fs12n">管理者：メールアドレス<span class="red">※</span></td>
            <td bgcolor="#ffffff" width="332">
            <!--{assign var=key value="admin_mail"}-->
            <span class="red"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
            <br><span class="fs10">※受注メールなどの宛先になります。：(例)example@ec-cube.net</span>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f2f1ec" width="150"><span class="fs12n">管理者：ログインID<span class="red">※</span></span><br/><span class="fs10">半角英数字<!--{$smarty.const.ID_MIN_LEN}-->～<!--{$smarty.const.ID_MAX_LEN}-->文字</span></td>
            <td bgcolor="#ffffff" width="332">
            <!--{assign var=key value="login_id"}-->
            <span class="red"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
            <br><span class="fs10">※管理機能にログインするためのIDです。</span>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f2f1ec" width="150"><span class="fs12n">管理者：パスワード<span class="red">※</span></span><br/><span class="fs10">半角英数字<!--{$smarty.const.ID_MIN_LEN}-->～<!--{$smarty.const.ID_MAX_LEN}-->文字</span></td>
            <td bgcolor="#ffffff" width="332">
            <!--{assign var=key value="login_pass"}-->
            <span class="red"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
            <input type="password" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$smarty.const.ID_MAX_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->;" size="40" class="box40" />
            <br><span class="fs10">※管理機能にログインするためのパスワードです。</span>
            </td>
        </tr>
    </table>
    </td>
</tr>
<tr><td height="20"></td></tr>
<tr><td align="left" class="fs12st">■管理画面の設定</td></tr>
<tr>
    <td bgcolor="#cccccc">
    <table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">

        <tr>
            <td bgcolor="#f2f1ec" width="150"><span class="fs12n">管理画面：ディレクトリ</span><br/><span class="fs10">半角英数字<!--{$smarty.const.ID_MIN_LEN}-->～<!--{$smarty.const.ID_MAX_LEN}-->文字</span></td>
            <td bgcolor="#ffffff" width="332">
            <!--{assign var=key value="admin_dir"}-->
            <span class="red"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape|default:admin}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->;" size="40" class="box40" />
            <br><span class="fs10">※管理画面のディレクトリ名です。https://[ホスト名].[ドメイン名]/[ショップディレクトリ]/[管理画面]/で管理画面にアクセスする場合の[管理画面]の部分。</span>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f2f1ec" width="150"><span class="fs12n">管理画面：SSL制限</span><br/><span class="fs10"></td>
            <td bgcolor="#ffffff" width="332">
            <!--{assign var=key value="admin_force_ssl"}-->
            <span class="red"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
            <input type="checkbox" name="<!--{$key}-->" id="<!--{$key}-->" value="1" <!--{if $arrForm[$key].value == 1}-->checked="checked"<!--{/if}--> /><label for="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->;" class="fs12n">SSLを強制する。</label>
            <br><span class="fs10">※管理画面へのアクセスをSSL経由(https)の接続に制限します。</span>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f2f1ec" width="150"><span class="fs12n">管理画面：IP制限</span><br/><span class="fs10"></td>
            <td bgcolor="#ffffff" width="332">
            <!--{assign var=key value="admin_allow_hosts"}-->
            <span class="red fs12n"><!--{$arrErr[$key]}--></span>
            <span class="fs10">※管理画面へのアクセスを特定のIPアドレスからの接続のみに制限します。アクセスを許可するIPアドレスを1行づつ入力してください。何も入力しない場合は全てを許可します。</span><br />
            <textarea name="<!--{$key}-->" class="fs12n box40"><!--{$arrForm[$key].value|escape}--></textarea>
            </td>
        </tr>
    </table>
    </td>
</tr>
<tr><td height="20"></td></tr>
<tr><td align="left" class="fs12st">■WEBサーバの設定</td></tr>
<tr>
    <td bgcolor="#cccccc">
    <table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
        <tr>
            <td bgcolor="#f2f1ec" width="150" class="fs12n">URL(通常)<span class="red">※</span></td>
            <td bgcolor="#ffffff" width="332" class="fs12">
            <!--{assign var=key value="normal_url"}-->
            <span class="red"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
            </td>
        </tr>
        <tr>
            <td bgcolor="#f2f1ec" width="150" class="fs12n">URL(セキュア)<span class="red">※</span></td>
            <td bgcolor="#ffffff" width="332" class="fs12">
            <!--{assign var=key value="secure_url"}-->
            <span class="red"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
            </td>
        </tr>
        <tr>
            <td bgcolor="#f2f1ec" width="150" class="fs12n">共通ドメイン</td>
            <td bgcolor="#ffffff" width="332">
            <!--{assign var=key value="domain"}-->
            <span class="red"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
            <br><span class="fs10">※通常URLとセキュアURLでサブドメインが異なる場合に指定します。</span>
            </td>
        </tr>
    </table>
    </td>
</tr>
</table>

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
    <tr><td height="20"></td></tr>
    <tr>
        <td align="center">
        <a href="#" onmouseover="chgImg('img/back_on.jpg','back')" onmouseout="chgImg('img/back.jpg','back')" onclick="document.form1['mode'].value='return_step0';document.form1.submit();return false;" /><img  width="105" src="img/back.jpg"  height="24" alt="前へ戻る" border="0" name="back"></a>
        <input type="image" onMouseover="chgImgImageSubmit('img/next_on.jpg',this)" onMouseout="chgImgImageSubmit('img/next.jpg',this)" src="img/next.jpg" width="105" height="24" alt="次へ進む" border="0" name="next">
        </td>
    </tr>
    <tr><td height="30"></td></tr>
</from>
</table>
