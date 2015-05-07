<?php

namespace Eccube\Controller;

use Eccube\Application;

class UserDataController
{

    public function index(Application $app)
    {
        $url = ltrim($app['request']->getRequestUri(), '/');
        $device_type_id = $this->getDeviceTypeId($app);
        $PageLayout = $app['eccube.repository.page_layout']->findOneBy(
            array('url'=> $url, 'device_type_id' => $device_type_id)
        );

        // テンプレートファイルの取得
        $templatePath = $app['eccube.repository.page_layout']
            ->getTemplatePath($device_type_id, true);
        $file = $templatePath . $PageLayout->getFileName() . '.twig';

        return $app['twig']->render($file);
    }

    /**
     * FIXME: アクセスしたデバイスによっての切替を実装する？
     * @param $app
     * @return integer
     */
    public function getDeviceTypeId($app)
    {
        return $app['config']['device_type_pc'];
    }
}
