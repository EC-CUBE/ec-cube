<?php
// +-----------------------------------------------------------------------+ 
// | Copyright (c) 2002-2003 Richard Heyes                                 | 
// | All rights reserved.                                                  | 
// |                                                                       | 
// | Redistribution and use in source and binary forms, with or without    | 
// | modification, are permitted provided that the following conditions    | 
// | are met:                                                              | 
// |                                                                       | 
// | o Redistributions of source code must retain the above copyright      | 
// |   notice, this list of conditions and the following disclaimer.       | 
// | o Redistributions in binary form must reproduce the above copyright   | 
// |   notice, this list of conditions and the following disclaimer in the | 
// |   documentation and/or other materials provided with the distribution.| 
// | o The names of the authors may not be used to endorse or promote      | 
// |   products derived from this software without specific prior written  | 
// |   permission.                                                         | 
// |                                                                       | 
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS   | 
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT     | 
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR | 
// | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT  | 
// | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, | 
// | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT      | 
// | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, | 
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY | 
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT   | 
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE | 
// | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.  | 
// |                                                                       | 
// +-----------------------------------------------------------------------+ 
// | Author: Richard Heyes <richard@php.net>                               | 
// +-----------------------------------------------------------------------+ 
// 
// $Id: SASL.php,v 1.4 2003/02/21 16:07:17 mj Exp $

/**
* Client implementation of various SASL mechanisms 
*
* @author  Richard Heyes <richard@php.net>
* @access  public
* @version 1.0
* @package Auth_SASL
*/

require_once('../PEAR.php');

class Auth_SASL
{
    /**
    * Factory class. Returns an object of the request
    * type.
    *
    * @param string $type One of: Anonymous
    *                             Plain
    *                             CramMD5
    *                             DigestMD5
    *                     Types are not case sensitive
    */
    function &factory($type)
    {
        switch (strtolower($type)) {
            case 'anonymous':
                $filename  = 'Auth/SASL/Anonymous.php';
                $classname = 'Auth_SASL_Anonymous';
                break;

            case 'login':
                $filename  = 'Auth/SASL/Login.php';
                $classname = 'Auth_SASL_Login';
                break;

            case 'plain':
                $filename  = 'Auth/SASL/Plain.php';
                $classname = 'Auth_SASL_Plain';
                break;

            case 'crammd5':
                $filename  = 'Auth/SASL/CramMD5.php';
                $classname = 'Auth_SASL_CramMD5';
                break;

            case 'digestmd5':
                $filename  = 'Auth/SASL/DigestMD5.php';
                $classname = 'Auth_SASL_DigestMD5';
                break;

            default:
                return PEAR::raiseError('Invalid SASL mechanism type');
                break;
        }

        require_once($filename);
        return new $classname();
    }
}

?>