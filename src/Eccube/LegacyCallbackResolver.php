<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube;

use Eccube\Framework\Display;
use Eccube\Framework\SessionFactory;
use Eccube\Framework\Helper\MobileHelper;
use Eccube\Framework\Helper\SessionHelper;
use Eccube\Framework\Util\GcUtils;
use Eccube\Framework\Util\Utils;
use Symfony\Component\HttpFoundation\Request;

class LegacyCallbackResolver extends \Silex\CallbackResolver
{
    const PAGE_PATTERN = '/\A\\\\Eccube(\\\\Plugin\\\\\w+)?\\\\Page\\\\/';

    private $app2;

    public function __construct(\Pimple $app)
    {
        $this->app2 = $app;

        parent::__construct($app);
    }

    /**
     * Returns true if the string is a valid service method representation.
     *
     * @param string $name
     *
     * @return Boolean
     */
    public function isValid($name)
    {
        return parent::isValid($name) || (is_string($name) && preg_match(static::PAGE_PATTERN, $name));
    }

    /**
     * Returns a callable given its string representation.
     *
     * @param string $name
     *
     * @return array A callable array
     *
     * @throws \InvalidArgumentException In case the method does not exist.
     */
    public function convertCallback($name)
    {
        if (preg_match(static::PAGE_PATTERN, $name)) {
            return function (Application $app, Request $request) use ($name) {
                // setpath
                $path_info = $request->getPathInfo();
                if (substr($path_info, -1) == '/') {
                    $path_info .= 'index.php';
                }
                $_SERVER['SCRIPT_NAME'] = str_replace('/index.php', '', $request->server->get('SCRIPT_NAME')) . $path_info . (substr($path_info, -1) == '/' ? 'index.php' : '');
                $_SERVER['SCRIPT_FILENAME'] = dirname($request->server->get('SCRIPT_FILENAME')).$path_info;

                // rtrim は PHP バージョン依存対策
                $GLOBALS['_realdir'] = rtrim(realpath(rtrim(realpath(dirname($request->server->get('SCRIPT_FILENAME'))), '/\\') . '/'), '/\\') . '/';
                $GLOBALS['_realdir'] = str_replace('\\', '/', $GLOBALS['_realdir']);
                $GLOBALS['_realdir'] = str_replace('//', '/', $GLOBALS['_realdir']);
                define('HTML_REALDIR', $GLOBALS['_realdir']);

                /** HTMLディレクトリからのDATAディレクトリの相対パス */
                define('HTML2DATA_DIR', '../app/');
                define('USE_FILENAME_DIR_INDEX', null);

                if (!defined('DATA_REALDIR')) {
                    define('DATA_REALDIR', HTML_REALDIR . HTML2DATA_DIR);
                }

                // アプリケーション初期化処理
                if (!defined('CACHE_REALDIR')) {
                    /** キャッシュ生成ディレクトリ */
                    define('CACHE_REALDIR', DATA_REALDIR . "cache/eccube/");
                }

                \Eccube\Framework\Helper\HandleErrorHelper::load();

                // アプリケーション初期化処理
                $objInit = new \Eccube\Framework\Initial();
                $objInit->init();

                // Page instance
                $objPage = new $name($app);
                if ($objPage instanceof \Eccube\Page\Admin\AbstractAdminPage) {
                    define('ADMIN_FUNCTION', true);
                } else {
                    define('FRONT_FUNCTION', true);
                }

                // 定数 SAFE が設定されている場合、DBアクセスを回避する。主に、エラー画面を意図する。
                if (!defined('SAFE') || !SAFE) {
                    // インストール中で無い場合、
                    if (!GcUtils::isInstallFunction()) {
                        // インストールチェック
                        Utils::sfInitInstall();

                        // セッション初期化・開始
                        $sessionFactory = SessionFactory::getInstance();
                        $sessionFactory->initSession();

                        /*
                         * 管理画面の場合は認証行う.
                         * 認証処理忘れ防止のため, \Eccube\Page\Admin::init() 等ではなく, ここでチェックする.
                         */
                        SessionHelper::adminAuthorization();
                    }
                }

                // bufferを初期化する
                if ($objPage instanceof \Eccube\Page\Admin\AbstractAdminPage) {
                    ob_start();
                } else {
                    // 絵文字変換 (除去) フィルターを組み込む。
                    ob_start(array('\\Eccube\\Framework\\MobileEmoji', 'handler'));

                    if (Application::alias('eccube.display')->detectDevice() == DEVICE_TYPE_MOBILE) {
                        // resize_image.phpは除外
                        if (!$objPage instanceof \Eccube\Page\ResizeImage) {
                            /* @var $objMobile MobileHelper */
                            $objMobile = Application::alias('eccube.helper.mobile');
                            $objMobile->sfMobileInit();
                        }
                    }
                }
                $objPage->init();
                $objPage->process();

                $response = ob_get_contents();
                ob_end_clean();

                return $response;
            };
        } else {
            return parent::convertCallback($name);
        }
    }
}
