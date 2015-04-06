maintainer       'Andrew Crump'
maintainer_email 'andrew@kotirisoftware.com'
license          'Apache 2.0'
description      'Acceptance tests for apache2'
long_description IO.read(File.join(File.dirname(__FILE__), 'README.md'))
version          '0.1.0'

depends          'apache2'
depends          'jpackage'
depends          'openldap'
depends          'tomcat'
depends          'yum', '< 3.0'

recipe           'apache2_test::default', 'Test example for default recipe'
recipe           'apache2_test::mod_auth_basic', 'Test example for basic authentication'
recipe           'apache2_test::mod_auth_digest', 'Test example for digest authentication'
recipe           'apache2_test::mod_auth_openid', 'Test example for openid authentication'
recipe           'apache2_test::mod_authnz_ldap', 'Test example for LDAP authentication'
recipe           'apache2_test::mod_authz_groupfile', 'Test example for group file authorization'
recipe           'apache2_test::mod_authz_listed_host', 'Test example for host-based authorization'
recipe           'apache2_test::mod_authz_unlisted_host', 'Test example for hosted-based authorization'
recipe           'apache2_test::mod_authz_user', 'Test example for named user authorization'
recipe           'apache2_test::mod_cgi', 'Test example for hosting a CGI script'
recipe           'apache2_test::mod_expires', 'Test example for setting cache expiry headers'
recipe           'apache2_test::mod_dav_svn', 'Test example for Subversion repository hosting'
recipe           'apache2_test::mod_perl', 'Test example for hosting a Perl application'
recipe           'apache2_test::mod_proxy_ajp', 'Test example for proxying requests to a Java application'
recipe           'apache2_test::mod_php5', 'Test example for hosting a PHP application'
recipe           'apache2_test::mod_python', 'Test example for hosting a Python application'
recipe           'apache2_test::mod_ssl', 'Test example for SSL'
recipe           'apache2_test::mod_status_remote', 'Test example for viewing server status'

%w{centos ubuntu}.each do |os|
  supports os
end

attribute 'apache_test/auth_username',
          :display_name => 'Test Username',
          :description => 'Username for the test user',
          :default => 'bork'

attribute 'apache_test/auth_password',
          :display_name => 'Test Password',
          :description => 'Password for the test user',
          :default => 'secret'

attribute 'apache_test/cache_expiry_seconds',
          :display_name => 'Cache Expiry (Seconds)',
          :description => 'The expiry time to set in caching response headers',
          :default => '60'

attribute 'apache_test/app_dir',
          :display_name => 'Application Directory',
          :description => 'Parent directory to deploy test applications under',
          :default => '/home/apache2/env'

attribute 'apache_test/cgi_dir',
          :display_name => 'CGI Directory',
          :description => 'Directory to install CGI scripts into',
          :default => '/usr/lib/cgi-bin'

attribute 'apache_test/root_dir',
          :display_name => 'Root Directory',
          :description => 'Webserver document root directory',
          :default => '/var/www'

attribute 'apache_test/remote_host_ip',
          :display_name => 'Remote Host IP',
          :description => 'IP Address to allow requests from',
          :default => '192.168'

attribute 'apache_test/ssl_dir',
          :display_name => 'SSL Directory',
          :description => 'Directory for SSL certificates',
          :default => '/home/apache2'

attribute 'apache_test/ssl_cert_file',
          :display_name => 'SSL Certificate Path',
          :description => 'File path for the generated self-signed certificate'

attribute 'apache_test/ssl_cert_key_file',
          :display_name => 'SSL Certificate Private Key',
          :description => 'File path for the generated private key'

attribute 'apache_test/svn_dir',
          :display_name => 'Subversion Directory',
          :description => 'File path for test Subversion repository',
          :default => '/home/apache2/svn'
