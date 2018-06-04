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

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseProcessor;

class OrderCodeProcessor implements PurchaseProcessor
{
    /**
     * @var EccubeConfig
     */
    private $eccubeConfig;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * OrderCodePurchaseProcessor constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EntityManagerInterface $entityManager, EccubeConfig $eccubeConfig)
    {
        $this->entityManager = $entityManager;

        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        $Order = $itemHolder;

        if ($Order instanceof \Eccube\Entity\Order) {
            if ($Order->getOrderCode()) {
                return ProcessResult::success();
            }

            $orderCode = preg_replace_callback('/\${(.*)}/U', function ($matches) use ($Order) {
                if (count($matches) == 2) {
                    switch ($matches[1]) {
                        case 'yyyy':
                            return date('Y');
                        case 'mm':
                            return date('m');
                        case 'dd':
                            return date('d');
                        default:
                            $no = explode(',', $matches[1]);
                            if (count($no) == 2 && $no[0] == 'number' && is_numeric($no[1])) {
                                return sprintf("%0{$no[1]}d", $Order->getId());
                            }

                            return '';
                    }
                }

                return '';
            }, $this->eccubeConfig['eccube_order_code_format']);

            $Order->setOrderCode($orderCode);
        }

        return ProcessResult::success();
    }
}
