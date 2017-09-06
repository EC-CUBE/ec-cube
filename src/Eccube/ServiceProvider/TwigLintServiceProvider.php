<?php

namespace Eccube\ServiceProvider;


use Eccube\Form\Validator\TwigLintValidator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TwigLintServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        if (!isset($app['twig'])) {
            throw new \LogicException(
                'You must register the TwigServiceProvider to use the TwigLintServiceProvider.'
            );
        }

        if (!isset($app['validator'])) {
            throw new \LogicException(
                'You must register the ValidatorServiceProvider to use the TwigLintServiceProvider.'
            );
        }

        $app[TwigLintValidator::class] = function (Container $app) {
            return new TwigLintValidator($app['twig']);
        };

        if (!isset($app['validator.validator_service_ids'])) {
            $app['validator.validator_service_ids'] = [];
        }

        $app['validator.validator_service_ids'] = array_merge(
            $app['validator.validator_service_ids'],
            [TwigLintValidator::class => TwigLintValidator::class]
        );
    }
}
