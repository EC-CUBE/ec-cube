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

use Symfony\Component\DependencyInjection\ContainerInterface;

class EccubeConfig implements \ArrayAccess
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
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
        return $this->container->getParameter($key);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->container->hasParameter($key);
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->container->setParameter($key, $value);
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
        $this->set($offset, $value);
    }

    /**
     * @param mixed $offset
     *
     * @throws \Exception
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        throw new \Exception();
    }
}
