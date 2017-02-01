<?php

/*
 * This file is part of the [code]
 *
 * Copyright (C) [year] [author]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\[code];

use Eccube\Application;

class [code]Event
{

    /** @var  \Eccube\Application $app */
    private $app;

    /**
     * [code]Event constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

[hookpoint_function]}
