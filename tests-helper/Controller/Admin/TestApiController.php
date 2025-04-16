<?php

namespace Eccube\TestsHelper\Controller\Admin;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Eccube\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;


class TestApiController extends AbstractController
{
    /**
     * テストとして商品のAPIを作成
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return JsonResponse
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    #[Route('/orders/count', name: 'test_api_orders_count', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function count(Request $request, OrderRepository $orderRepository): JsonResponse
    {
        $qb = $orderRepository->createQueryBuilder('o')
            ->select('COUNT(o.id)');

        // 標準的なwhere句を使う場合
        $filters = [
            'order_status' => 'o.OrderStatus',
            //'customer_id'  => 'o.Customer',
        ];

        foreach ($filters as $param => $field) {
            $value = $request->query->get($param);
            if ($value !== null && $value !== '') {
                $qb->andWhere(sprintf('%s = :%s', $field, $param))
                    ->setParameter($param, $value);
            }
        }

        // 商品名などの部分一致の検索処理
        /*
        $productName = $request->query->get('product_name');
        if ($productName !== null && $productName !== '') {
            // OrderItem を JOIN
            $qb->join('o.OrderItems', 'oi')
                ->andWhere('oi.product_name LIKE :product_name')
                ->setParameter('product_name', '%'.$productName.'%');
        }
        */

        // product_idなどのJOIN系の検索処理
        /*
        $productId = $request->query->get('product_id');
        if ($productId !== null && $productId !== '') {
            $qb->join('o.OrderItems', 'oi_prod') // 同じ OrderItems を別名でJOIN
            ->andWhere('oi_prod.Product = :product_id')
                ->setParameter('product_id', (int)$productId);
        }
        */

        $count = $qb->getQuery()->getSingleScalarResult();

        return new JsonResponse(['count' => (int)$count]);
    }

}
