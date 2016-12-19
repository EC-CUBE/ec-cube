<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\ServiceProvider;

use Silex\Application;
use Silex\ConstraintValidatorFactory;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\DefaultTranslator;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Silex\Api\BootableProviderInterface;
use Pimple\ServiceProviderInterface;
use Pimple\Container;

/**
 * Symfony Validator component Provider.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @deprecated since 3.0.0, to be removed in 3.1
 */
class ValidatorServiceProvider implements BootableProviderInterface, ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['validator'] = function ($app) {

            return new Validator(
                $app['validator.mapping.class_metadata_factory'],
                $app['validator.validator_factory'],
                isset($app['translator']) ? $app['translator'] : new DefaultTranslator(),
                'validators',
                $app['validator.object_initializers']
            );
        };

        $app['validator.mapping.class_metadata_factory'] = function ($app) {
            return new ClassMetadataFactory(new StaticMethodLoader());
        };

        $app['validator.validator_factory'] = function () use ($app) {
            $validators = isset($app['validator.validator_service_ids']) ? $app['validator.validator_service_ids'] : array();

            return new ConstraintValidatorFactory($app, $validators);
        };

        $app['validator.object_initializers'] = function ($app) {
            return array();
        };
    }

    public function boot(Application $app)
    {
    }
}
