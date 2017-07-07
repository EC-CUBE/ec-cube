<?php

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\ItemInterface;
use Eccube\Repository\DeliveryRepository;
use Eccube\Service\PurchaseFlow\ItemValidateException;
use Eccube\Service\PurchaseFlow\ValidatableItemProcessor;

/**
 * 商品種別に配送業者が設定されているかどうか.
 */
class DeliverySettingValidator extends ValidatableItemProcessor
{
    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    public function __construct(DeliveryRepository $deliveryRepository)
    {
        $this->deliveryRepository = $deliveryRepository;
    }

    protected function validate(ItemInterface $item)
    {
        if (!$item->isProduct()) {
            return;
        }

        $ProductType = $item->getProductClass()->getProductType();
        $Deliveries = $this->deliveryRepository->findBy(['ProductType' => $ProductType]);

        if (empty($Deliveries)) {
            throw new ItemValidateException('配送準備ができていないエラー');
        }
    }

    protected function handle(ItemInterface $item)
    {
        $item->setQuantity(0);
    }
}
