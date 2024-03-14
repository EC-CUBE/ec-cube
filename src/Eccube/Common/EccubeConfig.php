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

namespace Eccube\Common;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class EccubeConfig implements \ArrayAccess
{
    /**
     * @var ContainerBagInterface
     */
    protected $container;

    public function __construct(ContainerBagInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->container->get($key);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->container->has($key);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        throw new \LogicException();
    }

    /**
     * @param mixed $offset
     *
     * @throws \LogicException
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        throw new \LogicException();
    }
}
