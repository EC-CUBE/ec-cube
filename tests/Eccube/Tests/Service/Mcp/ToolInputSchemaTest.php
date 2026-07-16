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

use Eccube\Service\Mcp\ToolInputSchema;
use Mcp\Schema\Tool;
use PHPUnit\Framework\TestCase;

/**
 * `ToolInputSchema` のユニット検証。 DB / kernel 不要。
 *
 * JSON Schema (inputSchema) の正規化・nullable 除去・既定型・SDK の \stdClass 癖の吸収を確認する。
 */
final class ToolInputSchemaTest extends TestCase
{
    public function testPropertyAndRequiredNames(): void
    {
        $schema = $this->schema([
            'type' => 'object',
            'properties' => ['id' => ['type' => 'integer'], 'name' => ['type' => 'string']],
            'required' => ['id'],
        ]);

        $this->assertSame(['id', 'name'], $schema->propertyNames());
        $this->assertSame(['id'], $schema->requiredNames());
    }

    public function testBaseTypeStripsNullable(): void
    {
        $schema = $this->schema([
            'type' => 'object',
            'properties' => [
                'id' => ['type' => ['null', 'integer']],
                'name' => ['type' => 'string'],
            ],
        ]);

        $this->assertSame('integer', $schema->baseType('id'));
        $this->assertSame('string', $schema->baseType('name'));
    }

    public function testBaseTypeDefaultsToStringWhenTypeMissing(): void
    {
        $schema = $this->schema(['type' => 'object', 'properties' => ['x' => []]]);

        $this->assertSame('string', $schema->baseType('x'));
    }

    public function testArrayPropertyAndElementType(): void
    {
        $schema = $this->schema([
            'type' => 'object',
            'properties' => ['tags' => ['type' => 'array', 'items' => ['type' => 'integer']]],
        ]);

        $this->assertTrue($schema->isArray('tags'));
        $this->assertSame('integer', $schema->elementType('tags'));
    }

    public function testElementTypeDefaultsToStringWhenItemsMissing(): void
    {
        $schema = $this->schema(['type' => 'object', 'properties' => ['tags' => ['type' => 'array']]]);

        $this->assertSame('string', $schema->elementType('tags'));
    }

    public function testDescriptionFallsBackToEmptyString(): void
    {
        $schema = $this->schema([
            'type' => 'object',
            'properties' => ['a' => ['description' => 'hello'], 'b' => ['type' => 'string']],
        ]);

        $this->assertSame('hello', $schema->description('a'));
        $this->assertSame('', $schema->description('b'));
    }

    public function testNonArrayPropertyIsTreatedAsEmptySchema(): void
    {
        // SDK は各プロパティを配列で渡すが、 非配列でも掘り先で TypeError にせず既定に落ちる。
        $schema = $this->schema(['type' => 'object', 'properties' => ['broken' => 'not-an-array']]);

        $this->assertSame('string', $schema->baseType('broken'));
        $this->assertSame('', $schema->description('broken'));
        $this->assertFalse($schema->isArray('broken'));
    }

    public function testEmptyPropertiesNormalizedBySdkYieldNoOptions(): void
    {
        // 引数なしツールは SDK が properties を \stdClass 化する (Tool::fromArray の実挙動)。 VO は空扱いにする。
        $tool = Tool::fromArray(['name' => 't', 'inputSchema' => ['type' => 'object', 'properties' => []]]);

        $this->assertSame([], (new ToolInputSchema($tool))->propertyNames());
    }

    public function testUnionOfMultipleTypesFallsBackToString(): void
    {
        // 単一 nullable は基底型に還元、 複数型 union は一意に決められず string 扱い (誤 reject 回避)。
        $schema = $this->schema([
            'type' => 'object',
            'properties' => [
                'a' => ['type' => ['null', 'integer']],
                'b' => ['type' => ['integer', 'string']],
            ],
        ]);

        $this->assertSame('integer', $schema->baseType('a'));
        $this->assertSame('string', $schema->baseType('b'));
    }

    /**
     * @param array<string, mixed> $inputSchema
     */
    private function schema(array $inputSchema): ToolInputSchema
    {
        return new ToolInputSchema(new Tool('t', null, $inputSchema, null, null));
    }
}
