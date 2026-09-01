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

use Eccube\Common\EccubeConfig;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
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
    public function __construct(
        protected KernelInterface $kernel,
        private readonly ContainerInterface $container,
        private readonly EccubeConfig $eccubeConfig,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * cache:clear を実行できるか (= ビルド生成物を Web サーバーから作り直せるか).
     *
     * cache:clear は kernel.build_dir と kernel.cache_dir の双方へ書き込むため
     * (CacheClearCommand.php の is_writable() 検査), 権限を分離した構成では失敗する.
     * その場合はリクエスト処理中に生成されるキャッシュのみを削除し, ビルド生成物の
     * 再生成は CLI の eccube:cache:build に委ねる.
     */
    public function canClearBuildCache(): bool
    {
        return is_writable((string) $this->eccubeConfig->get('kernel.build_dir'))
            && is_writable((string) $this->eccubeConfig->get('kernel.cache_dir'));
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

        // 別の環境を指定された場合 (インストーラが prod を対象にする等) は, 現在の環境の
        // ディレクトリを見ても判定にならないため, 従来どおり cache:clear を試みる.
        if ($this->clearCacheAfterResponse === null && !$this->canClearBuildCache()) {
            return $this->clearRuntimeCache();
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
        $fs = new Filesystem();
        $fs->remove($this->runtimePath('twig'));

        // prod では事前コンパイル済みのテンプレート (kernel.build_dir/twig) が読み取り専用キャッシュ
        // として優先されるため, そちらを消さないと更新したテンプレートが反映されない.
        // 権限を分離した構成では削除できないため, その場合は eccube:cache:build の再実行が必要になる.
        $buildDir = rtrim((string) $this->eccubeConfig->get('kernel.build_dir'), '/');
        if (is_dir($buildDir.'/twig') && is_writable($buildDir)) {
            $fs->remove($buildDir.'/twig');
        }
    }

    /**
     * リクエスト処理中に生成されるキャッシュのみを削除します.
     *
     * ビルド生成物 (コンパイル済みコンテナ・ルーティング・事前コンパイル済みテンプレート) は
     * kernel.build_dir にあり Web サーバーから書き込めないため, ここでは削除しない.
     */
    public function clearRuntimeCache(): string
    {
        /** @var Psr6CacheClearer $poolClearer */
        $poolClearer = $this->container->get('cache.global_clearer');
        $poolClearer->clear((string) $this->eccubeConfig->get('eccube_runtime_dir'));

        $fs = new Filesystem();
        $fs->remove([
            $this->runtimePath('twig'),
            $this->runtimePath('translations'),
            $this->runtimePath('htmlpurifier'),
        ]);

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        $message = 'ビルドディレクトリへ書き込めないため, 実行時キャッシュのみ削除しました.'
            .' コンパイル済みコンテナとテンプレートを更新するには CLI で'
            .' bin/console eccube:cache:build を実行してください.';
        $this->logger->warning($message);

        return $message;
    }

    private function runtimePath(string $name): string
    {
        return rtrim((string) $this->eccubeConfig->get('eccube_runtime_dir'), '/').'/'.$name;
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
