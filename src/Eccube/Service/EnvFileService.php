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

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Dotenv\Exception\FormatException;

/**
 * .env ファイルへの書き込みが実行時に反映されるかを判定するサービス.
 *
 * セキュリティ設定・テンプレート設定などは .env を書き換えて機能を実現するが,
 * docker-compose の環境変数や本番環境では .env が使われず, 書き換えても反映されない.
 * ユーザーが変更できないことに気付けるよう, 反映されない理由を検出する.
 *
 * 反映不可の理由は 2 種類に分かれる.
 *   - ファイル単位で全キーが反映されない理由（getIneffectiveReasons):
 *     .env が存在しない / 書き込み不可 / .env.local.php が優先される.
 *   - キー単位で反映されない理由（getOverriddenKeys):
 *     OS のプロセス環境変数, または .env.local 等のカスケードファイルが .env を上書きしている.
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
     * .env への書き込みが（キーによらず）実行時のロードに反映されない理由を返す.
     *
     * ここで返るのはファイル単位で全キーが反映されない理由のみ.
     * 空配列であれば, 少なくとも .env ファイル自体は読み込まれる状態にある.
     * 個々のキーが上書きされているかは {@see getOverriddenKeys()} で判定する.
     *
     * @return string[] REASON_NOT_FOUND / REASON_NOT_WRITABLE / REASON_LOCAL_PHP の配列
     */
    public function getIneffectiveReasons(): array
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

        return $reasons;
    }

    /**
     * 対象キーのうち, .env への書き込みが実行時に反映されないキー（上書きされているキー）を返す.
     *
     * Symfony Dotenv は putenv を使わないため getenv だけでは判定できない.
     * .env 系ファイルから実際に populate されたキーは $_SERVER['SYMFONY_DOTENV_VARS'] に載るので,
     * そこに含まれ, かつカスケードファイルで再定義されていないキーは「.env が実効」＝反映されると判定する.
     * それ以外で $_ENV / $_SERVER / getenv に値があるキーは OS 環境変数側が勝つため反映されない.
     *
     * @param string[] $keys 対象の環境変数キー
     *
     * @return string[] 上書きされているキーの部分集合
     */
    public function getOverriddenKeys(array $keys): array
    {
        if ([] === $keys) {
            return [];
        }

        // Dotenv が実際に .env 系ファイルから populate したキーの一覧.
        // これらは .env（系）の値が実効値なので, .env への書き込みは反映される.
        $dotenvVars = array_flip(array_filter(explode(',',
            (string) ($_SERVER['SYMFONY_DOTENV_VARS'] ?? $_ENV['SYMFONY_DOTENV_VARS'] ?? '')
        )));

        // .env より後に読まれ .env の値を上書きするカスケードファイルで定義されたキー.
        $cascadeKeys = $this->getCascadeDefinedKeys();

        $overridden = [];
        foreach ($keys as $key) {
            // .env 系から populate されており, カスケードで再定義されていなければ反映される.
            if (isset($dotenvVars[$key]) && !isset($cascadeKeys[$key])) {
                continue;
            }
            // カスケードで再定義されている, もしくは Dotenv が触れていない
            // プロセス環境変数（$_ENV / $_SERVER / getenv）が存在する場合は反映されない.
            if (isset($cascadeKeys[$key])
                || isset($_ENV[$key]) || isset($_SERVER[$key]) || false !== getenv($key)) {
                $overridden[] = $key;
            }
        }

        return $overridden;
    }

    /**
     * 対象の環境変数について, .env への書き込みが有効に反映されるか.
     *
     * @param string[] $keys 対象の環境変数キー
     */
    public function isEffective(array $keys = []): bool
    {
        return [] === $this->getIneffectiveReasons() && [] === $this->getOverriddenKeys($keys);
    }

    /**
     * .env の後に読まれるカスケードファイルで定義されているキーの一覧を返す.
     *
     * Dotenv::loadEnv は .env → .env.local → .env.$env → .env.$env.local の順に読み,
     * 後から読んだファイルが .env の値を上書きする. ファイルの存在だけでは過剰なため,
     * 実際にキーを定義しているファイルのみを対象にする.
     *
     * @return array<string, true> キー名 => true
     */
    private function getCascadeDefinedKeys(): array
    {
        $appEnv = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null;
        $cascadeFiles = array_filter([
            '.env.local',
            $appEnv ? '.env.'.$appEnv : null,
            $appEnv ? '.env.'.$appEnv.'.local' : null,
        ]);

        $defined = [];
        foreach ($cascadeFiles as $file) {
            $path = $this->projectDir.'/'.$file;
            if (!is_file($path) || !is_readable($path)) {
                continue;
            }

            try {
                $parsed = (new Dotenv())->parse((string) file_get_contents($path), $path);
            } catch (FormatException) {
                // 解析できないファイルは判定対象から除外する（管理画面を落とさない）
                continue;
            }

            foreach (array_keys($parsed) as $name) {
                $defined[$name] = true;
            }
        }

        return $defined;
    }
}
