<?php
/**
 * This file contains the code for converting values between SOAP and PHP.
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

require_once 'SOAP/Base.php';

/**
 * SOAP::Value
 *
 * This class converts values between PHP and SOAP.
 *
 * Originally based on SOAPx4 by Dietrich Ayala
 * http://dietrich.ganx4.com/soapx4
 *
 * @access  public
 * @package SOAP
 * @author  Shane Caraveo <shane@php.net> Conversion to PEAR and updates
 * @author  Dietrich Ayala <dietrich@ganx4.com> Original Author
 */
class SOAP_Value
{
    /**
     * @var string
     */
    var $value = null;

    /**
     * @var string
     */
    var $name = '';

    /**
     * @var string
     */
    var $type = '';

    /**
     * Namespace
     *
     * @var string
     */
    var $namespace = '';
    var $type_namespace = '';

    var $attributes = array();

    /**
     * @var string
     */
    var $arrayType = '';

    var $options = array();

    var $nqn;
    var $tqn;

    /**
     * Constructor.
     *
     * @param string $name       Name of the SOAP value {namespace}name.
     * @param mixed $type        SOAP value {namespace}type. Determined
     *                           automatically if not set.
     * @param mixed $value       Value to set.
     * @param array $attributes  Attributes.
     */
    function SOAP_Value($name = '', $type = false, $value = null,
                        $attributes = array())
    {
        // Detect type if not passed.
        $this->nqn = new QName($name);
        $this->name = $this->nqn->name;
        $this->namespace = $this->nqn->namespace;
        $this->tqn = new QName($type);
        $this->type = $this->tqn->name;
        $this->type_prefix = $this->tqn->ns;
        $this->type_namespace = $this->tqn->namespace;
        $this->value = $value;
        $this->attributes = $attributes;
    }

    /**
     * Serializes this value.
     *
     * @param SOAP_Base $serializer  A SOAP_Base instance or subclass to
     *                               serialize with.
     *
     * @return string  XML representation of $this.
     */
    function serialize(&$serializer)
    {
        return $serializer->_serializeValue($this->value,
                                            $this->name,
                                            $this->type,
                                            $this->namespace,
                                            $this->type_namespace,
                                            $this->options,
                                            $this->attributes,
                                            $this->arrayType);
    }

}

/**
 * This class converts values between PHP and SOAP. It is a simple wrapper
 * around SOAP_Value, adding support for SOAP actor and mustunderstand
 * parameters.
 *
 * Originally based on SOAPx4 by Dietrich Ayala
 * http://dietrich.ganx4.com/soapx4
 *
 * @access  public
 * @package SOAP
 * @author  Shane Caraveo <shane@php.net> Conversion to PEAR and updates
 * @author  Dietrich Ayala <dietrich@ganx4.com> Original Author
 */
class SOAP_Header extends SOAP_Value
{
    /**
     * Constructor
     *
     * @param string $name             Name of the SOAP value {namespace}name.
     * @param mixed $type              SOAP value {namespace}type. Determined
     *                                 automatically if not set.
     * @param mixed $value             Value to set
     * @param integer $mustunderstand  Zero or one.
     * @param mixed $attributes        Attributes.
     */
    function SOAP_Header($name = '', $type, $value, $mustunderstand = 0,
                         $attributes = array())
    {
        if (!is_array($attributes)) {
            $actor = $attributes;
            $attributes = array();
        }

        parent::SOAP_Value($name, $type, $value, $attributes);

        if (isset($actor)) {
            $this->attributes['SOAP-ENV:actor'] = $actor;
        } elseif (!isset($this->attributes['SOAP-ENV:actor'])) {
            $this->attributes['SOAP-ENV:actor'] = 'http://schemas.xmlsoap.org/soap/actor/next';
        }
        $this->attributes['SOAP-ENV:mustUnderstand'] = (int)$mustunderstand;
    }

}

/**
 * This class handles MIME attachements per W3C's Note on Soap Attachements at
 * http://www.w3.org/TR/SOAP-attachments
 *
 * @access  public
 * @package SOAP
 * @author  Shane Caraveo <shane@php.net> Conversion to PEAR and updates
 */
class SOAP_Attachment extends SOAP_Value
{
    /**
     * Constructor.
     *
     * @param string $name      Name of the SOAP value <value_name>
     * @param string $type      The attachment's MIME type.
     * @param string $filename  The attachment's file name. Ignored if $file
     *                          is provide.
     * @param string $file      The attachment data.
     */
    function SOAP_Attachment($name = '', $type = 'application/octet-stream',
                             $filename, $file = null)
    {
        parent::SOAP_Value($name, null, null);

        $filedata = ($file === null) ? $this->_file2str($filename) : $file;
        $filename = basename($filename);
        if (PEAR::isError($filedata)) {
            $this->options['attachment'] = $filedata;
            return;
        }

        $cid = md5(uniqid(time()));

        $this->attributes['href'] = 'cid:' . $cid; 

        $this->options['attachment'] = array('body' => $filedata,
                                             'disposition' => $filename,
                                             'content_type' => $type,
                                             'encoding' => 'base64',
                                             'cid' => $cid);
    }

    /**
     * Returns the contents of the given file name as string.
     *
     * @access private
     *
     * @param string $file_name  The file location.
     *
     * @return string  The file data or a PEAR_Error.
     */
    function _file2str($file_name)
    {
        if (!is_readable($file_name)) {
            return PEAR::raiseError('File is not readable: ' . $file_name);
        }

        if (function_exists('file_get_contents')) {
            return file_get_contents($file_name);
        }

        if (!$fd = fopen($file_name, 'rb')) {
            return PEAR::raiseError('Could not open ' . $file_name);
        }
        $cont = fread($fd, filesize($file_name));
        fclose($fd);

        return $cont;
    }

}
