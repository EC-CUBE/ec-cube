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
    private readonly RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

    #[\Override]
    public function start(): bool
    {
        return $this->getSession()->start();
    }

    #[\Override]
    public function getId(): string
    {
        return $this->getSession()->getId();
    }

    #[\Override]
    public function setId(string $id): void
    {
        $this->getSession()->setId($id);
    }

    #[\Override]
    public function getName(): string
    {
        return $this->getSession()->getName();
    }

    #[\Override]
    public function setName(string $name): void
    {
        $this->getSession()->setName($name);
    }

    #[\Override]
    public function invalidate(?int $lifetime = null): bool
    {
        return $this->getSession()->invalidate($lifetime);
    }

    #[\Override]
    public function migrate(bool $destroy = false, ?int $lifetime = null): bool
    {
        return $this->getSession()->migrate($destroy, $lifetime);
    }

    #[\Override]
    public function save(): void
    {
        $this->getSession()->save();
    }

    #[\Override]
    public function has(string $name): bool
    {
        return $this->getSession()->has($name);
    }

    #[\Override]
    public function get(string $name, mixed $default = null): mixed
    {
        return $this->getSession()->get($name, $default);
    }

    #[\Override]
    public function set(string $name, mixed $value): void
    {
        $this->getSession()->set($name, $value);
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function all(): array
    {
        return $this->getSession()->all();
    }

    /**
     * @param array<string, mixed> $attributes
     */
    #[\Override]
    public function replace(array $attributes): void
    {
        $this->getSession()->replace($attributes);
    }

    #[\Override]
    public function remove(string $name): mixed
    {
        return $this->getSession()->remove($name);
    }

    #[\Override]
    public function clear(): void
    {
        $this->getSession()->clear();
    }

    #[\Override]
    public function isStarted(): bool
    {
        return $this->getSession()->isStarted();
    }

    #[\Override]
    public function registerBag(SessionBagInterface $bag): void
    {
        $this->getSession()->registerBag($bag);
    }

    #[\Override]
    public function getBag(string $name): SessionBagInterface
    {
        return $this->getSession()->getBag($name);
    }

    #[\Override]
    public function getMetadataBag(): MetadataBag
    {
        return $this->getSession()->getMetadataBag();
    }

    #[\Override]
    public function getFlashBag(): FlashBagInterface
    {
        /** @var FlashBagAwareSessionInterface $session */
        $session = $this->getSession();

        return $session->getFlashBag();
    }
}
