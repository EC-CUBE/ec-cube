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

namespace Eccube\Service\AgentCommerce\Catalog\Exception;

/**
 * ACP Feed のペイロード (metadata / Product) が schema.feed.json に適合しない場合の例外.
 *
 * push 前のクライアント側検証で必ず通すことで、ACP Feed API への不正データ送出を防ぐ。
 */
class AcpFeedValidationException extends AcpFeedException
{
    /**
     * @param array<int, array{property?: string, pointer?: string, message?: string}> $violations
     *                                                                                              justinrainbow/json-schema の getErrors() 形式の違反詳細
     */
    public function __construct(
        string $message,
        private readonly array $violations = [],
    ) {
        parent::__construct($message);
    }

    /**
     * @return array<int, array{property?: string, pointer?: string, message?: string}>
     */
    public function getViolations(): array
    {
        return $this->violations;
    }
}
