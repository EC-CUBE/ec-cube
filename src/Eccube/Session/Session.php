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

namespace Eccube\Session;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;

class Session implements SessionInterface, FlashBagAwareSessionInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

    public function start(): bool
    {
        return $this->getSession()->start();
    }

    public function getId(): string
    {
        return $this->getSession()->getId();
    }

    public function setId(string $id)
    {
        $this->getSession()->setId($id);
    }

    public function getName(): string
    {
        return $this->getSession()->getName();
    }

    public function setName(string $name)
    {
        $this->getSession()->setName($name);
    }

    public function invalidate(int $lifetime = null): bool
    {
        return $this->getSession()->invalidate($lifetime);
    }

    public function migrate(bool $destroy = false, int $lifetime = null): bool
    {
        return $this->getSession()->migrate($destroy, $lifetime);
    }

    public function save()
    {
        $this->getSession()->save();
    }

    public function has(string $name): bool
    {
        return $this->getSession()->has($name);
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->getSession()->get($name, $default);
    }

    public function set(string $name, mixed $value)
    {
        return $this->getSession()->set($name, $value);
    }

    public function all(): array
    {
        return $this->getSession()->all();
    }

    public function replace(array $attributes)
    {
        return $this->getSession()->replace($attributes);
    }

    public function remove(string $name): mixed
    {
        return $this->getSession()->remove($name);
    }

    public function clear()
    {
        $this->getSession()->clear();
    }

    public function isStarted(): bool
    {
        return $this->getSession()->isStarted();
    }

    public function registerBag(SessionBagInterface $bag)
    {
        $this->getSession()->registerBag($bag);
    }

    public function getBag(string $name): SessionBagInterface
    {
        return $this->getSession()->getBag();
    }

    public function getMetadataBag(): MetadataBag
    {
        return $this->getSession()->getMetadataBag();
    }

    public function getFlashBag(): FlashBagInterface
    {
        return $this->getSession()->getFlashBag();
    }
}
