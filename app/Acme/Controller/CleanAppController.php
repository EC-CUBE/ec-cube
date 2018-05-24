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

namespace Acme\Controller;

use Eccube\Annotation\Inject;
use Eccube\Repository\BaseInfoRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route(path="/cleanapp", service=CleanAppController::class)
 */
class CleanAppController
{
    /**
     * @var BaseInfoRepository
     * @Inject(BaseInfoRepository::class)
     */
    protected $repository;

    /**
     * @Route(path="/")
     *
     * @return string
     */
    public function index()
    {
        dump($this->repository->findAll());

        return 'ok';
    }
}
