<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
 * SC_Helper_Purchaseのテストの基底クラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_News_TestBase extends Common_TestCase
{

    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * DBにニュース情報を設定します。
     */
    protected function setUpNews()
    {
        $news = array(
          array(
            'update_date' => '2000-01-01 00:00:00',
            'news_id' => '1001',
            'news_title' => 'ニュース情報01',
            'rank' => '1',
            'creator_id' => '1',
            'del_flg' => '0'
            ),
          array(
            'update_date' => '2000-01-01 00:00:00',
            'news_id' => '1002',
            'news_title' => 'ニュース情報02',
            'rank' => '2',
            'creator_id' => '1',
            'del_flg' => '0'
            ),
          array(
            'update_date' => '2000-01-01 00:00:00',
            'news_id' => '1003',
            'news_title' => 'ニュース情報03',
            'rank' => '3',
            'creator_id' => '1',
            'del_flg' => '1'
            ),
          array(
            'update_date' => '2000-01-01 00:00:00',
            'news_id' => '1004',
            'news_title' => 'ニュース情報04',
            'rank' => '4',
            'creator_id' => '1',
            'del_flg' => '0'
            )
        );

        $this->objQuery->delete('dtb_news');
        foreach ($news as $key => $item) {
            $this->objQuery->insert('dtb_news', $item);
        }
    }
}

