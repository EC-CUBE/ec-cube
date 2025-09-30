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

use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Symfony\Component\DependencyInjection\Argument\ArgumentInterface;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * ORM3 環境で DoctrineBundle が AttributeDriver に第2/第3引数を渡す定義を、
 * 最終的に「paths の 1 引数」に統一し、Configuration の setMetadataDriverImpl も
 * 対応する *_attribute_metadata_driver サービス参照へ差し替える。
 */
final class StripReportFieldsArgPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $c): void
    {
        // 1) doctrine.orm.<em>_attribute_metadata_driver を 1 引数へ
        foreach ($c->getDefinitions() as $id => $def) {
            if ($this->isOrmAttrDriverServiceId($id)) {
                $this->forceOneArg($def);
            }
        }

        // 2) doctrine.orm.<em>_configuration の setMetadataDriverImpl を driver サービス参照に置換
        foreach ($c->getDefinitions() as $id => $def) {
            if ($this->isOrmConfigurationId($id)) {
                $this->fixOrmConfiguration($c, $id, $def);
            }
        }

        // 3) 念のため全定義を走査し、inline の AttributeDriver（派生含む）も 1 引数へ
        foreach ($c->getDefinitions() as $def) {
            $this->fixDefinitionDeep($c, $def);
        }
    }

    private function isOrmAttrDriverServiceId(string $id): bool
    {
        return str_starts_with($id, 'doctrine.orm.')
            && str_ends_with($id, '_attribute_metadata_driver');
    }

    private function isOrmConfigurationId(string $id): bool
    {
        return str_starts_with($id, 'doctrine.orm.')
            && str_ends_with($id, '_configuration');
    }

    private function emNameFromConfigId(string $id): string
    {
        return preg_replace('#^doctrine\.orm\.|_configuration$#', '', $id) ?: 'default';
    }

    private function driverServiceIdForEm(string $em): string
    {
        return "doctrine.orm.{$em}_attribute_metadata_driver";
    }

    private function forceOneArg(Definition $def): void
    {
        $args = $def->getArguments();
        if (\count($args) >= 1) {
            // paths の 1 引数だけにする（第2/第3引数を捨てる）
            $def->setArguments([$args[0]]);
            // trigger_error('AttributeDriver arguments are fixed to 1 (paths) by StripReportFieldsArgPass.', E_USER_DEPRECATED);
        }
    }

    private function fixOrmConfiguration(ContainerBuilder $c, string $id, Definition $def): void
    {
        // 念のためクラス確認（Configuration 以外でも ID で処理継続）
        $cls = $def->getClass();
        if (is_string($cls)) {
            $cls = $c->getParameterBag()->resolveValue($cls);
        }
        // 既存の setMetadataDriverImpl を除去
        $calls = array_values(array_filter(
            $def->getMethodCalls(),
            static fn (array $mc): bool => ($mc[0] ?? '') !== 'setMetadataDriverImpl'
        ));
        $def->setMethodCalls($calls);

        // 対象EMの driver サービス参照を明示設定
        $em = $this->emNameFromConfigId($id);
        $driverId = $this->driverServiceIdForEm($em);
        if ($c->hasDefinition($driverId) || $c->hasAlias($driverId)) {
            $def->addMethodCall('setMetadataDriverImpl', [new Reference($driverId)]);

            return;
        }

        // driver サービスが無い（想定外）場合は、既存の inline 定義を 1 引数へ矯正
        foreach ($def->getMethodCalls() as [$m, $mArgs]) {
            if ($m === 'setMetadataDriverImpl' && !empty($mArgs)) {
                $this->fixValueDeep($c, $mArgs[0]);
            }
        }
    }

    private function fixDefinitionDeep(ContainerBuilder $c, Definition $def): void
    {
        $cls = $def->getClass();
        if (is_string($cls)) {
            $cls = $c->getParameterBag()->resolveValue($cls);
            if (is_string($cls) && is_a($cls, AttributeDriver::class, true)) {
                $this->forceOneArg($def);
            }
        }
        foreach ($def->getArguments() as $v) {
            $this->fixValueDeep($c, $v);
        }
        foreach ($def->getMethodCalls() as [$m, $mArgs]) {
            foreach ($mArgs as $mv) {
                $this->fixValueDeep($c, $mv);
            }
        }
    }

    private function fixValueDeep(ContainerBuilder $c, mixed $value): void
    {
        if ($value instanceof Definition) {
            $this->fixDefinitionDeep($c, $value);

            return;
        }
        if ($value instanceof ServiceClosureArgument) {
            $inner = $value->getValues()[0] ?? null;
            if ($inner instanceof Definition) {
                $this->fixDefinitionDeep($c, $inner);
            }

            return;
        }
        if (is_array($value)) {
            foreach ($value as $v) {
                $this->fixValueDeep($c, $v);
            }

            return;
        }
        if ($value instanceof Reference || $value instanceof ArgumentInterface) {
            return; // 実体はないのでスキップ
        }
    }
}
