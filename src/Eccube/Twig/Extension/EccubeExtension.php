<?php

namespace Eccube\Twig\Extension;

use Silex\Application;

class EccubeExtension extends \Twig_Extension
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'image_info' => new \Twig_Function_Method($this, 'getImageInfo'),
        );
    }

    /**
     * Returns a list of filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'no_image_main_list' => new \Twig_Filter_Method($this, 'getNoImageMainList'),
            'no_image_main' => new \Twig_Filter_Method($this, 'getNoImageMain'),
        );
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getName()
    {
        return 'eccube';
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getImageInfo($path)
    {
        $image_info = array(
            'path'      => null,
            'width'     => null,
            'height'    => null,
            'type'      => null,
            'tag'       => null,
        );

        // TODO FIX PATH
        $realpath = realpath(__DIR__ . '/../../../../html' . $path);
        if (!$realpath) {
            return $image_info;
        }

        $info = getimagesize($realpath);
        if ($info) {
            $image_info = array(
                'path'      => $path,
                'width'     => $info[0],
                'height'    => $info[1],
                'type'      => $info[2],
                'tag'       => $info[3],
            );
        }

        return $image_info;
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getNoImageMainList($image)
    {
        return empty($image) ? 'noimage_main_list.jpg' : $image;
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getNoImageMain($image)
    {
        return empty($image) ? 'noimage_main.jpg' : $image;
    }
}
