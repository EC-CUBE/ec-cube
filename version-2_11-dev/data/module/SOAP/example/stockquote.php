<html><body>
<?
/**
 * Stockquote client.
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
 * @author     Shane Caraveo <Shane@Caraveo.com>   Port to PEAR and more
 * @author     Jan Schneider <jan@horde.org>       Maintenance
 * @copyright  2003-2007 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @link       http://pear.php.net/package/SOAP
 */

/* Include SOAP_Client class. */
require_once 'SOAP/Client.php';

echo '<br><strong>wsdl:</strong>';
$wsdl = new SOAP_WSDL('http://services.xmethods.net/soap/urn:xmethods-delayed-quotes.wsdl');
$soapclient = $wsdl->getProxy();
$ret = $soapclient->call('getQuote', array('symbol' => 'ibm'));
print_r($ret);

if (extension_loaded('overload')) {
	echo '<br><strong>overloaded:</strong>';
	$ret = $soapclient->getQuote('ibm');
	print_r($ret);
}

echo '<br><strong>non wsdl:</strong>';
$soapclient = new SOAP_Client('http://services.xmethods.net:80/soap');
$namespace = 'urn:xmethods-delayed-quotes';
/* Some SOAP servers require a Soapaction HTTP header.  PEAR::SOAP does not
 * use them in any way, other to send them if you supply them.  soapaction is
 * deprecated in later SOAP versions. */
$soapaction = 'urn:xmethods-delayed-quotes#getQuote';
$ret = $soapclient->call('getQuote',
                         array('symbol' => 'ibm'),
                         $namespace,
                         $soapaction);
print_r($ret);

?>
</html></body>
