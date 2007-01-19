<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: KUBO Atsuhiro <iteman@users.sourceforge.net>                            |
// +----------------------------------------------------------------------+
//
// $Id: Request.php,v 1.5 2006/11/07 09:25:14 kuboa Exp $
//

/**
 * Utility class that constructs appropriate class instance for miscellaneous
 * HTTP header containers
 *
 * @package  Net_UserAgent_Mobile
 * @category Networking
 * @author   KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @access   public
 * @version  $Revision: 1.5 $
 */
class Net_UserAgent_Mobile_Request
{

    /**#@+
     * @access public
     * @static
     */

    // }}}
    // {{{ factory()

    /**
     * create a new Net_UserAgent_Mobile_Request_XXX instance
     *
     * parses HTTP headers and constructs appropriate class instance.
     * If no argument is supplied, $_SERVER is used.
     *
     * @param mixed $stuff User-Agent string or object that works with
     *     HTTP_Request (not implemented)
     * @return mixed a newly created Net_UserAgent_Request object
     * @global array $_SERVER
     */
    function &factory($stuff = null)
    {
        if ($stuff === null) {
            $request = &new Net_UserAgent_Mobile_Request_Env($_SERVER);
        } else {
            $request =
                &new Net_UserAgent_Mobile_Request_Env(array(
                                                            'HTTP_USER_AGENT' => $stuff)
                                                      );
        }
        return $request;
    }

    /**#@-*/
}

/**
 * provides easy way to access environment variables
 *
 * @package  Net_UserAgent_Mobile
 * @category Networking
 * @author   KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @access   public
 * @version  $Revision: 1.5 $
 */
class Net_UserAgent_Mobile_Request_Env
{

    // {{{ properties

    /**#@+
     * @access private
     */

    /**
     * array of environment variables defined by Web Server
     * @var array
     */
    var $_env;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ constructor

    /**
     * constructor
     *
     * @param array $env
     */
    function Net_UserAgent_Mobile_Request_Env($env)
    {
        $this->_env = $env;
    }

    /**
     * returns a specified HTTP Header
     *
     * @param string $header
     * @return string
     */
    function get($header)
    {
        $header = strtr($header, '-', '_');
        return @$this->_env[ 'HTTP_' . strtoupper($header) ];
    }

    /**#@-*/
}

/*
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
?>
