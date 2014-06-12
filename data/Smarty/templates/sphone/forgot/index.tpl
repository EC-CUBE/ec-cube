<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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

<section id="windowcolumn">
    <h2 class="title">パスワードを忘れた方</h2>
    <form action="?" method="post" name="form1">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="mail_check" />
        <div class="intro">
            <p>ご登録時のメールアドレスと、ご登録されたお名前を入力して「次へ」ボタンをクリックしてください。</p>
        </div>
        <div class="window_area clearfix">
            <p>
                お名前<br />
                <span class="attention"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
                <input type="text" name="name01"
                    value="<!--{$arrForm.name01|default:''|h}-->"
                    maxlength="<!--{$smarty.const.STEXT_LEN}-->"
                    style="<!--{$arrErr.name01|sfGetErrorColor}-->;"
                    class="boxHarf text data-role-none" placeholder="姓"/>&nbsp;&nbsp;
                <input type="text" name="name02"
                    value="<!--{$arrForm.name02|default:''|h}-->"
                    maxlength="<!--{$smarty.const.STEXT_LEN}-->"
                    style="<!--{$arrErr.name02|sfGetErrorColor}-->;"
                    class="boxHarf text data-role-none" placeholder="名"/>
            </p>
            <hr />
            <p>
                メールアドレス<br />
                <span class="attention"><!--{$arrErr.email}--></span>
                <input type="email" name="email"
                value="<!--{$arrForm.email|default:$tpl_login_email|h}-->"
                style="<!--{$arrErr.email|sfGetErrorColor}-->;"
                maxlength="200" class="text boxLong data-role-none" />
            </p>
            <span class="attention"><!--{$errmsg}--></span>
            <hr />
            <p class="attentionSt">【重要】新しくパスワードを発行いたしますので、お忘れになったパスワードはご利用できなくなります。</p>
        </div>

        <div class="btn_area"><p><input class="btn data-role-none" type="submit" value="次へ" /></p></div>
    </form>
</section>
