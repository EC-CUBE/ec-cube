Description
===========

This cookbook defines acceptance tests for Apache2. It includes:

* A `features` sub-directory where the Cucumber features for the webserver
  are defined.
* Recipes that configure individual modules for use in order to be tested.

Requirements
============

## Cookbooks:

This cookbook depends on the `apache2` cookbook. It also relies on the `yum`
cookbook in order to add the EPEL repository on RHEL-derived distributions.

## Platforms:

* Ubuntu
* CentOS

Attributes
==========

* `node['apache_test']['auth_username']` - The username of the user for testing
  authentication and authorization.
* `node['apache_test']['auth_password']` - The password of the user for testing
  authentication and authorization.
* `node['apache_test']['cache_expiry_seconds']` - The cache expiry time in
  seconds.
* `node['apache_test']['app_dir']` - The local directory where test applications
  will be deployed.
* `node['apache_test']['cgi_dir']` - The local directory where CGI applications
  will be deployed.
* `node['apache_test']['root_dir']` - The root directory of the webserver.
* `node['apache_test']['remote_host_ip']` - The remote host IP address for
  authorization.
* `node['apache_test']['ssl_dir']` - The local directory containing the generated SSL key and certificate.
* `node['apache_test']['ssl_cert_file']` - The SSL certificate file.
* `node['apache_test']['ssl_cert_key_file']` - The private key.

Recipes
=======

* `default` - Simply includes apache2::default for a vanilla apache install.
* `mod_auth_basic` - Adds a web_app behind basic authentication for testing.
* `mod_auth_digest` - Adds a web_app behind digest authenticaiton for testing.
* `mod_auth_openid` - Adds a web_app behind openid authentication for testing.
* `mod_authnz_ldap` - Adds a web_app behind ldap-based authorization for testing.
* `mod_authz_groupfile` - Adds a web_app behind groupfile-based authorization for testing.
* `mod_authz_listed_host` - Adds a web_app behind host-based authorization for testing.
* `mod_authz_unlisted_host` - Adds a web_app behind host-based authorization for testing.
* `mod_authz_user` - Adds a web_app behind username-based authorization for testing.
* `mod_cgi` - Adds a CGI script (bash) that prints environment variables for testing.
* `mod_dav_svn` - Adds a web_app with an empty Subversion repository for testing.
* `mod_expires` - Adds a web_app that sets caching expiry headers for testing.
* `mod_perl` - Adds a Perl script running under mod_perl that prints environment variables for testing.
* `mod_php5` - Adds a PHP script running under mod_php5 that prints environment variables for testing.
* `mod_proxy_ajp` - Installs Tomcat with examples and configures proxying over AJP.
* `mod_python` - Adds a Python script running under mod_python that prints environment variables for testing.
* `mod_ssl` - Adds a self-signed SSL certificate and default website for testing.
* `mod_status_remote` - Enables remote access to stats for testing.

License and Authors
===================

Author:: Andrew Crump <andrew@kotirisoftware.com>

    Copyright:: 2012, Opscode, Inc

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

        http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
