<?php

namespace Eccube\Tests\DI\Test;

use Eccube\Annotation\Component;

/**
 * @Component
 */
class ComponentClass
{
public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
    }

    }
