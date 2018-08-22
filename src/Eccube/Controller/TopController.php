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

namespace Eccube\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Application;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TopController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @Template("index.twig")
     */
    public function index(Application $app, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // FIXME 引数の $app とは別物...
        $application = $this->get('app');
        // orm.em も使える
        $Page = $application['orm.em']->find(\Eccube\Entity\Page::class, 1);

        return [
        ];
    }

    /**
     * ページネーションのサンプル
     *
     * @Route("/pagination")
     *
     * @param Paginator $paginator
     * @param EntityManagerInterface $em
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index2(Paginator $paginator, EntityManagerInterface $em)
    {
        $qb = $em->createQueryBuilder()
            ->select('c')
            ->from('Eccube\Entity\Customer', 'c');

        /** @var SlidingPagination $pagination */
        $pagination = $paginator->paginate(
            $qb,
            1,
            10,
            [
                'distinct' => false,
            ]
        );

        dump($pagination->getTotalItemCount());

        foreach ($pagination as $customr) {
            dump($customr->getId());
        }

        return new Response('paginator sample.');
    }
}
