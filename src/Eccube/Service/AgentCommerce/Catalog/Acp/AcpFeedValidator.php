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
use Eccube\Service\AgentCommerce\Catalog\Exception\AcpFeedValidationException;
use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;

/**
 * ACP Feed のペイロード (metadata / Product) を vendored schema.feed.json に対し検証する.
 *
 * 検証は justinrainbow/json-schema で行う。schema は $defs を束ねたバンドルであり、
 * バンドルの $id を SchemaStorage に登録した上で {"$ref": "<$id>#/$defs/<Def>"} を
 * 与えて単一定義へ検証する。push 前に必ず通すことで不正データ送出を防ぐ。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/json-schema/schema.feed.json
 */
class AcpFeedValidator
{
    private ?object $schema = null;

    private ?string $schemaId = null;

    public function __construct(
        private readonly string $schemaPath = __DIR__.'/../../../../Resource/AgentCommerce/Acp/schema.feed.json',
    ) {
    }

    /**
     * FeedMetadata 連想配列を schema.feed.json の $defs/FeedMetadata に対し検証する.
     *
     * @param array<string, mixed> $metadata
     *
     * @throws AcpFeedValidationException 適合しない場合
     */
    public function validateMetadata(array $metadata): void
    {
        $this->validateAgainstDef($metadata, 'FeedMetadata');
    }

    /**
     * Product 連想配列を schema.feed.json の $defs/Product に対し検証する.
     *
     * @param array<string, mixed> $product
     *
     * @throws AcpFeedValidationException 適合しない場合
     */
    public function validateProduct(array $product): void
    {
        $this->validateAgainstDef($product, 'Product');
    }

    /**
     * 値を schema バンドルの指定 $defs エントリに対し検証する.
     *
     * @param array<string, mixed> $value
     *
     * @throws AcpFeedValidationException 適合しない場合
     */
    private function validateAgainstDef(array $value, string $def): void
    {
        $schema = $this->loadSchema();

        $storage = new SchemaStorage();
        $storage->addSchema((string) $this->schemaId, $schema);

        $validator = new Validator(new Factory($storage));

        // 連想配列を JSON Schema が扱える stdClass ツリーへ変換する。
        // 空配列は JSON では [] (array) のままで良いが、空オブジェクトは生じない想定。
        $data = $this->toObject($value);

        $refSchema = (object) ['$ref' => $this->schemaId.'#/$defs/'.$def];

        $validator->validate($data, $refSchema);

        if (!$validator->isValid()) {
            /** @var array<int, array{property?: string, pointer?: string, message?: string}> $errors */
            $errors = $validator->getErrors();
            $summary = implode('; ', array_map(
                static fn (array $e): string => sprintf('%s: %s', $e['property'] ?? $e['pointer'] ?? '(root)', $e['message'] ?? ''),
                $errors,
            ));

            throw new AcpFeedValidationException(sprintf('ACP Feed %s payload does not conform to schema.feed.json: %s', $def, $summary), $errors);
        }
    }

    /**
     * 連想配列を json_decode(json_encode(...)) で stdClass / array のツリーへ正規化する.
     *
     * @param array<string, mixed> $value
     */
    private function toObject(array $value): \stdClass
    {
        $encoded = json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $decoded = json_decode($encoded, false, 512, JSON_THROW_ON_ERROR);

        if (!$decoded instanceof \stdClass) {
            throw new AcpFeedException('ACP Feed payload must decode to an object.');
        }

        return $decoded;
    }

    /**
     * schema バンドルを読み込み $id を確定させる (初回のみ I/O).
     */
    private function loadSchema(): object
    {
        if ($this->schema !== null) {
            return $this->schema;
        }

        $path = $this->resolveSchemaPath();
        $contents = @file_get_contents($path);
        if ($contents === false) {
            throw new AcpFeedException(sprintf('Unable to read ACP feed schema file: %s', $path));
        }

        $decoded = json_decode($contents, false, 512, JSON_THROW_ON_ERROR);
        if (!$decoded instanceof \stdClass) {
            throw new AcpFeedException('ACP feed schema must decode to an object.');
        }

        $this->schema = $decoded;
        $this->schemaId = isset($decoded->{'$id'}) && \is_string($decoded->{'$id'})
            ? $decoded->{'$id'}
            : SchemaStorage::INTERNAL_PROVIDED_SCHEMA_URI;

        return $this->schema;
    }

    private function resolveSchemaPath(): string
    {
        // コンストラクタ既定値の相対パスを正規化する。明示指定があればそのまま使う。
        $real = realpath($this->schemaPath);
        if ($real !== false) {
            return $real;
        }

        // realpath に失敗してもファイルが存在すれば渡されたパスを使う。
        return $this->schemaPath;
    }
}
