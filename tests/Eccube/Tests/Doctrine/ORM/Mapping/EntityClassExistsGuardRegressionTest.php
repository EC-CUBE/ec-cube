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

namespace Eccube\Tests\Doctrine\ORM\Mapping;

use Eccube\Tests\EccubeTestCase;

/**
 * Entity の if (!class_exists()) ガード再発防止テスト (#6891).
 *
 * #6895 / #7051 で core Entity からガードを全廃した後も、
 * PluginGenerateCommand 雛形が旧パターンを出力し続ける再生産経路が残っていた。
 * 本テストはコア Entity とジェネレータ雛形への再混入を CI で検知する。
 *
 * 意図的に検査しない:
 * - codeception/_data/plugins (旧プラグイン再現用フィクスチャ)
 * - tests/** (PluginServiceTest 等の意図的な旧パターン)
 * - app/Customize/Entity (プロジェクト固有)
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6891
 * @see https://github.com/EC-CUBE/ec-cube/pull/6895 Entity の if(!class_exists()) ガード全廃
 */
final class EntityClassExistsGuardRegressionTest extends EccubeTestCase
{
    private const CLASS_EXISTS_GUARD_PATTERN = '/if\s*\(\s*!class_exists\s*\(/';

    public function testCoreEntityDirectoryHasNoClassExistsGuard(): void
    {
        $projectDir = static::getContainer()->getParameter('kernel.project_dir');
        $entityDir = $projectDir.'/src/Eccube/Entity';

        $violations = $this->findClassExistsGuardViolations($entityDir);

        $this->assertSame(
            [],
            $violations,
            'src/Eccube/Entity に if (!class_exists()) ガードが見つかりました。'
            .' #6891 再発防止。Skill eccube-entity を参照してください。'
            ."\n".implode("\n", $violations)
        );
    }

    public function testPluginGenerateCommandTemplateHasNoClassExistsGuard(): void
    {
        $projectDir = static::getContainer()->getParameter('kernel.project_dir');
        $commandFile = $projectDir.'/src/Eccube/Command/PluginGenerateCommand.php';

        $this->assertFileExists($commandFile);

        $contents = file_get_contents($commandFile);
        $this->assertIsString($contents);

        $this->assertDoesNotMatchRegularExpression(
            self::CLASS_EXISTS_GUARD_PATTERN,
            $contents,
            'PluginGenerateCommand の Entity 雛形に if (!class_exists()) ガードが含まれています。'
            .' #6891 再発防止。Skill eccube-entity を参照してください。'
        );
    }

    /**
     * @return list<string> 違反ファイルの相対パス
     */
    private function findClassExistsGuardViolations(string $directory): array
    {
        $violations = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());
            if (!\is_string($contents)) {
                continue;
            }

            if (preg_match(self::CLASS_EXISTS_GUARD_PATTERN, $contents)) {
                $violations[] = $file->getPathname();
            }
        }

        sort($violations);

        return $violations;
    }
}
