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

use Eccube\Entity\Master\RefundRequestStatus as Status;
use Eccube\Service\RefundRequestStateMachineContext;

$container->loadFromExtension('framework', [
    'workflows' => [
        'refund_request' => [
            'type' => 'state_machine',
            'marking_store' => [
                'type' => 'method',
            ],
            'supports' => [
                RefundRequestStateMachineContext::class,
            ],
            'initial_marking' => (string) Status::NEW,
            'places' => [
                (string) Status::NEW,
                (string) Status::PROCESSING,
                (string) Status::ACCEPTED,
                (string) Status::DECLINED,
                (string) Status::INFO_REQUESTED,
            ],
            'transitions' => [
                'start_processing' => [
                    'from' => (string) Status::NEW,
                    'to' => (string) Status::PROCESSING,
                ],
                'accept' => [
                    'from' => (string) Status::PROCESSING,
                    'to' => (string) Status::ACCEPTED,
                ],
                'decline' => [
                    'from' => (string) Status::PROCESSING,
                    'to' => (string) Status::DECLINED,
                ],
                'request_info' => [
                    'from' => (string) Status::PROCESSING,
                    'to' => (string) Status::INFO_REQUESTED,
                ],
                'resume_processing' => [
                    'from' => (string) Status::INFO_REQUESTED,
                    'to' => (string) Status::PROCESSING,
                ],
            ],
        ],
    ],
]);
