<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

$app = new \Eccube\Application();
$entityManager = $app['orm.em'];

return ConsoleRunner::createHelperSet($entityManager);