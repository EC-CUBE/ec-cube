<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework\Helper;

use Eccube\Application;
use Eccube\Framework\Query;

/**
 * メールテンプレートを管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 */
class MailtemplateHelper
{
    /**
     * メールテンプレートの情報を取得.
     *
     * @param  integer $template_id メールテンプレートID
     * @param  boolean $has_deleted 削除されたメールテンプレートも含む場合 true; 初期値 false
     * @return array
     */
    public function get($template_id, $has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $col = '*';
        $where = 'template_id = ?';
        if (!$has_deleted) {
            $where .= ' AND del_flg = 0';
        }
        $arrRet = $objQuery->select($col, 'dtb_mailtemplate', $where, array($template_id));

        return $arrRet[0];
    }

    /**
     * メールテンプレート一覧の取得.
     *
     * @param  boolean $has_deleted 削除されたメールテンプレートも含む場合 true; 初期値 false
     * @return array
     */
    public function getList($has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $col = '*';
        $where = '';
        if (!$has_deleted) {
            $where .= 'del_flg = 0';
        }
        $table = 'dtb_mailtemplate';
        $arrRet = $objQuery->select($col, $table, $where);

        return $arrRet;
    }

    /**
     * メールテンプレートの登録.
     *
     * @param  array    $sqlval
     * @return multiple 登録成功:メールテンプレートID, 失敗:FALSE
     */
    public function save($sqlval)
    {
        $objQuery = Application::alias('eccube.query');

        $template_id = $sqlval['template_id'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        // 存在確認
        $where = 'template_id = ?';
        $exist = $objQuery->exists('dtb_mailtemplate', $where, array($template_id));
        // 新規登録
        if (!$exist) {
            // INSERTの実行
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            if (!$sqlval['template_id']) {
                $sqlval['template_id'] = $objQuery->nextVal('dtb_mailtemplate_template_id');
            }
            $ret = $objQuery->insert('dtb_mailtemplate', $sqlval);
        // 既存編集
        } else {
            unset($sqlval['creator_id']);
            unset($sqlval['create_date']);
            $ret = $objQuery->update('dtb_mailtemplate', $sqlval, $where, array($template_id));
        }

        return ($ret) ? $sqlval['template_id'] : FALSE;
    }
}
