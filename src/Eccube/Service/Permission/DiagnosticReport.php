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

namespace Eccube\Service\Permission;

/**
 * 診断の実行結果.
 */
final readonly class DiagnosticReport
{
    /**
     * @param list<PermissionFinding> $findings
     * @param UserIdentity|null       $webServer Web サーバーの実行ユーザー. 特定できなかった場合は null
     * @param UserIdentity            $cli       診断を実行したユーザー
     */
    public function __construct(
        public array $findings,
        public ?UserIdentity $webServer,
        public UserIdentity $cli,
    ) {
    }

    public function hasError(): bool
    {
        return $this->countBy(FindingSeverity::NG) > 0;
    }

    public function countBy(FindingSeverity $severity): int
    {
        return count(array_filter($this->findings, static fn (PermissionFinding $finding) => $finding->severity === $severity));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'web_server' => $this->webServer?->toArray(),
            'cli' => $this->cli->toArray(),
            'summary' => [
                'ok' => $this->countBy(FindingSeverity::OK),
                'warn' => $this->countBy(FindingSeverity::WARN),
                'ng' => $this->countBy(FindingSeverity::NG),
            ],
            'findings' => array_map(static fn (PermissionFinding $finding) => $finding->toArray(), $this->findings),
        ];
    }
}
