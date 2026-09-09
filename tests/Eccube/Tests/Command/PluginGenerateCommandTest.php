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

namespace Eccube\Tests\Command;

use Eccube\Command\PluginGenerateCommand;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * eccube:plugin:generate が旧 if (!class_exists()) ガード無しの Entity を生成すること (#6891).
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6891
 */
final class PluginGenerateCommandTest extends EccubeTestCase
{
    private const PLUGIN_CODE = 'GuardTest6891';

    private const CLASS_EXISTS_GUARD_PATTERN = '/if\s*\(\s*!class_exists\s*\(/';

    private ?string $pluginDir = null;

    protected function tearDown(): void
    {
        if ($this->pluginDir !== null && is_dir($this->pluginDir)) {
            (new Filesystem())->remove($this->pluginDir);
        }

        parent::tearDown();
    }

    public function testGeneratedEntityHasNoClassExistsGuard(): void
    {
        $projectDir = static::getContainer()->getParameter('kernel.project_dir');
        $this->pluginDir = $projectDir.'/app/Plugin/'.self::PLUGIN_CODE;

        if (is_dir($this->pluginDir)) {
            (new Filesystem())->remove($this->pluginDir);
        }

        /** @var PluginGenerateCommand $command */
        $command = static::getContainer()->get(PluginGenerateCommand::class);

        $tester = new CommandTester($command);
        $exitCode = $tester->execute([
            'name' => 'Guard Test 6891',
            'code' => self::PLUGIN_CODE,
            'ver' => '1.0.0',
        ]);

        $this->assertSame(0, $exitCode);

        $entityFile = $this->pluginDir.'/Entity/Config.php';
        $this->assertFileExists($entityFile);

        $contents = file_get_contents($entityFile);
        $this->assertIsString($contents);

        $this->assertDoesNotMatchRegularExpression(
            self::CLASS_EXISTS_GUARD_PATTERN,
            $contents,
            '生成された Entity/Config.php に if (!class_exists()) ガードが含まれています。'
            .' #6891 再発防止。Skill eccube-entity を参照してください。'
        );
    }
}
