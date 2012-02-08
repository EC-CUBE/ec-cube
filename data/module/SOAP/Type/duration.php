<?php
/**
 * This file contains the code for the SOAP_Type_duration class.
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

/**
 * This is only an aproximation of duration, more work still to do.  See the
 * schema url for more info on duration.
 *
 * http://www.w3.org/TR/xmlschema-2/
 *
 * [Definition:]   duration represents a duration of time. The value space of
 * duration is a six-dimensional space where the coordinates designate the
 * Gregorian year, month, day, hour, minute, and second components
 * defined in  5.5.3.2 of [ISO 8601], respectively. These components are
 * ordered in their significance by their order of appearance i.e. as year,
 * month, day, hour, minute, and second.
 *
 * 3.2.6.1 Lexical representation
 * The lexical representation for duration is the [ISO 8601] extended
 * format PnYn MnDTnH nMnS, where nY represents the number of
 * years, nM the number of months, nD the number of days, 'T' is the
 * date/time separator, nH the number of hours, nM the number of
 * minutes and nS the number of seconds. The number of seconds
 * can include decimal digits to arbitrary precision.
 *
 * The values of the Year, Month, Day, Hour and Minutes components
 * are not restricted but allow an arbitrary integer. Similarly, the
 * value of the Seconds component allows an arbitrary decimal.
 * Thus, the lexical representation of duration does not follow the
 * alternative format of  5.5.3.2.1 of [ISO 8601].
 *
 * An optional preceding minus sign ('-') is allowed, to indicate a
 * negative duration. If the sign is omitted a positive duration is
 * indicated. See also ISO 8601 Date and Time Formats (D).
 *
 * For example, to indicate a duration of 1 year, 2 months, 3 days,
 * 10 hours, and 30 minutes, one would write: P1Y2M3DT10H30M.
 * One could also indicate a duration of minus 120 days as: -P120D.
 *
 * Reduced precision and truncated representations of this format
 * are allowed provided they conform to the following:
 *
 * If the number of years, months, days, hours, minutes, or seconds
 * in any expression equals zero, the number and its corresponding
 * designator *may* be omitted. However, at least one number and
 * its designator *must* be present.
 * The seconds part *may* have a decimal fraction.
 * The designator 'T' shall be absent if all of the time items are absent.
 * The designator 'P' must always be present.
 * For example, P1347Y, P1347M and P1Y2MT2H are all allowed; P0Y1347M
 * and P0Y1347M0D are allowed. P-1347M is not allowed although -P1347M
 * is allowed. P1Y2MT is not allowed.
 *
 * @access   public
 * @package  SOAP
 * @author   Shane Caraveo <shane@php.net> Port to PEAR and more
 * @author   Jan Schneider <jan@horde.org> Maintenance
 * @todo     Figure out best aproximation for year and month conversion to
 *           seconds
 */
class SOAP_Type_duration
{
    // format PnYnMnDTnHnMnS
    function unix_to_duration($seconds)
    {
        return SOAP_Type_duration::getduration($seconds);
    }

    function mod($a, $b, &$d, &$r)
    {
        $d = floor($a / $b);
        $r = $a % $b;
    }

    function getduration($seconds)
    {
        $neg = '';
        if ($seconds < 0) {
            $neg = '-';
            $seconds = $seconds * -1;
        }

        $_mi = 60;
        $_h = $_mi * 60;
        $_d = $_h * 24;
        // XXX how do we properly handle month and year values?
        $_m = $_d * 30;
        $_y = $_d * 365;

        SOAP_Type_duration::mod($seconds, $_y, $y, $seconds);
        SOAP_Type_duration::mod($seconds, $_m, $m, $seconds);
        SOAP_Type_duration::mod($seconds, $_d, $d, $seconds);
        SOAP_Type_duration::mod($seconds, $_h, $h, $seconds);
        SOAP_Type_duration::mod($seconds, $_mi, $mi, $s);

        $duration = $neg.'P';
        if ($y) $duration .= $y.'Y';
        if ($m) $duration .= $m.'M';
        if ($d) $duration .= $d.'D';
        if ($h || $mi || $s) $duration .='T';
        if ($h) $duration .= $h.'H';
        if ($mi) $duration .= $mi.'M';
        if ($s) $duration .= $s.'S';
        if ($duration == 'P' || $duration == '-P') $duration = 'PT0S';

        return $duration;
    }

    function mkduration($n, $Y, $Mo, $D, $H, $Mi, $S)
    {
        $_mi = 60;
        $_h = $_mi * 60;
        $_d = $_h * 24;
        // XXX how do we properly handle month and year values?
        $_m = $_d * 30;
        $_y = $_d * 365;

        $sec = $Y * $_y + $Mo * $_m + $D * $_d + $H * $_h + $Mi * $_mi + $S;
        if ($n == '-') $sec = $sec * -1;

        return $sec;
    }

    function duration_to_unix($duration)
    {
        if (preg_match('/(-)?P([0-9]+Y)?([0-9]+M)?([0-9]+D)?T?([0-9]+H)?([0-9]+M)?([0-9]+S)?/', $duration, $regs)) {
            return SOAP_Type_duration::mkduration($regs[1], $regs[2], $regs[3], $regs[4], $regs[5], $regs[6], $regs[7]);
        }
        return false;
    }

    function is_duration($duration)
    {
        return preg_match('/(-)?P([0-9]+Y)?([0-9]+M)?([0-9]+D)?T?([0-9]+H)?([0-9]+M)?([0-9]+S)?/', $duration, $regs);
    }

    function _test($time)
    {
        if (SOAP_Type_duration::is_duration($time)) {
            $t = SOAP_Type_duration::duration_to_unix($time);
            echo "Duration: $time is ".$t." seconds\n";
        } else {
            $t = SOAP_Type_duration::unix_to_duration($time);
            echo "Seconds: $time is ".$t." duration\n";
        }
        return $t;
    }

    function add($d1, $d2)
    {
        $s1 = SOAP_Type_duration::duration_to_unix($d1);
        $s2 = SOAP_Type_duration::duration_to_unix($d2);
        return SOAP_Type_duration::unix_to_duration($s1 + $s2);
    }

    function subtract($d1, $d2)
    {
        $s1 = SOAP_Type_duration::duration_to_unix($d1);
        $s2 = SOAP_Type_duration::duration_to_unix($d2);
        return SOAP_Type_duration::unix_to_duration($s1 - $s2);
    }

}

/* Tests. */
$t = SOAP_Type_duration::_test('P1Y2M3DT10H30M');
SOAP_Type_duration::_test($t);
$t = SOAP_Type_duration::_test('-P120D');
SOAP_Type_duration::_test($t);

/* Duration since 1970. */
$t = SOAP_Type_duration::_test(time());
SOAP_Type_duration::_test($t);

echo 'Add should be PT0S: ' . SOAP_Type_duration::add('-P120D','P4M') . "\n";
echo 'Subtract should be PT0S: ' . SOAP_Type_duration::subtract('P120D','P4M') . "\n";
