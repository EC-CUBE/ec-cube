<?php

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Doctrine\ORM\EntityManager;

class OrderCodePurchaseProcessor implements PurchaseProcessor
{
    
    private $orderNoFormat;
    
    private $entityManager;
    
    public function __construct(EntityManager $entityManager, $orderNoFormat)
    {
        $this->entityManager = $entityManager;
        
        $this->orderNoFormat = $orderNoFormat;
    }
    
    /**
     * {@inheritdoc}
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        $Order = $itemHolder;
        
        if ($Order instanceof \Eccube\Entity\Order) {
            if ($Order->getCode() == "") {
                if (is_null($Order->getId())) {
                    $this->entityManager->persist($Order);
                    $this->entityManager->flush();
                }
                
                $orderCode = preg_replace_callback('/\${(.*)}/U', function($matches) use ($Order) {
                    if (count($matches) == 2) {
                        switch ($matches[1]) {
                            case "yyyy":
                                return date('Y');
                            case "mm":
                                return date('m');
                            case "dd":
                                return date('d');
                            default:
                                $no = explode(',', $matches[1]);
                                if (count($no) == 2 && $no[0] == 'number' && is_numeric($no[1])) {
                                    return sprintf("%0{$no[1]}d", $Order->getId());
                                }
                                return "";
                        }
                    }
                    
                    return "";
                }, $this->orderNoFormat);
                
                $Order->setCode($orderCode);
            }
        }
        
        return ProcessResult::success();
    }
}