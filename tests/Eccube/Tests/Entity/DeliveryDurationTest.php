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

namespace Eccube\Tests\Entity;

use Eccube\Entity\DeliveryDuration;
use Eccube\Tests\EccubeTestCase;

class DeliveryDurationTest extends EccubeTestCase
{
    private $deliveryDurationRepository;

    public function setUp()
    {
        parent::setUp();
        $this->deliveryDurationRepository = $this->entityManager->getRepository(DeliveryDuration::class);
    }

    /**
     * @see https://github.com/EC-CUBE/ec-cube/pull/4405
     */
    public function testBackOrderedDuration()
    {
        $BackorderedId = 9;
        $BackorderedDuration = -1;

        $this->expected = $BackorderedDuration;
        $this->actual = $this->deliveryDurationRepository->find($BackorderedId)->getDuration();

        $this->verify();
    }
}
