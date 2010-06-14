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

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * ブロック の基底クラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_FrontParts_Bloc extends LC_Page {
    /**
     * ブロックファイルに応じて tpl_mainpage を設定する
     *
     * @param string $bloc_file ブロックファイル名
     * @return void
     */
    function setTplMainpage($bloc_file) {
        $debug_message = "";
        if (substr($bloc_file, 0, 1) == '/') {
            $this->tpl_mainpage = $bloc_file;
        } else {
            $user_bloc_path = USER_TEMPLATE_PATH . TEMPLATE_NAME . "/" . BLOC_DIR . $bloc_file;
            if (is_file($user_bloc_path)) {
                $this->tpl_mainpage = $user_bloc_path;
            } else {
                $this->tpl_mainpage = BLOC_PATH . $bloc_file;
            }
        }
        $debug_message = "block：" . $this->tpl_mainpage . "\n";
        GC_Utils::gfDebugLog($debug_message);
    }
}
?>
