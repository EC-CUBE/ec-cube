<?php

namespace Eccube\Entity;

use Doctrine\Common\Util\Inflector;

abstract class AbstractEntity implements \ArrayAccess
{

    public function offsetExists($offset)
    {
        $method = Inflector::classify($offset);

        return method_exists($this, "get$method") || method_exists($this, "is$method");
    }

    public function offsetSet($offset, $value)
    {
    }

    public function offsetGet($offset)
    {
        $method = Inflector::classify($offset);

        if (method_exists($this, "get$method")) {
            return $this->{"get$method"}();
        } elseif (method_exists($this, "is$method")) {
            return $this->{"is$method"}();
        }
    }

    public function offsetUnset($offset)
    {
    }

}
