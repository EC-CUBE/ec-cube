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
 * ACP Product Feed の生成 / バリデーション / push 処理で発生する例外の基底クラス.
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/openapi/openapi.feed.yaml
 */
class AcpFeedException extends \RuntimeException
{
}
