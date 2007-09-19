<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
FTPの接続が成功しました。<br/>
以下のフォルダに、インストールファイルを一時的に格納します。</br>
<!--{$local_save_dir}--><br/>
親ディレクトリに書き込み権限を与えてください。</br>
また、次の情報に注意して、インストールしてください</br>

<!--{section name=cnt loop=$arrFile}-->
	<!--{if $arrFile[cnt].main_file != ""}-->
		・<!--{$arrFile[cnt].main_file|escape}-->を書き込みます。</br>親ディレクトリに書き込み権限を与えてください。</br>
	<!--{/if}-->
	<!--{if $arrFile[cnt].sql_file != ""}-->
		・テーブル<!--{$arrFile[cnt].sql_file}-->を再構築しますので、ご注意ください。</br>データは自動的にバックアップを取ります</br>
	<!--{/if}-->
<!--{/section}-->

<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="install">
<input type="hidden" name="update_id" value="<!--{$smarty.post.update_id}-->">
<input type="button" name="back" value="戻る" onclick="location.href='./update.php'";>　<input type="submit" value="インストール">
</form1>