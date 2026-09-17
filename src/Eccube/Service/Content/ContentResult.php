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
 * コンテンツ操作 (DB レコード + twig ファイル) の結果.
 *
 * --dry-run と --format=json の出力源を兼ねるため, 変更内容は
 * フィールド単位 ($fieldChanges) とファイル単位 ($fileChanges) に分けて保持する.
 */
final readonly class ContentResult
{
    /**
     * @param list<string>                            $writtenPaths 書き込んだファイル
     * @param list<string>                            $removedPaths 削除したファイル
     * @param array<string, array{0: string, 1: string}> $fieldChanges フィールド名 => [変更前, 変更後]
     * @param array<string, array{0: string, 1: string}> $fileChanges  ファイルパス => [変更前, 変更後]
     */
    public function __construct(
        public ContentStatus $status,
        public ?int $id,
        public string $identifier,
        public array $writtenPaths = [],
        public array $removedPaths = [],
        public array $fieldChanges = [],
        public array $fileChanges = [],
    ) {
    }

    /**
     * @param array<string, array{0: string, 1: string}> $fieldChanges
     * @param array<string, array{0: string, 1: string}> $fileChanges
     */
    public function withChanges(array $fieldChanges, array $fileChanges): self
    {
        return new self(
            $this->status,
            $this->id,
            $this->identifier,
            $this->writtenPaths,
            $this->removedPaths,
            $fieldChanges,
            $fileChanges
        );
    }

    public function path(): ?string
    {
        return $this->writtenPaths[0] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status->value,
            'id' => $this->id,
            'identifier' => $this->identifier,
            'written_paths' => $this->writtenPaths,
            'removed_paths' => $this->removedPaths,
            'field_changes' => $this->fieldChanges,
            'file_changes' => array_keys($this->fileChanges),
        ];
    }
}
