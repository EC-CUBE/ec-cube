<?php
/**
 * This file contains the code for the DISCO server, providing DISO and WSDL
 * services.
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
 * @author     Dmitri Vinogradov <dimitri@vinogradov.de>
 * @author     Chuck Hagenbuch <chuck@horde.org>
 * @author     Jan Schneider <jan@horde.org>
 * @copyright  2003-2005 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @link       http://pear.php.net/package/SOAP
 */

/** SOAP_Base */
require_once 'SOAP/Base.php';

/**
 * @package SOAP
 */
class SOAP_DISCO_Server extends SOAP_Base_Object {

    var $namespaces     = array(SCHEMA_WSDL => 'wsdl', SCHEMA_SOAP => 'soap');
    var $import_ns      = array();
    var $wsdl           = '';
    var $disco          = '';
    var $_wsdl          = array();
    var $_disco         = array();
    var $_service_name  = '';
    var $_service_ns    = '';
    var $_service_desc  = '';
    var $_portname      = '';
    var $_bindingname   = '';
    var $soap_server    = NULL;


    function SOAP_DISCO_Server($soap_server, $service_name, $service_desc = '',
                               $import_ns = null)
    {
        parent::SOAP_Base_Object('Server');

        if ( !is_object($soap_server)
            || !get_class($soap_server) == 'soap_server') return;

        $this->_service_name = $service_name;
        $this->_service_ns = "urn:$service_name";
        $this->_service_desc = $service_desc;
        $this->import_ns = isset($import_ns) ? $import_ns : $this->import_ns;
        $this->soap_server = $soap_server;
        $this->host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    }

    function getDISCO()
    {
        $this->_generate_DISCO();
        return $this->disco;
    }

    function getWSDL()
    {
        $this->_generate_WSDL();
        return $this->wsdl;
    }

    function _generate_DISCO()
    {
        // DISCO
        $this->_disco['disco:discovery']['attr']['xmlns:disco'] = SCHEMA_DISCO;
        $this->_disco['disco:discovery']['attr']['xmlns:scl'] = SCHEMA_DISCO_SCL;
        $this->_disco['disco:discovery']['scl:contractRef']['attr']['ref'] =
            (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on')
            ? 'https://' . $this->host . $_SERVER['PHP_SELF'] . '?wsdl'
            : 'http://'  . $this->host . $_SERVER['PHP_SELF'] . '?wsdl';

        // generate disco xml
        $this->_generate_DISCO_XML($this->_disco);
    }

    function _generate_WSDL()
    {
        // WSDL
        if (is_array($this->soap_server->_namespaces)) {
            // need to get: typens, xsd & SOAP-ENC
            $flipped = array_flip($this->soap_server->_namespaces);
            $this->namespaces[$this->_service_ns] = 'tns';
            $this->namespaces[$flipped['xsd']] = 'xsd';
            $this->namespaces[$flipped[SOAP_BASE::SOAPENCPrefix()]] = SOAP_BASE::SOAPENCPrefix();
        }

        // DEFINITIONS
        $this->_wsdl['definitions']['attr']['name'] = $this->_service_name;
        $this->_wsdl['definitions']['attr']['targetNamespace'] = $this->_service_ns;
        foreach ($this->namespaces as $ns => $prefix) {
            $this->_wsdl['definitions']['attr']['xmlns:' . $prefix] = $ns;
        }
        $this->_wsdl['definitions']['attr']['xmlns'] = SCHEMA_WSDL;

        // Import namespaces. Seems to not work yet: wsdl.exe fom .NET can't
        // handle imported complete wsdl-definitions.
        if (count($this->import_ns)) {
            $i = 0;
            foreach ($this->import_ns as $_ns => $_location) {
                $this->_wsdl['definitions']['import'][$i]['attr']['location'] = $_location;
                $this->_wsdl['definitions']['import'][$i]['attr']['namespace'] = $_ns;
                $i++;
            }
        }
        $this->_wsdl['definitions']['types']['attr']['xmlns']='http://schemas.xmlsoap.org/wsdl/';
        $this->_wsdl['definitions']['types']['schema']=array();

        // Placeholder for messages
        $this->_wsdl['definitions']['message'] = array();

        // PORTTYPE-NAME
        $this->_portname = $this->_service_name . 'Port';
        $this->_wsdl['definitions']['portType']['attr']['name'] = $this->_portname;

        // BINDING-NAME
        $this->_bindingname = $this->_service_name . 'Binding';
        $this->_wsdl['definitions']['binding']['attr']['name'] = $this->_bindingname;
        $this->_wsdl['definitions']['binding']['attr']['type'] = 'tns:' . $this->_portname;
        $this->_wsdl['definitions']['binding']['soap:binding']['attr']['style'] = 'rpc';
        $this->_wsdl['definitions']['binding']['soap:binding']['attr']['transport'] = SCHEMA_SOAP_HTTP;

        // SERVICE
        $this->_wsdl['definitions']['service']['attr']['name'] = $this->_service_name . 'Service';
        $this->_wsdl['definitions']['service']['documentation']['attr'] = '';
        $this->_wsdl['definitions']['service']['documentation'] = htmlentities($this->_service_desc);
        $this->_wsdl['definitions']['service']['port']['attr']['name'] = $this->_portname;
        $this->_wsdl['definitions']['service']['port']['attr']['binding'] = 'tns:' . $this->_bindingname;
        $this->_wsdl['definitions']['service']['port']['soap:address']['attr']['location'] =
            (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on')
            ? 'https://' . $this->host . $_SERVER['PHP_SELF']
            : 'http://'  . $this->host . $_SERVER['PHP_SELF'];

        //
        $dispatch_keys = array_keys($this->soap_server->dispatch_objects);
        $dc = count($dispatch_keys);
        for ($di = 0; $di < $dc; $di++) {
            $namespace = $dispatch_keys[$di];
            $namespace_objects =& $this->soap_server->dispatch_objects[$namespace];
            $oc = count($namespace_objects);
            for ($oi = 0; $oi < $oc; $oi++) {
                $object = $namespace_objects[$oi];
                // types definitions
                $this->addSchemaFromMap($object->__typedef);
                // MESSAGES
                $this->addMethodsFromMap($object->__dispatch_map, $namespace, get_class($object));
            }
        }
        if (isset($this->soap_server->dispatch_map)) {
            $this->addMethodsFromMap($this->soap_server->dispatch_map, $namespace);
        }

        // generate wsdl
        $this->_generate_WSDL_XML();
    }

    function &_getSchema($namespace)
    {
        // SCHEMA
        $c = count($this->_wsdl['definitions']['types']['schema']);
        for($i = 0; $i < $c; $i++) {
            if ($this->_wsdl['definitions']['types']['schema'][$i]['attr']['targetNamespace'] == $namespace) {
                return $this->_wsdl['definitions']['types']['schema'][$i];
            }
        }

        // don't have this namespace
        $schema = array();
        $schema['attr'] = array();
        $schema['complexType'] = array();
        $schema['attr']['xmlns'] = array_search('xsd',$this->namespaces);
        $schema['attr']['targetNamespace'] = $namespace;
        $this->_wsdl['definitions']['types']['schema'][] =& $schema;

        return $schema;
    }

    function addSchemaFromMap(&$map)
    {
        if (!$map) {
            return;
        }

        foreach ($map as $_type_name => $_type_def) {
            list($typens,$type) = $this->_getTypeNs($_type_name);
            if ($typens == 'xsd') {
                // cannot add to xsd, lets use method_namespace
                $typens = 'tns';
            }
            $schema =& $this->_getSchema(array_search($typens, $this->namespaces));
            if (!$this->_ifComplexTypeExists($schema['complexType'], $type)) {
                $ctype =& $schema['complexType'][];
                $ctype['attr']['name'] = $type;
                foreach ($_type_def as $_varname => $_vartype) {
                    if (!is_int($_varname)) {
                        list($_vartypens,$_vartype) = $this->_getTypeNs($_vartype);
                        $ctype['all']['attr'] = '';
                        $el =& $ctype['all']['element'][];
                        $el['attr']['name'] = $_varname;
                        $el['attr']['type'] = $_vartypens . ':' . $_vartype;
                    } else {
                        $ctype['complexContent']['attr'] = '';
                        $ctype['complexContent']['restriction']['attr']['base'] = SOAP_BASE::SOAPENCPrefix().':Array';
                        foreach ($_vartype as $array_type) {
                            list($_vartypens, $_vartype) = $this->_getTypeNs($array_type);
                            $ctype['complexContent']['restriction']['attribute']['attr']['ref'] = SOAP_BASE::SOAPENCPrefix().':arrayType';
                            $ctype['complexContent']['restriction']['attribute']['attr']['wsdl:arrayType'] = $_vartypens . ':' . $_vartype . '[]';
                        }
                    }
                }
            }
        }
    }

    function addMethodsFromMap(&$map, $namespace, $classname = null)
    {
        if (!$map) {
            return;
        }

        foreach ($map as $method_name => $method_types) {
            if (array_key_exists('namespace', $method_types)) {
                $method_namespace = $method_types['namespace'];
            } else {
                $method_namespace = $namespace;
            }

            // INPUT
            $input_message = array('attr' => array('name' => $method_name . 'Request'));
            if (isset($method_types['in']) && is_array($method_types['in'])) {
                foreach ($method_types['in'] as $name => $type) {
                    list($typens, $type) = $this->_getTypeNs($type);
                    $part = array();
                    $part['attr']['name'] = $name;
                    $part['attr']['type'] = $typens . ':' . $type;
                    $input_message['part'][] = $part;
                }
            }
            $this->_wsdl['definitions']['message'][] = $input_message;

            // OUTPUT
            $output_message = array('attr' => array('name' => $method_name . 'Response'));
            if (isset($method_types['out']) && is_array($method_types['out'])) {
                foreach ($method_types['out'] as $name => $type) {
                    list($typens, $type) = $this->_getTypeNs($type);
                    $part = array();
                    $part['attr']['name'] = $name;
                    $part['attr']['type'] = $typens . ':' . $type;
                    $output_message['part'][] = $part;
                }
            }
            $this->_wsdl['definitions']['message'][] = $output_message;

            // PORTTYPES
            $operation = array();
            $operation['attr']['name'] = $method_name;
            // INPUT
            $operation['input']['attr']['message'] = 'tns:' . $input_message['attr']['name'];
            // OUTPUT
            $operation['output']['attr']['message'] = 'tns:' . $output_message['attr']['name'];
            $this->_wsdl['definitions']['portType']['operation'][] = $operation;

            // BINDING
            $binding = array();
            $binding['attr']['name'] = $method_name;
            $action = $method_namespace . '#' . ($classname ? $classname . '#' : '') . $method_name;
            $binding['soap:operation']['attr']['soapAction'] = $action;
            // INPUT
            $binding['input']['attr'] = '';
            $binding['input']['soap:body']['attr']['use'] = 'encoded';
            $binding['input']['soap:body']['attr']['namespace'] = $method_namespace;
            $binding['input']['soap:body']['attr']['encodingStyle'] = SOAP_SCHEMA_ENCODING;
            // OUTPUT
            $binding['output']['attr'] = '';
            $binding['output']['soap:body']['attr']['use'] = 'encoded';
            $binding['output']['soap:body']['attr']['namespace'] = $method_namespace;
            $binding['output']['soap:body']['attr']['encodingStyle'] = SOAP_SCHEMA_ENCODING;
            $this->_wsdl['definitions']['binding']['operation'][] = $binding;
        }
    }

    function _generate_DISCO_XML($disco_array)
    {
        $disco = '<?xml version="1.0"?>';
        foreach ($disco_array as $key => $val) {
            $disco .= $this->_arrayToNode($key,$val);
        }
        $this->disco = $disco;
    }

    function _generate_WSDL_XML()
    {
        $wsdl = '<?xml version="1.0"?>';
        foreach ($this->_wsdl as $key => $val) {
            $wsdl .= $this->_arrayToNode($key, $val);
        }
        $this->wsdl = $wsdl;
    }

    function _arrayToNode($node_name = '', $array)
    {
        $return = '';
        if (is_array($array)) {
            // we have a node if there's key 'attr'
            if (array_key_exists('attr',$array)) {
                $return .= "<$node_name";
                if (is_array($array['attr'])) {
                    foreach ($array['attr'] as $attr_name => $attr_value) {
                        $return .= " $attr_name=\"$attr_value\"";
                    }
                }

                // unset 'attr' and proceed other childs...
                unset($array['attr']);

                if (count($array) > 0) {
                    $i = 0;
                    foreach ($array as $child_node_name => $child_node_value) {
                        $return .= $i == 0 ? ">\n" : '';
                        $return .= $this->_arrayToNode($child_node_name,$child_node_value);
                        $i++;
                    }
                    $return .= "</$node_name>\n";
                } else {
                    $return .= " />\n";
                }
            } else {
                // we have no 'attr' key in array - so it's list of nodes with
                // the same name ...
                foreach ($array as $child_node_name => $child_node_value) {
                    $return .= $this->_arrayToNode($node_name,$child_node_value);
                }
            }
        } else {
            // $array is not an array
            if ($array !='') {
                // and its not empty
                $return .= "<$node_name>$array</$node_name>\n";
            } else {
                // and its empty...
                $return .= "<$node_name />\n";
            }
        }
        return $return;
    }

    function _getTypeNs($type)
    {
        preg_match_all("'\{(.*)\}'sm", $type, $m);
        if (isset($m[1][0]) && $m[1][0] != '') {
            if (!array_key_exists($m[1][0],$this->namespaces)) {
                $ns_pref = 'ns' . count($this->namespaces);
                $this->namespaces[$m[1][0]] = $ns_pref;
                $this->_wsdl['definitions']['attr']['xmlns:' . $ns_pref] = $m[1][0];
            }
            $typens = $this->namespaces[$m[1][0]];
            $type = preg_replace('/'.$m[0][0].'/', '', $type);
        } else {
            $typens = 'xsd';
        }
        return array($typens,$type);
    }

    function _ifComplexTypeExists($typesArray, $type_name)
    {
        if (is_array($typesArray)) {
            foreach ($typesArray as $type_data) {
                if ($type_data['attr']['name'] == $type_name) {
                    return true;
                }
            }
        }
        return false;
    }
}
