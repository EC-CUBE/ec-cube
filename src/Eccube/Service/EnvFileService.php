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

namespace Eccube\Service;

/**
 * .env ファイルへの書き込みが実行時に反映されるかを判定するサービス.
 *
 * セキュリティ設定・テンプレート設定などは .env を書き換えて機能を実現するが,
 * docker-compose の環境変数や本番環境では .env が使われず, 書き換えても反映されない.
 * ユーザーが変更できないことに気付けるよう, 反映されない理由を検出する.
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6130
 */
class EnvFileService
{
    /** .env ファイルが存在しない（.env を利用していない） */
    public const REASON_NOT_FOUND = 'not_found';

    /** .env ファイルに書き込み権限がない */
    public const REASON_NOT_WRITABLE = 'not_writable';

    /** .env.local.php（dump-env の最適化済みスナップショット）が存在し, .env より優先される */
    public const REASON_LOCAL_PHP = 'local_php';

    /** 対象の環境変数が OS のプロセス環境変数として設定されており, .env の値を上書きしている */
    public const REASON_OVERRIDDEN = 'overridden';

    public function __construct(private readonly string $projectDir)
    {
    }

    /**
     * .env への書き込みが実行時のロードに反映されない理由を返す.
     *
     * 空配列であれば .env への書き込みが有効に反映される.
     *
     * @param string[] $keys 対象の環境変数キー（OS 環境変数によるオーバーライド判定に使用）
     *
     * @return string[] REASON_* 定数の配列
     */
    public function getIneffectiveReasons(array $keys = []): array
    {
        $reasons = [];

        $envFile = $this->projectDir.'/.env';
        if (!file_exists($envFile)) {
            $reasons[] = self::REASON_NOT_FOUND;
        } elseif (!is_writable($envFile)) {
            $reasons[] = self::REASON_NOT_WRITABLE;
        }

        // .env.local.php があると bootEnv は .env より優先するため, .env の変更は反映されない
        if (file_exists($this->projectDir.'/.env.local.php')) {
            $reasons[] = self::REASON_LOCAL_PHP;
        }

        // bootEnv は OS のプロセス環境変数を上書きしないため, getenv が値を返すキーは .env の変更が反映されない
        foreach ($keys as $key) {
            if (false !== getenv($key)) {
                $reasons[] = self::REASON_OVERRIDDEN;
                break;
            }
        }

        return $reasons;
    }

    /**
     * 対象の環境変数について, .env への書き込みが有効に反映されるか.
     *
     * @param string[] $keys 対象の環境変数キー
     */
    public function isEffective(array $keys = []): bool
    {
        return [] === $this->getIneffectiveReasons($keys);
    }
}
