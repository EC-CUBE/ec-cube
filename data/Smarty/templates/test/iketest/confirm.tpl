FTPの接続が成功しました。<br/>
次の情報に注意して、インストールしてください</br>

<!--{section name=cnt loop=$arrFile}-->
	<!--{if $arrFile[cnt].main_file != ""}-->
		・<!--{$arrFile[cnt].main_file|escape}-->を上書きします。</br>親ディレクトリに書き込み権限を与えてください。</br>
	<!--{/if}-->
	<!--{if $arrFile[cnt].sql_file != ""}-->
		・テーブル<!--{$arrFile[cnt].sql_file}-->を再構築しますので、ご注意ください。</br>データは自動的にバックアップを取ります</br>
	<!--{/if}-->
<!--{/section}-->

<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="install">
<input type="hidden" name="update_id" value="<!--{$smarty.post.update_id}-->">
<input type="submit" value="インストール">
</form1>