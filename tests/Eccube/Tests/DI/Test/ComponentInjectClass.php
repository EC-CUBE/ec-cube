<?php

namespace Eccube\Tests\DI\Test;

use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;

/**
 * @Component
 */
class ComponentInjectClass
{
    /**
     * @Inject(ComponentClass::class)
     *
     * @var ComponentClass
     */
    public $component;
}
