<?php

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\ItemInterface;
use Eccube\Repository\DeliveryRepository;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\ValidatableItemProcessor;

/**
 * 販売種別に配送業者が設定されているかどうか.
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

    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
        if (!$item->isProduct()) {
            return;
        }

        $SaleType = $item->getProductClass()->getSaleType();
        $Deliveries = $this->deliveryRepository->findBy(['SaleType' => $SaleType, 'visible' => true]);

        if (empty($Deliveries)) {
            throw InvalidItemException::fromProductClass('cart.product.not.saletype', $item->getProductClass());
        }
    }

    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
        $item->setQuantity(0);
    }
}
