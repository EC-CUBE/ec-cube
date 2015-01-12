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
use Eccube\Framework\Helper\DbHelper;

/**
 * ニュースを管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 */
class NewsHelper
{

    /**
     * ニュースの情報を取得.
     *
     * @param  integer $news_id     ニュースID
     * @param  boolean $has_deleted 削除されたニュースも含む場合 true; 初期値 false
     * @return array
     */
    public function getNews($news_id, $has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $col = '*, cast(news_date as date) as cast_news_date';
        $where = 'news_id = ?';
        if (!$has_deleted) {
            $where .= ' AND del_flg = 0';
        }
        $arrRet = $objQuery->select($col, 'dtb_news', $where, array($news_id));

        return $arrRet[0];
    }

    /**
     * ニュース一覧の取得.
     *
     * @param  integer $dispNumber  表示件数
     * @param  integer $pageNumber  ページ番号
     * @param  boolean $has_deleted 削除されたニュースも含む場合 true; 初期値 false
     * @return array
     */
    public function getList($dispNumber = 0, $pageNumber = 0, $has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $col = '*, cast(news_date as date) as cast_news_date';
        $where = '';
        if (!$has_deleted) {
            $where .= 'del_flg = 0';
        }
        $table = 'dtb_news';
        $objQuery->setOrder('rank DESC');
        if ($dispNumber > 0) {
            if ($pageNumber > 0) {
                $objQuery->setLimitOffset($dispNumber, (($pageNumber - 1) * $dispNumber));
            } else {
                $objQuery->setLimit($dispNumber);
            }
        }
        $arrRet = $objQuery->select($col, $table, $where);

        return $arrRet;
    }

    /**
     * ニュースの登録.
     *
     * @param  array    $sqlval
     * @return multiple 登録成功:ニュースID, 失敗:FALSE
     */
    public function saveNews($sqlval)
    {
        $objQuery = Application::alias('eccube.query');

        $news_id = $sqlval['news_id'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        // 新規登録
        if ($news_id == '') {
            // INSERTの実行
            $sqlval['rank'] = $objQuery->max('rank', 'dtb_news') + 1;
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            $sqlval['news_id'] = $objQuery->nextVal('dtb_news_news_id');
            $ret = $objQuery->insert('dtb_news', $sqlval);
        // 既存編集
        } else {
            unset($sqlval['creator_id']);
            unset($sqlval['create_date']);
            $where = 'news_id = ?';
            $ret = $objQuery->update('dtb_news', $sqlval, $where, array($news_id));
        }

        return ($ret) ? $sqlval['news_id'] : FALSE;
    }

    /**
     * ニュースの削除.
     *
     * @param  integer $news_id ニュースID
     * @return void
     */
    public function deleteNews($news_id)
    {
        // ランク付きレコードの削除
        Application::alias('eccube.helper.db')->deleteRankRecord('dtb_news', 'news_id', $news_id);
    }

    /**
     * ニュースの表示順をひとつ上げる.
     *
     * @param  integer $news_id ニュースID
     * @return void
     */
    public function rankUp($news_id)
    {
        Application::alias('eccube.helper.db')->rankUp('dtb_news', 'news_id', $news_id);
    }

    /**
     * ニュースの表示順をひとつ下げる.
     *
     * @param  integer $news_id ニュースID
     * @return void
     */
    public function rankDown($news_id)
    {
        Application::alias('eccube.helper.db')->rankDown('dtb_news', 'news_id', $news_id);
    }

    /**
     * ニュースの表示順を指定する.
     *
     * @param  integer $news_id ニュースID
     * @param  integer $rank    移動先の表示順
     * @return void
     */
    public function moveRank($news_id, $rank)
    {
        Application::alias('eccube.helper.db')->moveRank('dtb_news', 'news_id', $news_id, $rank);
    }

    /**
     * ニュース記事数を計算.
     *
     * @param  boolean $has_deleted 削除されたニュースも含む場合 true; 初期値 false
     * @return integer ニュース記事数
     */
    public function getCount($has_deleted = false)
    {
        if (!$has_deleted) {
            $where = 'del_flg = 0';
        } else {
            $where = '';
        }

        return Application::alias('eccube.helper.db')->countRecords('dtb_news', $where);
    }
}
