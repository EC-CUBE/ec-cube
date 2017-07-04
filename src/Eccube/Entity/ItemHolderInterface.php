<?php

namespace Eccube\Entity;


use Eccube\Service\ItemValidateException;

interface ItemHolderInterface
{
    /**
     * @param string $error
     * @return void
     */
    public function addError($error);

    public function getItems();

    /**
     * @return ItemValidateException[]
     */
    public function getErrors();
}