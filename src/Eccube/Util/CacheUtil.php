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

namespace Eccube\Util;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheClearer\Psr6CacheClearer;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * キャッシュ関連のユーティリティクラス.
 */
class CacheUtil implements EventSubscriberInterface
{
    public const DOCTRINE_APP_CACHE_KEY = 'doctrine.app_cache_pool';
    private mixed $clearCacheAfterResponse = false;

    /**
     * CacheUtil constructor.
     */
    public function __construct(protected KernelInterface $kernel, private readonly ContainerInterface $container)
    {
    }

    /**
     * @param string $env
     */
    public function clearCache(?string $env = null): void
    {
        $this->clearCacheAfterResponse = $env;
    }

    /**
     * @throws \Exception
     */
    public function forceClearCache(TerminateEvent $event): string
    {
        if ($this->clearCacheAfterResponse === false) {
            return '';
        }

        $console = new Application($this->kernel);
        $console->setAutoExit(false);

        $command = [
            'command' => 'cache:clear',
            '--no-warmup' => true,
            '--no-ansi' => true,
        ];

        if ($this->clearCacheAfterResponse !== null) {
            $command['--env'] = $this->clearCacheAfterResponse;
        }

        $input = new ArrayInput($command);

        $output = new BufferedOutput(
            OutputInterface::VERBOSITY_DEBUG,
            true
        );

        $console->run($input, $output);

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        if (function_exists('apcu_clear_cache')) {
            apcu_clear_cache();
        }

        if (function_exists('wincache_ucache_clear')) {
            wincache_ucache_clear();
        }

        return $output->fetch();
    }

    /**
     * Doctrineのキャッシュを削除します.
     *
     * @throws \Exception
     */
    public function clearDoctrineCache(): ?string
    {
        /** @var Psr6CacheClearer $poolClearer */
        $poolClearer = $this->container->get('cache.global_clearer');
        if (!$poolClearer->hasPool(self::DOCTRINE_APP_CACHE_KEY)) {
            return null;
        }

        $console = new Application($this->kernel);
        $console->setAutoExit(false);

        $command = [
            'command' => 'cache:pool:clear',
            'pools' => [self::DOCTRINE_APP_CACHE_KEY],
            '--no-ansi' => true,
        ];

        $input = new ArrayInput($command);

        $output = new BufferedOutput(
            OutputInterface::VERBOSITY_DEBUG,
            true
        );

        $console->run($input, $output);

        return $output->fetch();
    }

    /**
     * Twigキャッシュを削除します.
     */
    public function clearTwigCache(): void
    {
        $cacheDir = $this->kernel->getCacheDir().'/twig';
        $fs = new Filesystem();
        $fs->remove($cacheDir);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::TERMINATE => 'forceClearCache'];
    }
}
