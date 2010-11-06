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
require_once(CLASS_PATH . "pages/admin/LC_Page_Admin.php");

/**
 * ログ のページクラス.
 *
 * @package Page
 * @author Seasoft 塚田将久
 * @version $Id: LC_Page_Admin_System_System.php 17576 2008-08-28 06:08:09Z Seasoft $
 */
class LC_Page_Admin_System_Log extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'system/log.tpl';
        $this->tpl_subnavi  = 'system/subnavi.tpl';
        $this->tpl_subno    = 'log';
        $this->tpl_mainno   = 'system';
        $this->tpl_subtitle = 'ログ表示';
        $this->line_max     = 50;
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        SC_Utils_Ex::sfIsSuccess(new SC_Session);
        
        $this->lfInitParam();
        
        if (SC_Utils::sfIsInt($tmp = $this->objForm->getValue('line'))) {
            $this->line_max = $tmp;
        }
        
        $this->tpl_ec_log = $this->getEccubeLog();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
    
    /**
     * パラメータの初期化
     *
     * @return array
     */
    function lfInitParam() {
        $this->objForm = new SC_FormParam;
        $this->objForm->addParam('line_max', 'line_max', INT_LEN, '', array('NUM_CHECK', 'MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        $this->objForm->setParam($_POST);
    }

    /**
     * EC-CUBE ログを取得する
     *
     * @return array
     */
    function getEccubeLog() {
        
        $index = 0;
        $arrLogs = array();
        for ($gen = 0 ; $gen <= MAX_LOG_QUANTITY; $gen++) {
            $path = LOG_PATH;
            if ($gen != 0) {
                $path .= ".$gen";
            }
            
            // ファイルが存在しない場合、前世代のログへ
            if (!file_exists($path)) continue;
            
            $arrLogTmp = array_reverse(file($path));
            
            $arrBodyReverse = array();
            foreach ($arrLogTmp as $line) {
                $line = chop($line);
                if (preg_match('/^(\d+\/\d+\/\d+ \d+:\d+:\d+) \[([^\]]+)\] (.*)$/', $line, $arrMatch)) {
                    $arrLogLine = array();
                    // 日時
                    $arrLogLine['date'] = $arrMatch[1];
                    // パス
                    $arrLogLine['path'] = $arrMatch[2];
                    // 内容
                    $arrBodyReverse[] = $arrMatch[3];
                    $arrLogLine['body'] = implode("\n", array_reverse($arrBodyReverse));
                    $arrBodyReverse = array();
                    
                    $arrLogs[] = $arrLogLine;
                    
                    // 上限に達した場合、処理を抜ける
                    if (count($arrLogs) >= $this->line_max) break 2;
                } else {
                    // 内容
                    $arrBodyReverse[] = $line;
                }
            }
        }
        return $arrLogs;
    }
}
