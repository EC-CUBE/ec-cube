<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Interactions;

use Facebook\WebDriver\Interactions\Internal\WebDriverButtonReleaseAction;
use Facebook\WebDriver\Interactions\Internal\WebDriverClickAndHoldAction;
use Facebook\WebDriver\Interactions\Internal\WebDriverMoveToOffsetAction;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriver;

class DragAndDropBy extends WebDriverActions
{
    /**
     * @param \Facebook\WebDriver\Remote\RemoteWebElement $source
     */
    public function __construct(WebDriver $driver, $source, $x_offset, $y_offset)
    {
        parent::__construct($driver);
        $this->action->addAction(
            new WebDriverClickAndHoldAction($this->mouse, $source)
        );
        $this->action->addAction(
            new WaitAction(1)
        );
        $this->action->addAction(
            new WebDriverMoveToOffsetAction($this->mouse, null, $x_offset, $y_offset)
        );
        $this->action->addAction(
            new WaitAction(1)
        );
        $this->action->addAction(
            new WebDriverButtonReleaseAction($this->mouse, null)
        );
        $this->action->addAction(
            new WaitAction(1)
        );
    }
}
