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
use Eccube\Plugin\AbstractPluginManager;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

/**
 * eccube:plugin:generate が生成する雛形の検証.
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6891
 * @see https://github.com/EC-CUBE/ec-cube/issues/7193
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
        $this->generate();

        $contents = $this->readGeneratedFile('Entity/Config.php');

        $this->assertDoesNotMatchRegularExpression(
            self::CLASS_EXISTS_GUARD_PATTERN,
            $contents,
            '生成された Entity/Config.php に if (!class_exists()) ガードが含まれています。'
            .' #6891 再発防止。Skill eccube-entity を参照してください。'
        );
    }

    /**
     * 削除済みパッケージのクラス (Sensio\...\Template 等) を use すると, 属性が解決できず設定画面が 500 になる (#7193).
     */
    public function testGeneratedFilesImportOnlyExistingClasses(): void
    {
        $this->generate();

        $pluginNamespace = 'Plugin\\'.self::PLUGIN_CODE.'\\';
        $files = iterator_to_array(Finder::create()->files()->name('*.php')->in((string) $this->pluginDir));
        $this->assertNotEmpty($files);

        foreach ($files as $file) {
            $contents = $file->getContents();

            // 構文エラーがあれば ParseError になる.
            \PhpToken::tokenize($contents, TOKEN_PARSE);

            preg_match_all('/^use\s+([\w\\\\]+)(?:\s+as\s+\w+)?;/m', $contents, $matches);
            foreach ($matches[1] as $import) {
                if (str_starts_with($import, $pluginNamespace)) {
                    continue;
                }
                // use Doctrine\ORM\Mapping as ORM は名前空間の import.
                if ($import === 'Doctrine\\ORM\\Mapping') {
                    continue;
                }

                $this->assertTrue(
                    class_exists($import) || interface_exists($import) || trait_exists($import),
                    sprintf('%s が存在しないクラス %s を use しています。', $file->getRelativePathname(), $import)
                );
            }
        }
    }

    /**
     * ConfigRepository::get() は id = 1 を読むため, 有効化時に初期レコードを作る PluginManager が要る (#7193).
     */
    public function testGeneratesPluginManagerCreatingInitialConfig(): void
    {
        $this->generate();

        $contents = $this->readGeneratedFile('PluginManager.php');
        $this->assertStringContainsString('$entityManager->find(Config::class, 1)', $contents);

        // PluginService は \Plugin\{code}\PluginManager を探す. 親とシグネチャが合わなければ読み込み時に Fatal になるため,
        // テストプロセスを巻き込まないよう別プロセスで読み込む.
        $projectDir = static::getContainer()->getParameter('kernel.project_dir');
        $class = 'Plugin\\'.self::PLUGIN_CODE.'\\PluginManager';
        $process = new Process([
            PHP_BINARY,
            '-r',
            'require $argv[1]; require $argv[2]; echo is_subclass_of($argv[3], $argv[4]) ? "ok" : "ng";',
            $projectDir.'/vendor/autoload.php',
            $this->pluginDir.'/PluginManager.php',
            $class,
            AbstractPluginManager::class,
        ]);
        $process->run();

        $this->assertSame('ok', trim($process->getOutput()), $process->getErrorOutput().$process->getOutput());
    }

    private function generate(): void
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
    }

    private function readGeneratedFile(string $path): string
    {
        $file = $this->pluginDir.'/'.$path;
        $this->assertFileExists($file);

        $contents = file_get_contents($file);
        $this->assertIsString($contents);

        return $contents;
    }
}
