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

namespace Eccube\Tests\Service;

use Eccube\Annotation\EntityExtension;
use Eccube\Service\EntityProxyService;
use Eccube\Tests\EccubeTestCase;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class EntityProxyServiceTest extends EccubeTestCase
{
    /**
     * @var string
     */
    private $tempOutputDir;

    /**
     * @var EntityProxyService
     */
    protected $entityProxyService;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->entityProxyService = self::$container->get(EntityProxyService::class);

        $this->tempOutputDir = tempnam(sys_get_temp_dir(), 'ProxyGeneratorTest');
        unlink($this->tempOutputDir);
        mkdir($this->tempOutputDir);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $files = Finder::create()
            ->in($this->tempOutputDir)
            ->files();
        $f = new Filesystem();
        $f->remove($files);

        parent::tearDown();
    }

    public function testGenerate()
    {
        $this->entityProxyService->generate([__DIR__], [], $this->tempOutputDir);

        $generatedFile = $this->tempOutputDir.'/src/Eccube/Entity/Product.php';
        self::assertTrue(file_exists($generatedFile));

        // Traitのuse句があるかどうか
        $tokens = Tokens::fromCode(file_get_contents($generatedFile));
        $sequence = $tokens->findSequence([
            [CT::T_USE_TRAIT],
            [T_NS_SEPARATOR],
            [T_STRING, 'Eccube'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Tests'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Service'],
            [T_NS_SEPARATOR],
            [T_STRING, 'EntityProxyServiceTest_ProductTrait'],
        ]);
        self::assertNotNull($sequence);
    }

    public function testGenerateFromOriginalFile()
    {
        $this->markTestIncomplete();

        $findSequence = static function (Tokens $tokens) {
            return $tokens->findSequence([
                [T_PRIVATE, 'private'],
                [T_VARIABLE, '$hoge'],
            ]);
        };

        $this->entityProxyService->generate([__DIR__], [], $this->tempOutputDir);

        $generatedFile = $this->tempOutputDir.'/src/Eccube/Entity/Product.php';
        self::assertTrue(file_exists($generatedFile));

        $tokens = Tokens::fromCode(file_get_contents($generatedFile));
        // private $hoge;がないことを確認
        self::assertNull($findSequence($tokens));

        // private $hoge;を挿入
        $additionalVariableTokens = [
            new Token([T_WHITESPACE, PHP_EOL.'    ']),
            new Token([T_PRIVATE, 'private']),
            new Token([T_WHITESPACE, ' ']),
            new Token([T_VARIABLE, '$hoge']),
            new Token(';'),
            new Token([T_WHITESPACE, PHP_EOL]),
        ];

        $classTokens = $tokens->findSequence([[T_CLASS], [T_STRING]]);
        $classTokenEnd = $tokens->getNextTokenOfKind(array_keys($classTokens)[0], ['{']);
        $tokens->insertAt($classTokenEnd + 1, $additionalVariableTokens);
        $newCode = $tokens->generateCode();
        $newTokens = Tokens::fromCode($newCode);

        // private $hoge;が存在することを確認
        self::assertNotNull($findSequence($newTokens));

        // 再生成する
        file_put_contents($generatedFile, $newCode);
        $this->entityProxyService->generate([__DIR__], [], $this->tempOutputDir);
        $regeneratedTokens = Tokens::fromCode(file_get_contents($generatedFile));

        // private $hoge;が存在しないことを確認
        self::assertNull($findSequence($regeneratedTokens));
    }

    public function testGenerateExcluded()
    {
        $this->entityProxyService->generate([__DIR__], [], $this->tempOutputDir);

        $generatedFile = $this->tempOutputDir.'/src/Eccube/Entity/Product.php';
        self::assertTrue(file_exists($generatedFile));

        $tokens = Tokens::fromCode(file_get_contents($generatedFile));
        $traitTokens = [
            [CT::T_USE_TRAIT],
            [T_NS_SEPARATOR],
            [T_STRING, 'Eccube'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Tests'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Service'],
            [T_NS_SEPARATOR],
            [T_STRING, 'EntityProxyServiceTest_ProductTrait'],
        ];

        self::assertNotNull($tokens->findSequence($traitTokens), 'Traitはあるはず');

        // 除外して生成
        $this->entityProxyService->generate([], [__DIR__], $this->tempOutputDir);
        $tokens = Tokens::fromCode(file_get_contents($generatedFile));
        self::assertNull($tokens->findSequence($traitTokens), 'Traitが外されているはず');
    }

    public function testAddTrait()
    {
        $entityTokens = Tokens::fromCode(<<< EOT
<?php
class EntityProxyServiceTest_Entity extends \\Eccube\\Entity\\AbstractEntity
{
}
EOT
);
        $method = new \ReflectionMethod(EntityProxyService::class, 'addTrait');
        $method->setAccessible(true);
        $method->invoke($this->entityProxyService, $entityTokens, '\\Eccube\\Tests\\Service\\EntityProxyServiceTest_Trait');

        $traitTokens = [
            [CT::T_USE_TRAIT],
            [T_NS_SEPARATOR],
            [T_STRING, 'Eccube'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Tests'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Service'],
            [T_NS_SEPARATOR],
            [T_STRING, 'EntityProxyServiceTest_Trait'],
        ];

        self::assertNotNull($entityTokens->findSequence($traitTokens), 'Traitはあるはず');
    }

    public function testAddMoreTrait()
    {
        $entityTokens = Tokens::fromCode(<<< EOT
<?php
class EntityProxyServiceTest_Entity extends \\Eccube\\Entity\\AbstractEntity
{
    use \\Eccube\\Tests\\Service\\EntityProxyServiceTest_Trait;
}
EOT
        );
        $method = new \ReflectionMethod(EntityProxyService::class, 'addTrait');
        $method->setAccessible(true);
        $method->invoke($this->entityProxyService, $entityTokens, '\\Eccube\\Tests\\Service\\EntityProxyServiceTest_ExTrait');

        $traitTokens = [
            [CT::T_USE_TRAIT],
            [T_NS_SEPARATOR],
            [T_STRING, 'Eccube'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Tests'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Service'],
            [T_NS_SEPARATOR],
            [T_STRING, 'EntityProxyServiceTest_Trait'],
            ',',
            [T_NS_SEPARATOR],
            [T_STRING, 'Eccube'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Tests'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Service'],
            [T_NS_SEPARATOR],
            [T_STRING, 'EntityProxyServiceTest_ExTrait'],
        ];

        self::assertNotNull($entityTokens->findSequence($traitTokens), 'Traitはあるはず');
    }

    public function testRemoveTrait()
    {
        $entityTokens = Tokens::fromCode(<<< EOT
<?php
class EntityProxyServiceTest_Entity extends \\Eccube\\Entity\\AbstractEntity
{
    use \\Eccube\\Tests\\Service\\EntityProxyServiceTest_Trait, \\Eccube\\Tests\\Service\\EntityProxyServiceTest_ExTrait;
}
EOT
        );
        $method = new \ReflectionMethod(EntityProxyService::class, 'removeTrait');
        $method->setAccessible(true);
        $method->invoke($this->entityProxyService, $entityTokens, '\\Eccube\\Tests\\Service\\EntityProxyServiceTest_ExTrait');

        $traitTokens = [
            [CT::T_USE_TRAIT],
            [T_NS_SEPARATOR],
            [T_STRING, 'Eccube'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Tests'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Service'],
            [T_NS_SEPARATOR],
            [T_STRING, 'EntityProxyServiceTest_Trait'],
            ';',
        ];

        self::assertNotNull($entityTokens->findSequence($traitTokens), 'Traitが削除されているはず');
    }

    public function testRemoveLastTrait()
    {
        $entityTokens = Tokens::fromCode(<<< EOT
<?php
class EntityProxyServiceTest_Entity extends \\Eccube\\Entity\\AbstractEntity
{
    use \\Eccube\\Tests\\Service\\EntityProxyServiceTest_Trait;
}
EOT
        );
        $method = new \ReflectionMethod(EntityProxyService::class, 'removeTrait');
        $method->setAccessible(true);
        $method->invoke($this->entityProxyService, $entityTokens, '\\Eccube\\Tests\\Service\\EntityProxyServiceTest_Trait');

        self::assertNull($entityTokens->getNextTokenOfKind(0, [CT::T_USE_TRAIT]), 'Traitのuse句が削除されているはず');
    }

    public function testRemoveTraitWhenImportedTrait()
    {
        $entityTokens = Tokens::fromCode(<<< EOT
<?php

use Eccube\\Entity\\PointTrait;

class EntityProxyServiceTest_Entity extends \\Eccube\\Entity\\AbstractEntity
{
    use PointTrait, \\Eccube\\Tests\\Service\\EntityProxyServiceTest_Trait;
}
EOT
        );
        $method = new \ReflectionMethod(EntityProxyService::class, 'removeTrait');
        $method->setAccessible(true);
        $method->invoke($this->entityProxyService, $entityTokens, '\\Eccube\\Tests\\Service\\EntityProxyServiceTest_Trait');

        $traitTokens = [
            [CT::T_USE_TRAIT],
            [T_STRING, 'PointTrait'],
            ';',
        ];

        self::assertNotNull($entityTokens->findSequence($traitTokens), 'PointTraitが残るはず');
    }
}

/**
 * @EntityExtension("Eccube\Entity\Product")
 */
trait EntityProxyServiceTest_ProductTrait
{
    public $testProperty;
}
