<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework;

use Eccube\Application;
use Eccube\Framework\Response;
use Eccube\Framework\View\SiteView;
use Eccube\Framework\View\SmartphoneView;
use Eccube\Framework\View\MobileView;
use Eccube\Framework\View\AdminView;

/**
 * Http コンテンツ出力を制御するクラス.
 *
 * @author Ryuichi Tokugami
 * @version $Id$
 */
class Display
{
    /** @var Response */
    public $response;

    /** 端末種別を保持する */
    // XXX プロパティとして保持する必要があるのか疑問。
    public static $device;

    /** @var \Eccube\Framework\View インスタンス */
    public $view;

    public $deviceSeted = false;

    /*
     * TODO php4を捨てたときに ここのコメントアウトを外してね。
     * const('MOBILE',1);
     * const('SMARTPHONE',2);
     * const('PC',10);
     * const('ADMIN',99);
     */

    public function __construct($hasPrevURL = true)
    {
        $this->response = Application::alias('eccube.response');
        if ($hasPrevURL) {
            $this->setPrevURL();
        }
    }

    public function setPrevURL()
    {
        // TODO SiteSession で実装した方が良さげ
        /* @var $objCartSess CartSession */
        $objCartSess = Application::alias('eccube.cart_session');
        $objCartSess->setPrevURL($_SERVER['REQUEST_URI']);
    }

    /**
     * LC_Page のパラメーターを, テンプレートに設定し, 出力の準備を行う.
     *
     * @param LC_Page $page LC_Page インスタンス
     * @param $is_admin boolean 管理画面を扱う場合 true
     */
    public function prepare($page, $is_admin = false)
    {
        if (!$this->deviceSeted || !is_null($this->view)) {
            $device = ($is_admin) ? DEVICE_TYPE_ADMIN : $this->detectDevice();
            $this->setDevice($device);
        }
        $this->assignobj($page);
        $this->view->setPage($page);
        $this->response->setResposeBody($this->view->getResponse($page->getTemplate()));
    }

    /**
     * リロードを行う.
     *
     * Response::reload() のラッパーです.
     */
    public function reload($queryString = array(), $removeQueryString = false)
    {
        $this->response->reload($queryString, $removeQueryString);
    }

    public function noAction()
    {
        return;
    }

    /**
     * ヘッダを追加する.
     */
    public function addHeader($name, $value)
    {
        $this->response->addHeader($name, $value);
    }

    /**
     * デバイス毎の出力方法を自動で変更する、ファサード
     * Enter description here ...
     */
    public function setDevice($device = DEVICE_TYPE_PC)
    {
        switch ($device) {
            case DEVICE_TYPE_MOBILE:
                if (USE_MOBILE === false) {
                    exit;
                }
                $this->response->setContentType('text/html');
                $this->setView(new MobileView(true, DEVICE_TYPE_MOBILE));
                break;
            case DEVICE_TYPE_SMARTPHONE:
                $this->setView(new SmartphoneView(true, DEVICE_TYPE_SMARTPHONE));
                break;
            case DEVICE_TYPE_PC:
                $this->setView(new SiteView(true, DEVICE_TYPE_PC));
                break;
            case DEVICE_TYPE_ADMIN:
                $this->setView(new AdminView());
        }
        $this->deviceSeted = true;
    }

    /**
     * View インスタンスを設定する.
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * 端末種別を判別する。
     *
     * Display::->MOBILE = ガラケー = 1
     * Display::->SMARTPHONE = スマホ = 2
     * Display::->PC = PC = 10
     *
     * @static
     * @param          $reset boolean
     * @return integer 端末種別ID
     */
    public static function detectDevice($reset = FALSE)
    {
        if (is_null(static::$device) || $reset) {
            $nu = new \Net_UserAgent_Mobile();
            $su = new SmartphoneUserAgent();
            if ($nu->isMobile()) {
                static::$device = DEVICE_TYPE_MOBILE;
            } elseif ($su->isSmartphone()) {
                static::$device = DEVICE_TYPE_SMARTPHONE;
            } else {
                static::$device = DEVICE_TYPE_PC;
            }
        }

        return static::$device;
    }

    public function assign($val1,$val2)
    {
        $this->view->assign($val1, $val2);
    }

    /**
     * @param LC_Page $obj
     */
    public function assignobj($obj)
    {
        $this->view->assignobj($obj);
    }

    public function assignarray($array)
    {
        $this->view->assignarray($array);
    }
}
