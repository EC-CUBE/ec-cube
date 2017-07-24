<?php

namespace Plugin\FormExtension\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Plugin\FormExtension\Form\Extension\EntryTypeExtension;

class FormExtensionServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app->extend(
            'form.type.extensions',
            function ($extensions) {
                $extensions[] = new EntryTypeExtension();

                return $extensions;
            }
        );
    }
}
