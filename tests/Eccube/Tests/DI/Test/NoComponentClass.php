<?php

namespace Eccube\Tests\DI\Test;

class NoComponentClass
{
public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
    }

    }
