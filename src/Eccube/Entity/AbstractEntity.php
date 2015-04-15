<?php
namespace Eccube\Entity;

use Doctrine\Common\Util\Inflector;

abstract class AbstractEntity implements \ArrayAccess
{
    public function offsetExists($offset) {
        $method = Inflector::classify($offset);
        return method_exists($this, "get$method");
    }

    public function offsetSet($offset, $value) {
    }

    public function offsetGet($offset) {
        $method = Inflector::classify($offset);
        return $this->{"get$method"}();
    }

    public function offsetUnset($offset) {
    }
}
