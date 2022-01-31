<?php

namespace Customize\Controller;


use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController extends AbstractController
{
    /**
     * 転送するURLのリスト
     *
     * https://www.kasetu.co.jp/biseibutsu/marumijinko.movie.html から
     * https://www.kasetu.co.jp/user_data/biseibutsu_marumijinko へ転送する場合
     *
     * 'marumijinko.movie.html' => 'biseibutsu_marumijinko' のように記述する
     *
     * @var string[]
     */
    public static $pageList = [
        'biseibutsu/aburamimizu.movie.html' => 'biseibutsu_m_aburamimizu',
    ];

    /**
     * @Route("/biseibutsu/{page}", methods={"GET"})
     */
    public function index(string $page)
    {
        if (isset(self::$pageList[$page])) {
            return new RedirectResponse('https://www.kasetu.co.jp/user_data/'.self::$pageList[$page]);
        }

        throw new NotFoundHttpException();
    }
}
