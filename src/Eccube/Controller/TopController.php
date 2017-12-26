<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Application;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(service=TopController::class)
 */
class TopController extends AbstractController
{
    protected $eccubeConfig;
    public function __construct($eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

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
