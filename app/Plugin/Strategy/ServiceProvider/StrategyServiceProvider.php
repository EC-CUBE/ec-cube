<?php

namespace Plugin\Strategy\ServiceProvider;

use Eccube\Service\Calculator\CalculateStrategyCollection;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Plugin\Strategy\Strategy\EmptyStrategy;

class StrategyServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        // サンプルの空Strategyをコンテナに登録
        $app['eccube.calculate.strategy.empty'] = function () {
            return new EmptyStrategy();
        };

        // 空Strategyを追加.
        $app['eccube.calculate.strategies'] = $app->extend(
            'eccube.calculate.strategies',
            function (CalculateStrategyCollection $Collection, Container $app) {
                $Collection->add($app['eccube.calculate.strategy.empty']);

                return $Collection;
            }
        );
    }
}
