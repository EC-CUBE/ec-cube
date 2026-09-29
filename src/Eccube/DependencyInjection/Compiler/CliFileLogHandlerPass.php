<?php

declare(strict_types=1);

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

namespace Eccube\DependencyInjection\Compiler;

use Eccube\Log\CliSuppressibleHandler;
use Monolog\Handler\StreamHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * ファイルへ書き込む monolog のハンドラを CliSuppressibleHandler でラップする.
 *
 * 権限を分離した構成では var/log が Web サーバー所有 (レーン W) になり, CLI からは
 * 書き込めない. ECCUBE_CLI_LOG_TO_FILE=0 のとき, CLI 実行時だけファイルへの書き込みを
 * 止められるようにする.
 *
 * コンパイル済みコンテナは Web と CLI で共有するため, ここで実行経路を判定することはできない.
 * 判定はハンドラ側 (実行時) で行い, 本パスはラップのみを担う.
 *
 * 対象は StreamHandler を継承したハンドラ (rotating_file / stream) に限る.
 * ConsoleHandler や FingersCrossedHandler はファイルを開かないため対象外だが,
 * FingersCrossedHandler が委譲する先 (main_rotating_file 等) はラップされるため,
 * バッファされたレコードの書き出しも抑止される.
 */
class CliFileLogHandlerPass implements CompilerPassInterface
{
    private const HANDLER_ID_PREFIX = 'monolog.handler.';

    private const INNER_ID_SUFFIX = '.cli_suppressible.inner';

    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            if (!str_starts_with($id, self::HANDLER_ID_PREFIX)) {
                continue;
            }
            if (str_ends_with($id, self::INNER_ID_SUFFIX)) {
                continue;
            }

            $class = $definition->getClass();
            if (null === $class) {
                continue;
            }

            $class = (string) $container->getParameterBag()->resolveValue($class);
            if (!is_a($class, StreamHandler::class, true)) {
                continue;
            }

            $innerId = $id.self::INNER_ID_SUFFIX;
            $container->setDefinition($innerId, $definition);
            $container->setDefinition($id, new Definition(CliSuppressibleHandler::class, [
                new Reference($innerId),
                '%env(bool:ECCUBE_CLI_LOG_TO_FILE)%',
            ]));
        }
    }
}
