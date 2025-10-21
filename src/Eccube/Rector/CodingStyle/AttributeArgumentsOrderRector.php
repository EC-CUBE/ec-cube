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

namespace Eccube\Rector\CodingStyle;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * すべての Attribute の引数をコンストラクタ引数順序に統一する Rector ルール
 *
 * リフレクションを使用してコンストラクタのパラメータ順序を取得し、
 * それに従って引数を並び替えます。
 *
 * 例:
 * #[Route('/path', requirements: ['id' => '\d+'], name: 'route_name')]
 * ↓
 * #[Route(path: '/path', name: 'route_name', requirements: ['id' => '\d+'])]
 */
final class AttributeArgumentsOrderRector extends AbstractRector
{
    /**
     * コンストラクタパラメータ順序のキャッシュ
     *
     * @var array<string, array<string, int>>
     */
    private array $constructorParameterOrderCache = [];

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node->attrGroups === []) {
            return null;
        }

        $hasChanged = false;

        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                if ($this->normalizeAttributeArguments($attribute)) {
                    $hasChanged = true;
                }
            }
        }

        return $hasChanged ? $node : null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'すべての Attribute の引数をコンストラクタ引数順序に統一する',
            [
                new CodeSample(
                    // Before
                    <<<'CODE_SAMPLE'
#[Route('/contact', requirements: ['id' => '\d+'], name: 'contact', methods: ['GET', 'POST'])]
public function contact() {}
CODE_SAMPLE,
                    // After
                    <<<'CODE_SAMPLE'
#[Route(path: '/contact', name: 'contact', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
public function contact() {}
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * Attribute の引数を正規化する
     *
     * 1. 第一引数が名前なし引数の場合、コンストラクタの第一パラメータ名を付与
     * 2. 引数をコンストラクタパラメータの順序に並び替え
     *
     * @return bool 変更があった場合 true
     */
    private function normalizeAttributeArguments(Attribute $attribute): bool
    {
        if ($attribute->args === []) {
            return false;
        }

        // Attribute のクラス名を取得
        $className = $this->resolveAttributeClassName($attribute);
        if ($className === null) {
            return false;
        }

        // コンストラクタのパラメータ順序を取得
        $parameterOrder = $this->getConstructorParameterOrder($className);
        if ($parameterOrder === []) {
            return false;
        }

        $hasChanged = false;
        $originalArgs = $attribute->args;

        // 第一引数が名前なし引数の場合、第一パラメータ名を付与
        $firstArg = $attribute->args[0] ?? null;
        if ($firstArg instanceof Arg && $firstArg->name === null) {
            $firstParameterName = array_key_first($parameterOrder);
            if ($firstParameterName !== null) {
                $firstArg->name = new Identifier($firstParameterName);
                $hasChanged = true;
            }
        }

        // 引数を名前でグループ化
        $argsByName = [];
        $unnamedArgs = [];
        foreach ($attribute->args as $arg) {
            if ($arg->name instanceof Identifier) {
                $argsByName[$arg->name->toString()] = $arg;
            } else {
                $unnamedArgs[] = $arg;
            }
        }

        // 名前なし引数が残っている場合は処理をスキップ（安全のため）
        if ($unnamedArgs !== []) {
            return $hasChanged;
        }

        // パラメータ順序に従って引数を並び替え
        $sortedArgs = [];
        foreach ($parameterOrder as $paramName => $position) {
            if (isset($argsByName[$paramName])) {
                $sortedArgs[] = $argsByName[$paramName];
                unset($argsByName[$paramName]);
            }
        }

        // 定義されていない引数は末尾に追加（プラグインなどで拡張されている場合に備えて）
        foreach ($argsByName as $arg) {
            $sortedArgs[] = $arg;
        }

        // 順序が変わったかチェック
        if ($this->hasArgumentOrderChanged($originalArgs, $sortedArgs)) {
            $attribute->args = $sortedArgs;
            $hasChanged = true;
        }

        return $hasChanged;
    }

    /**
     * Attribute のクラス名を解決する
     */
    private function resolveAttributeClassName(Attribute $attribute): ?string
    {
        $name = $attribute->name;

        // 完全修飾名の場合
        if ($name instanceof FullyQualified) {
            return $name->toString();
        }

        // 相対名の場合は use 文を考慮して解決
        $name->toString();

        // use 文から完全修飾名を取得
        return $this->nodeNameResolver->getName($name);
    }

    /**
     * コンストラクタのパラメータ順序を取得する
     *
     * @return array<string, int> パラメータ名 => 位置のマップ
     */
    private function getConstructorParameterOrder(string $className): array
    {
        // キャッシュをチェック
        if (isset($this->constructorParameterOrderCache[$className])) {
            return $this->constructorParameterOrderCache[$className];
        }

        try {
            $reflection = new \ReflectionClass($className);
            $constructor = $reflection->getConstructor();

            if ($constructor === null) {
                $this->constructorParameterOrderCache[$className] = [];

                return [];
            }

            $parameterOrder = [];
            $position = 0;
            foreach ($constructor->getParameters() as $parameter) {
                $parameterOrder[$parameter->getName()] = $position++;
            }

            $this->constructorParameterOrderCache[$className] = $parameterOrder;

            return $parameterOrder;
        } catch (\ReflectionException) {
            // クラスが見つからない場合はスキップ
            $this->constructorParameterOrderCache[$className] = [];

            return [];
        }
    }

    /**
     * 引数の順序が変わったかチェック
     *
     * @param Arg[] $originalArgs
     * @param Arg[] $sortedArgs
     */
    private function hasArgumentOrderChanged(array $originalArgs, array $sortedArgs): bool
    {
        if (count($originalArgs) !== count($sortedArgs)) {
            return true;
        }

        foreach ($originalArgs as $index => $originalArg) {
            $sortedArg = $sortedArgs[$index] ?? null;

            if ($sortedArg === null) {
                return true;
            }

            $originalName = $originalArg->name instanceof Identifier ? $originalArg->name->toString() : null;
            $sortedName = $sortedArg->name instanceof Identifier ? $sortedArg->name->toString() : null;

            if ($originalName !== $sortedName) {
                return true;
            }
        }

        return false;
    }
}
