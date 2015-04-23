<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

$app = new Eccube\Application(array(
    'env' => 'cli',
));
$entityManager = $app['orm.em'];

return ConsoleRunner::createHelperSet($entityManager);