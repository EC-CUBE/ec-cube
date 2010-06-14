<!--{*
/*
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

<form name="form1" method="post" action="?">
<input type="hidden" name="mode" value="install">
<input type="hidden" name="update_id" value="<!--{$smarty.post.update_id}-->">
<input type="button" name="back" value="戻る" onclick="location.href='./update.php'";>　<input type="submit" value="インストール">
</form1>
