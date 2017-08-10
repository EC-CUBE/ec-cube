<?php

namespace Acme\Controller;


use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Component(value="Acme\Controller\CleanAppController")
 * @Route(path="/cleanapp", service="Acme\Controller\CleanAppController")
 */
class CleanAppController
{
    /**
     * @Inject(value="eccube.repository.master.csv_type")
     */
    protected $repository;

    /**
     * @Route(path="/")
     * @return string
     */
    public function index()
    {
        dump($this->repository->findAll());

        return 'ok';
    }
}
