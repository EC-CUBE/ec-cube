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

namespace Eccube\Service\Permission;

use Eccube\Common\EccubeConfig;

/**
 * 書き込み先を権限レーンへ分類した一覧を組み立てる.
 *
 * パスは設定パラメータから解決し, このクラスをレーン定義の唯一の置き場所とする.
 */
class PermissionRequirementProvider
{
    public function __construct(private readonly EccubeConfig $eccubeConfig)
    {
    }

    /**
     * @return list<PermissionRequirement>
     */
    public function requirements(): array
    {
        return [...$this->webLane(), ...$this->sshLane()];
    }

    /**
     * リクエスト処理中に書き込みが発生するため, CLI へ移せないもの.
     *
     * @return list<PermissionRequirement>
     */
    private function webLane(): array
    {
        $projectDir = $this->path('kernel.project_dir');
        $env = (string) $this->eccubeConfig->get('kernel.environment');

        return [
            $this->create($projectDir.'/var', WriteLane::WEB, true),
            $this->create($this->path('kernel.cache_dir'), WriteLane::WEB, true),
            $this->create($this->path('kernel.logs_dir'), WriteLane::WEB, true),
            $this->create($projectDir.'/var/sessions/'.$env, WriteLane::WEB, true),
            $this->create($this->path('eccube_save_image_dir'), WriteLane::WEB),
            $this->create($this->path('eccube_temp_image_dir'), WriteLane::WEB),
            $this->create($this->path('eccube_save_refund_request_file_dir'), WriteLane::WEB),
            $this->create($this->path('eccube_temp_refund_request_file_dir'), WriteLane::WEB),
            $this->create($projectDir.'/app/keystore', WriteLane::WEB, true, 'エージェントコマースの鍵を保存する. 未使用であれば作成されない.'),
            $this->create(
                dirname($this->path('eccube_content_maintenance_file_path')),
                WriteLane::WEB,
                false,
                'メンテナンスファイル (ECCUBE_MAINTENANCE_FILE_PATH) の生成先. '
                .'既定ではプロジェクトルート直下のため, ルートを Web サーバーから書き込み可能にする必要がある.'
            ),
        ];
    }

    /**
     * CLI (SSH ログインユーザー) へ移せるもの. Web サーバーは読み取りのみでよい.
     *
     * @return list<PermissionRequirement>
     */
    private function sshLane(): array
    {
        $projectDir = $this->path('kernel.project_dir');

        return [
            $this->create($this->path('eccube_theme_app_dir'), WriteLane::SSH),
            $this->create($this->path('eccube_html_dir').'/user_data', WriteLane::SSH),
            $this->create($this->path('plugin_realdir'), WriteLane::SSH),
            $this->create(
                $this->path('plugin_data_realdir'),
                WriteLane::SSH,
                false,
                'プラグインが実行時にデータを書き込む場合は, そのプラグインについて Web サーバーの書き込み権限が必要になる.'
            ),
            $this->create(
                $projectDir.'/app/proxy',
                WriteLane::SSH,
                false,
                'ReloadSafeAttributeDriver がリクエスト処理中に一時プロキシを生成するため, '
                .'プラグイン操作を CLI へ寄せることが読み取り専用化の前提になる.'
            ),
            $this->create($this->path('eccube_html_plugin_dir'), WriteLane::SSH),
            $this->create($projectDir.'/vendor', WriteLane::SSH),
            $this->create($projectDir.'/composer.json', WriteLane::SSH),
            $this->create($projectDir.'/composer.lock', WriteLane::SSH),
            $this->create($projectDir.'/.env', WriteLane::SSH, true),
        ];
    }

    private function create(string $path, WriteLane $lane, bool $optional = false, ?string $note = null): PermissionRequirement
    {
        $projectDir = $this->path('kernel.project_dir');
        $label = str_starts_with($path, $projectDir.'/') ? substr($path, strlen($projectDir) + 1) : $path;

        return new PermissionRequirement($path, $lane, $label, $optional, $note);
    }

    private function path(string $key): string
    {
        return rtrim(str_replace('\\', '/', (string) $this->eccubeConfig->get($key)), '/');
    }
}
