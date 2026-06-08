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

namespace Eccube\Service\AgentCommerce\Catalog\Acp;

use Eccube\Service\AgentCommerce\Catalog\Exception\AcpFeedException;
use Eccube\Service\AgentCommerce\Catalog\Exception\AcpFeedTransportException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * AcpFeedClientInterface の標準実装.
 *
 * Symfony HttpClient で OpenAI ホストの ACP Feed API へ outbound 送出する。
 * base URL / Bearer api_key は env (ECCUBE_AGENT_COMMERCE_ACP_FEED_BASE_URL /
 * _API_KEY) から bind する。実機送出は acp_feed_enabled フラグ + 設定有無でガードし、
 * 無効 / 未設定なら例外を投げる。Bearer はログ / 例外メッセージに出さない。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/openapi/openapi.feed.yaml
 */
class AcpFeedClient implements AcpFeedClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly AcpFeedGenerator $generator,
        private readonly AcpFeedValidator $validator,
        private readonly string $baseUrl = '',
        #[\SensitiveParameter]
        private readonly string $apiKey = '',
    ) {
    }

    public function createFeed(?string $targetCountry = null): array
    {
        $this->assertEnabled();

        $payload = [];
        if ($targetCountry !== null && $targetCountry !== '') {
            $payload['target_country'] = $targetCountry;
        }

        return $this->request('POST', '/feeds', $payload);
    }

    public function getFeed(string $id): array
    {
        $this->assertEnabled();

        return $this->request('GET', '/feeds/'.rawurlencode($id), null);
    }

    public function getFeedProducts(string $id): array
    {
        $this->assertEnabled();

        $response = $this->request('GET', '/feeds/'.rawurlencode($id).'/products', null);

        $products = $response['products'] ?? null;
        if (!\is_array($products)) {
            throw new AcpFeedTransportException('ACP Feed API getFeedProducts response is missing the products array.');
        }

        /** @var array<int, array<string, mixed>> $products */
        return array_values($products);
    }

    public function upsertProducts(string $id, array $products): bool
    {
        $this->assertEnabled();

        // 送信前に各 Product を schema.feed.json で検証する (client 側検証が主)。
        foreach ($products as $product) {
            $this->validator->validateProduct($product);
        }

        $response = $this->request('PATCH', '/feeds/'.rawurlencode($id).'/products', ['products' => array_values($products)]);

        return (bool) ($response['accepted'] ?? false);
    }

    /**
     * 全置換 push: products.jsonl / metadata.json を生成し、POST /feeds で feed を登録するところまで行う.
     *
     * jsonl / metadata の取り込み自体は OpenAI 側の責務であり、本メソッドは生成物と
     * 登録した FeedMetadata を返す。生成した metadata は push 前に schema 検証する。
     *
     * @return array{products_jsonl: string, metadata: array<string, mixed>, feed: array<string, mixed>, product_count: int}
     */
    public function pushFullReplacement(string $feedId, ?\DateTimeImmutable $updatedAt = null): array
    {
        $this->assertEnabled();

        $stream = fopen('php://temp', 'w+');
        if ($stream === false) {
            throw new AcpFeedException('Unable to open a temporary stream for ACP feed generation.');
        }

        try {
            $count = $this->generator->generateProductsJsonl($stream);
            rewind($stream);
            $jsonl = stream_get_contents($stream);
            if ($jsonl === false) {
                throw new AcpFeedException('Failed to read generated products.jsonl from the temporary stream.');
            }
        } finally {
            fclose($stream);
        }

        $metadata = $this->generator->generateMetadata($feedId, $updatedAt);
        $this->validator->validateMetadata($metadata);

        $targetCountry = isset($metadata['target_country']) && \is_string($metadata['target_country'])
            ? $metadata['target_country']
            : null;
        $feed = $this->createFeed($targetCountry);

        return [
            'products_jsonl' => $jsonl,
            'metadata' => $metadata,
            'feed' => $feed,
            'product_count' => $count,
        ];
    }

    /**
     * base URL / api_key の設定有無を確認する.
     *
     * ACP feed push は outbound 送信であり、認証情報 (base URL + api_key) が設定されている
     * ことが実質の有効化条件となる。専用の有効化フラグは設けない。
     *
     * @throws AcpFeedException 未設定の場合
     */
    private function assertEnabled(): void
    {
        if ($this->baseUrl === '') {
            throw new AcpFeedException('ACP Feed base URL is not configured (ECCUBE_AGENT_COMMERCE_ACP_FEED_BASE_URL).');
        }

        if ($this->apiKey === '') {
            throw new AcpFeedException('ACP Feed API key is not configured (ECCUBE_AGENT_COMMERCE_ACP_FEED_API_KEY).');
        }
    }

    /**
     * Feed API へ 1 リクエスト送出し、JSON ボディを連想配列で返す.
     *
     * @param array<string, mixed>|null $body JSON ボディ (null は body なし)
     *
     * @return array<string, mixed>
     *
     * @throws AcpFeedTransportException 4xx/5xx / 通信障害時
     */
    private function request(string $method, string $path, ?array $body): array
    {
        $options = [
            'headers' => [
                'Authorization' => 'Bearer '.$this->apiKey,
                'Accept' => 'application/json',
            ],
        ];
        if ($body !== null) {
            $options['json'] = $body;
        }

        try {
            $response = $this->httpClient->request($method, $this->buildUrl($path), $options);
            $status = $response->getStatusCode();
            // 4xx/5xx を例外化したいので throw=false で本文を取得する。
            $content = $response->getContent(false);
        } catch (HttpClientExceptionInterface $e) {
            // 通信障害等。Bearer を含まないメッセージのみ伝播する。
            throw new AcpFeedTransportException(sprintf('ACP Feed API transport error during %s %s.', $method, $path), previous: $e);
        }

        $decoded = $content === '' ? [] : json_decode($content, true);
        if (!\is_array($decoded)) {
            $decoded = [];
        }

        if ($status >= 400) {
            throw $this->toTransportException($status, $decoded, $method, $path);
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }

    /**
     * ACP flat Error shape ({type, code, message, param?}) を例外へ変換する.
     *
     * @param array<string, mixed> $decoded
     */
    private function toTransportException(int $status, array $decoded, string $method, string $path): AcpFeedTransportException
    {
        $error = \is_array($decoded['error'] ?? null) ? $decoded['error'] : $decoded;

        $type = \is_string($error['type'] ?? null) ? $error['type'] : null;
        $code = \is_string($error['code'] ?? null) ? $error['code'] : null;
        $param = \is_string($error['param'] ?? null) ? $error['param'] : null;
        $message = \is_string($error['message'] ?? null)
            ? $error['message']
            : sprintf('ACP Feed API returned HTTP %d for %s %s.', $status, $method, $path);

        return new AcpFeedTransportException($message, $status, $type, $code, $param);
    }

    private function buildUrl(string $path): string
    {
        return rtrim($this->baseUrl, '/').'/'.ltrim($path, '/');
    }
}
