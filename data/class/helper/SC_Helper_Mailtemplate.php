<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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
 * メールテンプレートを管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 * @version $Id:$
 */
class SC_Helper_Mailtemplate
{
    /**
     * メールテンプレートの情報を取得.
     *
     * @param integer $template_id メールテンプレートID
     * @param boolean $has_deleted 削除されたメールテンプレートも含む場合 true; 初期値 false
     * @return array
     */
    public function get($template_id, $has_deleted = false)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
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
     * @param boolean $has_deleted 削除されたメールテンプレートも含む場合 true; 初期値 false
     * @return array
     */
    public function getList($has_deleted = false)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
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
     * @param array $sqlval
     * @return multiple 登録成功:メールテンプレートID, 失敗:FALSE
     */
    public function save($sqlval)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

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
