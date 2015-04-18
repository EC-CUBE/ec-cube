<?php

namespace Plugin\SampleForm;

use Symfony\Component\Form\FormEvent;

class SampleForm
{
    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        if ('contact' === $form->getName()) {
            $form->add('sample_form', 'text');
        }
    }
}