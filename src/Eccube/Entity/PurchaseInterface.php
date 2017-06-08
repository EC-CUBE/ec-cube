<?php

namespace Eccube\Entity;

interface PurchaseInterface
{
    public function setTotal($total);
    public function getTotal();
    public function getItems();
}
