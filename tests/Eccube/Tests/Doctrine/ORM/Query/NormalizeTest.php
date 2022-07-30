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
        if ($this->entityManager->getConnection()->getDriver()->getName() === 'pdo_pgsql')
        {
            $this->assertTrue(strpos($sql, 'あいうえおかきくけこさしすせそたちつてとなにぬねのはひふへほまみむめもやゆよらりるれろわをんがぎぐげござじずぜぞだぢづでどばびぶべぼぱぴぷぺぽぁぃぅぇぉっゃゅょゎゐゑー') !== false);
            $this->assertTrue(strpos($sql, 'アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲンガギグゲゴザジズゼゾダヂヅデドバビブベボパピプペポァィゥェォッャュョヮヰヱー') !== false) ;
        }
    }
}
