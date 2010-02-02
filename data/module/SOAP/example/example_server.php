<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Shane Caraveo <Shane@Caraveo.com>                           |
// +----------------------------------------------------------------------+
//
// $Id: example_server.php,v 1.6 2007/01/22 11:51:45 yunosh Exp $
//

// first, include the SOAP/Server class
require_once 'SOAP/Value.php';
require_once 'SOAP/Fault.php';

// SOAPStruct is defined in the following file
require_once 'example_types.php';

// create a class for your soap functions
class SOAP_Example_Server {
    /**
     * The dispactch map does not need to be used, but aids
     * the server class in knowing what parameters are used
     * with the functions.  This is the ONLY way to have
     * multiple OUT parameters.  If you use a dispatch map, you
     * MUST add ALL functions you wish to allow be called.  If
     * you do not use a dispatch map, then any public function
     * can be called from soap (in php4, we consider this to be
     * any function in the class unless it starts with underscore,
     * php5 support is not complete yet in this regard).  
     * if you do not define in/out parameters, the function can be
     * called with parameters, but no validation on parameters will
     * occure.
     */
    var $__dispatch_map = array();

    function SOAP_Example_Server() {
        /**
        * when generating wsdl for a server, you have to define
        * any special complex types that you use (ie classes).
        * using a namespace id before the type will create an
        * xml schema with the targetNamespace for the type
        * multiple types with the same namespace will appear
        * in the same schema section.  types with different
        * namespaces will be in seperate schema sections.
        * the following SOAPStruct typedef cooresponds to the
        * SOAPStruct class above.
        */
        $this->__typedef['{http://soapinterop.org/xsd}SOAPStruct'] = 
                    array(
                        'varString' => 'string',
                        'varInt' => 'int', 
                        'varFloat' => 'float'
                         );

        // an aliased function with multiple out parameters
	$this->__dispatch_map['echoStructAsSimpleTypes'] =
		array('in' => array('inputStruct' => '{http://soapinterop.org/xsd}SOAPStruct'),
		      'out' => array('outputString' => 'string', 'outputInteger' => 'int', 'outputFloat' => 'float'),
		      'alias' => 'myEchoStructAsSimpleTypes'
		      );
	$this->__dispatch_map['echoStringSimple'] =
		array('in' => array('inputStringSimple' => 'string'),
		      'out' => array('outputStringSimple' => 'string'),
		      );
	$this->__dispatch_map['echoString'] =
		array('in' => array('inputString' => 'string'),
		      'out' => array('outputString' => 'string'),
		      );
	$this->__dispatch_map['divide'] =
		array('in' => array('dividend' => 'int', 'divisor' => 'int'),
		      'out' => array('outputFloat' => 'float'),
		      );
	$this->__dispatch_map['echoStruct'] =
		array('in' => array('inputStruct' => '{http://soapinterop.org/xsd}SOAPStruct'),
		      'out' => array('outputStruct' => '{http://soapinterop.org/xsd}SOAPStruct'),
		      );
	
	$this->__dispatch_map['echoMimeAttachment'] = array();
    }

    /* this private function is called on by SOAP_Server to determine any
        special dispatch information that might be necessary.  This, for example,
        can be used to set up a dispatch map for functions that return multiple
        OUT parameters */
    function __dispatch($methodname) {
        if (isset($this->__dispatch_map[$methodname]))
            return $this->__dispatch_map[$methodname];
        return NULL;
    }

    // a simple echoString function
    function echoStringSimple($inputString)
    {
	return $inputString;
    }
    
    // an explicit echostring function
    function echoString($inputString)
    {
	return new SOAP_Value('outputString','string',$inputString);
    }

    function divide($dividend, $divisor)
    {
        // the soap server would normally catch errors like this
        // and return a fault, but this is how you do it yourself.
        if ($divisor == 0)
            return new SOAP_Fault('You cannot divide by zero', 'Client');
        else
            return $dividend / $divisor;
    }

    function echoStruct($inputStruct)
    {
        return $inputStruct->__to_soap('outputStruct');
    }
    
    /**
     * echoStructAsSimpleTypes
     * takes a SOAPStruct as input, and returns each of its elements
     * as OUT parameters
     *
     * this function is also aliased so you have to call it with
     * echoStructAsSimpleTypes
     *
     * SOAPStruct is defined as:
     *
     * struct SOAPStruct:
     *    string varString
     *    integer varInt
     *    float varFloat
     *
     */
    function myEchoStructAsSimpleTypes($struct)
    {
	# convert a SOAPStruct to an array
	return array(
	    new SOAP_Value('outputString','string',$struct->varString),
	    new SOAP_Value('outputInteger','int',$struct->varInt),
	    new SOAP_Value('outputFloat','float',$struct->varFloat)
	    );
    }
    
    function echoMimeAttachment($stuff)
    {
        return new SOAP_Attachment('return','application/octet-stream',NULL,$stuff);
    }    
}

?>