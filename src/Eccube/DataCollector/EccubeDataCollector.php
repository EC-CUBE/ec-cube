<?php

namespace Eccube\DataCollector;

use Eccube\Application;
use Eccube\Common\Constant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * EccubeDataCollector.
 *
 * @see https://github.com/bolt/bolt/blob/master/src/Profiler/BoltDataCollector.php
 */
class EccubeDataCollector extends DataCollector
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getName()
    {
        return 'eccube';
    }

    /**
     * Collect the date for the Toolbar item.
     *
     * @param Request    $request
     * @param Response   $response
     * @param \Exception $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = [
            'version'       => Constant::VERSION,
            'payoff'        => 'is the most popular e-commerce solution in Japan',
            'dashboardlink' => sprintf('<a href="%s">%s</a>', $this->app->url('admin_homepage'), 'admin'),
            'branding'      => null,
            'editlink'      => null,
            'edittitle'     => null,
        ];


        if (!empty($this->app['editlink'])) {
            $this->data['editlink'] = $this->app['editlink'];
            $this->data['edittitle'] = $this->app['edittitle'];
        }
    }

    /**
     * Getter for version.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->data['version'];
    }

    /**
     * Getter for branding.
     *
     * @return string
     */
    public function getBranding()
    {
        return $this->data['branding'];
    }

    /**
     * Getter for payoff.
     *
     * @return string
     */
    public function getPayoff()
    {
        return $this->data['payoff'];
    }

    /**
     * Getter for dashboardlink.
     *
     * @return string
     */
    public function getDashboardlink()
    {
        return $this->data['dashboardlink'];
    }

    /**
     * Getter for editlink.
     *
     * @return string
     */
    public function getEditlink()
    {
        return $this->data['editlink'];
    }

    /**
     * Getter for edittitle.
     *
     * @return string
     */
    public function getEdittitle()
    {
        return $this->data['edittitle'];
    }
}

