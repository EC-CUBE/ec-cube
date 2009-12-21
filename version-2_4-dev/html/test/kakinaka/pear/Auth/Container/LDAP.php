<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Storage driver for use against an LDAP server
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
 * @author     Jan Wagner <wagner@netsols.de> 
 * @author     Adam Ashley <aashley@php.net>
 * @author     Hugues Peeters <hugues.peeters@claroline.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Auth
 */

/**
 * Include Auth_Container base class
 */
require_once "Auth/Container.php";
/**
 * Include PEAR package for error handling
 */
require_once "PEAR.php";

/**
 * Storage driver for fetching login data from LDAP
 *
 * This class is heavily based on the DB and File containers. By default it
 * connects to localhost:389 and searches for uid=$username with the scope
 * "sub". If no search base is specified, it will try to determine it via
 * the namingContexts attribute. It takes its parameters in a hash, connects
 * to the ldap server, binds anonymously, searches for the user, and tries
 * to bind as the user with the supplied password. When a group was set, it
 * will look for group membership of the authenticated user. If all goes
 * well the authentication was successful.
 *
 * Parameters:
 *
 * host:        localhost (default), ldap.netsols.de or 127.0.0.1
 * port:        389 (default) or 636 or whereever your server runs
 * url:         ldap://localhost:389/
 *              useful for ldaps://, works only with openldap2 ?
 *              it will be preferred over host and port
 * version:     LDAP version to use, ususally 2 (default) or 3,
 *              must be an integer!
 * referrals:   If set, determines whether the LDAP library automatically
 *              follows referrals returned by LDAP servers or not. Possible
 *              values are true (default) or false.
 * binddn:      If set, searching for user will be done after binding
 *              as this user, if not set the bind will be anonymous.
 *              This is reported to make the container work with MS
 *              Active Directory, but should work with any server that
 *              is configured this way.
 *              This has to be a complete dn for now (basedn and
 *              userdn will not be appended).
 * bindpw:      The password to use for binding with binddn
 * basedn:      the base dn of your server
 * userdn:      gets prepended to basedn when searching for user
 * userscope:   Scope for user searching: one, sub (default), or base
 * userattr:    the user attribute to search for (default: uid)
 * userfilter:  filter that will be added to the search filter
 *              this way: (&(userattr=username)(userfilter))
 *              default: (objectClass=posixAccount)
 * attributes:  array of additional attributes to fetch from entry.
 *              these will added to auth data and can be retrieved via
 *              Auth::getAuthData(). An empty array will fetch all attributes,
 *              array('') will fetch no attributes at all (default)
 *              If you add 'dn' as a value to this array, the users DN that was
 *              used for binding will be added to auth data as well.
 * attrformat:  The returned format of the additional data defined in the
 *              'attributes' option. Two formats are available.
 *              LDAP returns data formatted in a
 *              multidimensional array where each array starts with a
 *              'count' element providing the number of attributes in the
 *              entry, or the number of values for attributes. When set
 *              to this format, the only way to retrieve data from the
 *              Auth object is by calling getAuthData('attributes').
 *              AUTH returns data formatted in a
 *              structure more compliant with other Auth Containers,
 *              where each attribute element can be directly called by
 *              getAuthData() method from Auth.
 *              For compatibily with previous LDAP container versions,
 *              the default format is LDAP.
 * groupdn:     gets prepended to basedn when searching for group
 * groupattr:   the group attribute to search for (default: cn)
 * groupfilter: filter that will be added to the search filter when
 *              searching for a group:
 *              (&(groupattr=group)(memberattr=username)(groupfilter))
 *              default: (objectClass=groupOfUniqueNames)
 * memberattr : the attribute of the group object where the user dn
 *              may be found (default: uniqueMember)
 * memberisdn:  whether the memberattr is the dn of the user (default)
 *              or the value of userattr (usually uid)
 * group:       the name of group to search for
 * groupscope:  Scope for group searching: one, sub (default), or base
 * start_tls:   enable/disable the use of START_TLS encrypted connection 
 *              (default: false)
 * debug:       Enable/Disable debugging output (default: false)
 * try_all:     Whether to try all user accounts returned from the search
 *              or just the first one. (default: false)
 *
 * To use this storage container, you have to use the following syntax:
 *
 * <?php
 * ...
 *
 * $a1 = new Auth("LDAP", array(
 *       'host' => 'localhost',
 *       'port' => '389',
 *       'version' => 3,
 *       'basedn' => 'o=netsols,c=de',
 *       'userattr' => 'uid'
 *       'binddn' => 'cn=admin,o=netsols,c=de',
 *       'bindpw' => 'password'));
 *
 * $a2 = new Auth('LDAP', array(
 *       'url' => 'ldaps://ldap.netsols.de',
 *       'basedn' => 'o=netsols,c=de',
 *       'userscope' => 'one',
 *       'userdn' => 'ou=People',
 *       'groupdn' => 'ou=Groups',
 *       'groupfilter' => '(objectClass=posixGroup)',
 *       'memberattr' => 'memberUid',
 *       'memberisdn' => false,
 *       'group' => 'admin'
 *       ));
 *
 * $a3 = new Auth('LDAP', array(
 *       'host' => 'ldap.netsols.de',
 *       'port' => 389,
 *       'version' => 3,
 *       'referrals' => false,
 *       'basedn' => 'dc=netsols,dc=de',
 *       'binddn' => 'cn=Jan Wagner,cn=Users,dc=netsols,dc=de',
 *       'bindpw' => 'password',
 *       'userattr' => 'samAccountName',
 *       'userfilter' => '(objectClass=user)',
 *       'attributes' => array(''),
 *       'group' => 'testing',
 *       'groupattr' => 'samAccountName',
 *       'groupfilter' => '(objectClass=group)',
 *       'memberattr' => 'member',
 *       'memberisdn' => true,
 *       'groupdn' => 'cn=Users',
 *       'groupscope' => 'one',
 *       'debug' => true);
 *
 * The parameter values have to correspond
 * to the ones for your LDAP server of course.
 *
 * When talking to a Microsoft ActiveDirectory server you have to
 * use 'samaccountname' as the 'userattr' and follow special rules
 * to translate the ActiveDirectory directory names into 'basedn'.
 * The 'basedn' for the default 'Users' folder on an ActiveDirectory
 * server for the ActiveDirectory Domain (which is not related to
 * its DNS name) "win2000.example.org" would be:
 * "CN=Users, DC=win2000, DC=example, DC=org'
 * where every component of the domain name becomes a DC attribute
 * of its own. If you want to use a custom users folder you have to
 * replace "CN=Users" with a sequence of "OU" attributes that specify
 * the path to your custom folder in reverse order.
 * So the ActiveDirectory folder
 *   "win2000.example.org\Custom\Accounts"
 * would become
 *   "OU=Accounts, OU=Custom, DC=win2000, DC=example, DC=org'
 *
 * It seems that binding anonymously to an Active Directory
 * is not allowed, so you have to set binddn and bindpw for
 * user searching.
 * 
 * LDAP Referrals need to be set to false for AD to work sometimes.
 *
 * Example a3 shows a full blown and tested example for connection to 
 * Windows 2000 Active Directory with group mebership checking
 *
 * Note also that if you want an encrypted connection to an MS LDAP 
 * server, then, on your webserver, you must specify 
 *        TLS_REQCERT never
 * in /etc/ldap/ldap.conf or in the webserver user's ~/.ldaprc (which
 * may or may not be read depending on your configuration).
 *
 *
 * @category   Authentication
 * @package    Auth
 * @author     Jan Wagner <wagner@netsols.de>
 * @author     Adam Ashley <aashley@php.net>
 * @author     Hugues Peeters <hugues.peeters@claroline.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8713 $
 * @link       http://pear.php.net/package/Auth
 */
class Auth_Container_LDAP extends Auth_Container
{

    // {{{ properties

    /**
     * Options for the class
     * @var array
     */
    var $options = array();

    /**
     * Connection ID of LDAP Link
     * @var string
     */
    var $conn_id = false;

    // }}}

    // {{{ Auth_Container_LDAP() [constructor]

    /**
     * Constructor of the container class
     *
     * @param  $params, associative hash with host,port,basedn and userattr key
     * @return object Returns an error object if something went wrong
     */
    function Auth_Container_LDAP($params)
    {
        if (false === extension_loaded('ldap')) {
            return PEAR::raiseError('Auth_Container_LDAP: LDAP Extension not loaded',
                    41, PEAR_ERROR_DIE);
        }

        $this->_setDefaults();

        if (is_array($params)) {
            $this->_parseOptions($params);
        }
    }

    // }}}
    // {{{ _prepare()

    /**
     * Prepare LDAP connection
     *
     * This function checks if we have already opened a connection to
     * the LDAP server. If that's not the case, a new connection is opened.
     *
     * @access private
     * @return mixed True or a PEAR error object.
     */
    function _prepare()
    {
        if (!$this->_isValidLink()) {
            $res = $this->_connect();
            if (PEAR::isError($res)) {
                return $res;
            }
        }
        return true;
    }

    // }}}
    // {{{ _connect()

    /**
     * Connect to the LDAP server using the global options
     *
     * @access private
     * @return object  Returns a PEAR error object if an error occurs.
     */
    function _connect()
    {
        // connect
        if (isset($this->options['url']) && $this->options['url'] != '') {
            $this->_debug('Connecting with URL', __LINE__);
            $conn_params = array($this->options['url']);
        } else {
            $this->_debug('Connecting with host:port', __LINE__);
            $conn_params = array($this->options['host'], $this->options['port']);
        }

        if (($this->conn_id = @call_user_func_array('ldap_connect', $conn_params)) === false) {
            return PEAR::raiseError('Auth_Container_LDAP: Could not connect to server.', 41);
        }
        $this->_debug('Successfully connected to server', __LINE__);

        // switch LDAP version
        if (is_numeric($this->options['version']) && $this->options['version'] > 2) {
            $this->_debug("Switching to LDAP version {$this->options['version']}", __LINE__);
            @ldap_set_option($this->conn_id, LDAP_OPT_PROTOCOL_VERSION, $this->options['version']);
        
            // start TLS if available
            if (isset($this->options['start_tls']) && $this->options['start_tls']) {           
                $this->_debug("Starting TLS session", __LINE__);
                if (@ldap_start_tls($this->conn_id) === false) {
                    return PEAR::raiseError('Auth_Container_LDAP: Could not start tls.', 41);
                }
            }
        }

        // switch LDAP referrals
        if (is_bool($this->options['referrals'])) {
          $this->_debug("Switching LDAP referrals to " . (($this->options['referrals']) ? 'true' : 'false'), __LINE__);
          @ldap_set_option($this->conn_id, LDAP_OPT_REFERRALS, $this->options['referrals']);
        }

        // bind with credentials or anonymously
        if (strlen($this->options['binddn']) && strlen($this->options['bindpw'])) {
            $this->_debug('Binding with credentials', __LINE__);
            $bind_params = array($this->conn_id, $this->options['binddn'], $this->options['bindpw']);
        } else {
            $this->_debug('Binding anonymously', __LINE__);
            $bind_params = array($this->conn_id);
        }

        // bind for searching
        if ((@call_user_func_array('ldap_bind', $bind_params)) === false) {
            $this->_debug();
            $this->_disconnect();
            return PEAR::raiseError("Auth_Container_LDAP: Could not bind to LDAP server.", 41);
        }
        $this->_debug('Binding was successful', __LINE__);

        return true;
    }

    // }}}
    // {{{ _disconnect()

    /**
     * Disconnects (unbinds) from ldap server
     *
     * @access private
     */
    function _disconnect()
    {
        if ($this->_isValidLink()) {
            $this->_debug('disconnecting from server');
            @ldap_unbind($this->conn_id);
        }
    }

    // }}}
    // {{{ _getBaseDN()

    /**
     * Tries to find Basedn via namingContext Attribute
     *
     * @access private
     */
    function _getBaseDN()
    {
        $err = $this->_prepare();
        if ($err !== true) {
            return PEAR::raiseError($err->getMessage(), $err->getCode());
        }

        if ($this->options['basedn'] == "" && $this->_isValidLink()) {
            $this->_debug("basedn not set, searching via namingContexts.", __LINE__);

            $result_id = @ldap_read($this->conn_id, "", "(objectclass=*)", array("namingContexts"));

            if (@ldap_count_entries($this->conn_id, $result_id) == 1) {

                $this->_debug("got result for namingContexts", __LINE__);

                $entry_id = @ldap_first_entry($this->conn_id, $result_id);
                $attrs = @ldap_get_attributes($this->conn_id, $entry_id);
                $basedn = $attrs['namingContexts'][0];

                if ($basedn != "") {
                    $this->_debug("result for namingContexts was $basedn", __LINE__);
                    $this->options['basedn'] = $basedn;
                }
            }
            @ldap_free_result($result_id);
        }

        // if base ist still not set, raise error
        if ($this->options['basedn'] == "") {
            return PEAR::raiseError("Auth_Container_LDAP: LDAP search base not specified!", 41);
        }
        return true;
    }

    // }}}
    // {{{ _isValidLink()

    /**
     * determines whether there is a valid ldap conenction or not
     *
     * @accessd private
     * @return boolean
     */
    function _isValidLink()
    {
        if (is_resource($this->conn_id)) {
            if (get_resource_type($this->conn_id) == 'ldap link') {
                return true;
            }
        }
        return false;
    }

    // }}}
    // {{{ _setDefaults()

    /**
     * Set some default options
     *
     * @access private
     */
    function _setDefaults()
    {
        $this->options['url']         = '';
        $this->options['host']        = 'localhost';
        $this->options['port']        = '389';
        $this->options['version']     = 2;
        $this->options['referrals']   = true;
        $this->options['binddn']      = '';
        $this->options['bindpw']      = '';
        $this->options['basedn']      = '';
        $this->options['userdn']      = '';
        $this->options['userscope']   = 'sub';
        $this->options['userattr']    = 'uid';
        $this->options['userfilter']  = '(objectClass=posixAccount)';
        $this->options['attributes']  = array(''); // no attributes
     // $this->options['attrformat']  = 'LDAP'; // returns attribute array as PHP LDAP functions return it
        $this->options['attrformat']  = 'AUTH'; // returns attribute like other Auth containers
        $this->options['group']       = '';
        $this->options['groupdn']     = '';
        $this->options['groupscope']  = 'sub';
        $this->options['groupattr']   = 'cn';
        $this->options['groupfilter'] = '(objectClass=groupOfUniqueNames)';
        $this->options['memberattr']  = 'uniqueMember';
        $this->options['memberisdn']  = true;
        $this->options['start_tls']   = false;
        $this->options['debug']       = false;
        $this->options['try_all']     = false; // Try all user ids returned not just the first one
    }

    // }}}
    // {{{ _parseOptions()

    /**
     * Parse options passed to the container class
     *
     * @access private
     * @param  array
     */
    function _parseOptions($array)
    {
        $array = $this->_setV12OptionsToV13($array);

        foreach ($array as $key => $value) {
            if (array_key_exists($key, $this->options)) {
                if ($key == 'attributes') {
                    if (is_array($value)) {
                        $this->options[$key] = $value;
                    } else {
                        $this->options[$key] = explode(',', $value);
                    }
                } else {
                    $this->options[$key] = $value;
                }
            }
        }
    }

    // }}}
    // {{{ _setV12OptionsToV13()

    /**
     * Adapt deprecated options from Auth 1.2 LDAP to Auth 1.3 LDAP
     * 
     * @author Hugues Peeters <hugues.peeters@claroline.net>
     * @access private
     * @param array
     * @return array
     */
    function _setV12OptionsToV13($array)
    {
        if (isset($array['useroc']))
            $array['userfilter'] = "(objectClass=".$array['useroc'].")";
        if (isset($array['groupoc']))
            $array['groupfilter'] = "(objectClass=".$array['groupoc'].")";
        if (isset($array['scope']))
            $array['userscope'] = $array['scope'];

        return $array;
    }

    // }}}
    // {{{ _scope2function()

    /**
     * Get search function for scope
     *
     * @param  string scope
     * @return string ldap search function
     */
    function _scope2function($scope)
    {
        switch($scope) {
        case 'one':
            $function = 'ldap_list';
            break;
        case 'base':
            $function = 'ldap_read';
            break;
        default:
            $function = 'ldap_search';
            break;
        }
        return $function;
    }

    // }}}
    // {{{ fetchData()

    /**
     * Fetch data from LDAP server
     *
     * Searches the LDAP server for the given username/password
     * combination.  Escapes all LDAP meta characters in username
     * before performing the query.
     *
     * @param  string Username
     * @param  string Password
     * @return boolean
     */
    function fetchData($username, $password)
    {
        $err = $this->_prepare();
        if ($err !== true) {
            return PEAR::raiseError($err->getMessage(), $err->getCode());
        }

        $err = $this->_getBaseDN();
        if ($err !== true) {
            return PEAR::raiseError($err->getMessage(), $err->getCode());
        }

        // UTF8 Encode username for LDAPv3
        if (@ldap_get_option($this->conn_id, LDAP_OPT_PROTOCOL_VERSION, $ver) && $ver == 3) {
            $this->_debug('UTF8 encoding username for LDAPv3', __LINE__);
            $username = utf8_encode($username);
        }

        // make search filter
        $filter = sprintf('(&(%s=%s)%s)',
                          $this->options['userattr'],
                          $this->_quoteFilterString($username),
                          $this->options['userfilter']);

        // make search base dn
        $search_basedn = $this->options['userdn'];
        if ($search_basedn != '' && substr($search_basedn, -1) != ',') {
            $search_basedn .= ',';
        }
        $search_basedn .= $this->options['basedn'];

        // attributes
        $attributes = $this->options['attributes'];

        // make functions params array
        $func_params = array($this->conn_id, $search_basedn, $filter, $attributes);

        // search function to use
        $func_name = $this->_scope2function($this->options['userscope']);

        $this->_debug("Searching with $func_name and filter $filter in $search_basedn", __LINE__);

        // search
        if (($result_id = @call_user_func_array($func_name, $func_params)) === false) {
            $this->_debug('User not found', __LINE__);
        } elseif (@ldap_count_entries($this->conn_id, $result_id) >= 1) { // did we get some possible results?

            $this->_debug('User(s) found', __LINE__);

            $first = true;
            $entry_id = null;

            do {
                
                // then get the user dn
                if ($first) {
                    $entry_id = @ldap_first_entry($this->conn_id, $result_id);
                    $first = false;
                } else {
                    $entry_id = @ldap_next_entry($this->conn_id, $entry_id);
                    if ($entry_id === false)
                        break;
                }
                $user_dn  = @ldap_get_dn($this->conn_id, $entry_id);

                // as the dn is not fetched as an attribute, we save it anyway
                if (is_array($attributes) && in_array('dn', $attributes)) {
                    $this->_debug('Saving DN to AuthData', __LINE__);
                    $this->_auth_obj->setAuthData('dn', $user_dn);
                }
            
                // fetch attributes
                if ($attributes = @ldap_get_attributes($this->conn_id, $entry_id)) {

                    if (is_array($attributes) && isset($attributes['count']) &&
                         $attributes['count'] > 0) {

                        // ldap_get_attributes() returns a specific multi dimensional array
                        // format containing all the attributes and where each array starts
                        // with a 'count' element providing the number of attributes in the
                        // entry, or the number of values for attribute. For compatibility
                        // reasons, it remains the default format returned by LDAP container
                        // setAuthData().
                        // The code below optionally returns attributes in another format,
                        // more compliant with other Auth containers, where each attribute
                        // element are directly set in the 'authData' list. This option is
                        // enabled by setting 'attrformat' to
                        // 'AUTH' in the 'options' array.
                        // eg. $this->options['attrformat'] = 'AUTH'

                        if ( strtoupper($this->options['attrformat']) == 'AUTH' ) {
                            $this->_debug('Saving attributes to Auth data in AUTH format', __LINE__);
                            unset ($attributes['count']);
                            foreach ($attributes as $attributeName => $attributeValue ) {
                                if (is_int($attributeName)) continue;
                                if (is_array($attributeValue) && isset($attributeValue['count'])) {
                                    unset ($attributeValue['count']);
                                }
                                if (count($attributeValue)<=1) $attributeValue = $attributeValue[0];
                                $this->_auth_obj->setAuthData($attributeName, $attributeValue);
                            }
                        }
                        else
                        {
                            $this->_debug('Saving attributes to Auth data in LDAP format', __LINE__);
                            $this->_auth_obj->setAuthData('attributes', $attributes);
                        }
                    }
                }
                @ldap_free_result($result_id);

                // need to catch an empty password as openldap seems to return TRUE
                // if anonymous binding is allowed
                if ($password != "") {
                    $this->_debug("Bind as $user_dn", __LINE__);

                    // try binding as this user with the supplied password
                    if (@ldap_bind($this->conn_id, $user_dn, $password)) {
                        $this->_debug('Bind successful', __LINE__);

                        // check group if appropiate
                        if (strlen($this->options['group'])) {
                            // decide whether memberattr value is a dn or the username
                            $this->_debug('Checking group membership', __LINE__);
                            $return = $this->checkGroup(($this->options['memberisdn']) ? $user_dn : $username);
                            $this->_disconnect();
                            return $return;
                        } else {
                            $this->_debug('Authenticated', __LINE__);
                            $this->_disconnect();
                            return true; // user authenticated
                        } // checkGroup
                    } // bind
                } // non-empty password
            } while ($this->options['try_all'] == true); // interate through entries
        } // get results
        // default
        $this->_debug('NOT authenticated!', __LINE__);
        $this->_disconnect();
        return false;
    }

    // }}}
    // {{{ checkGroup()

    /**
     * Validate group membership
     *
     * Searches the LDAP server for group membership of the
     * supplied username.  Quotes all LDAP filter meta characters in
     * the user name before querying the LDAP server.
     *
     * @param  string Distinguished Name of the authenticated User
     * @return boolean
     */
    function checkGroup($user)
    {
        $err = $this->_prepare();
        if ($err !== true) {
            return PEAR::raiseError($err->getMessage(), $err->getCode());
        }

        // make filter
        $filter = sprintf('(&(%s=%s)(%s=%s)%s)',
                          $this->options['groupattr'],
                          $this->options['group'],
                          $this->options['memberattr'],
                          $this->_quoteFilterString($user),
                          $this->options['groupfilter']);

        // make search base dn
        $search_basedn = $this->options['groupdn'];
        if ($search_basedn != '' && substr($search_basedn, -1) != ',') {
            $search_basedn .= ',';
        }
        $search_basedn .= $this->options['basedn'];

        $func_params = array($this->conn_id, $search_basedn, $filter,
                             array($this->options['memberattr']));
        $func_name = $this->_scope2function($this->options['groupscope']);

        $this->_debug("Searching with $func_name and filter $filter in $search_basedn", __LINE__);

        // search
        if (($result_id = @call_user_func_array($func_name, $func_params)) != false) {
            if (@ldap_count_entries($this->conn_id, $result_id) == 1) {
                @ldap_free_result($result_id);
                $this->_debug('User is member of group', __LINE__);
                return true;
            }
        }
        // default
        $this->_debug('User is NOT member of group', __LINE__);
        return false;
    }

    // }}}
    // {{{ _debug()

    /**
     * Outputs debugging messages
     *
     * @access private
     * @param string Debugging Message
     * @param integer Line number
     */
    function _debug($msg = '', $line = 0)
    {
        if ($this->options['debug'] == true) {
            if ($msg == '' && $this->_isValidLink()) {
                $msg = 'LDAP_Error: ' . @ldap_err2str(@ldap_errno($this->_conn_id));
            }
            print("$line: $msg <br />");
        }
    }

    // }}}
    // {{{ _quoteFilterString()

    /**
     * Escapes LDAP filter special characters as defined in RFC 2254.
     *
     * @access private
     * @param string Filter String
     */
    function _quoteFilterString($filter_str)
    {
        $metas        = array(  '\\',  '*',  '(',  ')',   "\x00");
        $quoted_metas = array('\\\\', '\*', '\(', '\)', "\\\x00");
        return str_replace($metas, $quoted_metas, $filter_str);
    }

    // }}}

}

?>
