<?php

namespace Eccube\Entity;


use Eccube\Service\ItemValidateException;

interface ItemHolderInterface
{
    /**
     * @param ItemValidateException $error
     * @return void
     */
    public function addError(ItemValidateException $error);

    public function getItems();

    /**
     * @return ItemValidateException[]
     */
    public function getErrors();
}