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

/**
 * @implements \ArrayAccess<string,mixed>
 */
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
     * @param string $key
     *
     * @return mixed
     */
    public function get($key): mixed
    {
        return $this->container->get($key);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key): bool
    {
        return $this->container->has($key);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    #[\Override]
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    #[\Override]
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    #[\ReturnTypeWillChange]
    #[\Override]
    public function offsetSet($offset, $value): void
    {
        throw new \LogicException();
    }

    /**
     * @param mixed $offset
     *
     * @throws \LogicException
     */
    #[\ReturnTypeWillChange]
    #[\Override]
    public function offsetUnset($offset): void
    {
        throw new \LogicException();
    }
}
