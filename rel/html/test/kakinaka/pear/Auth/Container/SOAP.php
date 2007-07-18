<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Storage driver for use against a SOAP service
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
 * @author     Bruno Pedro <bpedro@co.sapo.pt> 
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id: SOAP.php 8713 2006-12-01 05:08:34Z kakinaka $
 * @link       http://pear.php.net/package/Auth
 * @since      File available since Release 1.2.0
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
 * Include PEAR SOAP_Client
 */
require_once 'SOAP/Client.php';

/**
 * Storage driver for fetching login data from SOAP
 *
 * This class takes one parameter (options), where
 * you specify the following fields: endpoint, namespace,
 * method, encoding, usernamefield and passwordfield.
 *
 * You can use specify features of your SOAP service
 * by providing its parameters in an associative manner by
 * using the '_features' array through the options parameter.
 *
 * The 'matchpassword' option should be set to false if your
 * webservice doesn't return (username,password) pairs, but
 * instead returns error when the login is invalid.
 *
 * Example usage:
 *
 * <?php
 *
 * ...
 *
 * $options = array (
 *             'endpoint' => 'http://your.soap.service/endpoint',
 *             'namespace' => 'urn:/Your/Namespace',
 *             'method' => 'get',
 *             'encoding' => 'UTF-8',
 *             'usernamefield' => 'login',
 *             'passwordfield' => 'password',
 *             'matchpasswords' => false,
 *             '_features' => array (
 *                             'example_feature' => 'example_value',
 *                             'another_example'  => ''
 *                             )
 *             );
 * $auth = new Auth('SOAP', $options, 'loginFunction');
 * $auth->start();
 *
 * ...
 *
 * ?>
 *
 * @category   Authentication
 * @package    Auth
 * @author     Bruno Pedro <bpedro@co.sapo.pt>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8713 $
 * @link       http://pear.php.net/package/Auth
 * @since      Class available since Release 1.2.0
 */
class Auth_Container_SOAP extends Auth_Container
{

    // {{{ properties

    /**
     * Required options for the class
     * @var array
     * @access private
     */
    var $_requiredOptions = array(
            'endpoint',
            'namespace',
            'method',
            'encoding',
            'usernamefield',
            'passwordfield',
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

    /**
     * The SOAP client
     * @var mixed
     * @access public
     */
     var $soapClient = null;

    // }}}
    // {{{ Auth_Container_SOAP() [constructor]

    /**
     * Constructor of the container class
     *
     * @param  $options, associative array with endpoint, namespace, method,
     *                   usernamefield, passwordfield and optional features
     */
    function Auth_Container_SOAP($options)
    {
        $this->_options = $options;
        if (!isset($this->_options['matchpasswords'])) {
            $this->_options['matchpasswords'] = true;
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
        // check if all required options are set
        if (array_intersect($this->_requiredOptions, array_keys($this->_options)) != $this->_requiredOptions) {
            return false;
        } else {
            // create a SOAP client and set encoding
            $this->soapClient = new SOAP_Client($this->_options['endpoint']);
            $this->soapClient->setEncoding($this->_options['encoding']);
        }

        // set the trace option if requested
        if (isset($this->_options['trace'])) {
            $this->soapClient->__options['trace'] = true;
        }

        // set the timeout option if requested
        if (isset($this->_options['timeout'])) {
            $this->soapClient->__options['timeout'] = $this->_options['timeout'];
        }

        // assign username and password fields
        $usernameField = new SOAP_Value($this->_options['usernamefield'],'string', $username);
        $passwordField = new SOAP_Value($this->_options['passwordfield'],'string', $password);
        $SOAPParams = array($usernameField, $passwordField);

        // assign optional features
        foreach ($this->_features as $fieldName => $fieldValue) {
            $SOAPParams[] = new SOAP_Value($fieldName, 'string', $fieldValue);
        }

        // make SOAP call
        $this->soapResponse = $this->soapClient->call(
                $this->_options['method'],
                $SOAPParams,
                array('namespace' => $this->_options['namespace'])
                );

        if (!PEAR::isError($this->soapResponse)) {
            if ($this->_options['matchpasswords']) {
                // check if passwords match
                if ($password == $this->soapResponse->{$this->_options['passwordfield']}) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    // }}}

}
?>
