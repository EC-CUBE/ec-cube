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

namespace Eccube\Tests\Service\AgentCommerce\Schema;

use JsonSchema\Constraints\Factory as JsonSchemaFactory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;

/**
 * justinrainbow/json-schema を用いて、生成データを spec schema へ突き合わせる共通処理.
 *
 * schema はリポジトリに同梱せず (Apache-2.0 ライセンス露出回避)、以下から解決する:
 *   - ACP feed: src/Eccube/Resource/AgentCommerce/Acp/schema.feed.json
 *     (pre-push 検証で runtime に使う製品同梱リソース。ACP 2026-04-17 と同一 bundle)
 *   - UCP: 公式リポジトリの source/schemas ツリー。解決順は
 *       (1) 環境変数 ECCUBE_UCP_SCHEMA_DIR
 *       (2) var/agent-commerce-spec/ucp/source/schemas (CI で clone・gitignore)
 *       (3) specifications/ucp/source/schemas (ローカル開発クローン)
 *     いずれも無ければ markTestSkipped。取得手順は本ディレクトリ README.md 参照。
 *
 * UCP の schema は相対 $ref で多数のファイルに分割されているため、各ファイルを
 * その $id (https://ucp.dev/schemas/...) で SchemaStorage へ登録し、$ref を $id で解決する。
 * ACP は単一 bundle で内部 #/$defs/... を参照するため、bundle の $id で登録する。
 */
trait SchemaValidatorTrait
{
    private ?SchemaStorage $ucpSchemaStorage = null;

    private ?string $acpBundleId = null;

    private ?SchemaStorage $acpSchemaStorage = null;

    /**
     * リポジトリルート (tests/Eccube/Tests/Service/AgentCommerce/Schema から 6 階層上).
     */
    private function projectRoot(): string
    {
        return \dirname(__DIR__, 6);
    }

    /**
     * UCP spec schema ツリーのディレクトリを解決する (無ければ null).
     */
    private function ucpSchemaDir(): ?string
    {
        $candidates = [];
        $env = getenv('ECCUBE_UCP_SCHEMA_DIR');
        if (\is_string($env) && $env !== '') {
            $candidates[] = $env;
        }
        $candidates[] = $this->projectRoot().'/var/agent-commerce-spec/ucp/source/schemas';
        $candidates[] = $this->projectRoot().'/specifications/ucp/source/schemas';

        foreach ($candidates as $dir) {
            if (is_dir($dir)) {
                return $dir;
            }
        }

        return null;
    }

    /**
     * UCP schema ツリーを $id で登録した SchemaStorage を返す (遅延初期化).
     * schema が見つからない環境では markTestSkipped。
     */
    private function ucpSchemaStorage(): SchemaStorage
    {
        if ($this->ucpSchemaStorage !== null) {
            return $this->ucpSchemaStorage;
        }

        $dir = $this->ucpSchemaDir();
        if ($dir === null) {
            self::markTestSkipped('UCP spec schemas not found. Set ECCUBE_UCP_SCHEMA_DIR, or clone the UCP repo into var/agent-commerce-spec/ucp (see tests/Eccube/Tests/Service/AgentCommerce/README.md).');
        }

        $storage = new SchemaStorage();
        /** @var iterable<\SplFileInfo> $it */
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if ($file->getExtension() !== 'json') {
                continue;
            }
            $schema = json_decode((string) file_get_contents($file->getPathname()));
            if (\is_object($schema) && isset($schema->{'$id'})) {
                $storage->addSchema($schema->{'$id'}, $schema);
            }
        }

        return $this->ucpSchemaStorage = $storage;
    }

    /**
     * ACP feed bundle を $id で登録した SchemaStorage を返す (遅延初期化).
     */
    private function acpSchemaStorage(): SchemaStorage
    {
        if ($this->acpSchemaStorage !== null) {
            return $this->acpSchemaStorage;
        }

        $bundle = json_decode((string) file_get_contents($this->projectRoot().'/src/Eccube/Resource/AgentCommerce/Acp/schema.feed.json'));
        $this->acpBundleId = $bundle->{'$id'};
        $storage = new SchemaStorage();
        $storage->addSchema($this->acpBundleId, $bundle);

        return $this->acpSchemaStorage = $storage;
    }

    /**
     * データ (連想配列) を、$ref で指す schema 定義に対して検証する.
     *
     * @param array<string, mixed>|list<mixed> $data
     */
    private function validateAgainst(SchemaStorage $storage, string $schemaRef, array $data, string $message): void
    {
        $factory = new JsonSchemaFactory($storage);
        $validator = new Validator($factory);
        $decoded = json_decode((string) json_encode($data));
        $validator->validate($decoded, (object) ['$ref' => $schemaRef]);

        self::assertTrue(
            $validator->isValid(),
            $message.' Schema violations: '.json_encode($validator->getErrors())
        );
    }

    /**
     * データが schema に適合しない (= 検証エラーになる) ことを表明する.
     *
     * @param array<string, mixed>|list<mixed> $data
     */
    private function assertInvalidAgainst(SchemaStorage $storage, string $schemaRef, array $data, string $message): void
    {
        $factory = new JsonSchemaFactory($storage);
        $validator = new Validator($factory);
        $decoded = json_decode((string) json_encode($data));
        $validator->validate($decoded, (object) ['$ref' => $schemaRef]);

        self::assertFalse($validator->isValid(), $message);
    }

    /**
     * UCP schema 定義への参照 ($id + JSON pointer) に対して検証する.
     *
     * @param array<string, mixed>|list<mixed> $data
     */
    private function assertValidUcp(string $schemaRef, array $data, string $message): void
    {
        $this->validateAgainst($this->ucpSchemaStorage(), $schemaRef, $data, $message);
    }

    /**
     * @param array<string, mixed>|list<mixed> $data
     */
    private function assertInvalidUcp(string $schemaRef, array $data, string $message): void
    {
        $this->assertInvalidAgainst($this->ucpSchemaStorage(), $schemaRef, $data, $message);
    }

    /**
     * ACP feed bundle の $defs 定義に対して検証する.
     *
     * @param array<string, mixed>|list<mixed> $data
     */
    private function assertValidAcp(string $defName, array $data, string $message): void
    {
        $storage = $this->acpSchemaStorage();
        $this->validateAgainst($storage, $this->acpBundleId.'#/$defs/'.$defName, $data, $message);
    }

    /**
     * @param array<string, mixed>|list<mixed> $data
     */
    private function assertInvalidAcp(string $defName, array $data, string $message): void
    {
        $storage = $this->acpSchemaStorage();
        $this->assertInvalidAgainst($storage, $this->acpBundleId.'#/$defs/'.$defName, $data, $message);
    }
}
