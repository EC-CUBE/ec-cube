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
use Symfony\Component\Finder\Finder;

/**
 * Web サーバーの実行ユーザーを実測する.
 *
 * 実行ユーザー名は環境ごとに異なるため固定値を持たず, Web サーバーが生成したファイルの所有者から判定する.
 * 判定材料は次の 2 種類に分ける.
 *
 * - Web サーバーでのみ生成されるもの: 所有者をそのまま採用する
 * - bin/console でも生成されるもの (ログ): 診断の実行ユーザーと異なる場合のみ採用する.
 *   同じ uid だった場合は「Web サーバーが同一ユーザー」なのか「CLI が書いたファイル」なのか区別できないため.
 *
 * 配布物を含むディレクトリ (html/upload/save_image) は, 同梱画像の所有者を拾ってしまうため判定に使わない.
 */
class WebServerUserResolver
{
    public function __construct(private readonly EccubeConfig $eccubeConfig)
    {
    }

    public function resolve(): ?UserIdentity
    {
        $cli = $this->currentUser();

        foreach ($this->candidates() as [$dir, $pattern, $webServerOnly]) {
            $identity = $this->identityFromDir($dir, $pattern);
            if (!$identity instanceof UserIdentity) {
                continue;
            }

            if (!$webServerOnly && $identity->uid === $cli->uid) {
                continue;
            }

            return $identity;
        }

        return null;
    }

    /**
     * 診断を実行しているユーザー.
     */
    public function currentUser(): UserIdentity
    {
        $uid = getmyuid();
        $gid = getmygid();

        return new UserIdentity($uid === false ? -1 : $uid, $gid === false ? -1 : $gid, 'getmyuid()');
    }

    /**
     * 判定に使うディレクトリ, 採用するファイル名のパターン, Web サーバーでのみ生成されるかどうか.
     *
     * @return list<array{string, string, bool}>
     */
    private function candidates(): array
    {
        $projectDir = (string) $this->eccubeConfig->get('kernel.project_dir');
        $env = (string) $this->eccubeConfig->get('kernel.environment');

        return [
            // セッションファイルは Web リクエストでのみ生成される
            [$projectDir.'/var/sessions/'.$env, '/^sess_/', true],
            // アップロードされたファイル. 同梱されているのはドットファイルのみ
            [(string) $this->eccubeConfig->get('eccube_temp_image_dir'), '/^[^.]/', true],
            [(string) $this->eccubeConfig->get('eccube_temp_refund_request_file_dir'), '/^[^.]/', true],
            [(string) $this->eccubeConfig->get('eccube_save_refund_request_file_dir'), '/^[^.]/', true],
            // ログは bin/console 実行でも書き込まれる
            [$this->eccubeConfig->get('kernel.logs_dir').'/'.$env, '/\.log$/', false],
        ];
    }

    private function identityFromDir(string $dir, string $pattern): ?UserIdentity
    {
        if (!is_dir($dir)) {
            return null;
        }

        $finder = Finder::create()->files()->in($dir)->depth(0)->name($pattern);

        foreach ($finder as $file) {
            $ownership = PathOwnership::of($file->getPathname());
            if (!$ownership->exists) {
                continue;
            }

            return new UserIdentity($ownership->uid, $ownership->gid, $file->getPathname());
        }

        return null;
    }
}
