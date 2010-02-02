<?php
/**
 * This file loads all required libraries, defines constants used across the
 * SOAP package, and defines the base classes that most other classes of this
 * package extend.
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
 * @copyright  2003-2007 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @link       http://pear.php.net/package/SOAP
 */

define('MAIL_MIMEPART_CRLF', "\r\n");
require_once 'PEAR.php';

if (!defined('INF')) {
    define('INF', 1.8e307);
}
if (!defined('NAN')) {
    define('NAN', 0.0);
}

define('SOAP_LIBRARY_VERSION', '0.11.0');
define('SOAP_LIBRARY_NAME',    'PEAR-SOAP 0.11.0-beta');

// Set schema version.
define('SOAP_XML_SCHEMA_VERSION',  'http://www.w3.org/2001/XMLSchema');
define('SOAP_XML_SCHEMA_INSTANCE', 'http://www.w3.org/2001/XMLSchema-instance');
define('SOAP_XML_SCHEMA_1999',     'http://www.w3.org/1999/XMLSchema');
define('SOAP_SCHEMA',              'http://schemas.xmlsoap.org/wsdl/soap/');
define('SOAP_SCHEMA_ENCODING',     'http://schemas.xmlsoap.org/soap/encoding/');
define('SOAP_ENVELOP',             'http://schemas.xmlsoap.org/soap/envelope/');

define('SCHEMA_DISCO',             'http://schemas.xmlsoap.org/disco/');
define('SCHEMA_DISCO_SCL',         'http://schemas.xmlsoap.org/disco/scl/');

define('SCHEMA_SOAP',              'http://schemas.xmlsoap.org/wsdl/soap/');
define('SCHEMA_SOAP12',            'http://schemas.xmlsoap.org/wsdl/soap12/');
define('SCHEMA_SOAP_HTTP',         'http://schemas.xmlsoap.org/soap/http');
define('SCHEMA_WSDL_HTTP',         'http://schemas.xmlsoap.org/wsdl/http/');
define('SCHEMA_MIME',              'http://schemas.xmlsoap.org/wsdl/mime/');
define('SCHEMA_WSDL',              'http://schemas.xmlsoap.org/wsdl/');
define('SCHEMA_DIME',              'http://schemas.xmlsoap.org/ws/2002/04/dime/wsdl/');
define('SCHEMA_CONTENT',           'http://schemas.xmlsoap.org/ws/2002/04/content-type/');
define('SCHEMA_REF',               'http://schemas.xmlsoap.org/ws/2002/04/reference/');

define('SOAP_DEFAULT_ENCODING',  'UTF-8');

class SOAP_Base_Object extends PEAR
{

    /**
     * Supported encodings, limited by XML extension.
     *
     * @var array $_encodings
     */
    var $_encodings = array('ISO-8859-1', 'US-ASCII', 'UTF-8');

    /**
     * Fault code.
     *
     * @var string $_myfaultcode
     */
    var $_myfaultcode = '';

    /**
     * Recent PEAR_Error object.
     *
     * @var PEAR_Error $fault
     */
    var $fault = null;

    /**
     * Constructor.
     *
     * @param string $faultcode  Error code.
     */
    function SOAP_Base_Object($faultcode = 'Client')
    {
        $this->_myfaultcode = $faultcode;
        parent::PEAR('SOAP_Fault');
    }

    /**
     * Raises a SOAP error.
     *
     * Please refer to the SOAP definition for an impression of what a certain
     * parameter stands for.
     *
     * @param string|object $str  Error message or object.
     * @param string $detail      Detailed error message.
     * @param string $actorURI
     * @param mixed $code
     * @param mixed $mode
     * @param mixed $options
     * @param boolean $skipmsg
     */
    function &_raiseSoapFault($str, $detail = '', $actorURI = '', $code = null,
                              $mode = null, $options = null, $skipmsg = false)
    {
        // Pass through previous faults.
        $is_instance = isset($this) && is_a($this, 'SOAP_Base_Object');
        if (is_object($str)) {
            $fault =& $str;
        } else {
            if (!$code) {
                $code = $is_instance ? $this->_myfaultcode : 'Client';
            }
            require_once 'SOAP/Fault.php';
            $fault =& new SOAP_Fault($str,
                                     $code,
                                     $actorURI,
                                     $detail,
                                     $mode,
                                     $options);
        }
        if ($is_instance) {
            $this->fault =& $fault;
        }

        return $fault;
    }

    function _isfault()
    {
        return $this->fault != null;
    }

    function &_getfault()
    {
        return $this->fault;
    }

}

/**
 * Common base class of all SOAP classes.
 *
 * @access   public
 * @package  SOAP
 * @author   Shane Caraveo <shane@php.net> Conversion to PEAR and updates
 */
class SOAP_Base extends SOAP_Base_Object
{
    var $_XMLSchema = array('http://www.w3.org/2001/XMLSchema',
                            'http://www.w3.org/1999/XMLSchema');
    var $_XMLSchemaVersion = 'http://www.w3.org/2001/XMLSchema';

    // load types into typemap array
    var $_typemap = array(
        'http://www.w3.org/2001/XMLSchema' => array(
            'string' => 'string',
            'boolean' => 'boolean',
            'float' => 'float',
            'double' => 'float',
            'decimal' => 'float',
            'duration' => 'integer',
            'dateTime' => 'string',
            'time' => 'string',
            'date' => 'string',
            'gYearMonth' => 'integer',
            'gYear' => 'integer',
            'gMonthDay' => 'integer',
            'gDay' => 'integer',
            'gMonth' => 'integer',
            'hexBinary' => 'string',
            'base64Binary' => 'string',
            // derived datatypes
            'normalizedString' => 'string',
            'token' => 'string',
            'language' => 'string',
            'NMTOKEN' => 'string',
            'NMTOKENS' => 'string',
            'Name' => 'string',
            'NCName' => 'string',
            'ID' => 'string',
            'IDREF' => 'string',
            'IDREFS' => 'string',
            'ENTITY' => 'string',
            'ENTITIES' => 'string',
            'integer' => 'integer',
            'nonPositiveInteger' => 'integer',
            'negativeInteger' => 'integer',
            // longs (64bit ints) are not supported cross-platform.
            'long' => 'string',
            'int' => 'integer',
            'short' => 'integer',
            'byte' => 'string',
            'nonNegativeInteger' => 'integer',
            'unsignedLong' => 'integer',
            'unsignedInt' => 'integer',
            'unsignedShort' => 'integer',
            'unsignedByte' => 'integer',
            'positiveInteger'  => 'integer',
            'anyType' => 'string',
            'anyURI' => 'string',
            'QName' => 'string'
        ),
        'http://www.w3.org/1999/XMLSchema' => array(
            'i4' => 'integer',
            'int' => 'integer',
            'boolean' => 'boolean',
            'string' => 'string',
            'double' => 'float',
            'float' => 'float',
            'dateTime' => 'string',
            'timeInstant' => 'string',
            'base64Binary' => 'string',
            'base64' => 'string',
            'ur-type' => 'string'
        ),
        'http://schemas.xmlsoap.org/soap/encoding/' => array(
            'base64' => 'string',
            'array' => 'array',
            'Array' => 'array',
            'Struct' => 'array')
    );

    /**
     * Default class name to use for decoded response objects.
     *
     * @var string $_defaultObjectClassname
     */
    var $_defaultObjectClassname = 'stdClass';

    /**
     * Hash with used namespaces.
     *
     * @array
     */
    var $_namespaces;

    /**
     * The default namespace.
     *
     * @string
     */
    var $_namespace;

    var $_xmlEntities = array('&' => '&amp;',
                              '<' => '&lt;',
                              '>' => '&gt;',
                              "'" => '&apos;',
                              '"' => '&quot;');

    var $_doconversion = false;

    var $_attachments = array();

    var $_wsdl = null;

    /**
     * True if we use section 5 encoding, or false if this is literal.
     *
     * @var boolean $_section5
     */
    var $_section5 = true;

    // Handle type to class mapping.
    var $_auto_translation = false;
    var $_type_translation = array();

    /**
     * Constructor.
     *
     * @param string $faultcode  Error code.
     */
    function SOAP_Base($faultcode = 'Client')
    {
        parent::SOAP_Base_Object($faultcode);
        $this->_resetNamespaces();
    }

    /**
     * Sets the default namespace.
     *
     * @param string $namespace  The default namespace.
     */
    function setDefaultNamespace($namespace)
    {
        $this->_namespace = $namespace;
    }

    function _resetNamespaces()
    {
        $this->_namespaces = array(
            'http://schemas.xmlsoap.org/soap/envelope/' => 'SOAP-ENV',
            'http://www.w3.org/2001/XMLSchema' => 'xsd',
            'http://www.w3.org/2001/XMLSchema-instance' => 'xsi',
            'http://schemas.xmlsoap.org/soap/encoding/' => 'SOAP-ENC');
    }

    /**
     * Sets the schema version used in the SOAP message.
     *
     * @access private
     * @see $_XMLSchema
     *
     * @param string $schemaVersion  The schema version.
     */
    function _setSchemaVersion($schemaVersion)
    {
        if (!in_array($schemaVersion, $this->_XMLSchema)) {
            return $this->_raiseSoapFault("unsuported XMLSchema $schemaVersion");
        }
        $this->_XMLSchemaVersion = $schemaVersion;
        $tmpNS = array_flip($this->_namespaces);
        $tmpNS['xsd'] = $this->_XMLSchemaVersion;
        $tmpNS['xsi'] = $this->_XMLSchemaVersion . '-instance';
        $this->_namespaces = array_flip($tmpNS);
    }

    function _getNamespacePrefix($ns)
    {
        if ($this->_namespace && $ns == $this->_namespace) {
            return '';
        }
        if (isset($this->_namespaces[$ns])) {
            return $this->_namespaces[$ns];
        }
        $prefix = 'ns' . count($this->_namespaces);
        $this->_namespaces[$ns] = $prefix;
        return $prefix;
    }

    function _getNamespaceForPrefix($prefix)
    {
        $flipped = array_flip($this->_namespaces);
        if (isset($flipped[$prefix])) {
            return $flipped[$prefix];
        }
        return null;
    }

    function _isSoapValue(&$value)
    {
        return is_a($value, 'SOAP_Value');
    }

    function _serializeValue(&$value, $name = '', $type = false,
                             $elNamespace = null, $typeNamespace = null,
                             $options = array(), $attributes = array(),
                             $artype = '')
    {
        $namespaces = array();
        $arrayType = $array_depth = $xmlout_value = null;
        $typePrefix = $elPrefix = $xmlout_offset = $xmlout_arrayType = '';
        $xmlout_type = $xmlns = $ptype = $array_type_ns = '';

        if (!$name || is_numeric($name)) {
            $name = 'item';
        }

        if ($this->_wsdl) {
            list($ptype, $arrayType, $array_type_ns, $array_depth)
                = $this->_wsdl->getSchemaType($type, $name, $typeNamespace);
        }

        if (!$arrayType) {
            $arrayType = $artype;
        }
        if (!$ptype) {
            $ptype = $this->_getType($value);
        }
        if (!$type) {
            $type = $ptype;
        }

        if (strcasecmp($ptype, 'Struct') == 0 ||
            strcasecmp($type, 'Struct') == 0) {
            // Struct
            $vars = null;
            if (is_object($value)) {
                $vars = get_object_vars($value);
            } else {
                $vars = &$value;
            }
            if (is_array($vars)) {
                foreach (array_keys($vars) as $k) {
                    // Hide private vars.
                    if ($k[0] == '_') {
                        continue;
                    }
                    if (is_object($vars[$k])) {
                        if (is_a($vars[$k], 'SOAP_Value')) {
                            $xmlout_value .= $vars[$k]->serialize($this);
                        } else {
                            // XXX get the members and serialize them instead
                            // converting to an array is more overhead than we
                            // should really do.
                            $xmlout_value .= $this->_serializeValue(get_object_vars($vars[$k]), $k, false, $this->_section5 ? null : $elNamespace);
                        }
                    } else {
                        $xmlout_value .= $this->_serializeValue($vars[$k], $k, false, $this->_section5 ? null : $elNamespace);
                    }
                }
            }
        } elseif (strcasecmp($ptype, 'Array') == 0 ||
                  strcasecmp($type, 'Array') == 0) {
            // Array.
            $typeNamespace = SOAP_SCHEMA_ENCODING;
            $orig_type = $type;
            $type = 'Array';
            $numtypes = 0;
            $value = (array)$value;
            // XXX this will be slow on larger arrays.  Basically, it flattens
            // arrays to allow us to serialize multi-dimensional arrays.  We
            // only do this if arrayType is set, which will typically only
            // happen if we are using WSDL
            if (isset($options['flatten']) ||
                ($arrayType &&
                 (strchr($arrayType, ',') || strstr($arrayType, '][')))) {
                $numtypes = $this->_multiArrayType($value, $arrayType,
                                                   $ar_size, $xmlout_value);
            }

            $array_type = $array_type_prefix = '';
            if ($numtypes != 1) {
                $arrayTypeQName = new QName($arrayType);
                $arrayType = $arrayTypeQName->name;
                $array_types = array();
                $array_val = null;

                // Serialize each array element.
                $ar_size = count($value);
                foreach ($value as $array_val) {
                    if ($this->_isSoapValue($array_val)) {
                        $array_type = $array_val->type;
                        $array_types[$array_type] = 1;
                        $array_type_ns = $array_val->type_namespace;
                        $xmlout_value .= $array_val->serialize($this);
                    } else {
                        $array_type = $this->_getType($array_val);
                        $array_types[$array_type] = 1;
                        $xmlout_value .= $this->_serializeValue($array_val, 'item', $array_type, $this->_section5 ? null : $elNamespace);
                    }
                }

                $xmlout_offset = ' SOAP-ENC:offset="[0]"';
                if (!$arrayType) {
                    $numtypes = count($array_types);
                    if ($numtypes == 1) {
                        $arrayType = $array_type;
                    }
                    // Using anyType is more interoperable.
                    if ($array_type == 'Struct') {
                        $array_type = '';
                    } elseif ($array_type == 'Array') {
                        $arrayType = 'anyType';
                        $array_type_prefix = 'xsd';
                    } else {
                        if (!$arrayType) {
                            $arrayType = $array_type;
                        }
                    }
                }
            }
            if (!$arrayType || $numtypes > 1) {
                // Should reference what schema we're using.
                $arrayType = 'xsd:anyType';
            } else {
                if ($array_type_ns) {
                    $array_type_prefix = $this->_getNamespacePrefix($array_type_ns);
                } elseif (isset($this->_typemap[$this->_XMLSchemaVersion][$arrayType])) {
                    $array_type_prefix = $this->_namespaces[$this->_XMLSchemaVersion];
                }
                if ($array_type_prefix) {
                    $arrayType = $array_type_prefix . ':' . $arrayType;
                }
            }

            $xmlout_arrayType = ' SOAP-ENC:arrayType="' . $arrayType;
            if ($array_depth != null) {
                for ($i = 0; $i < $array_depth; $i++) {
                    $xmlout_arrayType .= '[]';
                }
            }
            $xmlout_arrayType .= "[$ar_size]\"";
        } elseif ($this->_isSoapValue($value)) {
            $xmlout_value = $value->serialize($this);
        } elseif ($type == 'string') {
            $xmlout_value = htmlspecialchars($value);
        } elseif ($type == 'rawstring') {
            $xmlout_value =& $value;
        } elseif ($type == 'boolean') {
            $xmlout_value = $value ? 'true' : 'false';
        } else {
            $xmlout_value =& $value;
        }

        // Add namespaces.
        if ($elNamespace) {
            $elPrefix = $this->_getNamespacePrefix($elNamespace);
            if ($elPrefix) {
                $xmlout_name = "$elPrefix:$name";
            } else {
                $xmlout_name = $name;
            }
        } else {
            $xmlout_name = $name;
        }

        if ($typeNamespace) {
            $typePrefix = $this->_getNamespacePrefix($typeNamespace);
            if ($typePrefix) {
                $xmlout_type = "$typePrefix:$type";
            } else {
                $xmlout_type = $type;
            }
        } elseif ($type &&
                  isset($this->_typemap[$this->_XMLSchemaVersion][$type])) {
            $typePrefix = $this->_namespaces[$this->_XMLSchemaVersion];
            if ($typePrefix) {
                $xmlout_type = "$typePrefix:$type";
            } else {
                $xmlout_type = $type;
            }
        }

        // Handle additional attributes.
        $xml_attr = '';
        if (count($attributes)) {
            foreach ($attributes as $k => $v) {
                $kqn = new QName($k);
                $vqn = new QName($v);
                $xml_attr .= ' ' . $kqn->fqn() . '="' . $vqn->fqn() . '"';
            }
        }

        // Store the attachment for mime encoding.
        if (isset($options['attachment']) &&
            !PEAR::isError($options['attachment'])) {
            $this->_attachments[] = $options['attachment'];
        }

        if ($this->_section5) {
            if ($xmlout_type) {
                $xmlout_type = " xsi:type=\"$xmlout_type\"";
            }
            if (is_null($xmlout_value)) {
                $xml = "\r\n<$xmlout_name$xmlout_type$xmlns$xmlout_arrayType" .
                    "$xml_attr xsi:nil=\"true\"/>";
            } else {
                $xml = "\r\n<$xmlout_name$xmlout_type$xmlns$xmlout_arrayType" .
                    "$xmlout_offset$xml_attr>$xmlout_value</$xmlout_name>";
            }
        } else {
            if (is_null($xmlout_value)) {
                $xml = "\r\n<$xmlout_name$xmlns$xml_attr/>";
            } else {
                $xml = "\r\n<$xmlout_name$xmlns$xml_attr>" .
                    $xmlout_value . "</$xmlout_name>";
            }
        }

        return $xml;
    }

    /**
     * Converts a PHP type to a SOAP type.
     *
     * @access   private
     *
     * @param string $value  The value to inspect.
     *
     * @return string  The value's SOAP type.
     */
    function _getType(&$value)
    {
        $type = gettype($value);
        switch ($type) {
        case 'object':
            if (is_a($value, 'soap_value')) {
                $type = $value->type;
            } else {
                $type = 'Struct';
            }
            break;

        case 'array':
            // Hashes are always handled as structs.
            if ($this->_isHash($value)) {
                $type = 'Struct';
            } else {
                $ar_size = count($value);
                reset($value);
                $key1 = key($value);
                if ($ar_size > 0 && is_a($key1, 'SOAP_Value')) {
                    // FIXME: for non-wsdl structs that are all the same type
                    $key2 = key($value);
                    if ($ar_size > 1 &&
                        $this->_isSoapValue($key1) &&
                        $this->_isSoapValue($key2) &&
                        $key1->name != $key2->name) {
                        // This is a struct, not an array.
                        $type = 'Struct';
                    } else {
                        $type = 'Array';
                    }
                } else {
                    $type = 'Array';
                }
            }
            break;

        case 'integer':
        case 'long':
            $type = 'int';
            break;

        case 'boolean':
            break;

        case 'double':
            // double is deprecated in PHP 4.2 and later.
            $type = 'float';
            break;

        case 'null':
            $type = '';
            break;

        case 'string':
        default:
            break;
        }

        return $type;
    }

    function _multiArrayType($value, &$type, &$size, &$xml)
    {
        if (is_array($value)) {
            // Seems we have a multi dimensional array, figure it out if we
            // do.
            for ($i = 0, $c = count($value); $i < $c; ++$i) {
                $this->_multiArrayType($value[$i], $type, $size, $xml);
            }

            $sz = count($value);
            if ($size) {
                $size = $sz . ',' . $size;
            } else {
                $size = $sz;
            }
            return 1;
        } elseif (is_object($value)) {
            $type = $value->type;
            $xml .= $value->serialize($this);
        } else {
            $type = $this->_getType($value);
            $xml .= $this->_serializeValue($value, 'item', $type);
        }
        $size = null;

        return 1;
    }

    /**
     * Returns whether a type is a base64 type.
     *
     * @param string $type  A type name.
     *
     * @return boolean  True if the type name is a base64 type.
     */
    function _isBase64Type($type)
    {
        return $type == 'base64' || $type == 'base64Binary';
    }

    /**
     * Returns whether an array is a hash.
     *
     * @param array $a  An array to check.
     *
     * @return boolean  True if the specified array is a hash.
     */
    function _isHash(&$a)
    {
        // I really dislike having to loop through this in PHP code, really
        // large arrays will be slow.  We need a C function to do this.
        $it = 0;
        foreach ($a as $k => $v) {
            // Checking the type is faster than regexp.
            if (!is_int($k)) {
                return true;
            }
            // If someone has a large hash they should really be defining the
            // type.
            if ($it++ > 10) {
                $this->_raiseSoapFault('Large associative array passed where a SOAP_Value was expected');
                return false;
            }
        }
        return false;
    }

    function _un_htmlentities($string)
    {
        $trans_tbl = get_html_translation_table(HTML_ENTITIES);
        $trans_tbl = array_flip($trans_tbl);
        return strtr($string, $trans_tbl);
    }

    /**
    *   Converts a SOAP_Value object into a StdClass PHP object
    */
    function &_decode(&$soapval)
    {
        if (!$this->_isSoapValue($soapval)) {
            return $soapval;
        } elseif (is_array($soapval->value)) {
            if ($soapval->type != 'Array') {
                $classname = $this->_defaultObjectClassname;
                if (isset($this->_type_translation[$soapval->tqn->fqn()])) {
                    // This will force an error in PHP if the class does not
                    // exist.
                    $classname = $this->_type_translation[$soapval->tqn->fqn()];
                } elseif (isset($this->_type_translation[$soapval->type])) {
                    // This will force an error in PHP if the class does not
                    // exist.
                    $classname = $this->_type_translation[$soapval->type];
                } elseif ($this->_auto_translation) {
                    if (class_exists($soapval->type)) {
                        $classname = $soapval->type;
                    } elseif ($this->_wsdl) {
                        $t = $this->_wsdl->getComplexTypeNameForElement($soapval->name, $soapval->namespace);
                        if ($t && class_exists($t)) {
                            $classname = $t;
                        }
                    }
                }
                $return =& new $classname;
            } else {
                $return = array();
            }

            $counter = 1;
            $isstruct = !is_array($return);
            foreach ($soapval->value as $item) {
                if (is_object($return)) {
                    if ($this->_wsdl) {
                        // Get this child's WSDL information.
                        // /$soapval->ns/$soapval->type/$item->ns/$item->name
                        $child_type = $this->_wsdl->getComplexTypeChildType(
                            $soapval->namespace,
                            $soapval->name,
                            $item->namespace,
                            $item->name);
                        if ($child_type) {
                            $item->type = $child_type;
                        }
                    }
                    if (!$isstruct || $item->type == 'Array') {
                        if (isset($return->{$item->name}) &&
                            is_object($return->{$item->name})) {
                            $return->{$item->name} =& $this->_decode($item);
                        } elseif (isset($return->{$item->name}) &&
                                  is_array($return->{$item->name})) {
                            $return->{$item->name}[] = $this->_decode($item);
                        } elseif (isset($return->{$item->name})) {
                            $return->{$item->name} = array(
                                $return->{$item->name},
                                $this->_decode($item)
                            );
                        } elseif (is_array($return)) {
                            $return[] =& $this->_decode($item);
                        } else {
                            $return->{$item->name} =& $this->_decode($item);
                        }
                    } elseif (isset($return->{$item->name})) {
                        //$isstruct = false;
                        if (count(get_object_vars($return)) == 1) {
                            $d =& $this->_decode($item);
                            $return = array($return->{$item->name}, $d);
                        } else {
                            $d =& $this->_decode($item);
                            $return->{$item->name} = array($return->{$item->name}, $d);
                        }
                    } else {
                        $return->{$item->name} =& $this->_decode($item);
                    }
                    // Set the attributes as members in the class.
                    if (method_exists($return, '__set_attribute')) {
                        foreach ($soapval->attributes as $key => $value) {
                            call_user_func_array(array(&$return,
                                                       '__set_attribute'),
                                                 array($key, $value));
                        }
                    }
                } else {
                    if ($soapval->arrayType && $this->_isSoapValue($item)) {
                        if ($this->_isBase64Type($item->type) &&
                            !$this->_isBase64Type($soapval->arrayType)) {
                            // Decode the value if we're losing the base64
                            // type information.
                            $item->value = base64_decode($item->value);
                        }
                        $item->type = $soapval->arrayType;
                    }
                    if (!$isstruct) {
                        $return[] = $this->_decode($item);
                    } elseif (isset($return[$item->name])) {
                        $isstruct = false;
                        $d =& $this->_decode($item);
                        $return = array($return[$item->name], $d);
                    } else {
                        $return[$item->name] = $this->_decode($item);
                    }
                }
            }

            return $return;
        }

        if ($soapval->type == 'boolean') {
            if ($soapval->value != '0' &&
                strcasecmp($soapval->value, 'false') != 0) {
                $soapval->value = true;
            } else {
                $soapval->value = false;
            }
        } elseif ($soapval->type &&
                  isset($this->_typemap[SOAP_XML_SCHEMA_VERSION][$soapval->type])) {
            // If we can, set variable type.
            settype($soapval->value,
                    $this->_typemap[SOAP_XML_SCHEMA_VERSION][$soapval->type]);
        }

        return $soapval->value;
    }

    /**
     * Creates the SOAP envelope with the SOAP envelop data.
     *
     * @param mixed $method
     * @param array $headers
     * @param string $encoding
     * @param array $options
     *
     * @return string
     */
    function makeEnvelope(&$method, &$headers,
                          $encoding = SOAP_DEFAULT_ENCODING,
                          $options = array())
    {
        $smsg = $header_xml = $ns_string = '';

        if ($headers) {
            $c = count($headers);
            for ($i = 0; $i < $c; $i++) {
                $header_xml .= $headers[$i]->serialize($this);
            }
            $header_xml = "<SOAP-ENV:Header>\r\n$header_xml\r\n</SOAP-ENV:Header>\r\n";
        }

        if (!isset($options['input']) || $options['input'] == 'parse') {
            if (is_array($method)) {
                $c = count($method);
                for ($i = 0; $i < $c; $i++) {
                    $smsg .= $method[$i]->serialize($this);
                }
            } else {
                $smsg = $method->serialize($this);
            }
        } else {
            $smsg = $method;
        }
        $body = "<SOAP-ENV:Body>\r\n" . $smsg . "\r\n</SOAP-ENV:Body>\r\n";

        foreach ($this->_namespaces as $k => $v) {
            $ns_string .= " xmlns:$v=\"$k\"\r\n";
        }
        if ($this->_namespace) {
            $ns_string .= " xmlns=\"{$this->_namespace}\"\r\n";
        }

        /* If 'use' == 'literal', we do not put in the encodingStyle.  This is
         * denoted by $this->_section5 being false.  'use' can be defined at a
         * more granular level than we are dealing with here, so this does not
         * work for all services. */
        $xml = "<?xml version=\"1.0\" encoding=\"$encoding\"?>\r\n\r\n".
            "<SOAP-ENV:Envelope $ns_string".
            ($this->_section5 ? ' SOAP-ENV:encodingStyle="' . SOAP_SCHEMA_ENCODING . '"' : '').
            ">\r\n".
            "$header_xml$body</SOAP-ENV:Envelope>\r\n";

        return $xml;
    }

    function _makeMimeMessage($xml, $encoding = SOAP_DEFAULT_ENCODING)
    {
        if (!@include_once 'Mail/mimePart.php') {
            return $this->_raiseSoapFault('MIME messages are unsupported, the Mail_Mime package is not installed');
        }

        // Encode any attachments.  See http://www.w3.org/TR/SOAP-attachments
        // Now we have to mime encode the message.
        $params = array('content_type' => 'multipart/related; type="text/xml"');
        $msg = new Mail_mimePart('', $params);

        // Add the xml part.
        $params['content_type'] = 'text/xml';
        $params['charset'] = $encoding;
        $params['encoding'] = 'base64';
        $msg->addSubPart($xml, $params);

        // Add the attachements
        for ($i = 0, $c = count($this->_attachments); $i < $c; ++$i) {
            $msg->addSubPart($this->_attachments[$i]['body'],
                             $this->_attachments[$i]);
        }

        return $msg->encode();
    }

    // TODO: this needs to be used from the Transport system.
    function _makeDIMEMessage($xml)
    {
        if (!@include_once 'Net/DIME.php') {
            return $this->_raiseSoapFault('DIME messages are unsupported, the Net_DIME package is not installed');
        }

        // Encode any attachments.  See
        // http://search.ietf.org/internet-drafts/draft-nielsen-dime-soap-00.txt
        // Now we have to DIME encode the message
        $dime = new Net_DIME_Message();
        $msg = $dime->encodeData($xml, SOAP_ENVELOP, null, NET_DIME_TYPE_URI);

        // Add the attachments.
        $c = count($this->_attachments);
        for ($i = 0; $i < $c; $i++) {
            $msg .= $dime->encodeData($this->_attachments[$i]['body'],
                                      $this->_attachments[$i]['content_type'],
                                      $this->_attachments[$i]['cid'],
                                      NET_DIME_TYPE_MEDIA);
        }
        $msg .= $dime->endMessage();

        return $msg;
    }

    function _decodeMimeMessage(&$data, &$headers, &$attachments)
    {
        if (!@include_once 'Mail/mimeDecode.php') {
            return $this->_raiseSoapFault('MIME messages are unsupported, the Mail_Mime package is not installed');
        }

        $params['include_bodies'] = true;
        $params['decode_bodies']  = true;
        $params['decode_headers'] = true;

        // Lame thing to have to do for decoding.
        $decoder =& new Mail_mimeDecode($data);
        $structure = $decoder->decode($params);

        if (isset($structure->body)) {
            $data = $structure->body;
            $headers = $structure->headers;

            return;
        } elseif (isset($structure->parts)) {
            $data = $structure->parts[0]->body;
            $headers = array_merge($structure->headers,
                                   $structure->parts[0]->headers);
            if (count($structure->parts) > 1) {
                $mime_parts = array_splice($structure->parts,1);
                // Prepare the parts for the SOAP parser.

                $c = count($mime_parts);
                for ($i = 0; $i < $c; $i++) {
                    $p =& $mime_parts[$i];
                    if (isset($p->headers['content-location'])) {
                        // TODO: modify location per SwA note section 3
                        // http://www.w3.org/TR/SOAP-attachments
                        $attachments[$p->headers['content-location']] = $p->body;
                    } else {
                        $cid = 'cid:' . substr($p->headers['content-id'], 1, -1);
                        $attachments[$cid] = $p->body;
                    }
                }
            }

            return;
        }

        $this->_raiseSoapFault('Mime parsing error', '', '', 'Server');
    }

    function _decodeDIMEMessage(&$data, &$headers, &$attachments)
    {
        if (!@include_once 'Net/DIME.php') {
            return $this->_raiseSoapFault('DIME messages are unsupported, the Net_DIME package is not installed');
        }

        // This SHOULD be moved to the transport layer, e.g. PHP itself should
        // handle parsing DIME ;)
        $dime =& new Net_DIME_Message();
        $err = $dime->decodeData($data);
        if (PEAR::isError($err)) {
            $this->_raiseSoapFault('Failed to decode the DIME message!', '', '', 'Server');
            return;
        }
        if (strcasecmp($dime->parts[0]['type'], SOAP_ENVELOP) != 0) {
            $this->_raiseSoapFault('DIME record 1 is not a SOAP envelop!', '', '', 'Server');
            return;
        }

        $data = $dime->parts[0]['data'];
        // Fake it for now.
        $headers['content-type'] = 'text/xml';
        $c = count($dime->parts);
        for ($i = 0; $i < $c; $i++) {
            $part =& $dime->parts[$i];
            // We need to handle URI's better.
            $id = strncmp($part['id'], 'cid:', 4)
                ? 'cid:' . $part['id']
                : $part['id'];
            $attachments[$id] = $part['data'];
        }
    }

    /**
     * @deprecated Use setTypeTranslation().
     */
    function __set_type_translation($type, $class = null)
    {
        $this->setTypeTranslation($type, $class);
    }

    /**
     * Explicitly sets the translation for a specific class.
     *
     * Auto translation works for all cases, but opens ANY class in the script
     * to be used as a data type, and may not be desireable.
     *
     * @param string $type   A SOAP type.
     * @param string $class  A PHP class name.
     */
    function setTypeTranslation($type, $class = null)
    {
        $tq = new QName($type);
        if (!$class) {
            $class = $tq->name;
        }
        $this->_type_translation[$type]=$class;
    }

}

/**
 * Class used to handle QNAME values in XML.
 *
 * @access   public
 * @package  SOAP
 * @author   Shane Caraveo <shane@php.net> Conversion to PEAR and updates
 */
class QName
{
    var $name = '';
    var $ns = '';
    var $namespace='';

    function QName($name, $namespace = '')
    {
        if ($name && $name[0] == '{') {
            preg_match('/\{(.*?)\}(.*)/', $name, $m);
            $this->name = $m[2];
            $this->namespace = $m[1];
        } elseif (substr_count($name, ':') == 1) {
            $s = explode(':', $name);
            $s = array_reverse($s);
            $this->name = $s[0];
            $this->ns = $s[1];
            $this->namespace = $namespace;
        } else {
            $this->name = $name;
            $this->namespace = $namespace;
        }

        // A little more magic than should be in a qname.
        $p = strpos($this->name, '[');
        if ($p) {
            // TODO: Need to re-examine this logic later.
            // Chop off [].
            $this->arraySize = explode(',', substr($this->name, $p + 1, -$p - 2));
            $this->arrayInfo = substr($this->name, $p);
            $this->name = substr($this->name, 0, $p);
        }
    }

    function fqn()
    {
        if ($this->namespace) {
            return '{' . $this->namespace . '}' . $this->name;
        } elseif ($this->ns) {
            return $this->ns . ':' . $this->name;
        }
        return $this->name;
    }

}
