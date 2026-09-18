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

namespace Eccube\DependencyInjection\Resource;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Symfony\Component\Config\Resource\SelfCheckingResourceInterface;

/**
 * dtb_plugin の有効・無効をコンパイル済みコンテナの鮮度に反映するリソース.
 *
 * EccubeExtension::configurePlugins() は、コンテナのコンパイル時に dtb_plugin を読んで
 * eccube.plugins.enabled を決め、その値から Twig パス・翻訳・ルーティングを登録する。
 * このリソースが無いと、コンテナの鮮度はファイルの更新日時だけで判定されるため、
 * プラグインを有効化・無効化してもコンテナは作り直されず、DB と食い違ったまま使われる。
 *
 * 特に eccube:plugin:enable は、コマンド自身のカーネル起動でコンテナを作り直すことがあり、
 * その時点では enabled がまだ更新されていないため「有効化は成功したのに反映されない」状態になる。
 *
 * 鮮度の判定は kernel.debug が true のときだけ行われる (ConfigCache が debug のときのみ
 * リソースを検査する) ため、本番環境で毎リクエスト DB へ問い合わせることはない。
 */
class PluginStateResource implements SelfCheckingResourceInterface
{
    /**
     * @param array<string, bool> $state プラグインコード => 有効かどうか
     */
    public function __construct(private readonly array $state)
    {
    }

    #[\Override]
    public function __toString(): string
    {
        return 'eccube.plugin_state';
    }

    #[\Override]
    public function isFresh(int $timestamp): bool
    {
        $current = self::currentState();

        // DB へ接続できないときは鮮度を判定できない。作り直しても同じ状態にしか
        // ならないため、古いとは扱わない (インストール前や DB 停止中に毎回
        // コンパイルし直すのを避ける)。
        if (null === $current) {
            return true;
        }

        return $current === $this->state;
    }

    /**
     * dtb_plugin から現在の有効・無効を読む.
     *
     * @return array<string, bool>|null DB へ接続できない場合は null
     */
    public static function currentState(): ?array
    {
        $conn = self::createConnection();
        if (null === $conn) {
            return null;
        }

        try {
            $plugins = $conn->executeQuery('select * from dtb_plugin')->fetchAllAssociative();
        } catch (\Throwable) {
            // テーブルが無い (インストール前) 場合など.
            return null;
        } finally {
            $conn->close();
        }

        return self::toState($plugins);
    }

    /**
     * dtb_plugin の行から、コード => 有効かどうか の連想配列を作る.
     *
     * 比較に使うため、コード順に並べておく.
     *
     * @param array<int, array<string, mixed>> $plugins
     *
     * @return array<string, bool>
     */
    public static function toState(array $plugins): array
    {
        $state = [];
        foreach ($plugins as $plugin) {
            if (!array_key_exists('code', $plugin)) {
                continue;
            }
            $state[(string) $plugin['code']] = array_key_exists('enabled', $plugin) && (bool) $plugin['enabled'];
        }
        ksort($state);

        return $state;
    }

    /**
     * DATABASE_URL から接続を作る.
     *
     * EccubeExtension::configurePlugins() と同じ理由で、コンテナのサービスは使わず
     * DsnParser で DATABASE_URL を展開して直接接続する.
     */
    public static function createConnection(): ?Connection
    {
        $databaseUrl = (string) env('DATABASE_URL');
        if ('' === $databaseUrl) {
            return null;
        }

        $dsnParser = new DsnParser([
            'db2' => 'ibm_db2',
            'mssql' => 'pdo_sqlsrv',
            'mysql' => 'pdo_mysql',
            'mysql2' => 'pdo_mysql',
            'postgres' => 'pdo_pgsql',
            'postgresql' => 'pdo_pgsql',
            'pgsql' => 'pdo_pgsql',
            'sqlite' => 'pdo_sqlite',
            'sqlite3' => 'pdo_sqlite',
        ]);

        try {
            $conn = DriverManager::getConnection($dsnParser->parse($databaseUrl));
            // Connection::connect() は DBAL 4 で protected のため、軽いクエリで疎通を確かめる.
            $conn->executeQuery('select 1');
        } catch (\Throwable) {
            return null;
        }

        return $conn;
    }
}
