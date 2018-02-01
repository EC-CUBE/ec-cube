<?php

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
     * @return mixed
     */
    public function get($key)
    {
        return $this->container->getParameter($key);
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return $this->container->hasParameter($key);
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value)
    {
        return $this->container->setParameter($key, $value);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @param mixed $offset
     * @throws \Exception
     */
    public function offsetUnset($offset)
    {
        throw new \Exception();
    }
}