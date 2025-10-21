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

namespace Eccube\Exception;

class PluginApiException extends \Exception
{
    /** @var array<string, mixed>|null */
    private $curlInfo;

    /**
     * PluginApiException constructor.
     *
     * @param array<string, mixed>|null $curlInfo
     *
     * @return void
     */
    public function __construct($curlInfo)
    {
        parent::__construct(self::getResponseErrorMessage($curlInfo), (int) $curlInfo['http_code']);
        $this->curlInfo = $curlInfo;
    }

    /**
     * @param array<string, mixed> $info
     *
     * @return string
     */
    private static function getResponseErrorMessage($info): string
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

    /**
     * @return array<string, array<string, string>|null>
     */
    public function __debugInfo(): array
    {
        return [
            'curlInfo' => $this->curlInfo,
        ];
    }

    /**
     * @return array<string, array<string, string>|null>
     */
    public function __serialize(): array
    {
        return [
            'curlInfo' => $this->curlInfo,
        ];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return parent::__toString().', CURL_INFO:'.json_encode($this->curlInfo ?? []);
    }
}
