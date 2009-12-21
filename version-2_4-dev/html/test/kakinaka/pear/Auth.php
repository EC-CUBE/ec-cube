<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * The main include file for Auth package
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
 * @author     Martin Jansen <mj@php.net>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Auth
 */

/**
 * Returned if session exceeds idle time
 */
define('AUTH_IDLED',                    -1);
/**
 * Returned if session has expired
 */
define('AUTH_EXPIRED',                  -2);
/** 
 * Returned if container is unable to authenticate user/password pair
 */
define('AUTH_WRONG_LOGIN',              -3);
/**
 * Returned if a container method is not supported.
 */
define('AUTH_METHOD_NOT_SUPPORTED',     -4);
/**
 * Returned if new Advanced security system detects a breach
 */
define('AUTH_SECURITY_BREACH',          -5);

/**
 * PEAR::Auth
 *
 * The PEAR::Auth class provides methods for creating an
 * authentication system using PHP.
 *
 * @category   Authentication
 * @package    Auth
 * @author     Martin Jansen <mj@php.net>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8718 $
 * @link       http://pear.php.net/package/Auth
 */
class Auth {

    // {{{ properties

    /**
     * Auth lifetime in seconds
     *
     * If this variable is set to 0, auth never expires
     *
     * @var  integer
     * @see  setExpire(), checkAuth()
     */
    var $expire = 0;

    /**
     * Has the auth session expired?
     *
     * @var   bool
     * @see   checkAuth()
     */
    var $expired = false;

    /**
     * Maximum idletime in seconds
     *
     * The difference to $expire is, that the idletime gets
     * refreshed each time checkAuth() is called. If this
     * variable is set to 0, idletime is never checked.
     *
     * @var integer
     * @see setIdle(), checkAuth()
     */
    var $idle = 0;

    /**
     * Is the maximum idletime over?
     *
     * @var boolean
     * @see checkAuth()
     */
    var $idled = false;

    /**
     * Storage object
     *
     * @var object
     * @see Auth(), validateLogin()
     */
    var $storage = '';

    /**
     * User-defined function that creates the login screen
     *
     * @var string
     */
    var $loginFunction = '';

    /**
     * Should the login form be displayed
     *
     * @var   bool
     * @see   setShowlogin()
     */
    var $showLogin = true;
    
    /**
      * Is Login Allowed from this page
      *
      * @var  bool
      * @see setAllowLogin
      */
    var $allowLogin = true;

    /**
     * Current authentication status
     *
     * @var string
     */
    var $status = '';

    /**
     * Username
     *
     * @var string
     */
    var $username = '';

    /**
     * Password
     *
     * @var string
     */
    var $password = '';

    /**
     * Login callback function name
     *
     * @var string
     * @see setLoginCallback()
     */
    var $loginCallback = '';

    /**
     * Failed Login callback function name
     *
     * @var string
     * @see setFailedLoginCallback()
     */
    var $loginFailedCallback = '';

    /**
     * Logout callback function name
     *
     * @var string
     * @see setLogoutCallback()
     */
    var $logoutCallback = '';

    /**
     * Auth session-array name
     *
     * @var string
     */
    var $_sessionName = '_authsession';

    /**
     * Package Version
     *
     * @var string
     */
    var $version = "@version@";

    /**
     * Flag to use advanced security
     * When set extra checks will be made to see if the 
     * user's IP or useragent have changed across requests. 
     * Turned off by default to preserve BC.
     *
     * @var boolean
     */     
    var $advancedsecurity = false;

    /**
     * Username key in POST array
     *
     * @var string
     */
    var $_postUsername = 'username';

    /**
     * Password key in POST array
     *
     * @var string
     */
    var $_postPassword = 'password';

    /**
     * Holds a reference to the session auth variable
     * @var array
     */
    var $session;

    /**
     * Holds a reference to the global server variable
     * @var array
     */
    var $server;

    /**
     * Holds a reference to the global post variable
     * @var array
     */
    var $post;

    /**
     * Holds a reference to the global cookie variable
     * @var array
     */
    var $cookie;

    /**
     * A hash to hold various superglobals as reference
     * @var array
     */
    var $authdata;
    
    /**
      * How many times has checkAuth been called
      * var int
      */
    var $authChecks = 0;

    // }}}
    // {{{ Auth() [constructor]

    /**
     * Constructor
     *
     * Set up the storage driver.
     *
     * @param string    Type of the storage driver
     * @param mixed     Additional options for the storage driver
     *                  (example: if you are using DB as the storage
     *                   driver, you have to pass the dsn string here)
     *
     * @param string    Name of the function that creates the login form
     * @param boolean   Should the login form be displayed if neccessary?
     * @return void
     */
    function Auth($storageDriver, $options = '', $loginFunction = '', $showLogin = true)
    {
        $this->applyAuthOptions($options);

        // Start the session suppress error if already started
        if(!session_id()){
            @session_start();
            if(!session_id()) {
                // Throw error
                include_once 'PEAR.php';
                PEAR::throwError('Session could not be started by Auth, '
                        .'possibly headers are already sent, try putting '
                        .'ob_start in the beginning of your script');
            }
        }

        // Make Sure Auth session variable is there
        if(!isset($_SESSION[$this->_sessionName])) {
            $_SESSION[$this->_sessionName] = array();
        }

        // Assign Some globals to internal references, this will replace _importGlobalVariable
        $this->session =& $_SESSION[$this->_sessionName];
        $this->server =& $_SERVER;
        $this->post =& $_POST;
        $this->cookie =& $_COOKIE;

        if ($loginFunction != '' && is_callable($loginFunction)) {
            $this->loginFunction = $loginFunction;
        }

        if (is_bool($showLogin)) {
            $this->showLogin = $showLogin;
        }

        if (is_object($storageDriver)) {
            $this->storage =& $storageDriver;
            // Pass a reference to auth to the container, ugly but works
            // this is used by the DB container to use method setAuthData not staticaly.
            $this->storage->_auth_obj =& $this;
        } else {
            // $this->storage = $this->_factory($storageDriver, $options);
            // 
            $this->storage_driver = $storageDriver;
            $this->storage_options =& $options;
        }
    }

    // }}}
    // {{{ applyAuthOptions()

    /**
      * Set the Auth options 
      *
      * Some options which are Auth specific will be applied
      * the rest will be left for usage by the container
      * 
      * @param array    An array of Auth options
      * @return array   The options which were not applied
      * @access private
      */
    function &applyAuthOptions(&$options)
    {
        if(is_array($options)){
            if (!empty($options['sessionName'])) {
                $this->_sessionName = $options['sessionName'];
                unset($options['sessionName']);
            }
            if (isset($options['allowLogin'])) {
                $this->allowLogin = $options['allowLogin'];
                unset($options['allowLogin']);
            }
            if (!empty($options['postUsername'])) {
                $this->_postUsername = $options['postUsername'];
                unset($options['postUsername']);
            }
            if (!empty($options['postPassword'])) {
                $this->_postPassword = $options['postPassword'];
                unset($options['postPassword']);
            }
            if (isset($options['advancedsecurity'])) {
                $this->advancedsecurity = $options['advancedsecurity'];
                unset($options['advancedsecurity']);
            }
        }
        return($options);
    }

    // }}}
    // {{{ _loadStorage()
    
    /**
      * Load Storage Driver if not already loaded
      *
      * Suspend storage instantiation to make Auth lighter to use 
      * for calls which do not require login
      *
      * @return bool    True if the conainer is loaded, false if the container
      *                 is already loaded
      * @access private
      */
    function _loadStorage()
    {
        if(!is_object($this->storage)) {
            $this->storage =& $this->_factory($this->storage_driver, 
                    $this->storage_options);
            $this->storage->_auth_obj =& $this;
            return(true);
        }
        return(false);
    }

    // }}}
    // {{{ _factory()

    /**
     * Return a storage driver based on $driver and $options
     *
     * @static
     * @param  string $driver  Type of storage class to return
     * @param  string $options Optional parameters for the storage class
     * @return object Object   Storage object
     * @access private
     */
    function &_factory($driver, $options = '')
    {
        $storage_class = 'Auth_Container_' . $driver;
        include_once 'Auth/Container/' . $driver . '.php';
        $obj =& new $storage_class($options);
        return $obj;
    }

    // }}}
    // {{{ assignData()

    /**
     * Assign data from login form to internal values
     *
     * This function takes the values for username and password
     * from $HTTP_POST_VARS/$_POST and assigns them to internal variables.
     * If you wish to use another source apart from $HTTP_POST_VARS/$_POST,
     * you have to derive this function.
     *
     * @global $HTTP_POST_VARS, $_POST
     * @see    Auth
     * @return void
     * @access private
     */
    function assignData()
    {
        if (   isset($this->post[$this->_postUsername]) 
            && $this->post[$this->_postUsername] != '') {
            $this->username = (get_magic_quotes_gpc() == 1 
                    ? stripslashes($this->post[$this->_postUsername]) 
                    : $this->post[$this->_postUsername]);
        }
        if (   isset($this->post[$this->_postPassword]) 
            && $this->post[$this->_postPassword] != '') {
            $this->password = (get_magic_quotes_gpc() == 1 
                    ? stripslashes($this->post[$this->_postPassword]) 
                    : $this->post[$this->_postPassword] );
        }
    }

    // }}}
    // {{{ start()

    /**
     * Start new auth session
     *
     * @return void
     * @access public
     */
    function start()
    {
        $this->assignData();
        if (!$this->checkAuth() && $this->allowLogin) {
            $this->login();
        }
    }

    // }}}
    // {{{ login()

    /**
     * Login function
     *
     * @return void
     * @access private
     */
    function login()
    {
        $login_ok = false;
        $this->_loadStorage();
        
        // Check if using challenge response
        (isset($this->post['authsecret']) && $this->post['authsecret'] == 1) 
            ? $usingChap = true 
            : $usingChap = false;

        
        // When the user has already entered a username, we have to validate it.
        if (!empty($this->username)) {
            if (true === $this->storage->fetchData($this->username, $this->password, $usingChap)) {
                $this->session['challengekey'] = md5($this->username.$this->password);
                $login_ok = true;
            }
        }

        if (!empty($this->username) && $login_ok) {
            $this->setAuth($this->username);
            if (is_callable($this->loginCallback)) {
                call_user_func_array($this->loginCallback, array($this->username, &$this));
            }
        }

        // If the login failed or the user entered no username, 
        // output the login screen again.
        if (!empty($this->username) && !$login_ok) {
            $this->status = AUTH_WRONG_LOGIN;
            if (is_callable($this->loginFailedCallback)) {
                call_user_func_array($this->loginFailedCallback, array($this->username, &$this));
            }
        }

        if ((empty($this->username) || !$login_ok) && $this->showLogin) {
            if (is_callable($this->loginFunction)) {
                call_user_func_array($this->loginFunction, array($this->username, $this->status, &$this));
            } else {
                // BC fix Auth used to use drawLogin for this
                // call is sub classes implement this
                if (is_callable(array($this, 'drawLogin'))) {
                    return $this->drawLogin($this->username, $this);
                }

                // New Login form
                include_once 'Auth/Frontend/Html.php';
                return Auth_Frontend_Html::render($this, $this->username);
            }
        } else {
            return;
        }
    }

    // }}}
    // {{{ setExpire()

    /**
     * Set the maximum expire time
     *
     * @param  integer time in seconds
     * @param  bool    add time to current expire time or not
     * @return void
     * @access public
     */
    function setExpire($time, $add = false)
    {
        $add ? $this->expire += $time : $this->expire = $time;
    }

    // }}}
    // {{{ setIdle()

    /**
     * Set the maximum idle time
     *
     * @param  integer time in seconds
     * @param  bool    add time to current maximum idle time or not
     * @return void
     * @access public
     */
    function setIdle($time, $add = false)
    {
        $add ? $this->idle += $time : $this->idle = $time;
    }

    // }}}
    // {{{ setSessionName()

    /**
     * Set name of the session to a customized value.
     *
     * If you are using multiple instances of PEAR::Auth
     * on the same domain, you can change the name of
     * session per application via this function.
     * This will chnage the name of the session variable 
     * auth uses to store it's data in the session
     *
     * @param  string New name for the session
     * @return void
     * @access public
     */
    function setSessionName($name = 'session')
    {
        $this->_sessionName = '_auth_'.$name;
        $this->session =& $_SESSION[$this->_sessionName];
    }

    // }}}
    // {{{ setShowLogin()

    /**
     * Should the login form be displayed if neccessary?
     *
     * @param  bool    show login form or not
     * @return void
     * @access public
     */
    function setShowLogin($showLogin = true)
    {
        $this->showLogin = $showLogin;
    }

    // }}}
    // {{{ setAllowLogin()

    /**
     * Should the login form be displayed if neccessary?
     *
     * @param  bool    show login form or not
     * @return void
     * @access public
     */
    function setAllowLogin($allowLogin = true)
    {
        $this->allowLogin = $allowLogin;
    }

    // }}}
    // {{{ setLoginCallback()
    
    /**
     * Register a callback function to be called on user login.
     * The function will receive two parameters, the username and a reference to the auth object.
     *
     * @param  string  callback function name
     * @return void
     * @see    setLogoutCallback()
     * @access public
     */
    function setLoginCallback($loginCallback)
    {
        $this->loginCallback = $loginCallback;
    }

    // }}}
    // {{{ setFailedLoginCallback()

    /**
     * Register a callback function to be called on failed user login.
     * The function will receive a single parameter, the username and a reference to the auth object.
     *
     * @param  string  callback function name
     * @return void
     * @access public
     */
    function setFailedLoginCallback($loginFailedCallback)
    {
        $this->loginFailedCallback = $loginFailedCallback;
    }

    // }}}
    // {{{ setLogoutCallback()

    /**
     * Register a callback function to be called on user logout.
     * The function will receive three parameters, the username and a reference to the auth object.
     *
     * @param  string  callback function name
     * @return void
     * @see    setLoginCallback()
     * @access public
     */
    function setLogoutCallback($logoutCallback)
    {
        $this->logoutCallback = $logoutCallback;
    }

    // }}}
    // {{{ setAuthData()

    /**
     * Register additional information that is to be stored
     * in the session.
     *
     * @param  string  Name of the data field
     * @param  mixed   Value of the data field
     * @param  boolean Should existing data be overwritten? (default
     *                 is true)
     * @return void
     * @access public
     */
    function setAuthData($name, $value, $overwrite = true)
    {
        if (!empty($this->session['data'][$name]) && $overwrite == false) {
            return;
        }
        $this->session['data'][$name] = $value;
    }

    // }}}
    // {{{ getAuthData()

    /**
     * Get additional information that is stored in the session.
     *
     * If no value for the first parameter is passed, the method will
     * return all data that is currently stored.
     *
     * @param  string Name of the data field
     * @return mixed  Value of the data field.
     * @access public
     */
    function getAuthData($name = null)
    {
        if (!isset($this->session['data'])) {
            return null;
        }    
        if(!isset($name)) {
            return $this->session['data'];
        }
        if (isset($name) && isset($this->session['data'][$name])) {
            return $this->session['data'][$name];
        }
        return null;        
    }

    // }}}
    // {{{ setAuth()

    /**
     * Register variable in a session telling that the user
     * has logged in successfully
     *
     * @param  string Username
     * @return void
     * @access public
     */
    function setAuth($username)
    {
    
        // #2021 - Change the session id to avoid session fixation attacks php 4.3.3 > 
        session_regenerate_id(true);

        if (!isset($this->session) || !is_array($this->session)) {
            $this->session = array();
        }

        if (!isset($this->session['data'])) {
            $this->session['data'] = array();
        }

        $this->session['sessionip'] = isset($this->server['REMOTE_ADDR']) 
            ? $this->server['REMOTE_ADDR'] 
            : '';
        $this->session['sessionuseragent'] = isset($this->server['HTTP_USER_AGENT']) 
            ? $this->server['HTTP_USER_AGENT'] 
            : '';

        // This should be set by the container to something more safe
        // Like md5(passwd.microtime)
        if(empty($this->session['challengekey'])) {
            $this->session['challengekey'] = md5($username.microtime());
        }

        $this->session['challengecookie'] = md5($this->session['challengekey'].microtime());
        setcookie('authchallenge', $this->session['challengecookie']);

        $this->session['registered'] = true;
        $this->session['username']   = $username;
        $this->session['timestamp']  = time();
        $this->session['idle']       = time();
    }

    // }}}
    // {{{ setAdvancedSecurity()
    
    /**
      * Enables advanced security checks
      *
      * Currently only ip change and useragent change 
      * are detected
      * @todo Add challenge cookies - Create a cookie which changes every time 
      *       and contains some challenge key which the server can verify with
      *       a session var cookie might need to be crypted (user pass)
      * @param bool Enable or disable
      * @return void
      * @access public
      */
    function setAdvancedSecurity($flag=true)
    {
        $this->advancedsecurity = $flag;
    }

    // }}}
    // {{{ checkAuth()

    /**
     * Checks if there is a session with valid auth information.
     *
     * @access public
     * @return boolean  Whether or not the user is authenticated.
     */
    function checkAuth()
    {
        $this->authChecks++;
        if (isset($this->session)) {
            // Check if authentication session is expired
            if (   $this->expire > 0
                && isset($this->session['timestamp'])
                && ($this->session['timestamp'] + $this->expire) < time()) {
                $this->expired = true;
                $this->status = AUTH_EXPIRED;
                $this->logout();
                return false;
            }

            // Check if maximum idle time is reached
            if (   $this->idle > 0
                && isset($this->session['idle']) 
                && ($this->session['idle'] + $this->idle) < time()) {
                $this->idled = true;
                $this->status = AUTH_IDLED;
                $this->logout();
                return false;
            }

            if (   isset($this->session['registered']) 
                && isset($this->session['username']) 
                && $this->session['registered'] == true 
                && $this->session['username'] != '') {
                Auth::updateIdle();

                if ($this->advancedsecurity) {
                    
                    // Only Generate the challenge once
                    if($this->authChecks == 1) {
                        $this->session['challengecookieold'] = $this->session['challengecookie'];
                        $this->session['challengecookie'] = md5($this->session['challengekey'].microtime());
                        setcookie('authchallenge', $this->session['challengecookie']);
                    }
                    
                    // Check for ip change
                    if (   isset($this->server['REMOTE_ADDR']) 
                        && $this->session['sessionip'] != $this->server['REMOTE_ADDR']) {
                        // Check if the IP of the user has changed, if so we 
                        // assume a man in the middle attack and log him out
                        $this->expired = true;
                        $this->status = AUTH_SECURITY_BREACH;
                        $this->logout();
                        return false;
                    }
                    
                    // Check for useragent change
                    if (   isset($this->server['HTTP_USER_AGENT']) 
                        && $this->session['sessionuseragent'] != $this->server['HTTP_USER_AGENT']) {
                        // Check if the User-Agent of the user has changed, if 
                        // so we assume a man in the middle attack and log him out
                        $this->expired = true;
                        $this->status = AUTH_SECURITY_BREACH;
                        $this->logout();
                        return false;
                    }
    
                    // Check challenge cookie here, if challengecookieold is not set 
                    // this is the first time and check is skipped
                    // TODO when user open two pages similtaneuly (open in new window,open 
                    // in tab) auth breach is caused find out a way around that if possible
                    if (   isset($this->session['challengecookieold']) 
                        && $this->session['challengecookieold'] != $this->cookie['authchallenge']) {
                        $this->expired = true;
                        $this->status = AUTH_SECURITY_BREACH;
                        $this->logout();
                        $this->login();
                        return false;
                    }
                }

                return true;
            }
        }
        return false;
    }

    // }}}
    // {{{ staticCheckAuth() [static]

    /**
     * Statically checks if there is a session with valid auth information.
     *
     * @access public
     * @see checkAuth
     * @return boolean  Whether or not the user is authenticated.
     * @static
     */
    function staticCheckAuth($options = null)
    {
        static $staticAuth;
        if(!isset($staticAuth)) {
            $staticAuth = new Auth('null', $options);
        }
        return $staticAuth->checkAuth();
    }

    // }}}
    // {{{ getAuth()

    /**
     * Has the user been authenticated?
     *
     * @access public
     * @return bool  True if the user is logged in, otherwise false.
     */
    function getAuth()
    {
        return $this->checkAuth();
    }

    // }}}
    // {{{ logout()

    /**
     * Logout function
     *
     * This function clears any auth tokens in the currently
     * active session and executes the logout callback function,
     * if any
     *
     * @access public
     * @return void
     */
    function logout()
    {
        if (is_callable($this->logoutCallback)) {
            call_user_func_array($this->logoutCallback, array($this->session['username'], &$this));
        }

        $this->username = '';
        $this->password = '';
        
        $this->session = null;
    }

    // }}}
    // {{{ updateIdle()

    /**
     * Update the idletime
     *
     * @access private
     * @return void
     */
    function updateIdle()
    {
        $this->session['idle'] = time();
    }

    // }}}
    // {{{ getUsername()

    /**
     * Get the username
     *
     * @return string
     * @access public
     */
    function getUsername()
    {
        if (isset($this->session['username'])) {
            return($this->session['username']);
        }
        return('');
    }

    // }}}
    // {{{ getStatus()

    /**
     * Get the current status
     *
     * @return string
     * @access public
     */
    function getStatus()
    {
        return $this->status;
    }

    // }}}
    // {{{ getPostUsernameField()
    
    /**
     * Gets the post varible used for the username
     * 
     * @return string
     * @access public
     */
    function getPostUsernameField()
    {
        return($this->_postUsername);
    }

    // }}}
    // {{{ getPostPasswordField()

    /**
     * Gets the post varible used for the username
     * 
     * @return string
     * @access public
     */
    function getPostPasswordField()
    {
        return($this->_postPassword);
    }

    // }}}
    // {{{ sessionValidThru()

    /**
     * Returns the time up to the session is valid
     *
     * @access public
     * @return integer
     */
    function sessionValidThru()
    {
        if (!isset($this->session['idle'])) {
            return 0;
        }
        if ($this->idle == 0) {
            return 0;
        }
        return ($this->session['idle'] + $this->idle);
    }

    // }}}
    // {{{ listUsers()

    /**
     * List all users that are currently available in the storage
     * container
     *
     * @access public
     * @return array
     */
    function listUsers()
    {
        $this->_loadStorage();
        return $this->storage->listUsers();
    }

    // }}}
    // {{{ addUser()

    /**
     * Add user to the storage container
     *
     * @access public
     * @param  string Username
     * @param  string Password
     * @param  mixed  Additional parameters
     * @return mixed  True on success, PEAR error object on error
     *                and AUTH_METHOD_NOT_SUPPORTED otherwise.
     */
    function addUser($username, $password, $additional = '')
    {
        $this->_loadStorage();
        return $this->storage->addUser($username, $password, $additional);
    }

    // }}}
    // {{{ removeUser()

    /**
     * Remove user from the storage container
     *
     * @access public
     * @param  string Username
     * @return mixed  True on success, PEAR error object on error
     *                and AUTH_METHOD_NOT_SUPPORTED otherwise.
     */
    function removeUser($username)
    {
        $this->_loadStorage();
        return $this->storage->removeUser($username);
    }

    // }}}
    // {{{ changePassword()

    /**
     * Change password for user in the storage container
     *
     * @access public
     * @param string Username
     * @param string The new password 
     * @return mixed True on success, PEAR error object on error
     *               and AUTH_METHOD_NOT_SUPPORTED otherwise.
     */
    function changePassword($username, $password)
    {
        $this->_loadStorage();
        return $this->storage->changePassword($username, $password);
    }

    // }}}

}
?>
