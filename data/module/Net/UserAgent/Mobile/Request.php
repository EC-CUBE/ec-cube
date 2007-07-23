<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Networking
 * @package    Net_UserAgent_Mobile
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2003-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id: Request.php,v 1.6 2007/02/20 15:18:26 kuboa Exp $
 * @since      File available since Release 0.1
 */

// {{{ Net_UserAgent_Mobile_Request

/**
 * Utility class that constructs appropriate class instance for miscellaneous
 * HTTP header containers
 *
 * @category   Networking
 * @package    Net_UserAgent_Mobile
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2003-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 0.30.0
 * @since      Class available since Release 0.1
 */
class Net_UserAgent_Mobile_Request
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

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

    /**#@+
     * @access private
     */

    /**#@-*/

    // }}}
}

// }}}
// {{{ Net_UserAgent_Mobile_Request_Env

/**
 * provides easy way to access environment variables
 *
 * @category   Networking
 * @package    Net_UserAgent_Mobile
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2003-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 0.30.0
 * @since      Class available since Release 0.1
 */
class Net_UserAgent_Mobile_Request_Env
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

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

    /**#@+
     * @access private
     */

    /**#@-*/

    // }}}
}

// }}}

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
