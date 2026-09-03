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

        return new DiagnosticReport($findings, $webServer, $cli);
    }

    /**
     * 1 件を判定する. ファイルシステムには触れず, 渡された所有者情報だけで判断する.
     *
     * @param UserIdentity|null $webServer Web サーバーの実行ユーザー. 特定できなかった場合は null
     * @param UserIdentity|null $cli       診断の実行ユーザー. 特定できなかった場合は null
     */
    public function evaluate(
        PermissionRequirement $requirement,
        PathOwnership $ownership,
        ?UserIdentity $webServer,
        ?UserIdentity $cli,
    ): PermissionFinding {
        if (!$ownership->exists) {
            if ($requirement->optional) {
                return new PermissionFinding($requirement, $ownership, FindingSeverity::OK, '未作成 (必要になった時点で生成されます)');
            }

            return new PermissionFinding($requirement, $ownership, FindingSeverity::NG, '存在しません', '作成してください.');
        }

        // 任意のローカルユーザーから書ける時点でレーン S の前提が崩れているため,
        // Web サーバーの uid を特定できていなくても NG と判定できる
        if ($requirement->lane === WriteLane::SSH && $ownership->isWorldWritable()) {
            return new PermissionFinding(
                $requirement,
                $ownership,
                FindingSeverity::NG,
                '任意のローカルユーザーから書き込めます (想定: 読み取りのみ)',
                'bin/console が umask(0000) を設定するため, CLI が作成したディレクトリは 0777 になります. '
                .'other の書き込み権限を外してください.'
            );
        }

        if (!$webServer instanceof UserIdentity) {
            return new PermissionFinding(
                $requirement,
                $ownership,
                FindingSeverity::WARN,
                'Web サーバーの実行ユーザーを特定できないため判定できません'
            );
        }

        if ($ownership->hasUnknownAncestor()) {
            return new PermissionFinding(
                $requirement,
                $ownership,
                FindingSeverity::WARN,
                '祖先ディレクトリの権限を確認できないため到達可否を判定できません',
                'open_basedir 等で上位ディレクトリを参照できません.'
            );
        }

        // 診断の実行ユーザーは対象パスを stat できている以上, 祖先も通り抜けられている.
        // 判定が必要なのは Web サーバーだけ
        $unreachable = $ownership->unreachableAncestorFor($webServer);
        if ($unreachable instanceof PathOwnership) {
            return new PermissionFinding(
                $requirement,
                $ownership,
                FindingSeverity::NG,
                'Web サーバーから到達できません',
                sprintf(
                    '祖先ディレクトリ %s (uid=%d gid=%d %s) に実行権限がないため, 配下へ到達できません. '
                    .'実行権限を付与するか, 所有者を変更してください.',
                    $unreachable->path,
                    $unreachable->uid,
                    $unreachable->gid,
                    $unreachable->permissionsString()
                )
            );
        }

        return $requirement->lane === WriteLane::WEB
            ? $this->evaluateWebLane($requirement, $ownership, $webServer)
            : $this->evaluateSshLane($requirement, $ownership, $webServer, $cli);
    }

    private function evaluateWebLane(PermissionRequirement $requirement, PathOwnership $ownership, UserIdentity $webServer): PermissionFinding
    {
        if (!$ownership->isWritableBy($webServer)) {
            $hint = sprintf('リクエスト処理中に書き込みが発生します. 所有者を uid=%d gid=%d へ変更するか, 書き込み権限を付与してください.', $webServer->uid, $webServer->gid);
            if ($ownership->isDir) {
                $hint .= ' ディレクトリはエントリの作成・削除に実行 (x) 権限も必要です.';
            }

            return new PermissionFinding($requirement, $ownership, FindingSeverity::NG, 'Web サーバーから書き込めません', $hint);
        }

        if ($ownership->isWorldWritable()) {
            return new PermissionFinding(
                $requirement,
                $ownership,
                FindingSeverity::WARN,
                'Web サーバーから書き込めますが, 任意のローカルユーザーからも書き込めます',
                'bin/console が umask(0000) を設定するため, CLI が作成したディレクトリは 0777 になります '
                .'(dev では index.php も umask(0000) を設定するため Web からの作成も同様です). '
                .'同一サーバーの他ユーザーから書き換えられます.'
            );
        }

        return new PermissionFinding($requirement, $ownership, FindingSeverity::OK, 'Web サーバーから書き込めます');
    }

    private function evaluateSshLane(
        PermissionRequirement $requirement,
        PathOwnership $ownership,
        UserIdentity $webServer,
        ?UserIdentity $cli,
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
            $hint = 'このレーンは読み取り権限が必要です.';
            if ($ownership->isDir) {
                $hint .= ' ディレクトリは配下のファイルを開くために実行 (x) 権限も必要です.';
            }

            return new PermissionFinding($requirement, $ownership, FindingSeverity::NG, 'Web サーバーから読み取れません', $hint);
        }

        // CLI の実行ユーザーを特定できない環境では, CLI 側の書き込み可否は判定しない
        if ($cli instanceof UserIdentity && !$ownership->isWritableBy($cli)) {
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
