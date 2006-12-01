<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File::Passwd
 * 
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   FileFormats
 * @package    File_Passwd
 * @author     Michael Wallner <mike@php.net>
 * @copyright  2003-2005 Michael Wallner
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/File_Passwd
 */

/**
* Requires PEAR.
*/
//require_once 'PEAR.php';

/**
* Encryption constants.
*/
// SHA encryption.
define('FILE_PASSWD_SHA',   'sha');
// MD5 encryption
define('FILE_PASSWD_MD5',   'md5');
// DES encryption
define('FILE_PASSWD_DES',   'des');
// NT hash encryption.
define('FILE_PASSWD_NT',    'nt');
// LM hash encryption.
define('FILE_PASSWD_LM',    'lm');
// PLAIN (no encryption)
define('FILE_PASSWD_PLAIN', 'plain');

/**
* Error constants.
*/
// Undefined error.
define('FILE_PASSWD_E_UNDEFINED',                   0);
// Invalid file format.
define('FILE_PASSWD_E_INVALID_FORMAT',              1);
define('FILE_PASSWD_E_INVALID_FORMAT_STR',          'Passwd file has invalid format.');
// Invalid extra property.
define('FILE_PASSWD_E_INVALID_PROPERTY',            2);
define('FILE_PASSWD_E_INVALID_PROPERTY_STR',        'Invalid property \'%s\'.');
// Invalid characters.
define('FILE_PASSWD_E_INVALID_CHARS',               3);
define('FILE_PASSWD_E_INVALID_CHARS_STR',           '%s\'%s\' contains illegal characters.');
// Invalid encryption mode.
define('FILE_PASSWD_E_INVALID_ENC_MODE',            4);
define('FILE_PASSWD_E_INVALID_ENC_MODE_STR',        'Encryption mode \'%s\' not supported.');
// Exists already.
define('FILE_PASSWD_E_EXISTS_ALREADY',              5);
define('FILE_PASSWD_E_EXISTS_ALREADY_STR',          '%s\'%s\' already exists.');
// Exists not.
define('FILE_PASSWD_E_EXISTS_NOT',                  6);
define('FILE_PASSWD_E_EXISTS_NOT_STR',              '%s\'%s\' doesn\'t exist.');
// User not in group.
define('FILE_PASSWD_E_USER_NOT_IN_GROUP',           7);
define('FILE_PASSWD_E_USER_NOT_IN_GROUP_STR',       'User \'%s\' doesn\'t exist in group \'%s\'.');
// User not in realm.
define('FILE_PASSWD_E_USER_NOT_IN_REALM',           8);
define('FILE_PASSWD_E_USER_NOT_IN_REALM_STR',       'User \'%s\' doesn\'t exist in realm \'%s\'.');
// Parameter must be of type array.
define('FILE_PASSWD_E_PARAM_MUST_BE_ARRAY',         9);
define('FILE_PASSWD_E_PARAM_MUST_BE_ARRAY_STR',     'Parameter %s must be of type array.');
// Method not implemented.
define('FILE_PASSWD_E_METHOD_NOT_IMPLEMENTED',      10);
define('FILE_PASSWD_E_METHOD_NOT_IMPLEMENTED_STR',  'Method \'%s()\' not implemented.');
// Directory couldn't be created.
define('FILE_PASSWD_E_DIR_NOT_CREATED',             11);
define('FILE_PASSWD_E_DIR_NOT_CREATED_STR',         'Couldn\'t create directory \'%s\'.');
// File couldn't be opened.
define('FILE_PASSWD_E_FILE_NOT_OPENED',             12);
define('FILE_PASSWD_E_FILE_NOT_OPENED_STR',         'Couldn\'t open file \'%s\'.');
// File coudn't be locked.
define('FILE_PASSWD_E_FILE_NOT_LOCKED',             13);
define('FILE_PASSWD_E_FILE_NOT_LOCKED_STR',         'Couldn\'t lock file \'%s\'.');
// File couldn't be unlocked.
define('FILE_PASSWD_E_FILE_NOT_UNLOCKED',           14);
define('FILE_PASSWD_E_FILE_NOT_UNLOCKED_STR',       'Couldn\'t unlock file.');
// File couldn't be closed.
define('FILE_PASSWD_E_FILE_NOT_CLOSED',             15);
define('FILE_PASSWD_E_FILE_NOT_CLOSED_STR',         'Couldn\'t close file.');

/**
* Allowed 64 chars for salts
*/
$GLOBALS['_FILE_PASSWD_64'] =
    './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

/**
* The package File_Passwd provides classes and methods 
* to handle many different kinds of passwd files.
* 
* The File_Passwd class in certain is a factory container for its special 
* purpose extension classes, each handling a specific passwd file format.
* It also provides a static method for reasonable fast user authentication.
* Beside that it offers some encryption methods used by the extensions.
*
* @author       Michael Wallner <mike@php.net>
* @version      $Revision$
* 
* Usage Example:
* <code>
*  $passwd = &File_Passwd::factory('Unix');
* </code>
*/
class File_Passwd
{
    /**
    * Get API version
    *
    * @static
    * @access   public
    * @return   string          API version
    */
    function apiVersion()
    {
    	return '1.0.0';
    }

    /**
    * Generate salt
    *
    * @access   public
    * @return   mixed
    * @param    int     $length     salt length
    * @return   string  the salt
    */
    function salt($length = 2)
    {
        $salt = '';
        $length = (int) $length;
        $length < 2 && $length = 2;
        for($i = 0; $i < $length; $i++) {
            $salt .= $GLOBALS['_FILE_PASSWD_64'][rand(0, 63)];
        }
        return $salt;
    }

    /**
    * No encryption (plaintext)
    *
    * @access   public
    * @return   string  plaintext input
    * @param    string  plaintext passwd
    */
    function crypt_plain($plain)
    {
        return $plain;
    }
    
    /**
    * DES encryption
    *
    * @static
    * @access   public
    * @return   string  crypted text
    * @param    string  $plain  plaintext to encrypt
    * @param    string  $salt   the salt to use for encryption (2 chars)
    */
    function crypt_des($plain, $salt = null)
    {
        (is_null($salt) || strlen($salt) < 2) && $salt = File_Passwd::salt(2);
        return crypt($plain, $salt);
    }
    
    /**
    * MD5 encryption
    *
    * @static
    * @access   public
    * @return   string  crypted text
    * @param    string  $plain  plaintext to encrypt
    * @param    string  $salt   the salt to use for encryption 
    *                           (>2 chars starting with $1$)
    */
    function crypt_md5($plain, $salt = null)
    {
        if (
            is_null($salt) || 
            strlen($salt) < 3 || 
            !preg_match('/^\$1\$/', $salt))
        {
            $salt = '$1$' . File_Passwd::salt(8);
        }
        return crypt($plain, $salt);
    }
    
    /**
    * SHA1 encryption
    *
    * Returns a PEAR_Error if sha1() is not available (PHP<4.3).
    * 
    * @static
    * @throws   PEAR_Error
    * @access   public
    * @return   mixed   crypted string or PEAR_Error
    * @param    string  $plain  plaintext to encrypt
    */
    function crypt_sha($plain)
    {
        if (!function_exists('sha1')) {
            return PEAR::raiseError(
                'SHA1 encryption is not available (PHP < 4.3).',
                FILE_PASSWD_E_INVALID_ENC_MODE
            );
        }
        $hash = PEAR_ZE2 ? sha1($plain, true) : pack('H40', sha1($plain));
        return '{SHA}' . base64_encode($hash);

    }
        
    /**
    * APR compatible MD5 encryption
    *
    * @access   public
    * @return   mixed
    * @param    string  $plain  plaintext to crypt
    * @param    string  $salt   the salt to use for encryption
    */
    function crypt_apr_md5($plain, $salt = null)
    {
        if (is_null($salt)) {
            $salt = File_Passwd::salt(8);
        } elseif (preg_match('/^\$apr1\$/', $salt)) {
            $salt = preg_replace('/^\$apr1\$([^$]+)\$.*/', '\\1', $salt);
        } else {
            $salt = substr($salt, 0,8);
        }
        
        $length  = strlen($plain);
        $context = $plain . '$apr1$' . $salt;
        
        if (PEAR_ZE2) {
            $binary = md5($plain . $salt . $plain, true);
        } else {
            $binary = pack('H32', md5($plain . $salt . $plain));
        }
        
        for ($i = $length; $i > 0; $i -= 16) {
            $context .= substr($binary, 0, min(16 , $i));
        }
        for ( $i = $length; $i > 0; $i >>= 1) {
            $context .= ($i & 1) ? chr(0) : $plain[0];
        }
        
        $binary = PEAR_ZE2 ? md5($context, true) : pack('H32', md5($context));
        
        for($i = 0; $i < 1000; $i++) {
            $new = ($i & 1) ? $plain : $binary;
            if ($i % 3) {
                $new .= $salt;
            }
            if ($i % 7) {
                $new .= $plain;
            }
            $new .= ($i & 1) ? $binary : $plain;
            $binary = PEAR_ZE2 ? md5($new, true) : pack('H32', md5($new));
        }
        
        $p = array();
        for ($i = 0; $i < 5; $i++) {
            $k = $i + 6;
            $j = $i + 12;
            if ($j == 16) {
                $j = 5;
            }
            $p[] = File_Passwd::_64(
                (ord($binary[$i]) << 16) |
                (ord($binary[$k]) << 8) |
                (ord($binary[$j])),
                5
            );
        }
        
        return 
            '$apr1$' . $salt . '$' . implode($p) . 
            File_Passwd::_64(ord($binary[11]), 3);
    }

    /**
    * Convert hexadecimal string to binary data
    *
    * @static
    * @access   private
    * @return   mixed
    * @param    string  $hex
    */
    function _hexbin($hex)
    {
        $rs = '';
        $ln = strlen($hex);
        for($i = 0; $i < $ln; $i += 2) {
            $rs .= chr(hexdec($hex{$i} . $hex{$i+1}));
        }
        return $rs;
    }
    
    /**
    * Convert to allowed 64 characters for encryption
    *
    * @static
    * @access   private
    * @return   string
    * @param    string  $value
    * @param    int     $count
    */
    function _64($value, $count)
    {
        $result = '';
        while(--$count) {
            $result .= $GLOBALS['_FILE_PASSWD_64'][$value & 0x3f];
            $value >>= 6;
        }
        return $result;
    }

    /**
    * Factory for new extensions
    * 
    * o Unix        for standard Unix passwd files
    * o CVS         for CVS pserver passwd files
    * o SMB         for SMB server passwd files
    * o Authbasic   for AuthUserFiles
    * o Authdigest  for AuthDigestFiles
    * o Custom      for custom formatted passwd files
    * 
    * Returns a PEAR_Error if the desired class/file couldn't be loaded.
    * 
    * @static   use &File_Passwd::factory() for instantiating your passwd object
    * 
    * @throws   PEAR_Error
    * @access   public
    * @return   object    File_Passwd_$class - desired Passwd object
    * @param    string    $class the desired subclass of File_Passwd
    */
    function &factory($class)
    {
        $class = ucFirst(strToLower($class));
        if (!@include_once "File/Passwd/$class.php") {
            return PEAR::raiseError("Couldn't load file Passwd/$class.php", 0);
        }
        $class = 'File_Passwd_'.$class;
        if (!class_exists($class)) {
            return PEAR::raiseError("Couldn't load class $class.", 0);
        }
        $instance = &new $class();
        return $instance;
    }
    
    /**
    * Fast authentication of a certain user
    * 
    * Though this approach should be reasonable fast, it is NOT
    * with APR compatible MD5 encryption used for htpasswd style
    * password files encrypted in MD5. Generating one MD5 password
    * takes about 0.3 seconds!
    * 
    * Returns a PEAR_Error if:
    *   o file doesn't exist
    *   o file couldn't be opened in read mode
    *   o file couldn't be locked exclusively
    *   o file couldn't be unlocked (only if auth fails)
    *   o file couldn't be closed (only if auth fails)
    *   o invalid <var>$type</var> was provided
    *   o invalid <var>$opt</var> was provided
    * 
    * Depending on <var>$type</var>, <var>$opt</var> should be:
    *   o Smb:          encryption method (NT or LM)
    *   o Unix:         encryption method (des or md5)
    *   o Authbasic:    encryption method (des, sha or md5)
    *   o Authdigest:   the realm the user is in
    *   o Cvs:          n/a (empty)
    *   o Custom:       array of 2 elements: encryption function and delimiter
    * 
    * @static   call this method statically for a reasonable fast authentication
    * 
    * @throws   PEAR_Error
    * @access   public
    * @return   return      mixed   true if authenticated, 
    *                               false if not or PEAR_error
    * 
    * @param    string      $type   Unix, Cvs, Smb, Authbasic or Authdigest
    * @param    string      $file   path to passwd file
    * @param    string      $user   the user to authenticate
    * @param    string      $pass   the plaintext password
    * @param    mixed       $opt    o Smb:          NT or LM
    *                               o Unix:         des or md5
    *                               o Authbasic     des, sha or md5
    *                               o Authdigest    realm the user is in
    *                               o Custom        encryption function and
    *                                               delimiter character
    */
    function staticAuth($type, $file, $user, $pass, $opt = '')
    {
        $type = ucFirst(strToLower($type));
        if (!@include_once "File/Passwd/$type.php") {
            return PEAR::raiseError("Couldn't load file Passwd/$type.php", 0);
        }
        $func = array('File_Passwd_' . $type, 'staticAuth');
        return call_user_func($func, $file, $user, $pass, $opt);
    }
}
?>
