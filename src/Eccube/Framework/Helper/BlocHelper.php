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
use Eccube\Framework\Helper\FileManagerHelper;
use Eccube\Framework\Helper\PageLayoutHelper;
use Eccube\Framework\Util\Utils;

/**
 * ブロックを管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 */
class BlocHelper
{
    private $device_type_id = NULL;

    public function __construct($devide_type_id = DEVICE_TYPE_PC)
    {
        $this->device_type_id = $devide_type_id;
    }

    /**
     * ブロックの情報を取得.
     *
     * @param  integer $bloc_id ブロックID
     * @return array
     */
    public function getBloc($bloc_id)
    {
        $objQuery = Application::alias('eccube.query');
        $col = '*';
        $where = 'bloc_id = ? AND device_type_id = ?';
        $arrRet = $objQuery->getRow($col, 'dtb_bloc', $where, array($bloc_id, $this->device_type_id));
        if (Utils::isAbsoluteRealPath($arrRet['tpl_path'])) {
            $tpl_path = $arrRet['tpl_path'];
        } else {
            $tpl_path = Application::alias('eccube.helper.page_layout')->getTemplatePath($this->device_type_id) . BLOC_DIR . $arrRet['tpl_path'];
        }
        if (file_exists($tpl_path)) {
            $arrRet['bloc_html'] = file_get_contents($tpl_path);
        }

        return $arrRet;
    }

    /**
     * ブロック一覧の取得.
     *
     * @return array
     */
    public function getList()
    {
        $objQuery = Application::alias('eccube.query');
        $col = '*';
        $where = 'device_type_id = ?';
        $table = 'dtb_bloc';
        $arrRet = $objQuery->select($col, $table, $where, array($this->device_type_id));

        return $arrRet;
    }

    /**
     * where句で条件を指定してブロック一覧を取得.
     *
     * @param string $where
     * @param array  $sqlval
     */
    public function getWhere($where = '', $sqlval = array())
    {
        $objQuery = Application::alias('eccube.query');
        $col = '*';
        $where = 'device_type_id = ? ' . (Utils::isBlank($where) ? $where : 'AND ' . $where);
        array_unshift($sqlval, $this->device_type_id);
        $table = 'dtb_bloc';
        $arrRet = $objQuery->select($col, $table, $where, $sqlval);

        return $arrRet;
    }

    /**
     * ブロックの登録.
     *
     * @param  array    $sqlval
     * @return multiple 登録成功:ブロックID, 失敗:FALSE
     */
    public function save($sqlval)
    {
        $objQuery = Application::alias('eccube.query');
        $objQuery->begin();

        // blod_id が空の場合は新規登録
        $is_new = Utils::isBlank($sqlval['bloc_id']);
        $bloc_dir = Application::alias('eccube.helper.page_layout')->getTemplatePath($sqlval['device_type_id']) . BLOC_DIR;
        // 既存データの重複チェック
        if (!$is_new) {
            $arrExists = $this->getBloc($sqlval['bloc_id']);

            // 既存のファイルが存在する場合は削除しておく
            $exists_file = $bloc_dir . $arrExists[0]['filename'] . '.tpl';
            if (file_exists($exists_file)) {
                unlink($exists_file);
            }
        }

        $table = 'dtb_bloc';
        $arrValues = $objQuery->extractOnlyColsOf($table, $sqlval);
        $arrValues['tpl_path'] = $sqlval['filename'] . '.tpl';
        $arrValues['update_date'] = 'CURRENT_TIMESTAMP';

        // 新規登録
        if ($is_new || Utils::isBlank($arrExists)) {
            $objQuery->setOrder('');
            $arrValues['bloc_id'] = 1 + $objQuery->max('bloc_id', $table, 'device_type_id = ?',
                                                       array($arrValues['device_type_id']));
            $arrValues['create_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->insert($table, $arrValues);
        // 更新
        } else {
            $objQuery->update($table, $arrValues, 'bloc_id = ? AND device_type_id = ?',
                              array($arrValues['bloc_id'], $arrValues['device_type_id']));
        }

        $bloc_path = $bloc_dir . $arrValues['tpl_path'];
        if (!Application::alias('eccube.helper.file_manager')->sfWriteFile($bloc_path, $sqlval['bloc_html'])) {
            $objQuery->rollback();

            return false;
        }

        $objQuery->commit();

        return $arrValues['bloc_id'];
    }

    /**
     * ブロックの削除.
     *
     * @param  integer $bloc_id
     * @return boolean
     */
    public function delete($bloc_id)
    {
        $objQuery = Application::alias('eccube.query');
        $objQuery->begin();

        $arrExists = $this->getWhere('bloc_id = ? AND deletable_flg = 1', array($bloc_id));
        $is_error = false;
        if (!Utils::isBlank($arrExists)) {
            $objQuery->delete('dtb_bloc', 'bloc_id = ? AND device_type_id = ?',
                              array($arrExists[0]['bloc_id'], $arrExists[0]['device_type_id']));
            $objQuery->delete('dtb_blocposition', 'bloc_id = ? AND device_type_id = ?',
                              array($arrExists[0]['bloc_id'], $arrExists[0]['device_type_id']));

            $bloc_dir = Application::alias('eccube.helper.page_layout')->getTemplatePath($this->device_type_id) . BLOC_DIR;
            $exists_file = $bloc_dir . $arrExists[0]['filename'] . '.tpl';

            // ファイルの削除
            if (file_exists($exists_file)) {
                if (!unlink($exists_file)) {
                    $is_error = true;
                }
            }
        } else {
            $is_error = true;
        }

        if ($is_error) {
            $objQuery->rollback();

            return false;
        }
        $objQuery->commit();

        return true;
    }

    /**
     * 端末種別IDのメンバー変数を取得.
     *
     * @return integer 端末種別ID
     */
    public function getDeviceTypeID()
    {
        return $this->device_type_id;
    }
}
