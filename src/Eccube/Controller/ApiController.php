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

namespace Eccube\Controller;

use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Entity\Product;
use Eccube\Form\Type\Admin\SearchCustomerType;
use Eccube\Form\Type\Admin\SearchOrderType;
use Eccube\Form\Type\Admin\SearchProductType;
use Eccube\GraphQL\Types;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Util\FormUtil;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @var Types
     */
    private $types;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    public function __construct(Types $types, ProductRepository $productRepository, OrderRepository $orderRepository, CustomerRepository $customerRepository)
    {
        $this->types = $types;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @Route("/api", name="api")
     * @Security("has_role('ROLE_OAUTH2_READ')")
     */
    public function index(Request $request)
    {
        $body = json_decode($request->getContent(), true);
        $schema = $this->getSchema();
        $result = GraphQL::executeQuery($schema, $body['query']);

        return $this->json($result);
    }

    private function getSchema()
    {
        return new Schema([
            'query' => new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'products' => $this->createQuery(Product::class, SearchProductType::class, function ($searchData) {
                        return $this->productRepository->getQueryBuilderBySearchDataForAdmin($searchData)->getQuery()->getResult();
                    }),
                    'orders' => $this->createQuery(Order::class, SearchOrderType::class, function ($searchData) {
                        return $this->orderRepository->getQueryBuilderBySearchDataForAdmin($searchData)->getQuery()->getResult();
                    }),
                    'customers' => $this->createQuery(Customer::class, SearchCustomerType::class, function ($searchData) {
                        return $this->customerRepository->getQueryBuilderBySearchData($searchData)->getQuery()->getResult();
                    }),
                ],
                'typeLoader' => function ($name) {
                    return $this->types->get($name);
                },
            ]),
        ]);
    }

    private function createQuery($entityClass, $searchFormType, $resolver)
    {
        $builder = $this->formFactory->createBuilder($searchFormType, null, ['csrf_protection' => false]);
        $args = array_reduce($builder->getForm()->all(), function ($acc, $form) {
            /* @var FormInterface $form */
            $formConfig = $form->getConfig();
            $type = Type::string();
            if ($formConfig->getOption('multiple')) {
                $type = Type::listOf($type);
            }
            if ($formConfig->getOption('required') && !$formConfig->getOption('multiple')) {
                $type = Type::nonNull($type);
            }
            $acc[$form->getName()] = [
                'type' => $type,
                'description' => $formConfig->getOption('label') ? trans($formConfig->getOption('label')) : null,
            ];

            return $acc;
        }, []);

        return [
            'type' => Type::listOf($this->types->get($entityClass)),
            'args' => $args,
            'resolve' => function ($root, $args) use ($builder, $resolver) {
                $form = $builder->getForm();
                FormUtil::submitAndGetData($form, $args);

                return $resolver($form->getData());
            },
        ];
    }
}
