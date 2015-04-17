<?php

namespace Plugin\SamplePayment;

use Symfony\Component\Form\FormEvent;

class PaymentForm
{
    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        if ('shopping' === $form->getName()) {
            $form->add('cart', 'sample_payment');
        }
    }

}