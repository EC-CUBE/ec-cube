<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\E2E;

use AcceptanceTester;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Codeception\Util\Fixtures;
use Codeception\Util\Locator;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Page\Admin\SalesReportPage;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertNotEmpty;

/**
 * @group plugin
 * @group e2e_plugin
 */
class PL04SalesReportCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    /**
     * @group install
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function sales_01(AcceptanceTester $I)
    {
        $I->retry(10, 200);
        if ($I->seePluginIsInstalled('売上集計プラグイン', true)) {
            $I->wantToUninstallPlugin('売上集計プラグイン');
            $I->seePluginIsNotInstalled('売上集計プラグイン');
        }
        $I->wantToInstallPlugin('売上集計プラグイン');
        $I->seePluginIsInstalled('売上集計プラグイン');
    }

    /**
     * @group install
     * @param AcceptanceTester $I
     * @return void
     */
    public function sales_02(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/store/plugin');
        $recommendPluginRow = Locator::contains('//tr', '売上集計プラグイン');
        $I->see('売上集計プラグイン');
        $I->see('無効', $recommendPluginRow);
        $I->clickWithLeftButton("(//tr[contains(.,'売上集計プラグイン')]//i[@class='fa fa-play fa-lg text-secondary'])[1]");
        $I->see('「売上集計プラグイン」を有効にしました。');
        $I->see('売上集計プラグイン', $recommendPluginRow);
        $I->see('有効', $recommendPluginRow);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function sales_03(AcceptanceTester $I)
    {
        // See Empty Orders Page
        $I->retry(10, 200);

        $createCustomer = Fixtures::get('createCustomer');
        $createOrders = Fixtures::get('createOrders');
        /** @var Order[] $newOrders */

        $newOrders = $createOrders(
            $createCustomer(),
            1,
            [],
            OrderStatus::IN_PROGRESS,
            CarbonPeriod::between(
                Carbon::createFromFormat('Y-m-d', '2022-06-02'),
                Carbon::createFromFormat('Y-m-d', '2022-06-30')
            )
        );

        $I->amOnPage('/admin/plugin/sales_report/term');
        $I->see('売上管理');
        $I->see('期間別集計');
        $I->selectOption('#sales_report_monthly_year', '2022');
        $I->selectOption('#sales_report_monthly_month', '6');
        $I->clickWithLeftButton(Locator::contains('//button', '月度で集計'));

        // check if html exists on page
        $I->seeInSource('<canvas id="chart"');

        // Check console log for errors
        $I->wait(10);
        $chartId = $I->executeJS('return Chart.instances[0].chart.canvas.id');
        var_dump($chartId);
        $I->assertNotEmpty($chartId);
        $I->assertEquals('chart', $chartId);

        // Check Graph data
        $graphData = $this->get_string_between($I->grabPageSource(), 'var graphData = ', ';');
        $I->assertJson($graphData);
        $I->assertNotEmpty((json_decode($graphData))->labels);
        $I->assertNotEmpty((json_decode($graphData))->datasets);

        // Check table data (gender)
        // Check table data (purchase total)
        // Check table data (average total)
        for ($y = 3; $y < 32; $y++) {
            $I->dontSee('￥0', sprintf("(//table[@id='term-table']//tr)[%s]//td[10]", $y));
            $I->dontSee('￥0', sprintf("(//table[@id='term-table']//tr)[%s]//td[11]", $y));

            if ($newOrders[$y - 3]->getCustomer()->getSex()->getId() === 1) {
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[3]", $y));
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[6]", $y));
            } elseif ($newOrders[$y - 3]->getCustomer()->getSex()->getId() === 2) {
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[4]", $y));
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[8]", $y));
            } else {
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[5]", $y));
            }
            $I->see(Carbon::instance($newOrders[$y - 3]->getOrderDate())->format('Y-m-d'), sprintf("(//table[@id='term-table']//tr)[%s]//td[1]", $y));
        }
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function sales_04(AcceptanceTester $I)
    {
        // See Empty Orders Page
        $I->retry(10, 200);

        $createCustomer = Fixtures::get('createCustomer');
        $createOrders = Fixtures::get('createOrders');
        /** @var Order[] $newOrders */

        $newOrders = $createOrders(
            $createCustomer(),
            1,
            [],
            OrderStatus::IN_PROGRESS,
            CarbonPeriod::between(
                Carbon::createFromFormat('Y-m-d', '2022-04-01'),
                Carbon::createFromFormat('Y-m-d', '2022-05-31')
            )
        );

        $I->amOnPage('/admin/plugin/sales_report/term');
        $I->see('売上管理');
        $I->see('期間別集計');

        $I->fillDate('#sales_report_term_start', Carbon::createFromFormat('Y-m-d', '2022-04-01'), 'jp', 3, 3);
        $I->fillDate('#sales_report_term_end', Carbon::createFromFormat('Y-m-d', '2022-05-31'), 'jp', 3, 3);

        $I->clickWithLeftButton(Locator::contains('//button', '期間で集計'));

        // check if html exists on page
        $I->seeInSource('<canvas id="chart"');

        // Check console log for errors
        $I->wait(10);
        $chartId = $I->executeJS('return Chart.instances[0].chart.canvas.id');
        var_dump($chartId);
        $I->assertNotEmpty($chartId);
        $I->assertEquals('chart', $chartId);

        // Check Graph data
        $graphData = $this->get_string_between($I->grabPageSource(), 'var graphData = ', ';');
        $I->assertJson($graphData);
        $I->assertNotEmpty((json_decode($graphData))->labels);
        $I->assertNotEmpty((json_decode($graphData))->datasets);

        for ($y = 2; $y < 62; $y++) {
            $I->dontSee('￥0', sprintf("(//table[@id='term-table']//tr)[%s]//td[10]", $y));
            $I->dontSee('￥0', sprintf("(//table[@id='term-table']//tr)[%s]//td[11]", $y));

            if ($newOrders[$y - 2]->getCustomer()->getSex()->getId() === 1) {
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[3]", $y));
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[6]", $y));
            } elseif ($newOrders[$y - 2]->getCustomer()->getSex()->getId() === 2) {
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[4]", $y));
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[8]", $y));
            } else {
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[5]", $y));
            }

            $I->see(Carbon::instance($newOrders[$y - 2]->getOrderDate())->format('Y-m-d'), sprintf("(//table[@id='term-table']//tr)[%s]//td[1]", $y));
        }
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function sales_05(AcceptanceTester $I)
    {
        // See Empty Orders Page
        $I->retry(10, 200);

        $createCustomer = Fixtures::get('createCustomer');
        $createOrders = Fixtures::get('createOrders');
        /** @var Customer $customer */
        $customer = $createCustomer();

        /** @var Order[] $newOrders */
        $newOrders = $createOrders(
            $customer,
            1,
            [],
            OrderStatus::IN_PROGRESS,
            CarbonPeriod::between(
                Carbon::createFromFormat('Y-m-d', '2022-01-01'),
                Carbon::createFromFormat('Y-m-d', '2022-03-31')
            )
        );

        $I->amOnPage('/admin/plugin/sales_report/term');
        $I->see('売上管理');
        $I->see('期間別集計');

        // 月別
        $I->checkOption('#sales_report_unit_1');
        $I->fillDate('#sales_report_term_start', Carbon::createFromFormat('Y-m-d', '2022-01-01'), 'jp', 3, 3);
        $I->fillDate('#sales_report_term_end', Carbon::createFromFormat('Y-m-d', '2022-03-31'), 'jp', 3, 3);

        $I->clickWithLeftButton(Locator::contains('//button', '期間で集計'));

        // check if html exists on page
        $I->seeInSource('<canvas id="chart"');

        // Check console log for errors
        $I->wait(10);
        $chartId = $I->executeJS('return Chart.instances[0].chart.canvas.id');
        var_dump($chartId);
        $I->assertNotEmpty($chartId);
        $I->assertEquals('chart', $chartId);

        // Check Graph data
        $graphData = $this->get_string_between($I->grabPageSource(), 'var graphData = ', ';');
        $I->assertJson($graphData);
        $I->assertNotEmpty((json_decode($graphData))->labels);
        $I->assertNotEmpty((json_decode($graphData))->datasets);

        for ($y = 1; $y < 4; $y++) {
            $int = ($y * 28) - random_int(1, 28);
            $monthRandomOrder = $newOrders[$int];
            $I->dontSee('￥0', sprintf("(//table[@id='term-table']//tr)[%s]//td[10]", $y + 1));
            $I->dontSee('￥0', sprintf("(//table[@id='term-table']//tr)[%s]//td[11]", $y + 1));

            if ($customer->getSex()->getId() === 1) {
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[3]", $y + 1));
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[6]", $y + 1));
            } elseif ($customer->getSex()->getId() === 2) {
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[4]", $y + 1));
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[8]", $y + 1));
            } else {
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[5]", $y + 1));
            }

            $I->see(Carbon::create(2022, $y, 2)->format('Y-m'), sprintf("(//table[@id='term-table']//tr)[%s]//td[1]", $y + 1));
        }
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function sales_06(AcceptanceTester $I)
    {
        // See Empty Orders Page
        $I->retry(10, 200);

        $createCustomer = Fixtures::get('createCustomer');
        $createOrders = Fixtures::get('createOrders');
        /** @var Customer $customer */
        $customer = $createCustomer();

        /** @var Order[] $newOrders */
        $newOrders = $createOrders(
            $customer,
            1,
            [],
            OrderStatus::IN_PROGRESS,
            CarbonPeriod::between(
                Carbon::createFromFormat('Y-m-d', '2022-04-01'),
                Carbon::createFromFormat('Y-m-d', '2022-04-14')
            )
        );

        $I->amOnPage('/admin/plugin/sales_report/term');
        $I->see('売上管理');
        $I->see('期間別集計');

        // 曜日別
        $I->checkOption('#sales_report_unit_2');
        $I->fillDate('#sales_report_term_start', Carbon::createFromFormat('Y-m-d', '2022-04-01'), 'jp', 3, 3);
        $I->fillDate('#sales_report_term_end', Carbon::createFromFormat('Y-m-d', '2022-04-14'), 'jp', 3, 3);
        $I->clickWithLeftButton(Locator::contains('//button', '期間で集計'));

        // check if html exists on page
        $I->seeInSource('<canvas id="chart"');

        // Check console log for errors
        $I->wait(10);
        $chartId = $I->executeJS('return Chart.instances[0].chart.canvas.id');
        var_dump($chartId);
        $I->assertNotEmpty($chartId);
        $I->assertEquals('chart', $chartId);

        // Check Graph data
        $graphData = $this->get_string_between($I->grabPageSource(), 'var graphData = ', ';');
        $I->assertJson($graphData);
        $I->assertNotEmpty((json_decode($graphData))->labels);
        $I->assertNotEmpty((json_decode($graphData))->datasets);

        $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        for ($y = 1; $y < 8; $y++) {
            $I->dontSee('￥0', sprintf("(//table[@id='term-table']//tr)[%s]//td[10]", $y + 1));
            $I->dontSee('￥0', sprintf("(//table[@id='term-table']//tr)[%s]//td[11]", $y + 1));

            if ($customer->getSex()->getId() === 1) {
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[3]", $y + 1));
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[6]", $y + 1));
            } elseif ($customer->getSex()->getId() === 2) {
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[4]", $y + 1));
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[8]", $y + 1));
            } else {
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[5]", $y + 1));
            }

            $I->see($daysOfWeek[$y - 1], sprintf("(//table[@id='term-table']//tr)[%s]//td[1]", $y + 1));
        }
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function sales_07(AcceptanceTester $I)
    {
        // See Empty Orders Page
        $I->retry(10, 200);

        $createCustomer = Fixtures::get('createCustomer');
        $createOrders = Fixtures::get('createOrders');
        /** @var Customer $customer */
        $customer = $createCustomer();

        /** @var Order[] $newOrders */
        $newOrders = $createOrders(
            $customer,
            1,
            [],
            OrderStatus::IN_PROGRESS,
            CarbonPeriod::createFromArray([
                '2022-05-01 00:00:00', 'PT1H', '2022-05-03 00:00:00', CarbonPeriod::EXCLUDE_END_DATE
            ])
        );

        $I->amOnPage('/admin/plugin/sales_report/term');
        $I->see('売上管理');
        $I->see('期間別集計');

        // 曜日別
        $I->checkOption('#sales_report_unit_3');
        $I->fillDate('#sales_report_term_start', Carbon::createFromFormat('Y-m-d', '2022-05-01'), 'jp', 3, 3);
        $I->fillDate('#sales_report_term_end', Carbon::createFromFormat('Y-m-d', '2022-05-02'), 'jp', 3, 3);
        $I->clickWithLeftButton(Locator::contains('//button', '期間で集計'));

        // check if html exists on page
        $I->seeInSource('<canvas id="chart"');

        // Check console log for errors
        $I->wait(10);
        $chartId = $I->executeJS('return Chart.instances[0].chart.canvas.id');
        $I->assertNotEmpty($chartId);
        $I->assertEquals('chart', $chartId);

        // Check Graph data
        $graphData = $this->get_string_between($I->grabPageSource(), 'var graphData = ', ';');
        $I->assertJson($graphData);
        $I->assertNotEmpty((json_decode($graphData))->labels);
        $I->assertNotEmpty((json_decode($graphData))->datasets);

        for ($y = 0; $y < 24; $y++) {
            $I->dontSee('￥0', sprintf("(//table[@id='term-table']//tr)[%s]//td[10]", $y + 2));
            $I->dontSee('￥0', sprintf("(//table[@id='term-table']//tr)[%s]//td[11]", $y + 2));

            if ($customer->getSex()->getId() === 1) {
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[3]", $y + 2));
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[6]", $y + 2));
            } elseif ($customer->getSex()->getId() === 2) {
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[4]", $y + 2));
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[8]", $y + 2));
            } else {
                $I->dontSee('0', sprintf("(//table[@id='term-table']//tr)[%s]//td[5]", $y + 2));
            }

            $I->see(str_pad($y, 2, '0', STR_PAD_LEFT), sprintf("(//table[@id='term-table']//tr)[%s]//td[1]", $y + 2));
        }
    }

    public function sales_08(AcceptanceTester $I)
    {

    }


    private function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}
