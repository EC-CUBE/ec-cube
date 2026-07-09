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
 * ACP Feed API (OpenAI ホスト) への push が HTTP 4xx/5xx で失敗した場合の例外.
 *
 * ACP の flat Error shape ({type, code, message, param?}) をパースして保持する。
 * Bearer 等の認証情報はメッセージに含めない。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/openapi/openapi.feed.yaml
 */
class AcpFeedTransportException extends AcpFeedException
{
    public function __construct(
        string $message,
        private readonly int $statusCode = 0,
        private readonly ?string $errorType = null,
        private readonly ?string $errorCode = null,
        private readonly ?string $param = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorType(): ?string
    {
        return $this->errorType;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getParam(): ?string
    {
        return $this->param;
    }
}
