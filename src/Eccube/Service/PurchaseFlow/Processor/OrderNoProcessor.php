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

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Order;
use Eccube\Repository\OrderRepository;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Util\StringUtil;

class OrderNoProcessor implements ItemHolderPreprocessor
{
    /**
     * @var EccubeConfig
     */
    private $eccubeConfig;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * OrderNoProcessor constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param OrderRepository $orderRepository
     */
    public function __construct(EccubeConfig $eccubeConfig, OrderRepository $orderRepository)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        $Order = $itemHolder;

        if ($Order instanceof Order) {
            if ($Order->getOrderNo()) {
                return;
            }

            if (null === $Order->getId()) {
                return;
            }

            $format = $this->eccubeConfig['eccube_order_no_format'];
            if (empty($format)) {
                // フォーマットが設定されていなければ受注IDが設定される
                $Order->setOrderNo($Order->getId());
            } else {
                do {
                    $orderNo = preg_replace_callback('/\{(.*)}/U', function ($matches) use ($Order) {
                        if (count($matches) === 2) {
                            $dateTime = new \DateTime('now', new \DateTimeZone($this->eccubeConfig->get('timezone')));
                            switch ($matches[1]) {
                                case 'yyyy':
                                    return $dateTime->format('Y');
                                case 'yy':
                                    return $dateTime->format('y');
                                case 'mm':
                                    return $dateTime->format('m');
                                case 'dd':
                                    return $dateTime->format('d');
                                default:
                                    $res = explode(',', str_replace(' ', '', $matches[1]));
                                    if (count($res) === 2 && is_numeric($res[1])) {
                                        if ($res[0] === 'id') {
                                            return sprintf("%0{$res[1]}d", $Order->getId());
                                        } elseif ($res[0] === 'random') {
                                            $random = random_int(1, (int) str_repeat('9', $res[1]));

                                            return sprintf("%0{$res[1]}d", $random);
                                        } elseif ($res[0] === 'random_alnum') {
                                            return strtoupper(StringUtil::random($res[1]));
                                        }
                                    }

                                    return $Order->getId();
                            }
                        }

                        return $Order->getId();
                    }, $format);

                    $tempOrder = $this->orderRepository->findOneBy([
                        'order_no' => $orderNo,
                    ]);
                } while ($tempOrder);

                $Order->setOrderNo($orderNo);
            }
        }
    }
}
