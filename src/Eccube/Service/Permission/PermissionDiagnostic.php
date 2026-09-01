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

/**
 * 権限レーンの期待値と, 実際の所有者・パーミッションを突き合わせる.
 */
class PermissionDiagnostic
{
    public function __construct(
        private readonly PermissionRequirementProvider $requirementProvider,
        private readonly WebServerUserResolver $webServerUserResolver,
    ) {
    }

    public function run(): DiagnosticReport
    {
        $webServer = $this->webServerUserResolver->resolve();
        $cli = $this->webServerUserResolver->currentUser();

        $findings = [];
        foreach ($this->requirementProvider->requirements() as $requirement) {
            $findings[] = $this->evaluate($requirement, PathOwnership::of($requirement->path), $webServer, $cli);
        }

        $warmupFallback = $this->detectWarmupFallback();
        if ($warmupFallback instanceof PermissionFinding) {
            $findings[] = $warmupFallback;
        }

        return new DiagnosticReport($findings, $webServer, $cli);
    }

    /**
     * 事前コンパイル漏れのテンプレートがリクエスト処理中にコンパイルされていないか調べる.
     */
    private function detectWarmupFallback(): ?PermissionFinding
    {
        $requirement = $this->requirementProvider->warmupFallbackRequirement();
        if (!$requirement instanceof PermissionRequirement || !is_dir($requirement->path)) {
            return null;
        }

        if (!$this->hasCompiledTemplate($requirement->path)) {
            return null;
        }

        return new PermissionFinding(
            $requirement,
            PathOwnership::of($requirement->path),
            FindingSeverity::WARN,
            '事前コンパイルされていないテンプレートがリクエスト処理中にコンパイルされています',
            'bin/console eccube:cache:build を実行してください. '
            .'ビルドディレクトリを読み取り専用で運用する場合, ここに生成物が増え続けます.'
        );
    }

    private function hasCompiledTemplate(string $dir): bool
    {
        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                if ($file instanceof \SplFileInfo && $file->isFile() && $file->getExtension() === 'php') {
                    return true;
                }
            }
        } catch (\UnexpectedValueException) {
            // 読み取りできないディレクトリは判定不能として扱う.
            return false;
        }

        return false;
    }

    /**
     * 1 件を判定する. ファイルシステムには触れず, 渡された所有者情報だけで判断する.
     */
    public function evaluate(
        PermissionRequirement $requirement,
        PathOwnership $ownership,
        ?UserIdentity $webServer,
        UserIdentity $cli,
    ): PermissionFinding {
        if (!$ownership->exists) {
            if ($requirement->optional) {
                return new PermissionFinding($requirement, $ownership, FindingSeverity::OK, '未作成 (必要になった時点で生成されます)');
            }

            return new PermissionFinding($requirement, $ownership, FindingSeverity::NG, '存在しません', '作成してください.');
        }

        if (!$webServer instanceof UserIdentity) {
            return new PermissionFinding(
                $requirement,
                $ownership,
                FindingSeverity::WARN,
                'Web サーバーの実行ユーザーを特定できないため判定できません'
            );
        }

        return $requirement->lane === WriteLane::WEB
            ? $this->evaluateWebLane($requirement, $ownership, $webServer)
            : $this->evaluateSshLane($requirement, $ownership, $webServer, $cli);
    }

    private function evaluateWebLane(PermissionRequirement $requirement, PathOwnership $ownership, UserIdentity $webServer): PermissionFinding
    {
        if (!$ownership->isWritableBy($webServer)) {
            return new PermissionFinding(
                $requirement,
                $ownership,
                FindingSeverity::NG,
                'Web サーバーから書き込めません',
                sprintf('リクエスト処理中に書き込みが発生します. 所有者を uid=%d gid=%d へ変更するか, 書き込み権限を付与してください.', $webServer->uid, $webServer->gid)
            );
        }

        if ($ownership->isWorldWritable()) {
            return new PermissionFinding(
                $requirement,
                $ownership,
                FindingSeverity::WARN,
                'Web サーバーから書き込めますが, 任意のローカルユーザーからも書き込めます',
                'ECCUBE_UMASK に 0000 を設定していると, アプリケーションが作成するディレクトリは 0777, '
                .'ファイルは 0666 になります. 同一サーバーの他ユーザーから書き換えられるため, '
                .'権限を分離できる環境では ECCUBE_UMASK を空にしてください.'
            );
        }

        return new PermissionFinding($requirement, $ownership, FindingSeverity::OK, 'Web サーバーから書き込めます');
    }

    private function evaluateSshLane(
        PermissionRequirement $requirement,
        PathOwnership $ownership,
        UserIdentity $webServer,
        UserIdentity $cli,
    ): PermissionFinding {
        if ($ownership->isWritableBy($webServer)) {
            return new PermissionFinding(
                $requirement,
                $ownership,
                FindingSeverity::NG,
                'Web サーバーから書き込み可能です (想定: 読み取りのみ)',
                'CLI (SSH ログインユーザー) からのみ書き込める権限へ変更してください.'
            );
        }

        if (!$ownership->isReadableBy($webServer)) {
            return new PermissionFinding(
                $requirement,
                $ownership,
                FindingSeverity::NG,
                'Web サーバーから読み取れません',
                'このレーンは読み取り権限が必要です.'
            );
        }

        if (!$ownership->isWritableBy($cli)) {
            return new PermissionFinding(
                $requirement,
                $ownership,
                FindingSeverity::WARN,
                'Web サーバーは読み取りのみですが, 診断の実行ユーザーからも書き込めません',
                'このパスを書き換える CLI コマンドは, 書き込み権限を持つユーザーで実行してください.'
            );
        }

        return new PermissionFinding($requirement, $ownership, FindingSeverity::OK, 'Web サーバーは読み取りのみです');
    }
}
