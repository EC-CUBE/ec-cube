<?php

namespace Eccube\Controller\Block;

use Eccube\Application;

class NewsController
{
    public function index(Application $app)
    {
        $NewsList = $app['orm.em']->getRepository('\Eccube\Entity\News')
            ->findAll();

        return $app['view']->render('Block/news.twig', array(
            'NewsList' => $NewsList,
        ));
    }
}
