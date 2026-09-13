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
 * 1 パスの診断結果.
 */
final readonly class PermissionFinding
{
    public function __construct(
        public PermissionRequirement $requirement,
        public PathOwnership $ownership,
        public FindingSeverity $severity,
        public string $message,
        public ?string $hint = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'path' => $this->requirement->label,
            'lane' => $this->requirement->lane->value,
            'severity' => $this->severity->value,
            'message' => $this->message,
            'hint' => $this->hint,
            'note' => $this->requirement->note,
            'exists' => $this->ownership->exists,
            'owner_uid' => $this->ownership->exists ? $this->ownership->uid : null,
            'owner_gid' => $this->ownership->exists ? $this->ownership->gid : null,
            'permissions' => $this->ownership->exists ? $this->ownership->permissionsString() : null,
        ];
    }
}
