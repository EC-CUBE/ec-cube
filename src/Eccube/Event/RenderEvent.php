<?php

namespace Eccube\Event;

use Symfony\Component\EventDispatcher\Event;

class RenderEvent extends Event
{
    private $source;

    public function __construct($source)
    {
        $this->source = $source;
    }

    public function setSource($source)
    {
        $this->source = $source;
    }

    public function getSource()
    {
        return $this->source;
    }
}