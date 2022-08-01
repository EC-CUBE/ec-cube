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

 namespace Eccube\Tests\Doctrine\ORM\Query;

use Eccube\Tests\EccubeTestCase;

class NormalizeTest extends EccubeTestCase
{
    public function testGetSql()
    {
        $sql = $this->entityManager->createQueryBuilder()
            ->select('p.id')->from('Eccube\Entity\Product', 'p')
            ->where('NORMALIZE(p.name) LIKE :name')
            ->getQuery()->getSql();
        switch ($this->entityManager->getConnection()->getDriver()->getDatabasePlatform()->getName())
        {
            case 'postgresql':
                $this->assertStringContainsString('LOWER(TRANSLATE(', $sql);
                $this->assertStringContainsString('あいうえおかきくけこさしすせそたちつてとなにぬねのはひふへほまみむめもやゆよらりるれろわをんがぎぐげござじずぜぞだぢづでどばびぶべぼぱぴぷぺぽぁぃぅぇぉっゃゅょゎゐゑー', $sql);
                $this->assertStringContainsString('アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲンガギグゲゴザジズゼゾダヂヅデドバビブベボパピプペポァィゥェォッャュョヮヰヱー', $sql);
                break;
            case 'mysql':
                $this->assertStringContainsString('CONVERT(', $sql);
                $this->assertStringContainsString('USING utf8) COLLATE utf8_unicode_ci', $sql);
                break;
            case 'sqlite':
                $this->assertStringContainsString('LOWER(', $sql);
                break;
        }
    }
}
