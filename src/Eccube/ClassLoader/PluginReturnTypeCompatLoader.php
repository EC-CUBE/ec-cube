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

namespace Eccube\ClassLoader;

/**
 * Symfony 7 / EC-CUBE コアで追加された戻り値型宣言にプラグインを自動対応させるオートローダー.
 *
 * PHP では親クラス/インターフェースのメソッドに戻り値型がある場合,
 * 子クラスでも同じ型を宣言しなければ Fatal Error になる.
 *
 * このローダーは Plugin 名前空間のクラスファイルを読み込む際に,
 * 親クラス/インターフェースからリフレクションで戻り値型を取得し,
 * 子クラスに不足している戻り値型を自動補完する.
 */
class PluginReturnTypeCompatLoader
{
    private string $projectRoot;

    public function __construct(string $projectRoot)
    {
        $this->projectRoot = $projectRoot;
    }

    public function register(): void
    {
        spl_autoload_register([$this, 'loadClass'], true, true);
    }

    public function loadClass(string $class): void
    {
        if (!str_starts_with($class, 'Plugin\\')) {
            return;
        }

        $file = $this->resolveFile($class);
        if ($file === null) {
            return;
        }

        $source = file_get_contents($file);

        if (!preg_match('/\bclass\s+\w+\s+(extends|implements)\b/s', $source)) {
            return;
        }

        $typedMethods = $this->collectParentTypedMethods($source);
        if (empty($typedMethods)) {
            return;
        }

        $patched = $this->patchReturnTypes($source, $typedMethods);
        if ($patched === $source) {
            return;
        }

        eval('?>'.$patched);
    }

    private function resolveFile(string $class): ?string
    {
        $relativePath = str_replace('\\', '/', $class).'.php';
        $relativePath = preg_replace('#^Plugin/#', 'app/Plugin/', $relativePath);
        $file = $this->projectRoot.'/'.$relativePath;

        return is_file($file) ? $file : null;
    }

    /**
     * 親クラスとインターフェースから戻り値型が宣言されているメソッドを収集する.
     *
     * @return array<string, string> メソッド名 => 戻り値型文字列
     */
    private function collectParentTypedMethods(string $source): array
    {
        $fqcns = [];

        // extends
        if (preg_match('/class\s+\w+\s+extends\s+([\w\\\\]+)/', $source, $m)) {
            $fqcn = $this->resolveFqcn($m[1], $source);
            if ($fqcn !== null) {
                $fqcns[] = $fqcn;
            }
        }

        // implements (カンマ区切りで複数)
        if (preg_match('/class\s+\w+(?:\s+extends\s+[\w\\\\]+)?\s+implements\s+([\w\\\\,\s]+?)(?:\{|$)/s', $source, $m)) {
            foreach (preg_split('/\s*,\s*/', trim($m[1])) as $iface) {
                $fqcn = $this->resolveFqcn(trim($iface), $source);
                if ($fqcn !== null) {
                    $fqcns[] = $fqcn;
                }
            }
        }

        $typedMethods = [];
        foreach ($fqcns as $fqcn) {
            if (!class_exists($fqcn) && !interface_exists($fqcn)) {
                continue;
            }

            try {
                $ref = new \ReflectionClass($fqcn);
            } catch (\ReflectionException $e) {
                continue;
            }

            foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED) as $method) {
                $name = $method->getName();
                if (!$method->hasReturnType() || isset($typedMethods[$name])) {
                    continue;
                }
                $typedMethods[$name] = $this->returnTypeToString($method->getReturnType());
            }
        }

        return $typedMethods;
    }

    /**
     * ReflectionType を文字列に変換する.
     */
    private function returnTypeToString(\ReflectionType $type): string
    {
        if ($type instanceof \ReflectionNamedType) {
            $name = $type->getName();
            if ($type->allowsNull() && $name !== 'mixed' && $name !== 'void' && $name !== 'null') {
                return '?'.$name;
            }

            return $name;
        }

        // union / intersection types
        return (string) $type;
    }

    /**
     * ソース中のメソッドに不足している戻り値型を補完する.
     *
     * @param array<string, string> $typedMethods メソッド名 => 戻り値型
     */
    private function patchReturnTypes(string $source, array $typedMethods): string
    {
        foreach ($typedMethods as $methodName => $returnType) {
            // function methodName(...) { にマッチ (戻り値型がないもののみ)
            // 戻り値型がある場合は ) と { の間に : が入るためマッチしない
            $pattern = '/(function\s+'.preg_quote($methodName, '/').'\s*\([^)]*\))\s*\{/';
            $replacement = '$1: '.$returnType.' {';
            $source = preg_replace($pattern, $replacement, $source);
        }

        return $source;
    }

    /**
     * ソース中の use 文と namespace からクラスの FQCN を解決する.
     */
    private function resolveFqcn(string $shortName, string $source): ?string
    {
        if (str_contains($shortName, '\\')) {
            return ltrim($shortName, '\\');
        }

        // use 文から探す
        if (preg_match('/use\s+([\w\\\\]*\\\\'.preg_quote($shortName, '/').')\s*;/', $source, $m)) {
            return $m[1];
        }

        // 同一 namespace 内として解決
        if (preg_match('/namespace\s+([\w\\\\]+)\s*;/', $source, $m)) {
            return $m[1].'\\'.$shortName;
        }

        return null;
    }
}
