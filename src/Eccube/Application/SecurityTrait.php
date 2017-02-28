<?php

namespace Eccube\Application;

/**
 * Security trait.
 */
trait SecurityTrait
{
    public function user()
    {
        return $this['user'];
    }
}
