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

namespace Eccube\Tests\DependencyInjection\Compiler;

use Eccube\Common\EccubeNav;
use Eccube\DependencyInjection\Compiler\NavCompilerPass;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NavCompilerPassTest extends EccubeTestCase
{
    /**
     * DefaultNavを追加
     */
    public function testDefaultNav()
    {
        $container = $this->createContainer();

        $container->addCompilerPass(new NavCompilerPass());
        $container->compile();

        $eccubeNav = $container->getParameter('eccube_nav');

        // DefaultNavの全要素が含まれている
        self::assertArraySubset(DefaultNav::getNav(), $eccubeNav);

        // DefaultNav以外の要素が含まれていない
        self::assertEquals(DefaultNav::getNav(), $eccubeNav);
    }

    /**
     * DefaultNavにAddNavを追加
     *
     * @dataProvider addNavProvider
     *
     * @param $class
     * @param $expected
     */
    public function testAddNav($class, $expected)
    {
        $container = $this->createContainer();

        $container->register($class)
            ->addTag(NavCompilerPass::NAV_TAG);

        $container->addCompilerPass(new NavCompilerPass());
        $container->compile();

        $eccubeNav = $container->getParameter('eccube_nav');

        // DefaultNavの全要素が含まれている
        self::assertArraySubset(DefaultNav::getNav(), $eccubeNav);

        // AddNavの全要素が含まれている
        self::assertArraySubset($expected, $eccubeNav);
    }

    public function addNavProvider()
    {
        return [
            [AddNav1::class, AddNav1::getNav()],
            [AddNav2::class, AddNav2::getNav()],
            [AddNav3::class, AddNav3::getNav()],
        ];
    }

    /**
     * DefaultNavをUpdateNavで更新
     */
    public function testUpdateNav()
    {
        $container = $this->createContainer();

        $container->register(UpdateNav::class)
            ->addTag(NavCompilerPass::NAV_TAG);

        $container->addCompilerPass(new NavCompilerPass());
        $container->compile();

        $eccubeNav = $container->getParameter('eccube_nav');

        // DefaultNavから変更されている
        self::assertNotEquals(DefaultNav::getNav(), $eccubeNav);

        // nav['default']['name'] 以外のDefaultNavの全要素が含まれている
        $expected = DefaultNav::getNav();
        unset($expected['default']['name']);
        self::assertArraySubset($expected, $eccubeNav);

        // UpdateNavの全要素が含まれている
        self::assertArraySubset(UpdateNav::getNav(), $eccubeNav);
    }

    /**
     * @return ContainerBuilder
     */
    public function createContainer()
    {
        $container = new ContainerBuilder();

        $container->setParameter('eccube_nav', []);

        $container->register(DefaultNav::class)
            ->addTag(NavCompilerPass::NAV_TAG);

        return $container;
    }
}

/**
 * デフォルト
 *
 * Class DefaultNav
 */
class DefaultNav implements EccubeNav
{
    public static function getNav()
    {
        return [
            'default' => [
                'name' => 'default',
                'icon' => 'fa-cube',
                'child' => [
                    'default_1' => [
                        'name' => 'default-1',
                        'url' => 'admin_homepage',
                    ],
                    'default_2' => [
                        'name' => 'default-2',
                        'child' => [
                            'default_2_1' => [
                                'name' => 'default-2-1',
                                'url' => 'admin_homepage',
                            ],
                            'default_2_2' => [
                                'name' => 'default-2-2',
                                'url' => 'admin_homepage',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}

/**
 * 1階層目に追加
 *
 * Class AddNav1
 */
class AddNav1 implements EccubeNav
{
    public static function getNav()
    {
        return [
            'add' => [
                'name' => 'add',
                'icon' => 'fa-cube',
                'url' => 'admin_homepage',
            ],
        ];
    }
}

/**
 * 2階層目に追加
 *
 * Class AddNav2
 */
class AddNav2 implements EccubeNav
{
    public static function getNav()
    {
        return [
            'default' => [
                'child' => [
                    'default_add' => [
                        'name' => 'default-add',
                        'url' => 'admin_homepage',
                    ],
                ],
            ],
        ];
    }
}

/**
 * 3階層目に追加
 *
 * Class AddNav3
 */
class AddNav3 implements EccubeNav
{
    public static function getNav()
    {
        return [
            'default' => [
                'child' => [
                    'default_2' => [
                        'child' => [
                            'default_2_add' => [
                                'name' => 'default-2-add',
                                'url' => 'admin_homepage',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}

/**
 * 上書き
 *
 * Class UpdateNav
 */
class UpdateNav implements EccubeNav
{
    public static function getNav()
    {
        return [
            'default' => [
                'name' => 'update',
            ],
        ];
    }
}
