<?php
namespace Eccube2\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="/prefix")
 */
class TestController
{

    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="test/{var}"),
     *      @SLX\Assert(variable="var", regex="\d+"),
     *      @SLX\Convert(variable="var", callback="\Eccube2\Controller\TestController::converter")
     * )
     */
    public function testMethod($var)
    {

        return new Response("test Method: $var");
    }

    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="test2")
     * )
     */
    public function testMethod2()
    {
        return new Response("test Method2");
    }

    public static function converter($var)
    {
        return $var;
    }
}
