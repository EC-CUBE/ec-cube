<?php

namespace Eccube\Tests\DI\Test;

use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;

/**
 * @Component
 */
class ComponentInjectClass
{
    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
    }

    /**
     * @Inject(ComponentClass::class)
     *
     * @var ComponentClass
     */
    public $component;
}
