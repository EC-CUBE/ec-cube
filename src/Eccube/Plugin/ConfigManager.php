<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Plugin;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * プラグインの config.yml, event.yml を扱うクラス
 *
 * TODO プラグインからこのクラスをカスタマイズすることはできないので,
        static メソッドにしているが, DI コンテナでの管理を検討する.
 */
class ConfigManager
{
    /**
     * すべてのプラグインの設定情報を返す.
     *
     * すべてのプラグインの config.yml 及び event.yml を読み込み、連想配列で返す.
     * キャッシュファイルが存在する場合は、キャッシュを利用する.
     * キャッシュファイルが存在しない場合は、キャッシュを生成する.
     * $app['debug'] = true の場合は、キャッシュを利用しない.
     *
     * @return array
     */
    public static function getPluginConfigAll($debug = false)
    {
        if ($debug) {
            return self::parsePluginConfigs();
        }
        $pluginConfigCache = self::getPluginConfigCacheFile();
        if (file_exists($pluginConfigCache)) {
            return require $pluginConfigCache;
        }
        if (self::writePluginConfigCache($pluginConfigCache) === false) {
            return self::parsePluginConfigs();
        } else {
            return require $pluginConfigCache;
        }
    }

    /**
     * プラグイン設定情報のキャッシュを書き込む.
     *
     * @param string $cacheFile
     *
     * @return int|boolean file_put_contents() の結果
     */
    public static function writePluginConfigCache($cacheFile = null)
    {
        if (is_null($cacheFile)) {
            $cacheFile = self::getPluginConfigCacheFile();
        }
        $pluginConfigs = self::parsePluginConfigs();
        $temp_dir = self::getPluginTempRealDir();
        if (!file_exists($temp_dir)) {
            @mkdir($temp_dir);
        }

        return file_put_contents($cacheFile, sprintf('<?php return %s', var_export($pluginConfigs, true)).';');
    }

    /**
     * プラグイン設定情報のキャッシュファイルを削除する.
     *
     * @return boolean
     */
    public static function removePluginConfigCache()
    {
        $cacheFile = self::getPluginConfigCacheFile();
        if (file_exists($cacheFile)) {
            return unlink($cacheFile);
        }

        return false;
    }

    /**
     * プラグイン設定情報のキャッシュファイルパスを返す.
     *
     * @return string
     */
    public static function getPluginConfigCacheFile()
    {
        return self::getPluginTempRealDir().'/config_cache.php';
    }

    public static function getPluginTempRealDir()
    {
        return __DIR__.'/../../../app/cache/plugin';
    }

    public static function getPluginRealDir()
    {
        return __DIR__.'/../../../app/Plugin';
    }

    /**
     * プラグイン設定情報をパースし, 連想配列で返す.
     *
     * すべてのプラグインを探索し、 config.yml 及び event.yml をパースする.
     * パースした情報を連想配列で返す.
     *
     * @return array
     */
    public static function parsePluginConfigs()
    {
        $finder = Finder::create()
            ->in(self::getPluginRealDir())
            ->directories()
            ->depth(0);
        $finder->sortByName();

        $pluginConfigs = [];
        foreach ($finder as $dir) {
            $code = $dir->getBaseName();
            if (!$code) {
                //PHP5.3のgetBaseNameバグ対応
                if (PHP_VERSION_ID < 50400) {
                    $code = $dir->getFilename();
                }
            }
            $file = $dir->getRealPath().'/config.yml';
            $config = null;
            if (file_exists($file)) {
                $config = Yaml::parse(file_get_contents($file));
            } else {
                continue;
            }

            $file = $dir->getRealPath().'/event.yml';
            $event = null;
            if (file_exists($file)) {
                $event = Yaml::parse(file_get_contents($file));
            }
            if (!is_null($config)) {
                $pluginConfigs[$code] = [
                    'config' => $config,
                    'event' => $event,
                ];
            }
        }

        return $pluginConfigs;
    }
}
