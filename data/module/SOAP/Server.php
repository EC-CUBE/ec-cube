<?php
/**
 * This file contains the code for the SOAP server.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 2.02 of the PHP license,
 * that is bundled with this package in the file LICENSE, and is available at
 * through the world-wide-web at http://www.php.net/license/2_02.txt.  If you
 * did not receive a copy of the PHP license and are unable to obtain it
 * through the world-wide-web, please send a note to license@php.net so we can
 * mail you a copy immediately.
 *
 * @category   Web Services
 * @package    SOAP
 * @author     Dietrich Ayala <dietrich@ganx4.com> Original Author
 * @author     Shane Caraveo <Shane@Caraveo.com>   Port to PEAR and more
 * @author     Chuck Hagenbuch <chuck@horde.org>   Maintenance
 * @author     Jan Schneider <jan@horde.org>       Maintenance
 * @copyright  2003-2005 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @link       http://pear.php.net/package/SOAP
 */

require_once 'SOAP/Base.php';
require_once 'SOAP/Fault.php';
require_once 'SOAP/Parser.php';
require_once 'SOAP/Value.php';
require_once 'SOAP/WSDL.php';

/**
 * SOAP Server Class
 *
 * Originaly based on SOAPx4 by Dietrich Ayala
 * http://dietrich.ganx4.com/soapx4
 *
 * @access   public
 * @package  SOAP
 * @author   Shane Caraveo <shane@php.net> Conversion to PEAR and updates
 * @author   Dietrich Ayala <dietrich@ganx4.com> Original Author
 */
class SOAP_Server extends SOAP_Base
{
    /**
     *
     * @var array
     */
    var $dispatch_map = array(); // create empty dispatch map
    var $dispatch_objects = array();
    var $soapobject = null;
    var $call_methodname = null;
    var $callHandler = null;
    var $callValidation = true;

    /**
     * A list of headers that are going to be sent back to the client.
     *
     * @var array
     */
    var $headers = array();

    /**
     *
     * @var string
     */
    var $request = '';

    /**
     *
     * @var string  XML-Encoding
     */
    var $xml_encoding = SOAP_DEFAULT_ENCODING;
    var $response_encoding = 'UTF-8';

    var $result = 'successful'; // for logging interop results to db

    var $endpoint = ''; // the uri to ME!

    var $service = ''; //soapaction header
    var $method_namespace = null;

    /**
     * Options.
     *
     * @var array
     */
    var $_options = array('use' => 'encoded',
                          'style' => 'rpc',
                          'parameters' => 0,
                          'http_status_success' => '200 OK',
                          'http_status_fault' => '500 SOAP Fault');

    function SOAP_Server($options = null)
    {
        ini_set('track_errors', 1);
        parent::SOAP_Base('Server');

        if (is_array($options)) {
            if (isset($options['use'])) {
                $this->_options['use'] = $options['use'];
            }
            if (isset($options['style'])) {
                $this->_options['style'] = $options['style'];
            }
            if (isset($options['parameters'])) {
                $this->_options['parameters'] = $options['parameters'];
            }
        }
        // assume we encode with section 5
        $this->_section5 = true;
        if ($this->_options['use'] == 'literal') {
            $this->_section5 = false;
        }
    }

    /**
     * Error handler for errors that happen in proxied methods.
     *
     * To always return a valid SOAP response even on errors that don't happen
     * in this code, the errors are catched, transformed to a SOAP fault and
     * immediately sent to the client.
     *
     * @see http://www.php.net/set_error_handler
     */
    function _errorHandler($errno, $errmsg, $filename, $linenum)
    {
        /* The error handler should ignore '0' errors, eg. hidden by @ - see
         * the set_error_handler manual page. (thanks to Alan Knowles). */
        if (!$errno || !error_reporting() || $errno == E_NOTICE ||
            (defined('E_STRICT') && $errno == constant('E_STRICT'))) {
            return false;
        }

        $this->fault = new SOAP_Fault($errmsg, 'Server', 'PHP', "Errno: $errno\nFilename: $filename\nLineno: $linenum\n");

        $this->_sendResponse();
        exit;
    }

    function _getContentEncoding($content_type)
    {
        /* Get the character encoding of the incoming request treat incoming
         * data as UTF-8 if no encoding set. */
        $this->xml_encoding = 'UTF-8';
        if (strpos($content_type, '=')) {
            $enc = strtoupper(str_replace('"', '', substr(strstr($content_type, '='), 1)));
            if (!in_array($enc, $this->_encodings)) {
                return false;
            }
            $this->xml_encoding = $enc;
        }

        return true;
    }


    /**
     * Parses the request and posts or returns the response.
     *
     * @param string $data      The SOAP request data.
     * @param string $endpoint  The service endpoint. Determined automatically
     *                          if left empty.
     * @param boolean $test
     * @param boolean $return   Whether to return the SOAP response data
     *                          instead of sending it to the client.
     */
    function service($data, $endpoint = '', $test = false, $return = false)
    {
        $response = null;
        $attachments = array();
        $useEncoding = 'DIME';

        /* Figure out our endpoint. */
        $this->endpoint = $endpoint;
        if (!$test && !$this->endpoint) {
            /* We'll try to build our endpoint. */
            $this->endpoint = 'http://' . $_SERVER['SERVER_NAME'];
            if ($_SERVER['SERVER_PORT']) {
                $this->endpoint .= ':' . $_SERVER['SERVER_PORT'];
            }
            $this->endpoint .= $_SERVER['SCRIPT_NAME'];
        }

        /* Get the character encoding of the incoming request treat incoming
         * data as UTF-8 if no encoding set. */
        if (isset($_SERVER['CONTENT_TYPE'])) {
            if (strcasecmp($_SERVER['CONTENT_TYPE'], 'application/dime') == 0) {
                $this->_decodeDIMEMessage($data, $headers, $attachments);
                $useEncoding = 'DIME';
            } elseif (stristr($_SERVER['CONTENT_TYPE'], 'multipart/related')) {
                /* This is a mime message, let's decode it. */
                $data = 'Content-Type: ' .
                    stripslashes($_SERVER['CONTENT_TYPE']) .
                    "\r\n\r\n" . $data;
                $this->_decodeMimeMessage($data, $headers, $attachments);
                $useEncoding = 'Mime';
            }
            if (!isset($headers['content-type'])) {
                $headers['content-type'] = stripslashes($_SERVER['CONTENT_TYPE']);
            }
            if (!$this->fault &&
                !$this->_getContentEncoding($headers['content-type'])) {
                $this->xml_encoding = SOAP_DEFAULT_ENCODING;
                /* Found encoding we don't understand; return a fault. */
                $this->_raiseSoapFault('Unsupported encoding, use one of ISO-8859-1, US-ASCII, UTF-8', '', '', 'Server');
            }
        }

        /* If this is not a POST with Content-Type text/xml, try to return a
         * WSDL file. */
        if (!$this->fault && !$test &&
            ($_SERVER['REQUEST_METHOD'] != 'POST' ||
             strncmp($headers['content-type'], 'text/xml', 8) != 0)) {
            /* This is not possibly a valid SOAP request, try to return a WSDL
             * file. */
            $this->_raiseSoapFault('Invalid SOAP request, must be POST with content-type: text/xml, got: ' . (isset($headers['content-type']) ? $headers['content-type'] : 'Nothing!'), '', '', 'Server');
        }

        if (!$this->fault) {
            /* $response is a SOAP_Msg object. */
            $soap_msg = $this->parseRequest($data, $attachments);

            /* Handle Mime or DIME encoding. */
            /* TODO: DIME decoding should move to the transport, do it here
             * for now and for ease of getting it done. */
            if (count($this->_attachments)) {
                if ($useEncoding == 'Mime') {
                    $soap_msg = $this->_makeMimeMessage($soap_msg);
                } else {
                    // default is dime
                    $soap_msg = $this->_makeDIMEMessage($soap_msg);
                    $this->headers['Content-Type'] = 'application/dime';
                }
                if (PEAR::isError($soap_msg)) {
                    return $this->_raiseSoapFault($soap_msg);
                }
            }

            if (is_array($soap_msg)) {
                $response = $soap_msg['body'];
                if (count($soap_msg['headers'])) {
                    $this->headers = $soap_msg['headers'];
                }
            } else {
                $response = $soap_msg;
            }
        }

        if ($return) {
            if ($this->fault) {
                $response = $this->fault->message();
            }
            return $response;
        }

        $this->_sendResponse($response);
    }

    /**
     * Sends the final HTTP response to the client, including the HTTP header
     * and the HTTP body.
     *
     * If an error happened, it returns a SOAP fault instead of the response
     * body.
     *
     * @param string $response  The response body.
     */
    function _sendResponse($response = '')
    {
        /* Make distinction between the different SAPIs, running PHP as CGI or
         * as a module. */
        if (stristr(php_sapi_name(), 'cgi') === 0) {
            $hdrs_type = 'Status:';
        } else {
            $hdrs_type = 'HTTP/1.1';
        }

        if ($this->fault) {
            $hdrs = $hdrs_type . ' ' . $this->_options['http_status_fault'] . "\r\n";
            $response = $this->fault->message($this->response_encoding);
        } else {
            $hdrs = $hdrs_type . ' ' . $this->_options['http_status_success'] . "\r\n";
        }
        header($hdrs);

        $this->headers['Server'] = SOAP_LIBRARY_NAME;
        if (!isset($this->headers['Content-Type'])) {
            $this->headers['Content-Type'] = 'text/xml; charset=' .
                $this->response_encoding;
        }
        $this->headers['Content-Length'] = strlen($response);

        foreach ($this->headers as $k => $v) {
            header("$k: $v");
            $hdrs .= "$k: $v\r\n";
        }

        $this->response = $hdrs . "\r\n" . $response;
        print $response;
    }

    function &callMethod($methodname, &$args)
    {
        if ($this->callHandler) {
            $ret = @call_user_func_array($this->callHandler, array($methodname, $args));
            return $ret;
        }

        set_error_handler(array($this, '_errorHandler'));

        if ($args) {
            /* Call method with parameters. */
            if (isset($this->soapobject) && is_object($this->soapobject)) {
                $ret = call_user_func_array(array(&$this->soapobject, $methodname), $args);
            } else {
                $ret = call_user_func_array($methodname, $args);
            }
        } else {
            /* Call method withour parameters. */
            if (is_object($this->soapobject)) {
                $ret = call_user_func(array(&$this->soapobject, $methodname));
            } else {
                $ret = call_user_func($methodname);
            }
        }

        restore_error_handler();

        return $ret;
    }

    /**
     * Creates SOAP_Value objects with return values from method.
     * Uses method signature to determine type.
     *
     * @param mixed $method_response  The result(s).
     * @param array|string $type      The type(s) of the return value(s).
     * @param string $return_name     The name of the return value.
     * @param string $namespace       The namespace of the return value.
     *
     * @return array  List of SOAP_Value objects.
     */
    function buildResult(&$method_response, &$return_type,
                         $return_name = 'return', $namespace = '')
    {
        if (is_a($method_response, 'SOAP_Value')) {
            $return_val = array($method_response);
        } else {
            if (is_array($return_type) && is_array($method_response)) {
                $i = 0;

                foreach ($return_type as $key => $type) {
                    if (is_numeric($key)) {
                        $key = 'item';
                    }
                    if (is_a($method_response[$i], 'SOAP_Value')) {
                        $return_val[] =& $method_response[$i++];
                    } else {
                        $qn = new QName($key, $namespace);
                        $return_val[] = new SOAP_Value($qn->fqn(), $type, $method_response[$i++]);
                    }
                }
            } else {
                if (is_array($return_type)) {
                    $keys = array_keys($return_type);
                    if (!is_numeric($keys[0])) {
                        $return_name = $keys[0];
                    }
                    $values = array_values($return_type);
                    $return_type = $values[0];
                }
                $qn = new QName($return_name, $namespace);
                $return_val = array(new SOAP_Value($qn->fqn(), $return_type, $method_response));
            }
        }
        return $return_val;
    }

    function parseRequest($data = '', $attachments = null)
    {
        /* Parse response, get SOAP_Parser object. */
        $parser =& new SOAP_Parser($data, $this->xml_encoding, $attachments);
        /* If fault occurred during message parsing. */
        if ($parser->fault) {
            $this->fault = $parser->fault;
            return null;
        }

        /* Handle message headers. */
        $request_headers = $parser->getHeaders();
        $header_results = array();

        if ($request_headers) {
            if (!is_a($request_headers, 'SOAP_Value')) {
                $this->_raiseSoapFault('Parser did not return SOAP_Value object: ' . $request_headers, '', '', 'Server');
                return null;
            }
            if ($request_headers->value) {
                /* Handle headers now. */
                foreach ($request_headers->value as $header_val) {
                    $f_exists = $this->validateMethod($header_val->name, $header_val->namespace);

                    /* TODO: this does not take into account message routing
                     * yet. */
                    $myactor = !$header_val->actor ||
                        $header_val->actor == 'http://schemas.xmlsoap.org/soap/actor/next' ||
                        $header_val->actor == $this->endpoint;

                    if (!$f_exists && $header_val->mustunderstand && $myactor) {
                        $this->_raiseSoapFault('I don\'t understand header ' . $header_val->name, '', '', 'MustUnderstand');
                        return null;
                    }

                    /* We only handle the header if it's for us. */
                    $isok = $f_exists && $myactor;

                    if ($isok) {
                        /* Call our header now! */
                        $header_method = $header_val->name;
                        $header_data = array($this->_decode($header_val));
                        /* If there are parameters to pass. */
                        $hr =& $this->callMethod($header_method, $header_data);
                        if (PEAR::isError($hr)) {
                            $this->_raiseSoapFault($hr);
                            return null;
                        }
                        $results = $this->buildResult($hr, $this->return_type, $header_method, $header_val->namespace);
                        $header_results[] = $results[0];
                    }
                }
            }
        }

        /* Handle the method call. */
        /* Evaluate message, getting back a SOAP_Value object. */
        $this->call_methodname = $this->methodname = $parser->root_struct_name[0];

        /* Figure out the method namespace. */
        $this->method_namespace = $parser->message[$parser->root_struct[0]]['namespace'];

        if ($this->_wsdl) {
            $this->_setSchemaVersion($this->_wsdl->xsd);
            $dataHandler = $this->_wsdl->getDataHandler($this->methodname, $this->method_namespace);
            if ($dataHandler)
                $this->call_methodname = $this->methodname = $dataHandler;

            $this->_portName = $this->_wsdl->getPortName($this->methodname);
            if (PEAR::isError($this->_portName)) {
                $this->_raiseSoapFault($this->_portName);
                return null;
            }
            $opData = $this->_wsdl->getOperationData($this->_portName, $this->methodname);
            if (PEAR::isError($opData)) {
                $this->_raiseSoapFault($opData);
                return null;
            }
            $this->_options['style'] = $opData['style'];
            $this->_options['use'] = $opData['output']['use'];
            $this->_options['parameters'] = $opData['parameters'];
        }

        /* Does method exist? */
        if (!$this->methodname ||
            !$this->validateMethod($this->methodname, $this->method_namespace)) {
            $this->_raiseSoapFault('method "' . $this->method_namespace . $this->methodname . '" not defined in service', '', '', 'Server');
            return null;
        }

        if (!$request_val = $parser->getResponse()) {
            return null;
        }
        if (!is_a($request_val, 'SOAP_Value')) {
            $this->_raiseSoapFault('Parser did not return SOAP_Value object: ' . $request_val, '', '', 'Server');
            return null;
        }

        /* Verify that SOAP_Value objects in request match the methods
         * signature. */
        if (!$this->verifyMethod($request_val)) {
            /* verifyMethod() creates the fault. */
            return null;
        }

        /* Need to set special error detection inside the value class to
         * differentiate between no params passed, and an error decoding. */
        $request_data = $this->__decodeRequest($request_val);
        if (PEAR::isError($request_data)) {
            $this->_raiseSoapFault($request_data);
            return null;
        }
        $method_response =& $this->callMethod($this->call_methodname, $request_data);
        if (PEAR::isError($method_response)) {
            $this->_raiseSoapFault($method_response);
            return null;
        }

        if ($this->_options['parameters'] ||
            !$method_response ||
            $this->_options['style'] == 'rpc') {
            /* Get the method result. */
            if (is_null($method_response)) {
                $return_val = null;
            } else {
                $return_val = $this->buildResult($method_response, $this->return_type);
            }

            $qn = new QName($this->methodname . 'Response', $this->method_namespace);
            $methodValue = new SOAP_Value($qn->fqn(), 'Struct', $return_val);
        } else {
            $methodValue =& $method_response;
        }
        return $this->makeEnvelope($methodValue, $header_results, $this->response_encoding);
    }

    function &__decodeRequest($request, $shift = false)
    {
        if (!$request) {
            $decoded = null;
            return $decoded;
        }

        /* Check for valid response. */
        if (PEAR::isError($request)) {
            $fault = &$this->_raiseSoapFault($request);
            return $fault;
        } else if (!is_a($request, 'SOAP_Value')) {
            $fault = &$this->_raiseSoapFault('Invalid data in server::__decodeRequest');
            return $fault;
        }

        /* Decode to native php datatype. */
        $requestArray = $this->_decode($request);
        /* Fault? */
        if (PEAR::isError($requestArray)) {
            $fault = &$this->_raiseSoapFault($requestArray);
            return $fault;
        }
        if (is_object($requestArray) &&
            get_class($requestArray) == 'stdClass') {
            $requestArray = get_object_vars($requestArray);
        } elseif ($this->_options['style'] == 'document') {
            $requestArray = array($requestArray);
        }
        if (is_array($requestArray)) {
            if (isset($requestArray['faultcode']) ||
                isset($requestArray['SOAP-ENV:faultcode'])) {
                $faultcode = $faultstring = $faultdetail = $faultactor = '';
                foreach ($requestArray as $k => $v) {
                    if (stristr($k, 'faultcode')) {
                        $faultcode = $v;
                    }
                    if (stristr($k, 'faultstring')) {
                        $faultstring = $v;
                    }
                    if (stristr($k, 'detail')) {
                        $faultdetail = $v;
                    }
                    if (stristr($k, 'faultactor')) {
                        $faultactor = $v;
                    }
                }
                $fault = &$this->_raiseSoapFault($faultstring, $faultdetail, $faultactor, $faultcode);
                return $fault;
            }
            /* Return array of return values. */
            if ($shift && count($requestArray) == 1) {
                $decoded = array_shift($requestArray);
                return $decoded;
            }
            return $requestArray;
        }
        return $requestArray;
    }

    function verifyMethod($request)
    {
        if (!$this->callValidation) {
            return true;
        }

        $params = $request->value;

        /* Get the dispatch map if one exists. */
        $map = null;
        if (array_key_exists($this->methodname, $this->dispatch_map)) {
            $map = $this->dispatch_map[$this->methodname];
        } elseif (isset($this->soapobject)) {
            if (method_exists($this->soapobject, '__dispatch')) {
                $map = $this->soapobject->__dispatch($this->methodname);
            } elseif (method_exists($this->soapobject, $this->methodname)) {
                /* No map, all public functions are SOAP functions. */
                return true;
            }
        }
        if (!$map) {
            $this->_raiseSoapFault('SOAP request specified an unhandled method "' . $this->methodname . '"', '', '', 'Client');
            return false;
        }

        /* If we aliased the SOAP method name to a PHP function, change
         * call_methodname so we do the right thing. */
        if (array_key_exists('alias', $map) && !empty($map['alias'])) {
            $this->call_methodname = $map['alias'];
        }

        /* If there are input parameters required. */
        if ($map['in']) {
            $this->input_value = count($map['in']);
            $this->return_type = false;
            if (is_array($map['out'])) {
                $this->return_type = count($map['out']) > 1
                    ? $map['out']
                    : array_shift($map['out']);
            }
            if (is_array($params)) {
                /* Validate the number of parameters. */
                if (count($params) == count($map['in'])) {
                    /* Make array of param types. */
                    foreach ($params as $param) {
                        $p[] = strtolower($param->type);
                    }
                    $sig_t = array_values($map['in']);
                    /* Validate each param's type. */
                    for ($i = 0; $i < count($p); $i++) {
                        /* If SOAP types do not match, it's still fine if the
                         * mapped php types match this allows using plain PHP
                         * variables to work (i.e. stuff like Decimal would
                         * fail otherwise). We consider this only error if the
                         * types exist in our type maps, and they differ. */
                        if (strcasecmp($sig_t[$i], $p[$i]) != 0 &&
                            isset($this->_typemap[SOAP_XML_SCHEMA_VERSION][$sig_t[$i]]) &&
                            strcasecmp($this->_typemap[SOAP_XML_SCHEMA_VERSION][$sig_t[$i]], $this->_typemap[SOAP_XML_SCHEMA_VERSION][$p[$i]]) != 0) {

                            $param = $params[$i];
                            $this->_raiseSoapFault("SOAP request contained mismatching parameters of name $param->name had type [{$p[$i]}], which did not match signature's type: [{$sig_t[$i]}], matched? " . (strcasecmp($sig_t[$i], $p[$i])), '', '', 'Client');
                            return false;
                        }
                    }
                    return true;
                } else {
                    /* Wrong number of params. */
                    $this->_raiseSoapFault('SOAP request contained incorrect number of parameters. method "' . $this->methodname . '" required ' . count($map['in']) . ' and request provided ' . count($params), '', '', 'Client');
                    return false;
                }
            } else {
                /* No params. */
                $this->_raiseSoapFault('SOAP request contained incorrect number of parameters. method "' . $this->methodname . '" requires ' . count($map['in']) . ' parameters, and request provided none.', '', '', 'Client');
                return false;
            }
        }

        /* We'll try it anyway. */
        return true;
    }

    function validateMethod($methodname, $namespace = null)
    {
        unset($this->soapobject);

        if (!$this->callValidation) {
            return true;
        }

        /* No SOAP access to private functions. */
        if ($methodname[0] == '_') {
            return false;
        }

        /* if it's in our function list, ok */
        if (array_key_exists($methodname, $this->dispatch_map) &&
            (!$namespace ||
             !array_key_exists('namespace', $this->dispatch_map[$methodname]) ||
             $namespace == $this->dispatch_map[$methodname]['namespace'])) {
            if (array_key_exists('namespace', $this->dispatch_map[$methodname]))
                $this->method_namespace = $this->dispatch_map[$methodname]['namespace'];
            return true;
        }

        /* if it's in an object, it's ok */
        if (isset($this->dispatch_objects[$namespace])) {
            $c = count($this->dispatch_objects[$namespace]);
            for ($i = 0; $i < $c; $i++) {
                $obj =& $this->dispatch_objects[$namespace][$i];
                /* If we have a dispatch map, and the function is not in the
                 * dispatch map, then it is not callable! */
                if (method_exists($obj, '__dispatch')) {
                    if ($obj->__dispatch($methodname)) {
                        $this->method_namespace = $namespace;
                        $this->soapobject =& $obj;
                        return true;
                    }
                } elseif (method_exists($obj, $methodname)) {
                    $this->method_namespace = $namespace;
                    $this->soapobject =& $obj;
                    return true;
                }
            }
        }

        return false;
    }

    function addObjectMap(&$obj, $namespace = null, $service_name = 'Default',
                          $service_desc = '')
    {
        if (!$namespace) {
            if (isset($obj->namespace)) {
                // XXX a bit of backwards compatibility
                $namespace = $obj->namespace;
            } else {
                $this->_raiseSoapFault('No namespace provided for class!', '', '', 'Server');
                return false;
            }
        }
        if (!isset($this->dispatch_objects[$namespace])) {
            $this->dispatch_objects[$namespace] = array();
        }
        $this->dispatch_objects[$namespace][] =& $obj;

        // Create internal WSDL structures for object

        // XXX Because some internal workings of PEAR::SOAP decide whether to
        // do certain things by the presence or absence of _wsdl, we should
        // only create a _wsdl structure if we know we can fill it; if
        // __dispatch_map or __typedef for the object is missing, we should
        // avoid creating it. Later, when we are using PHP 5 introspection, we
        // will be able to make the data for all objects without any extra
        // information from the developers, and this condition should be
        // dropped.

        // XXX Known issue: if imported WSDL (bindWSDL) or another WSDL source
        // is used to add _wsdl structure information, then addObjectWSDL is
        // used, there is a high possibility of _wsdl data corruption;
        // therefore you should avoid using __dispatch_map/__typedef
        // definitions AND other WSDL data sources in the same service. We
        // exclude classes that don't have __typedefs to allow external WSDL
        // files to be used with classes with no internal type definitions
        // (the types are defined in the WSDL file). When addObjectWSDL is
        // refactored to not cause corruption, this restriction can be
        // relaxed.

        // In summary, if you add an object with both a dispatch map and type
        // definitions, then previous WSDL file operation and type definitions
        // will be overwritten.
        if (isset($obj->__dispatch_map) && isset($obj->__typedef)) {
            $this->addObjectWSDL($obj, $namespace, $service_name, $service_desc);
        }

        return true;
    }

    /**
     * Adds a method to the dispatch map.
     */
    function addToMap($methodname, $in, $out, $namespace = null, $alias = null)
    {
        if (!$this->callHandler && !function_exists($methodname)) {
            $this->_raiseSoapFault('Error mapping function', '', '', 'Server');
            return false;
        }

        $this->dispatch_map[$methodname]['in'] = $in;
        $this->dispatch_map[$methodname]['out'] = $out;
        $this->dispatch_map[$methodname]['alias'] = $alias;
        if ($namespace) {
            $this->dispatch_map[$methodname]['namespace'] = $namespace;
        }

        return true;
    }

    function setCallHandler($callHandler, $validation = true)
    {
        $this->callHandler = $callHandler;
        $this->callValidation = $validation;
    }

    /**
     * @deprecated use bindWSDL from now on
     */
    function bind($wsdl_url)
    {
        $this->bindWSDL($wsdl_url);
    }

    /**
     * @param  string a url to a WSDL resource
     * @return void
     */
    function bindWSDL($wsdl_url)
    {
        /* Instantiate WSDL class. */
        $this->_wsdl = new SOAP_WSDL($wsdl_url);
        if ($this->_wsdl->fault) {
            $this->_raiseSoapFault($this->_wsdl->fault);
        }
    }

    /**
     * @return void
     */
    function addObjectWSDL(&$wsdl_obj, $targetNamespace, $service_name,
                           $service_desc = '')
    {
        if (!isset($this->_wsdl)) {
            $this->_wsdl = new SOAP_WSDL;
        }

        $this->_wsdl->parseObject($wsdl_obj, $targetNamespace, $service_name, $service_desc);

        if ($this->_wsdl->fault) {
            $this->_raiseSoapFault($this->_wsdl->fault);
        }
    }

}
