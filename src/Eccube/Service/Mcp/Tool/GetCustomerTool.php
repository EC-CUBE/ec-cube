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

namespace Eccube\Service\Mcp\Tool;

use Eccube\Repository\CustomerRepository;
use Eccube\Service\Mcp\EntityArraySerializer;
use Eccube\Service\Mcp\McpScope;
use Eccube\Service\Mcp\ToolInvoker;
use Mcp\Capability\Attribute\McpTool;

/**
 * 会員詳細取得ツール (`get_customer`)。
 *
 * 必要 scope: `mcp:customer:read`。 会員 ID で取得する。
 * 出力は Api44 allow_list (`Eccube\Entity\Customer`) の項目のみ (氏名・連絡先・住所等の PII)。
 */
final readonly class GetCustomerTool
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private EntityArraySerializer $serializer,
        private ToolInvoker $invoker,
    ) {
    }

    /**
     * 会員 ID から会員詳細を取得する。
     *
     * @param int $id 会員 ID
     *
     * @return array<string, mixed> 見つからなければ空配列
     */
    #[McpTool(
        name: 'get_customer',
        description: 'EC-CUBE の会員 ID から会員詳細 (氏名・連絡先・住所など) を取得する。 読み取り専用。 必要 scope: mcp:customer:read。 allow_list に PII が含まれる。',
    )]
    public function get(int $id): array
    {
        return $this->invoker->invoke(
            toolName: 'get_customer',
            requiredScope: McpScope::ROLE_CUSTOMER_READ,
            args: ['id' => $id],
            work: function () use ($id): array {
                $customer = $this->customerRepository->find($id);
                if (null === $customer) {
                    return [
                        'data' => [],
                        'summary' => ['found' => false],
                    ];
                }

                return [
                    'data' => $this->serializer->toArray($customer),
                    'summary' => ['found' => true, 'customer_id' => $customer->getId()],
                ];
            },
        );
    }
}
