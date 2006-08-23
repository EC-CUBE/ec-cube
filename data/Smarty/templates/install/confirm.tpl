<?php
/*
  $Id: install_4.php,v 1.4 2003/04/18 10:29:44 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
?>
<p><span class="pageHeading">osCommerce</span><br><font color="#9a9a9a">オープンソース・Eコマース・ソリューション</font></p>

<p class="pageTitle">新規インストール</p>

<p><b>Step 2: osCommerce の設定（確認ページ）</b></p>

<form name="form1" action="<!--{$smarty.server.PHP_SELF}-->" method="post">
<input type="hidden" name="mode" value="complete">
<!--{foreach key=keyname item=val from=$arrForm}-->
<!--{assign var=key value=$keyname}--> 
<input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->">
<!--{/foreach}-->

<!--{if $create_db}-->
<p>新規DBの作成を行います<br>作成後、データベース構造をインポートします</p>
<!--{else}-->
<p>DBへの接続が成功しました<br>OSデータベース構造をインポートするので、この処理を中断しないでください。</p>
<!--{/if}-->
<p>また、次の設定ファイルに設定値が書き込まれます:<br>
<!--{$conf_file}--><br>
設定ファイルの場所を変更しないで下さい。また、設定ファイルには書き込み権限を与えてください。
<br></p>

以下、設定情報の確認になります。下記内容でよろしければ、次へをクリックし、設定ファイルへの書き込みと<br>
OSデータベース構造をインポートします。
</br>

<p><b>1. インストールのためのオプションを設定してください:</b></p>
<!--{*
<p><input type="checkbox" name="db_import", value="database"><b>OSDBのインポート</b><br>
OSDBテーブル構造をインポートします。</p>

<p><input type="checkbox" name="install" value="configure"><b>自動設定</b><br>
ここで指定したウェブ・サーバとデータベース・サーバに関する情報を、
カタログ・ページおよび管理ツールの環境設定ファイルに自動的に保存します。</p>
*}-->
<p><b>2. ウェブ・サーバに関する情報を入力してください:</b></p>

<p><b>ウェブ・サーバのルート・ディレクトリ</b><br>
<!--{assign var=key value="root_dir"}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.ROOT_DIR}--><br>

<p><b>3. データベース・サーバに関する情報を入力してください:</b></p>

<p><b>データベース・サーバ</b><br>
<!--{assign var=key value="db_server"}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.DB_SERVER}-->
<br>
</p>

<p><b>ユーザ名</b><br>
<!--{assign var=key value="db_username"}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.DB_USERNAME}-->
<br>
注意: 「データベースのインポート」を上で選択している場合には、
この接続アカウントには Create および Drop 権限が必要です。</p>

<p><b>データベース名</b><br>
<!--{assign var=key value="db_name"}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.DB_NAME}-->
<br>

<p><b>データベースエラーメール送信先</b><br>
<!--{assign var=key value="db_error_mail_to"}-->
<!--{$arrErr[$key]}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.DB_ERROR_MAIL_TO}-->
<br>

<p><b>データベースエラーメール件名</b><br>
<!--{assign var=key value="db_error_mail_subject"}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.DB_ERROR_MAIL_SUBJECT}-->
<br>
データベースに関するエラーメールの件名を指定します。</p>

<p><b>クッキー持ち越し用ドメイン</b><br>
<!--{assign var=key value="domain_name"}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.DOMAIN_NAME}-->
<br>
指定したドメイン以下でCookieを有効にします。wwwなどのサブドメインは含めず、先頭にコロンをつけて指定してください。例えば<i>.yourdomain.jp</i></p>

<p><b>HTTPサーバー</b><br>
<!--{assign var=key value="site_url"}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.SITE_URL}-->
<br>
HTTPサーバーのURLを指定します。</p>

<p><b>HTTPSサーバー</b><br>
<!--{assign var=key value="ssl_url"}-->
<!--{$arrForm[$key].value|escape|default:$smarty.const.SSL_URL}-->
<br>
SSL接続の場合、HTTPSサーバーのURLを指定します。</p>
<!--{*
<p><?php echo osc_draw_checkbox_field('USE_PCONNECT', 'true'); ?> <b>持続的な接続を使用する</b><br>
持続的なデータベース接続を可能にします。共有サーバを使用している場合は、オフにしてください</p>

<p><?php echo osc_draw_radio_field('STORE_SESSIONS', 'files', true); ?> <b>セッション情報をファイルに保存する</b><br>
<?php echo osc_draw_radio_field('STORE_SESSIONS', 'mysql'); ?> <b>セッション情報をデータベースに保存する</b><br>
PHPセッション情報の保存先を指定してください。</p>


<p>次のエラーが発生しました:</p>

<p><div class="boxMe"><b>設定ファイルが存在しません。あるいは、適当なパーミッションがセットされていません。</b><br><br>次のように操作してください:
<ul class="boxMe"><li>cd <?php echo $HTTP_POST_VARS['DIR_FS_DOCUMENT_ROOT'] . $HTTP_POST_VARS['DIR_FS_CATALOG']; ?>includes/</li><li>touch configure.php</li><li>chmod 706 configure.php</li></ul>
<ul class="boxMe"><li>cd <?php echo $HTTP_POST_VARS['DIR_FS_DOCUMENT_ROOT'] . $HTTP_POST_VARS['DIR_FS_ADMIN']; ?>/includes/</li><li>touch configure.php</li><li>chmod 706 configure.php</li></ul></div></p>

<p class="noteBox">もし <i>chmod 706</i> でうまく動かなければ、<i>chmod 777</i>を試してください。</p>

<p class="noteBox">もしあなたがMicrosoft Windows環境でインストールを実行している場合は、新しいファイルを作成する事が出来るように、既存の設定ファイルの名前を変えてみてください。</p>

<form name="install" action="install.php?step=4" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><a href="index.php"><img src="images/button_cancel.gif" border="0" alt="キャンセル"></a></td>
    <td align="center"><input type="image" src="images/button_retry.gif" border="0" alt="再試行"></td>
  </tr>
</table>

</form>
*}-->

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><a href="<!--{$smarty.server.PHP_SELF}-->" onclick="fnModeSubmit('return', '', ''); return false;"><img src="images/button_cancel.gif" border="0" alt="キャンセル"></a></td>
    <td align="center"><input type="hidden" name="install[]" value="configure"><input type="image" src="images/button_continue.gif" border="0" alt="次へ"></td>
  </tr>
</table>

</form>


