<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
<script type="text/javascript">
<!--
// ラジオボタンによる表示・非表示
function fnChangeVisible(check_id, mod_id){

    if (document.getElementById(check_id).checked){
        document.getElementById(mod_id).onclick = false;
        document.getElementById(mod_id).src = '../img/install/next.jpg';
    } else {
        document.getElementById(mod_id).disabled = true;
        document.getElementById(mod_id).src = '../img/install/next_off.jpg';
    }
}
//-->
</script>

<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->" />
<input type="hidden" name="step" value="0" />

<!--{foreach key=key item=item from=$arrHidden}-->
    <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->

<tr><td height="30"></td></tr>
<tr><td align="left" class="fs12st">■使用許諾契約書の同意</td></tr>
<tr><td align="left" class="fs12">
    以下の使用許諾契約書をお読みください。<br/>
    インストールを続行するにはこの契約書に同意する必要があります。
</td></tr>
<tr><td height="10"></td></tr>
<tr>
    <td bgcolor="#cccccc" class="fs12">
    <table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
        <tr>
            <td bgcolor="#ffffff" class="fs12">
            <div id="agreement">
                ===ソフトウェア使用許諾書にご同意下さい===<br/>
                <br/>
                株式会社ロックオン(以下「弊社」という)では、お客様が本ソフトウェアをご利用になるためには、下記「ソフトウェア使用契約書」の内容を承諾して頂くことが条件になって<br/>
                おります。本ソフトウェアをインストールまたはコピー、ご使用になった時点で下記「ソフトウェア使用許諾書」にご同意いただいたものとみなします。<br/><br/>
                --------------------- ソフトウェア使用許諾書 ---------------------<br/><br/>
                1.ライセンス<br/><br/>
                EC-CUBEでは製品の使用にあたって、無償のGPLライセンスと有償の商用ライセンスのどちらかを選択することができる「デュアルライセンス方式」を採用しております。各ライセンスの主な特徴は以下の通りです。<br/><br/>
                1-1.GPLライセンス<br/><br/>
                無償でEC-CUBEを使用することができ、複製、改変、頒布を行うことができるが、EC-CUBEを使用したアプリケーションを頒布する場合には、そのアプリケーションのソースコードを公開し、利用可能な状態にしなくてはならない。<br/><br/>
                ※ 改変(カスタマイズ)する際は、プログラムファイル(PHPファイル等)のヘッダー部分に記載しております著作権表示以外の箇所は全て改変いただけます。<br/><br/>
                ※ GPLライセンス(GNU 一般公衆利用許諾契約書)の正式な条件については、http://www.fsf.org/licenses/ (日本語訳http://www.opensource.jp/gpl/gpl.ja.html)を参照して下さい。<br/><br/>
                1-2.商用ライセンス<br/><br/>
                EC-CUBE商用ライセンスは、GPLライセンスに準拠したくない方向けのライセンスです。<br/>
                EC-CUBE商用ライセンスを購入いただけますと、商用ライセンスの範囲で、ご自身のアプリケーションをオープンソースにする必要はありません。<br/><br/>
                ※ GPLライセンスに準拠しない全てのご利用において、商用ライセンスが必要となります。<br/><br/>
                ※ 商用ライセンスの詳細に関しては、http://www.ec-cube.net/license/business.phpを参照して下さい。<br/><br/>
                2.免責<br/><br/>
                2-1.利用者は、本ソフトウエアの使用に基づいて発生した一切の直接・間接の損害(データ滅失、サーバーダウン、業務停滞、第三者からのクレーム等)および危険はすべて利用者のみが負うことをここに確認し、同意するものとします。<br/>
                2-2.いかなる場合であっても、不法行為、契約その他いかなる法的根拠による場合でも、本ソフトウエアの供給者、再販売業者、および各情報コンテンツの提供会社は、お客様その他の第三者に対し、営業価値の喪失、業務の停止、コンピューターの故障による損害、その他あらゆる商業的損害・損失等を含め一切の直接的、間接的、特殊的、付随的または結果的損失、損害について責任を負いません。さらに、弊社は、第三者のいかなるクレームに対しても責任を負いません。<br/><br/>
                3.サイト情報の収集<br/><br/>
                3-1 EC-CEBEをインストールする際はサイトURL、店名、EC-CUBEバージョン、PHP情報、DB情報等の情報を弊社にて収集させて戴くことをここに確認し、同意するものとする。<br/>
            </div>
            </td>
        </tr>
    </table>
    </td>
</tr>
<tr><td height="10"></td></tr>
<!--{assign var=key value="agreement"}-->
<tr><td align="left" class="fs12"><input type="radio" id="agreement_yes" name="<!--{$key}-->" value=true onclick="fnChangeVisible('agreement_yes', 'next');" <!--{if $arrHidden[$key]}-->checked<!--{/if}-->><label for="agreement_yes">同意する</label>　<input type="radio" id="agreement_no" name="<!--{$key}-->" value=false onclick="fnChangeVisible('agreement_yes', 'next');" <!--{if !$arrHidden[$key]|h}-->checked<!--{/if}-->><label for="agreement_no">同意しない</label></td></tr>
</table>

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
    <tr><td height="20"></td></tr>
    <tr>
        <td align="center">
        <a href="#" onmouseover="chgImg('../img/install/back_on.jpg','back')" onmouseout="chgImg('../img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_welcome';document.form1.submit();" /><img  width="105" src="../img/install/back.jpg"  height="24" alt="前へ戻る" border="0" name="back"></a>
        <a href="#" onclick="document.form1.submit();"><input type='image' onMouseover="chgImgImageSubmit('../img/install/next_on.jpg',this)" onMouseout="chgImgImageSubmit('../img/install/next.jpg',this)" src="../img/install/next.jpg" width="105" height="24" alt="次へ進む" border="0" name="next" id="next"></a>
        </td>
    </tr>
    <tr><td height="30"></td></tr>
</form>
</table>
