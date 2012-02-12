<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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

/**
 * PEAR モジュール HTTP_Request の読み込みを行う
 *
 * r21237 より前の誤ったファイル配置を前提とした決済モジュールでの利用を意図している。
 * EC-CUBE 本体や一般的なカスタマイズにおいては、HTTP ディレクトリ配下の Request.php を直接利用する。
 * @deprecated
 */

trigger_error('従来互換用の HTTP_Request が読み込まれました。', E_WARNING);
require_once DATA_REALDIR . 'module/HTTP/Request.php';
