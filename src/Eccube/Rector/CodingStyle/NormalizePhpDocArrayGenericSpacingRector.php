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

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * PHPDoc のジェネリクス表記のカンマ後のスペースを統一する Rector ルール
 *
 * @param, @return, @var タグの ClassName<key, value> 形式において、
 * カンマの後にスペースを1つ入れる形式に統一します。
 *
 * array だけでなく、Collection などの任意のクラスのジェネリクス表記に対応。
 *
 * 例:
 *
 * @param array<string,mixed> $options
 * @param array<string, mixed> $options
 *
 * @var Collection<int,Product> $products
 * ↓
 * @var Collection<int, Product> $products
 */
final class NormalizePhpDocArrayGenericSpacingRector extends AbstractRector
{
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Property::class];
    }

    /**
     * @param ClassMethod|Property $node
     */
    public function refactor(Node $node): ?Node
    {
        $docComment = $node->getDocComment();
        if ($docComment === null) {
            return null;
        }

        $originalText = $docComment->getText();
        $normalizedText = $this->normalizeGenericSpacing($originalText);

        // 変更があった場合のみノードを更新
        if ($originalText !== $normalizedText) {
            $node->setDocComment(new Doc($normalizedText));

            return $node;
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'PHPDoc のジェネリクス表記のカンマ後のスペースを統一する',
            [
                new CodeSample(
                    // Before
                    <<<'CODE_SAMPLE'
/**
 * @param array<string,mixed> $options
 * @var Collection<int,Product> $products
 * @return array<int,string>
 */
public function process(array $options): array
{
}
CODE_SAMPLE,
                    // After
                    <<<'CODE_SAMPLE'
/**
 * @param array<string, mixed> $options
 * @var Collection<int, Product> $products
 * @return array<int, string>
 */
public function process(array $options): array
{
}
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * ジェネリクス表記のカンマ後のスペースを統一する
     *
     * ClassName<key,value> を ClassName<key, value> に変換
     * ネストしたジェネリクス (Collection<int, array<string,mixed>>) にも対応
     *
     * 対応パターン:
     * - array<key,value>
     * - Collection<key,value>
     * - 任意のクラス名<key,value>
     */
    private function normalizeGenericSpacing(string $docComment): string
    {
        // ClassName<...,...> パターンを検出して、カンマの後にスペースがない場合はスペースを追加
        // \b[a-zA-Z_][a-zA-Z0-9_\\]* でクラス名を検出（array, Collection, SlidingPagination など）
        // ネストに対応するため、再帰的に処理
        return preg_replace_callback(
            '/\b([a-zA-Z_][a-zA-Z0-9_\\\\]*)<([^<>]+(?:<[^<>]+>)*)>/',
            function ($matches) {
                $className = $matches[1];
                $content = $matches[2];

                // カンマの後にスペースがない箇所を検出して修正
                // ただし、既にスペースがある場合は何もしない
                $normalized = preg_replace(
                    '/,(?! )/',  // カンマの後にスペースがない
                    ', ',         // カンマ + スペースに置換
                    $content
                );

                return $className.'<'.$normalized.'>';
            },
            $docComment
        );
    }
}
