<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Auth Controller
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Authentication
 * @package    Auth
 * @author     Yavor Shahpasov <yavo@netsmart.com.cy>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Auth
 * @since      File available since Release 1.3.0
 */

/**
 * Controlls access to a group of php access 
 * and redirects to a predefined login page as 
 * needed
 *
 * In all pages
 * <code>
 * include_once('Auth.php');
 * include_once('Auth/Controller.php');
 * $_auth = new Auth('File', 'passwd');
 * $authController = new Auth_Controller($_auth, 'login.php', 'index.php');
 * $authController->start();
 * </code>
 *
 * In login.php
 * <code>
 * include_once('Auth.php');
 * include_once('Auth/Controller.php');
 * $_auth = new Auth('File', 'passwd');
 * $authController = new Auth_Controller($_auth, 'login.php', 'index.php');
 * $authController->start();
 * if( $authController->isAuthorised() ){
 *   $authController->redirectBack();
 * }  
 * </code>
 *
 * @category   Authentication
 * @author     Yavor Shahpasov <yavo@netsmart.com.cy>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8715 $
 * @link       http://pear.php.net/package/Auth
 * @since      Class available since Release 1.3.0
 */
class Auth_Controller
{

    // {{{ properties

    /** 
     * The Auth instance this controller is managing
     *
     * @var object Auth
     */
    var $auth = null;
    
    /**
     * The login URL
     * @var string
     * */
    var $login = null;
    
    /**
     * The default index page to use when the caller page is not set
     *
     * @var string 
     */
    var $default = null;
    
    /** 
     * If this is set to true after a succesfull login the 
     * Auth_Controller::redirectBack() is invoked automatically 
     *
     * @var boolean
     */
    var $autoRedirectBack = false;

    // }}}
    // {{{ Auth_Controller() [constructor]
    
    /**
     * Constructor
     *
     * @param Auth An auth instance
     * @param string The login page
     * @param string The default page to go to if return page is not set
     * @param array Some rules about which urls need to be sent to the login page
     * @return void
     * @todo Add a list of urls which need redirection
     */
    function Auth_Controller(&$auth_obj, $login='login.php', $default='index.php', $accessList=array())
    {
        $this->auth =& $auth_obj;
        $this->_loginPage = $login;
        $this->_defaultPage = $default;
        @session_start();
        if (!empty($_GET['return']) && $_GET['return'] && !strstr($_GET['return'], $this->_loginPage)) {
            $this->auth->setAuthData('returnUrl', $_GET['return']);
        }

        if(!empty($_GET['authstatus']) && $this->auth->status == '') {
            $this->auth->status = $_GET['authstatus'];
        }
    }

    // }}}
    // {{{ setAutoRedirectBack()
    
    /** 
     * Enables auto redirection when login is done
     * 
     * @param bool Sets the autoRedirectBack flag to this
     * @see Auth_Controller::autoRedirectBack
     * @return void
     */
    function setAutoRedirectBack($flag = true)
    {
        $this->autoRedirectBack = $flag;
    }

    // }}}
    // {{{ redirectBack()
    
    /**
     * Redirects Back to the calling page
     *
     * @return void
     */
    function redirectBack()
    {
        // If redirectback go there
        // else go to the default page
        
        $returnUrl = $this->auth->getAuthData('returnUrl');
        if(!$returnUrl) {
            $returnUrl = $this->_defaultPage;
        }
        
        // Add some entropy to the return to make it unique
        // avoind problems with cached pages and proxies
        if(strpos($returnUrl, '?') === false) {
            $returnUrl .= '?';
        }
        $returnUrl .= uniqid('');

        // Track the auth status
        if($this->auth->status != '') {
            $url .= '&authstatus='.$this->auth->status;
        }        
        header('Location:'.$returnUrl);
        print("You could not be redirected to <a href=\"$returnUrl\">$returnUrl</a>");
    }

    // }}}
    // {{{ redirectLogin()
    
    /**
      * Redirects to the login Page if not authorised
      * 
      * put return page on the query or in auth
      *
      * @return void
      */
    function redirectLogin()
    {
        // Go to the login Page
        
        // For Auth, put some check to avoid infinite redirects, this should at least exclude
        // the login page
        
        $url = $this->_loginPage;
        if(strpos($url, '?') === false) {
            $url .= '?';
        }

        if(!strstr($_SERVER['PHP_SELF'], $this->_loginPage)) {
            $url .= 'return='.urlencode($_SERVER['PHP_SELF']);
        }

        // Track the auth status
        if($this->auth->status != '') {
            $url .= '&authstatus='.$this->auth->status;
        }

        header('Location:'.$url);
        print("You could not be redirected to <a href=\"$url\">$url</a>");
    }

    // }}}
    // {{{ start()
    
    /**
      * Starts the Auth Procedure
      *
      * If the page requires login the user is redirected to the login page
      * otherwise the Auth::start is called to initialize Auth
      *
      * @return void
      * @todo Implement an access list which specifies which urls/pages need login and which do not
      */
    function start()
    {
        // Check the accessList here
        // ACL should be a list of urls with allow/deny
        // If allow set allowLogin to false
        // Some wild card matching should be implemented ?,*
        if(!strstr($_SERVER['PHP_SELF'], $this->_loginPage) && !$this->auth->checkAuth()) {
            $this->redirectLogin();
        } else {
            $this->auth->start();
            // Logged on and on login page
            if(strstr($_SERVER['PHP_SELF'], $this->_loginPage) && $this->auth->checkAuth()){
                $this->autoRedirectBack ? 
                    $this->redirectBack() :
                    null ;
            }
        }
        
        
    }

    // }}}
    // {{{ isAuthorised()
  
    /**
      * Checks is the user is logged on
      * @see Auth::checkAuth()
      */
    function isAuthorised()
    {
        return($this->auth->checkAuth());
    }

    // }}}
    // {{{ checkAuth()

    /**
      * Proxy call to auth
      * @see Auth::checkAuth()
      */
    function checkAuth()
    {
        return($this->auth->checkAuth());
    }

    // }}}
    // {{{ logout()

    /**
      * Proxy call to auth
      * @see Auth::logout()
      */
    function logout()
    {
        return($this->auth->logout());
    }

    // }}}
    // {{{ getUsername()

    /**
      * Proxy call to auth
      * @see Auth::getUsername()
      */
    function getUsername()
    {
        return($this->auth->getUsername());
    }

    // }}}
    // {{{ getStatus()

    /**
      * Proxy call to auth
      * @see Auth::getStatus()
      */
    function getStatus()
    {
        return($this->auth->getStatus());
    }

    // }}}

}

?>
