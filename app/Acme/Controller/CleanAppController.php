<?php

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
