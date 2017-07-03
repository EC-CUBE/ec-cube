<?php

namespace Eccube\Entity;


interface ItemHolderInterface
{
    public function addError($error);

    public function getItems();
}