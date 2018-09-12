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

namespace Eccube\Exception;

class PluginApiException extends \Exception
{
    private $curlInfo;

    /**
     * PluginApiException constructor.
     *
     * @param $curlInfo
     */
    public function __construct($curlInfo)
    {
        parent::__construct(self::getResponseErrorMessage($curlInfo), $curlInfo['http_code']);
        $this->curlInfo = $curlInfo;
    }

    private static function getResponseErrorMessage($info)
    {
        if (!empty($info)) {
            $messageId = 'admin.store.package.api.'.$info['http_code'].'.error';
            $message = trans($messageId);
            if ($message === $messageId) {
                $statusCode = $info['http_code'];
                $message = $info['message'];
                $message = $statusCode.' : '.$message;
            }
        } else {
            $message = trans('ownerstore.text.error.timeout');
        }

        return $message;
    }
}
