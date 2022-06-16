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
        $initialTradeLawRows = $this->tradeLawRepository->findAll();

        // Check initial row count equals 15.
        $this->assertEquals(15, count($initialTradeLawRows));

        $notFoundNames = [
            '販売業者', '代表責任者', '所在地', '電話番号', 'メールアドレス', 'URL', '商品代金以外の必要料金',
            '引き渡し時期', 'お支払方法', '返品・交換について'
        ];

        $foundTimes = 1;

        foreach($initialTradeLawRows as $initialTradeLawRow) {
            // Check that all fields are turned off initially.
            $this->assertEquals(false, $initialTradeLawRow->isDisplayOrderScreen());
            if (in_array($initialTradeLawRow->getName(), $notFoundNames)) {
                $foundTimes++;
            }
        }

        // Check that initial key values are found.
        $this->assertEquals(10, $foundTimes);
    }
}
