<?php

namespace Eccube\Tests\Di\Test;

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
