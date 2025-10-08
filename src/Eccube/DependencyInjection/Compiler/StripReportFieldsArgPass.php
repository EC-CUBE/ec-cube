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

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Symfony\Component\DependencyInjection\Argument\ArgumentInterface;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * ORM3 環境で DoctrineBundle が AttributeDriver に第2/第3引数を渡す定義を、
 * 最終的に「paths の 1 引数」に統一する。
 * MappingDriverChain は維持し、SchemaService が複数の名前空間を扱えるようにする。
 * See https://github.com/doctrine/DoctrineBundle/issues/1844
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

        // 2) MappingDriverChain の addDriver() 呼び出しを修正
        foreach ($c->getDefinitions() as $id => $def) {
            if ($this->isMappingDriverChainId($id)) {
                $this->fixMappingDriverChainCalls($c, $id, $def);
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
            && (str_ends_with($id, '_attribute_metadata_driver')
                || str_ends_with($id, '_attribute_metadata_driver.inner'));
    }

    private function isMappingDriverChainId(string $id): bool
    {
        return str_starts_with($id, 'doctrine.orm.')
            && str_ends_with($id, '_metadata_driver');
    }

    private function forceOneArg(Definition $def): void
    {
        $args = $def->getArguments();
        if (\count($args) > 1) {
            // paths の 1 引数だけにする（第2/第3引数を捨てる）
            $def->setArguments([$args[0]]);
        }
    }

    private function fixMappingDriverChainCalls(ContainerBuilder $c, string $chainId, Definition $def): void
    {
        $calls = $def->getMethodCalls();
        foreach ($calls as $i => [$method, $args]) {
            if ($method === 'addDriver' && isset($args[0])) {
                // addDriver() の第1引数（ドライバー）を修正
                $args[0] = $this->fixDriverArgument($c, $args[0]);
                $calls[$i] = [$method, $args];
            }
        }
        $def->setMethodCalls($calls);
    }

    private function fixDriverArgument(ContainerBuilder $c, mixed $driver): mixed
    {
        if ($driver instanceof Definition) {
            $this->fixDefinitionDeep($c, $driver);

            return $driver;
        }

        return $driver;
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
