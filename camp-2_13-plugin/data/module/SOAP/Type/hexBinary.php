<?php
/**
 * This class provides methods to detect and convert binary data from an to
 * hexadecimal strings.
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
 * @copyright  2003-2007 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @link       http://pear.php.net/package/SOAP
 */
class SOAP_Type_hexBinary {

    function to_bin($value)
    {
        return pack('H' . strlen($value), $value);
    }

    function to_hex($value)
    {
        return bin2hex($value);
    }

    function is_hexbin($value)
    {
        // First see if there are any invalid chars.
        if (!strlen($value) || preg_match('/[^A-Fa-f0-9]/', $value)) {
            return false;
        }

        return strcasecmp($value, SOAP_Type_hexBinary::to_hex(SOAP_Type_hexBinary::to_bin($value))) == 0;
    }

}
