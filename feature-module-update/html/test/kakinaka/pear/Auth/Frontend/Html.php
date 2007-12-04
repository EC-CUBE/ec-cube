<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Standard Html Login form
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
 * @author     Martin Jansen <mj@php.net>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Auth
 * @since      File available since Release 1.3.0
 */

/**
 * Standard Html Login form
 * 
 * @category   Authentication
 * @package    Auth
 * @author     Yavor Shahpasov <yavo@netsmart.com.cy>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8715 $
 * @link       http://pear.php.net/package/Auth
 * @since      Class available since Release 1.3.0
 */
class Auth_Frontend_Html {
    
    // {{{ render()

    /**
     * Displays the login form
     *
     * @param object The calling auth instance
     * @param string The previously used username
     * @return void
     */
    function render(&$caller, $username = '') {
        $loginOnClick = 'return true;';
        
        // Try To Use Challene response
        // TODO javascript might need some improvement for work on other browsers
        if($caller->advancedsecurity && $caller->storage->supportsChallengeResponse() ) {

            // Init the secret cookie
            $caller->session['loginchallenege'] = md5(microtime());

            print "\n";
            print '<script language="JavaScript">'."\n";

            include 'Auth/Frontend/md5.js';

            print "\n";
            print ' function securePassword() { '."\n";
            print '   var pass = document.getElementById(\''.$caller->getPostPasswordField().'\');'."\n";
            print '   var secret = document.getElementById(\'authsecret\')'."\n";
            //print '   alert(pass);alert(secret); '."\n";

            // If using md5 for password storage md5 the password before 
            // we hash it with the secret
            // print '   alert(pass.value);';
            if ($caller->storage->getCryptType() == 'md5' ) {
                print '   pass.value = hex_md5(pass.value); '."\n";
                #print '   alert(pass.value);';
            }

            print '   pass.value = hex_md5(pass.value+\''.$caller->session['loginchallenege'].'\'); '."\n";
            // print '   alert(pass.value);';
            print '   secret.value = 1;'."\n";
            print '   var doLogin = document.getElementById(\'doLogin\')'."\n";
            print '   doLogin.disabled = true;'."\n";
            print '   return true;';
            print ' } '."\n";
            print '</script>'."\n";;
            print "\n";

            $loginOnClick = ' return securePassword(); ';
        }

        print '<center>'."\n";

        $status = '';
        if (!empty($caller->status) && $caller->status == AUTH_EXPIRED) {
            $status = '<i>Your session has expired. Please login again!</i>'."\n";
        } else if (!empty($caller->status) && $caller->status == AUTH_IDLED) {
            $status = '<i>You have been idle for too long. Please login again!</i>'."\n";
        } else if (!empty ($caller->status) && $caller->status == AUTH_WRONG_LOGIN) {
            $status = '<i>Wrong login data!</i>'."\n";
        } else if (!empty ($caller->status) && $caller->status == AUTH_SECURITY_BREACH) {
            $status = '<i>Security problem detected. </i>'."\n";
        }
        
        print '<form method="post" action="'.$caller->server['PHP_SELF'].'" '
            .'onSubmit="'.$loginOnClick.'">'."\n";
        print '<table border="0" cellpadding="2" cellspacing="0" '
            .'summary="login form" align="center" >'."\n";
        print '<tr>'."\n";
        print '    <td colspan="2" bgcolor="#eeeeee"><strong>Login </strong>'
            .$status.'</td>'."\n";
        print '</tr>'."\n";
        print '<tr>'."\n";
        print '    <td>Username:</td>'."\n";
        print '    <td><input type="text" id="'.$caller->getPostUsernameField()
            .'" name="'.$caller->getPostUsernameField().'" value="' . $username 
            .'" /></td>'."\n";
        print '</tr>'."\n";
        print '<tr>'."\n";
        print '    <td>Password:</td>'."\n";
        print '    <td><input type="password" id="'.$caller->getPostPasswordField()
            .'" name="'.$caller->getPostPasswordField().'" /></td>'."\n";
        print '</tr>'."\n";
        print '<tr>'."\n";
        
        //onClick=" '.$loginOnClick.' "
        print '    <td colspan="2" bgcolor="#eeeeee"><input value="Login" '
            .'id="doLogin" name="doLogin" type="submit" /></td>'."\n";
        print '</tr>'."\n";
        print '</table>'."\n";

        // Might be a good idea to make the variable name variable 
        print '<input type="hidden" id="authsecret" name="authsecret" value="" />';
        print '</form>'."\n";
        print '</center>'."\n";
    }

    // }}}
    
}

?>
