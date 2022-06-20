<?php

namespace Eccube\Tests\Repository;

use Eccube\Repository\TradeLawRepository;
use Eccube\Tests\EccubeTestCase;

class TradeLawRepositoryTest extends EccubeTestCase
{
    /**
     * @var TradeLawRepository
     */
    private $tradeLawRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tradeLawRepository = $this->entityManager->getRepository(\Eccube\Entity\TradeLaw::class);
    }

    public function testInitialDataCount()
    {
        $initialTradeLawRows = $this->tradeLawRepository->findBy([], ['sortNo' => 'ASC']);

        // Check initial row count equals 15.
        $this->assertEquals(15, count($initialTradeLawRows));

        $notFoundNames = [
            1 => '販売業者', 2 => '代表責任者',3 => '所在地',4 => '電話番号',5 => 'メールアドレス',6 => 'URL', 7 => '商品代金以外の必要料金',
            8 => '引き渡し時期', 9 => 'お支払方法', 10 => '返品・交換について'
        ];

        $foundTimes = 1;

        foreach($initialTradeLawRows as $initialTradeLawRow) {
            // Check that all fields are turned off initially.
            $this->assertEquals(false, $initialTradeLawRow->isDisplayOrderScreen());
            $this->assertEquals($foundTimes, $initialTradeLawRow->getSortNo());
            if($foundTimes < 10) {
                $this->assertContains($initialTradeLawRow->getName(), $notFoundNames);
            }
            $this->assertNull($initialTradeLawRow->getDescription());
            $foundTimes++;
        }
        // Check that initial key values are found.
        $this->assertEquals(16, $foundTimes);
    }
}
