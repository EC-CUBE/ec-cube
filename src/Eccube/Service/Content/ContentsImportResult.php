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

namespace Eccube\Service\Content;

/**
 * アーカイブの 1 件を取り込んだ結果.
 *
 * --continue-on-error では失敗しても続行するため, 成功と失敗を同じ列で扱えるようにする.
 */
final readonly class ContentsImportResult
{
    private function __construct(
        public string $section,
        public string $identifier,
        public ?ContentStatus $status,
        public ?string $error,
    ) {
    }

    public static function ok(string $section, string $identifier, ContentStatus $status): self
    {
        return new self($section, $identifier, $status, null);
    }

    public static function failed(string $section, string $identifier, string $error): self
    {
        return new self($section, $identifier, null, $error);
    }

    public function isError(): bool
    {
        return null !== $this->error;
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'section' => $this->section,
            'identifier' => $this->identifier,
            'status' => $this->status?->value,
            'error' => $this->error,
        ];
    }
}
