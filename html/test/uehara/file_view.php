<?php
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

require_once("../../require.php");

// ソースとして表示するファイルを定義(直接実行しないファイル)
$arrViewFile = array(
					 'html',
					 'htm',
					 'tpl',
					 'php',
					 'css',
					 'js',
);

// 拡張子取得
$arrResult = split('\.', $_GET['file']);
$ext = $arrResult[count($arrResult)-1];

// ファイル内容表示
if(in_array($ext, $arrViewFile)) {
	// ファイルを読み込んで表示
	header("Content-type: text/plain\n\n");
	print(sfReadFile(USER_PATH.$_GET['file']));
} else {
	header("Location: ".USER_URL.$_GET['file']);
}
?>
