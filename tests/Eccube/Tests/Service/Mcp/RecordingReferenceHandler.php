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

namespace Eccube\Tests\Service\Mcp;

use Mcp\Capability\Registry\ElementReference;
use Mcp\Capability\Registry\ReferenceHandlerInterface;

/**
 * `ScopeEnforcingReferenceHandler` のテスト用スタブ。 委譲 (inner) が呼ばれたか、 何回呼ばれたかを記録する。
 */
final class RecordingReferenceHandler implements ReferenceHandlerInterface
{
    public int $calls = 0;

    public function __construct(private readonly mixed $returnValue)
    {
    }

    #[\Override]
    public function handle(ElementReference $reference, array $arguments): mixed
    {
        ++$this->calls;

        return $this->returnValue;
    }
}
