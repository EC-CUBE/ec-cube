<?php

namespace Eccube\Controller\Block;

use Eccube\Application;

class RecommendController
{
    public function index(Application $app)
    {
        $BestProducts = $app['eccube.repository.recommend_product']
            ->getList()
        ;
        return $app['view']->render('Block/recommend.twig', compact('BestProducts'));
    }
}
