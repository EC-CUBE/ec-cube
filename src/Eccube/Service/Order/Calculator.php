<?php
namespace Eccube\Service\Order;

interface Calculator {
    function calculate(Eccube\Entity\Order $order);
    function getName();
}
