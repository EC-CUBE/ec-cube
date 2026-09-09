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

namespace Eccube\Tests\Service\Permission;

use Eccube\Common\EccubeConfig;
use Eccube\Service\Permission\PermissionRequirement;
use Eccube\Service\Permission\PermissionRequirementProvider;
use Eccube\Service\Permission\WriteLane;
use PHPUnit\Framework\TestCase;

/**
 * レーン定義の組み立てを検証する.
 */
final class PermissionRequirementProviderTest extends TestCase
{
    private const PROJECT_DIR = '/var/www/html';

    public function testLanesAreAssigned(): void
    {
        $requirements = $this->requirementsByLabel();

        $this->assertSame(WriteLane::WEB, $requirements['var/cache/prod']->lane);
        $this->assertSame(WriteLane::WEB, $requirements['html/upload/save_image']->lane);
        $this->assertSame(WriteLane::SSH, $requirements['app/template']->lane);
        $this->assertSame(WriteLane::SSH, $requirements['html/user_data']->lane);
        $this->assertSame(WriteLane::SSH, $requirements['composer.json']->lane);
    }

    public function testRuntimeGeneratedPathsAreOptional(): void
    {
        $requirements = $this->requirementsByLabel();

        $this->assertTrue($requirements['var/cache/prod']->optional);
        $this->assertFalse($requirements['html/upload/save_image']->optional);
    }

    public function testMaintenanceFileDirectoryIsMergedWhenItPointsToAnExistingRequirement(): void
    {
        // ECCUBE_MAINTENANCE_FILE_PATH を var/ 配下へ移した場合, var の重複行を作らない
        $requirements = $this->requirementsByLabel(self::PROJECT_DIR.'/var/.maintenance');

        $this->assertArrayHasKey('var', $requirements);
        $this->assertStringContainsString('メンテナンスファイル', (string) $requirements['var']->note);
        // 一方が必須なら必須として扱う
        $this->assertFalse($requirements['var']->optional);
    }

    public function testMaintenanceFileDirectoryIsListedSeparatelyWhenItIsTheProjectRoot(): void
    {
        // 既定はプロジェクトルート直下のため, ルート自体がレーン W の対象になる
        $requirements = $this->requirementsByLabel();

        $this->assertArrayHasKey('.', $requirements);
        $this->assertSame(WriteLane::WEB, $requirements['.']->lane);
        $this->assertSame(self::PROJECT_DIR, $requirements['.']->path);
    }

    public function testPathsAreUnique(): void
    {
        $paths = array_map(
            static fn (PermissionRequirement $requirement) => $requirement->path,
            $this->provider()->requirements()
        );

        $this->assertSame(array_unique($paths), $paths);
    }

    /**
     * @return array<string, PermissionRequirement>
     */
    private function requirementsByLabel(?string $maintenanceFilePath = null): array
    {
        $requirements = [];
        foreach ($this->provider($maintenanceFilePath)->requirements() as $requirement) {
            $requirements[$requirement->label] = $requirement;
        }

        return $requirements;
    }

    private function provider(?string $maintenanceFilePath = null): PermissionRequirementProvider
    {
        $values = [
            'kernel.project_dir' => self::PROJECT_DIR,
            'kernel.environment' => 'prod',
            'kernel.cache_dir' => self::PROJECT_DIR.'/var/cache/prod',
            'kernel.logs_dir' => self::PROJECT_DIR.'/var/log',
            'eccube_save_image_dir' => self::PROJECT_DIR.'/html/upload/save_image',
            'eccube_temp_image_dir' => self::PROJECT_DIR.'/html/upload/temp_image',
            'eccube_save_refund_request_file_dir' => self::PROJECT_DIR.'/html/upload/refund_request/save',
            'eccube_temp_refund_request_file_dir' => self::PROJECT_DIR.'/html/upload/refund_request/temp',
            'eccube_content_maintenance_file_path' => $maintenanceFilePath ?? self::PROJECT_DIR.'/.maintenance',
            'eccube_theme_app_dir' => self::PROJECT_DIR.'/app/template',
            'eccube_html_dir' => self::PROJECT_DIR.'/html',
            'eccube_html_plugin_dir' => self::PROJECT_DIR.'/html/plugin',
            'plugin_realdir' => self::PROJECT_DIR.'/app/Plugin',
            'plugin_data_realdir' => self::PROJECT_DIR.'/app/PluginData',
        ];

        $eccubeConfig = $this->createMock(EccubeConfig::class);
        $eccubeConfig->method('get')->willReturnCallback(static fn (string $key): mixed => $values[$key] ?? null);

        return new PermissionRequirementProvider($eccubeConfig);
    }
}
