<?php
/**
 * This file contains the code for the SOAP date/time clas.
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
 * @author     Jan Schneider <jan@horde.org>       Maintenance
 * @copyright  2003-2005 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @link       http://pear.php.net/package/SOAP
 */

/**
 * This class converts from and to unix timestamps and ISO 8601 date/time.
 *
 * @access   public
 * @package  SOAP
 * @author   Dietrich Ayala <dietrich@ganx4.com> Original Author
 * @author   Shane Caraveo <shane@php.net>       Port to PEAR and more
 * @author   Jan Schneider <jan@horde.org>       Maintenance
 */
class SOAP_Type_dateTime 
{
    var $_iso8601 =
        '# 1: centuries & years CCYY-
         (-?[0-9]{4})-
         # 2: months MM-
         ([0-9]{2})-
         # 3: days DD
         ([0-9]{2})
         # 4: separator T
         T
         # 5: hours hh:
         ([0-9]{2}):
         # 6: minutes mm:
         ([0-9]{2}):
         # 7: seconds ss.ss...
         ([0-9]{2})(\.[0-9]*)?
         # 8: Z to indicate UTC, -+HH:MM:SS.SS... for local zones
         (Z|[+\-][0-9]{4}|[+\-][0-9]{2}:[0-9]{2})?';

    var $timestamp = -1;

    /**
     * Constructor.
     *
     * @param string|integer $date  The timestamp or ISO 8601 formatted
     *                              date and time this object is going to
     *                              represent.
     */
    function SOAP_Type_dateTime($date = -1)
    {
        if ($date == -1) {
            $this->timestamp = time();
        } elseif (is_int($date)) {
            $this->timestamp = $date;
        } else {
            $this->timestamp = $this->toUnixtime($date);
        }
    }

    /**
     * Alias of {@link SOAP_Type_dateTime::toUTC}.
     */
    function toSOAP($date = NULL)
    {
        return $this->toUTC($date);
    }

    /**
     * Converts this object or a timestamp to an ISO 8601 date/time string.
     *
     * @param integer $timestamp  A unix timestamp
     *
     * @return string  An ISO 8601 formatted date/time string.
     */
    function toString($timestamp = 0)
    {
        if (!$timestamp) {
            $timestamp = $this->timestamp;
        }
        if ($timestamp < 0) {
            return 0;
        }

        //simulate PHP5's P parameter
        $zone = date('O', $timestamp);
        if (strlen($zone) == 5) {
            $zone = substr($zone, 0, 3) . ':' . substr($zone, 3);
        }
        return date('Y-m-d\TH:i:s', $timestamp) . $zone;
    }

    /**
     * Splits a date/time into its components.
     *
     * @param string|integer $datestr  A unix timestamp or ISO 8601 date/time
     *                                 string. If empty, this object is used.
     *
     * @return boolean|array  An array with the date and time components or
     *                        false on failure.
     */
    function _split($datestr)
    {
        if (!$datestr) {
            $datestr = $this->toString();
        } elseif (is_int($datestr)) {
            $datestr = $this->toString($datestr);
        }

        if (preg_match('/' . $this->_iso8601 . '/x', $datestr, $regs)) {
            if (empty($regs[8])) {
                $timestamp = strtotime(sprintf('%04d-%02d-%02d %02d:%02d:%02d',
                                               $regs[1],
                                               $regs[2],
                                               $regs[3],
                                               $regs[4],
                                               $regs[5],
                                               $regs[6]));
                $regs[8] = date('O', $timestamp);
            }
            if ($regs[8] != 'Z') {
                $op = substr($regs[8], 0, 1);
                $h = substr($regs[8], 1, 2);
                if (strstr($regs[8], ':')) {
                    $m = substr($regs[8], 4, 2);
                } else {
                    $m = substr($regs[8], 3, 2);
                }
                if ($op == '+') {
                    $regs[4] = $regs[4] - $h;
                    if ($regs[4] < 0) {
                        $regs[4] += 24;
                    }
                    $regs[5] = $regs[5] - $m;
                    if ($regs[5] < 0) {
                        $regs[5] += 60;
                    }
                } else {
                    $regs[4] = $regs[4] + $h;
                    if ($regs[4] > 23) {
                        $regs[4] -= 24;
                    }
                    $regs[5] = $regs[5] + $m;
                    if ($regs[5] > 59) {
                        $regs[5] -= 60;
                    }
                }
            }
            return $regs;
        }

        return false;
    }

    /**
     * Returns an ISO 8601 formatted UTC date/time string.
     *
     * @param string|integer $datestr  @see SOAP_Type_dateTime::_split
     *
     * @return string  The ISO 8601 formatted UTC date/time string.
     */
    function toUTC($datestr = null)
    {
        $regs = $this->_split($datestr);

        if ($regs) {
            return sprintf('%04d-%02d-%02dT%02d:%02d:%02dZ',
                           $regs[1],
                           $regs[2],
                           $regs[3],
                           $regs[4],
                           $regs[5],
                           $regs[6]);
        }

        return '';
    }

    /**
     * Returns a unix timestamp.
     *
     * @param string|integer $datestr  @see SOAP_Type_dateTime::_split
     *
     * @return integer  The unix timestamp.
     */
    function toUnixtime($datestr = null)
    {
        $regs = $this->_split($datestr);
        if ($regs) {
            return strtotime(sprintf('%04d-%02d-%02d %02d:%02d:%02dZ',
                                     $regs[1],
                                     $regs[2],
                                     $regs[3],
                                     $regs[4],
                                     $regs[5],
                                     $regs[6]));
        }
        return -1;
    }

    /**
     * Compares two dates or this object with a second date.
     *
     * @param string|integer $date1  A unix timestamp or ISO 8601 date/time
     *                               string.
     * @param string|integer $date2  A unix timestamp or ISO 8601 date/time
     *                               string. If empty, this object is used.
     *
     * @return integer  The difference between the first and the second date.
     */
    function compare($date1, $date2 = null)
    {
        if (is_null($date2)) {
            $date2 = $date1;
            $date1 = $this->timestamp;
        }
        if (!is_int($date1)) {
            $date1 = $this->toUnixtime($date1);
        }
        if (!is_int($date2)) {
            $date2 = $this->toUnixtime($date2);
        }

        if ($date1 != -1 && $date2 != -1) {
            return $date1 - $date2;
        }

        return -1;
    }

}
