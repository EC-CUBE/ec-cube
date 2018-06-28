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

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Repository\OrderRepository;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseProcessor;
use Eccube\Util\StringUtil;

class OrderNoProcessor implements PurchaseProcessor
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

        if ($Order instanceof \Eccube\Entity\Order) {
            if ($Order->getOrderNo()) {
                return ProcessResult::success();
            }

            $format = $this->eccubeConfig['eccube_order_no_format'];
            if (empty($format)) {
                // フォーマットが設定されていなければ受注IDが設定される
                $Order->setOrderNo($Order->getId());
            } else {
                do {
                    $orderNo = preg_replace_callback('/\{(.*)}/U', function ($matches) use ($Order) {
                        if (count($matches) == 2) {
                            switch ($matches[1]) {
                                case 'yyyy':
                                    return date('Y');
                                case 'yy':
                                    return date('y');
                                case 'mm':
                                    return date('m');
                                case 'dd':
                                    return date('d');
                                default:
                                    $res = explode(',', $matches[1]);
                                    if (count($res) == 2 && is_numeric($res[1])) {
                                        if ($res[0] == 'id') {
                                            return sprintf("%0{$res[1]}d", $Order->getId());
                                        } elseif ($res[0] == 'random') {
                                            $rondom = substr(mt_rand(1, 999999999), 0, $res[1]);

                                            return sprintf("%0{$res[1]}d", $rondom);
                                        } elseif ($res[0] == 'randomalphanum') {
                                            return strtoupper(StringUtil::random(5));
                                        }
                                    }

                                    return '';
                            }
                        }

                        return '';
                    }, $format);

                    $tempOrder = $this->orderRepository->findOneBy([
                        'order_no' => $orderNo,
                    ]);
                } while ($tempOrder);

                $Order->setOrderNo($orderNo);
            }
        }

        return ProcessResult::success();
    }
}
