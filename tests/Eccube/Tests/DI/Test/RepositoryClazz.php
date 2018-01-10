<?php

namespace Eccube\Tests\DI\Test;

use Eccube\Annotation\Repository;

/**
 * @Repository
 */
class RepositoryClazz
{
public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
    }

    }
