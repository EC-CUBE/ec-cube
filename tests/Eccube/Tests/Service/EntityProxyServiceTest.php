<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Tests\Service;

use Eccube\Annotation\EntityExtension;
use Eccube\Service\EntityProxyService;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

class EntityProxyServiceTest extends TestCase
{
    private $tempOutputDir;

    protected function setUp()
    {
        $this->tempOutputDir = tempnam(sys_get_temp_dir(), 'ProxyGeneratorTest');
        unlink($this->tempOutputDir);
        mkdir($this->tempOutputDir);
    }

    protected function tearDown()
    {
        foreach (glob($this->tempOutputDir.'/*') as $file) {
            unlink($file);
        }
        rmdir($this->tempOutputDir);
    }

    public function testGenerate()
    {
        $generator = new EntityProxyService();
        $generator->generate([__DIR__], [], $this->tempOutputDir);

        $generatedFile = $this->tempOutputDir.'/Product.php';
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

    public function testGenerateExcluded()
    {
        $generator = new EntityProxyService();
        $generator->generate([__DIR__], [], $this->tempOutputDir);

        $generatedFile = $this->tempOutputDir.'/Product.php';
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
        $generator->generate([], [__DIR__], $this->tempOutputDir);
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
        $method->invoke(new EntityProxyService(), $entityTokens, '\\Eccube\\Tests\\Service\\EntityProxyServiceTest_Trait');

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
        $method->invoke(new EntityProxyService(), $entityTokens, '\\Eccube\\Tests\\Service\\EntityProxyServiceTest_ExTrait');

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
        $method->invoke(new EntityProxyService(), $entityTokens, '\\Eccube\\Tests\\Service\\EntityProxyServiceTest_ExTrait');

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
            ';'
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
        $method->invoke(new EntityProxyService(), $entityTokens, '\\Eccube\\Tests\\Service\\EntityProxyServiceTest_Trait');

        self::assertNull($entityTokens->getNextTokenOfKind(0, [CT::T_USE_TRAIT]), 'Traitのuse句が削除されているはず');
    }

}

/**
 * @EntityExtension("Eccube\Entity\Product")
 */
trait EntityProxyServiceTest_ProductTrait
{
    public $testProperty;
}
