<?php
$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
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
 * SC_Helper_Taxのテストの基底クラス.
 *
 * @author Nobuhiko Kimoto
 * @version $Id$
 */
class SC_Helper_TaxRule_TestBase extends Common_TestCase
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
     * DBにテストデータを登録する
     */
    protected function setUpTax()
    {
        $_SESSION['member_id'] = 1;
        $taxs = array(
            array(
                'tax_rule_id' => 1000,
                'apply_date' => '2014-01-01 00:00:00',
                'tax_rate' => '5',
                'product_id' => '0',
                'product_class_id' => '0',
                'del_flg' => '0',
                'member_id' => 1,
                'create_date' => 'CURRENT_TIMESTAMP',
                'update_date' => 'CURRENT_TIMESTAMP',
            ),
            array(
                'tax_rule_id' => 1001,
                'apply_date' => '2000-01-01 00:00:00',
                'tax_rate' => '6',
                'product_id' => '0',
                'product_class_id' => '0',
                'del_flg' => '0',
                'member_id' => 1,
                'create_date' => 'CURRENT_TIMESTAMP',
                'update_date' => 'CURRENT_TIMESTAMP',
            ),
            array(
                'tax_rule_id' => 1002,
                'apply_date' => '2099-02-01 00:00:00',
                'tax_rate' => '7',
                'product_id' => '0',
                'product_class_id' => '0',
                'del_flg' => '0',
                'member_id' => 1,
                'create_date' => 'CURRENT_TIMESTAMP',
                'update_date' => 'CURRENT_TIMESTAMP',
            ),
            array(
                'tax_rule_id' => 1003,
                'apply_date' => '2014-02-02 00:00:00',
                'tax_rate' => '8',
                'product_id' => '1000',
                'product_class_id' => '0',
                'del_flg' => '0',
                'member_id' => 1,
                'create_date' => 'CURRENT_TIMESTAMP',
                'update_date' => 'CURRENT_TIMESTAMP',
            ),
            array(
                'tax_rule_id' => 1004,
                'apply_date' => '2014-02-03 00:00:00',
                'tax_rate' => '9',
                'product_id' => '1000',
                'product_class_id' => '2000',
                'del_flg' => '0',
                'member_id' => 1,
                'create_date' => 'CURRENT_TIMESTAMP',
                'update_date' => 'CURRENT_TIMESTAMP',
            ),
        );

        $this->objQuery->delete('dtb_tax_rule');
        foreach ($taxs as $key => $item) {
            //$this->objQuery->insert('dtb_tax_rule', $item);
            $this->objTaxRule->setTaxRule(
                1,
                $item['tax_rate'],
                $item['apply_date'],
                NULL,
                0,
                $item['product_id'],
                $item['product_class_id']
            );
        }
    }
}
