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

namespace Eccube\PHPStan\Rules\Mcp;

use Eccube\Service\Mcp\McpAuditLogger;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Psr\Log\LoggerInterface;

/**
 * MCP の `mcp` チャネルへのログ書き込みを `McpAuditLogger` に集約させるための静的解析ルール (設計 §8 #4)。
 *
 * 検出対象は **`__construct` の引数名 `$mcpLogger`** (Symfony Monolog の autowire 規約で `mcp` チャネルの
 * `LoggerInterface` が自動 inject される名前)。 これを `McpAuditLogger` 以外のクラスで持つと、 監査ログの
 * Single Source of Truth が崩れるため error とする。
 *
 * カスタマイズ側 (`app/Customize/` / `app/Plugin/`) でも検出したい場合は phpstan の `paths` 設定を拡張する。
 * core ベンダ側では `src/Eccube` を対象にこのルールを適用済み。
 *
 * @implements Rule<ClassMethod>
 */
final class NoDirectMcpLoggerInjectionRule implements Rule
{
    private const ERROR_IDENTIFIER = 'eccube.mcp.directLoggerInjection';

    /**
     * 検出をスキップする許可クラス。 `McpAuditLogger` 自身は `$mcpLogger` を inject するのが本来の役割なので除外する。
     *
     * @var list<class-string>
     */
    private const ALLOWED_CLASSES = [
        McpAuditLogger::class,
    ];

    #[\Override]
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    #[\Override]
    public function processNode(Node $node, Scope $scope): array
    {
        // $node は generic implements により ClassMethod であることが保証される
        if ('__construct' !== $node->name->toString()) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (null === $classReflection) {
            return [];
        }
        if (\in_array($classReflection->getName(), self::ALLOWED_CLASSES, true)) {
            return [];
        }

        $errors = [];
        foreach ($node->params as $param) {
            $error = $this->checkParam($param, $classReflection->getName());
            if (null !== $error) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

    /**
     * 引数 1 つを検査し、 違反していれば error を返す。 違反しなければ null。
     */
    private function checkParam(Param $param, string $className): ?IdentifierRuleError
    {
        if (!$param->var instanceof Variable) {
            return null;
        }
        if ('mcpLogger' !== $param->var->name) {
            return null;
        }
        if (!$this->isLoggerType($param)) {
            return null;
        }

        return RuleErrorBuilder::message(\sprintf(
            '%s で $mcpLogger を直接 inject しないでください。 mcp チャネルへのログ書き込みは %s を経由してください (設計 §8 #4: 監査ログ Single Source of Truth)。',
            $className,
            McpAuditLogger::class,
        ))
            ->identifier(self::ERROR_IDENTIFIER)
            ->line($param->getStartLine())
            ->build();
    }

    /**
     * 引数の型注釈が `Psr\Log\LoggerInterface` 自体 (フル修飾) かを判定する。
     * 型注釈なし、 もしくは別の型 (例: 独自 Logger サブクラス) は検出しない。
     * 現実の Symfony Monolog autowire は LoggerInterface でしか入らないため、 サブクラス検出は省略する。
     */
    private function isLoggerType(Param $param): bool
    {
        $type = $param->type;
        if (null === $type) {
            return false;
        }

        $typeName = match (true) {
            $type instanceof Name => $type->toString(),
            $type instanceof NullableType && $type->type instanceof Name => $type->type->toString(),
            default => null,
        };
        if (null === $typeName) {
            return false;
        }

        return LoggerInterface::class === ltrim($typeName, '\\');
    }
}
