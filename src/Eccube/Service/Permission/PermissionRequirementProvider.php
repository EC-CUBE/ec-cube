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
        $requirements = [];
        foreach ([...$this->webLane(), ...$this->sshLane()] as $requirement) {
            $registered = $requirements[$requirement->path] ?? null;
            $requirements[$requirement->path] = $registered instanceof PermissionRequirement
                ? $this->merge($registered, $requirement)
                : $requirement;
        }

        return array_values($requirements);
    }

    /**
     * 事前コンパイルされなかったテンプレートのフォールバック先.
     *
     * prod では twig が [build_dir/twig (読み取り専用), runtime_dir/twig] の 2 層構成になり,
     * build 側に無いテンプレートだけがリクエスト処理中に runtime 側へコンパイルされる.
     * ここに生成物があれば eccube:cache:build の warmup 漏れを意味する.
     *
     * dev は auto_reload が有効で 2 層構成にならず, runtime 側が通常の出力先になるため対象外.
     */
    public function warmupFallbackRequirement(): ?PermissionRequirement
    {
        if ($this->eccubeConfig->get('kernel.debug')) {
            return null;
        }

        return $this->create(
            $this->path('eccube_runtime_dir').'/twig',
            WriteLane::WEB,
            true,
            'ビルドディレクトリに無いテンプレートのフォールバック先.'
        );
    }

    /**
     * 同じパスが複数の役割を持つ場合 (メンテナンスファイルの生成先が var/ を指す場合等) に 1 件へまとめる.
     *
     * レーンと表示名は先に定義したものを採用し, 注意書きは両方を残す. 一方でも必須なら必須として扱う.
     */
    private function merge(PermissionRequirement $registered, PermissionRequirement $duplicated): PermissionRequirement
    {
        $notes = array_filter([$registered->note, $duplicated->note]);

        return new PermissionRequirement(
            $registered->path,
            $registered->lane,
            $registered->label,
            $registered->optional && $duplicated->optional,
            $notes === [] ? null : implode(' ', $notes),
        );
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
            $this->create($this->path('eccube_runtime_dir'), WriteLane::WEB, true),
            $this->create($this->path('kernel.logs_dir'), WriteLane::WEB, true),
            $this->create($projectDir.'/var/sessions/'.$env, WriteLane::WEB, true),
            $this->create($this->path('eccube_save_image_dir'), WriteLane::WEB),
            $this->create($this->path('eccube_temp_image_dir'), WriteLane::WEB),
            $this->create($this->path('eccube_save_refund_request_file_dir'), WriteLane::WEB),
            $this->create($this->path('eccube_temp_refund_request_file_dir'), WriteLane::WEB),
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
            $this->create(
                $projectDir.'/var',
                WriteLane::SSH,
                true,
                'Web サーバーが書き込むのは配下の var/runtime・var/sessions・var/log で, '
                .'var 自体へは書き込まない. Web サーバーからは通過 (r-x) できればよい.'
            ),
            $this->create(
                $projectDir.'/app/keystore',
                WriteLane::SSH,
                true,
                '秘密鍵の格納先. CLI で事前に配置し Web サーバーからは読み取りのみとする. '
                .'実行時に鍵を生成する機能 (AcpMessageSigner) を使う場合のみ Web サーバーの書き込み権限が必要になる.'
            ),
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
            $this->create(
                $this->path('kernel.build_dir'),
                WriteLane::SSH,
                true,
                'コンパイル済みコンテナ・ルーティング・事前コンパイル済みテンプレートの出力先. '
                .'生成は bin/console eccube:cache:build で行う.'
            ),
            $this->create(
                $this->path('kernel.cache_dir'),
                WriteLane::SSH,
                true,
                'ビルド時にのみ使用する. 実行時のキャッシュは eccube_runtime_dir 側に生成される.'
            ),
            $this->create($projectDir.'/.env', WriteLane::SSH, true),
        ];
    }

    private function create(string $path, WriteLane $lane, bool $optional = false, ?string $note = null): PermissionRequirement
    {
        $projectDir = $this->path('kernel.project_dir');
        $label = match (true) {
            $path === $projectDir => '.',
            str_starts_with($path, $projectDir.'/') => substr($path, strlen($projectDir) + 1),
            default => $path,
        };

        return new PermissionRequirement($path, $lane, $label, $optional, $note);
    }

    private function path(string $key): string
    {
        return rtrim(str_replace('\\', '/', (string) $this->eccubeConfig->get($key)), '/');
    }
}
