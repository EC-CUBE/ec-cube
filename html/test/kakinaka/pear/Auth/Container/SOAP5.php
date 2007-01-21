<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Storage driver for use against a SOAP service using PHP5 SoapClient
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Authentication
 * @package    Auth
 * @author     Based upon Auth_Container_SOAP by Bruno Pedro <bpedro@co.sapo.pt>
 * @author     Marcel Oelke <puRe@rednoize.com>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id: SOAP5.php 8713 2006-12-01 05:08:34Z kakinaka $
 * @since      File available since Release 1.4.0
 */

/**
 * Include Auth_Container base class
 */
require_once "Auth/Container.php";
/**
 * Include PEAR package for error handling
 */
require_once "PEAR.php";

/**
 * Storage driver for fetching login data from SOAP using the PHP5 Builtin SOAP
 * functions. This is a modification of the SOAP Storage driver from Bruno Pedro
 * thats using the PEAR SOAP Package.
 *
 * This class takes one parameter (options), where
 * you specify the following fields: 
 *  * location and uri, or wsdl file
 *  * method to call on the SOAP service
 *  * usernamefield, the name of the parameter where the username is supplied
 *  * passwordfield, the name of the parameter where the password is supplied
 *  * matchpassword, whether to look for the password in the response from
 *                   the function call or assume that no errors means user
 *                   authenticated.
 *
 * See http://www.php.net/manual/en/ref.soap.php for further details
 * on options for the PHP5 SoapClient which are passed through.
 *
 * Example usage without WSDL:
 *
 * <?php
 *
 * $options = array (
 *       'wsdl'           => NULL,
 *       'location'       => 'http://your.soap.service/endpoint',
 *       'uri'            => 'urn:/Your/Namespace',
 *       'method'         => 'checkAuth',        
 *       'usernamefield'  => 'username',
 *       'passwordfield'  => 'password',
 *       'matchpasswords' => false,          
 *       '_features' => array (
 *           'extra_parameter'    => 'example_value',
 *           'another_parameter'  => 'foobar'
 *       )
 *   );
 *
 * $auth = new Auth('SOAP5', $options);
 * $auth->start();
 *
 * ?>
 *
 * Example usage with WSDL:
 *
 * <?php
 *
 * $options = array (
 *       'wsdl'           => 'http://your.soap.service/wsdl',
 *       'method'         => 'checkAuth',        
 *       'usernamefield'  => 'username',
 *       'passwordfield'  => 'password',
 *       'matchpasswords' => false,          
 *       '_features' => array (
 *           'extra_parameter'    => 'example_value',
 *           'another_parameter'  => 'foobar'
 *       )
 *   );
 *
 * $auth = new Auth('SOAP5', $options);
 * $auth->start();
 *
 * ?>
 *
 * @category   Authentication
 * @package    Auth
 * @author     Based upon Auth_Container_SOAP by Bruno Pedro <bpedro@co.sapo.pt>
 * @author     Marcel Oelke <puRe@rednoize.com>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8713 $
 * @since      Class available since Release 1.4.0
 */
class Auth_Container_SOAP5 extends Auth_Container
{

    // {{{ properties

    /**
     * Required options for the class
     * @var array
     * @access private
     */
    var $_requiredOptions = array(
            'location', 
            'uri',
            'method',
            'usernamefield',
            'passwordfield',
            'wsdl',
            );

    /**
     * Options for the class
     * @var array
     * @access private
     */
    var $_options = array();

    /**
     * Optional SOAP features
     * @var array
     * @access private
     */
    var $_features = array();

    /**
     * The SOAP response
     * @var array
     * @access public
     */
    var $soapResponse = array();
     
    // }}}
    // {{{ Auth_Container_SOAP5()

    /**
     * Constructor of the container class
     *
     * @param  $options, associative array with endpoint, namespace, method,
     *                   usernamefield, passwordfield and optional features
     */
    function Auth_Container_SOAP5($options)
    {
        $this->_setDefaults();

        foreach ($options as $name => $value) {
            $this->_options[$name] = $value;
        }

        if (!empty($this->_options['_features'])) {
            $this->_features = $this->_options['_features'];
            unset($this->_options['_features']);
        }        
    }

    // }}}
    // {{{ fetchData()

    /**
     * Fetch data from SOAP service
     *
     * Requests the SOAP service for the given username/password
     * combination.
     *
     * @param  string Username
     * @param  string Password
     * @return mixed Returns the SOAP response or false if something went wrong
     */
    function fetchData($username, $password)
    {        
        $result = $this->_validateOptions();
        if (PEAR::isError($result))
            return $result;

        // create a SOAP client
        $soapClient = new SoapClient($this->_options["wsdl"], $this->_options);
        
        $params = array();        
        // first, assign the optional features
        foreach ($this->_features as $fieldName => $fieldValue) {
            $params[$fieldName] = $fieldValue;
        }
        // assign username and password ...
        $params[$this->_options['usernamefield']] = $username;
        $params[$this->_options['passwordfield']] = $password;                
                
        try {
            $this->soapResponse = $soapClient->__soapCall($this->_options['method'], $params);
                        
            if ($this->_options['matchpasswords']) {
                // check if passwords match
                if ($password == $this->soapResponse[$this->_options['passwordfield']]) {
                    return true;
                } else {
                    return false;
                }
            } else {                
                return true;
            }
        } catch (SoapFault $e) {
            return PEAR::raiseError("Error retrieving authentication data. Received SOAP Fault: ".$e->faultstring, $e->faultcode);
        }        
    }

    // }}}
    // {{{ _validateOptions()
    
    /**
     * Validate that the options passed to the container class are enough for us to proceed
     *
     * @access private
     * @param  array
     */
    function _validateOptions($array)
    {
        if (   (   is_null($this->options['wsdl'])
                && is_null($this->options['location'])
                && is_null($this->options['uri']))
            || (   is_null($this->options['wsdl'])
                && (   is_null($this->options['location'])
                    || is_null($this->options['uri'])))) {
            return PEAR::raiseError('Either a WSDL file or a location/uri pair must be specified.');
        }
        if (is_null($this->options['method'])) {
            return PEAR::raiseError('A method to call on the soap service must be specified.');
        }
        return true;
    }
    
    // }}}
    // {{{ _setDefaults()

    /**
     * Set some default options
     *
     * @access private
     * @return void
     */
    function _setDefaults()
    {
        $this->options['wsdl']           = null;
        $this->options['location']       = null;
        $this->options['uri']            = null;
        $this->options['method']         = null;
        $this->options['usernamefield']  = 'username';
        $this->options['passwordfield']  = 'password';
        $this->options['matchpasswords'] = true;
    }

    // }}}
        
}
?>
