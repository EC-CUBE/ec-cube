#
# Author:: Seth Chisamore (<schisamo@opscode.com>)
# Cookbook Name:: php
# Attribute:: default
#
# Copyright 2011, Opscode, Inc.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
#

lib_dir = 'lib'
default['php']['install_method'] = 'package'
default['php']['directives'] = {}
default['php']['bin'] = 'php'

default['php']['pear'] = 'pear'
default['php']['pecl'] = 'pecl'

case node['platform_family']
when 'rhel', 'fedora'
  lib_dir = node['kernel']['machine'] =~ /x86_64/ ? 'lib64' : 'lib'
  default['php']['conf_dir']      = '/etc'
  default['php']['ext_conf_dir']  = '/etc/php.d'
  default['php']['fpm_user']      = 'nobody'
  default['php']['fpm_group']     = 'nobody'
  default['php']['ext_dir']       = "/usr/#{lib_dir}/php/modules"
  if node['platform_version'].to_f < 6
    default['php']['packages'] = %w{ php53 php53-devel php53-cli php-pear }
  else
    default['php']['packages'] = %w{ php php-devel php-cli php-pear }
  end
when 'debian'
  default['php']['conf_dir']      = '/etc/php5/cli'
  default['php']['ext_conf_dir']  = '/etc/php5/conf.d'
  default['php']['fpm_user']      = 'www-data'
  default['php']['fpm_group']     = 'www-data'
  default['php']['packages']      = %w{ php5-cgi php5 php5-dev php5-cli php-pear }
when 'suse'
  default['php']['conf_dir']      = '/etc/php5/cli'
  default['php']['ext_conf_dir']  = '/etc/php5/conf.d'
  default['php']['fpm_user']      = 'wwwrun'
  default['php']['fpm_group']     = 'www'
  default['php']['packages']      = %w{ apache2-mod_php5 php5-pear }
  lib_dir = node['kernel']['machine'] =~ /x86_64/ ? 'lib64' : 'lib'
when 'windows'
  default['php']['windows']['msi_name']      = 'PHP 5.3.28'
  default['php']['windows']['msi_source']    = 'http://windows.php.net/downloads/releases/php-5.3.28-nts-Win32-VC9-x86.msi'
  default['php']['bin']           = 'php.exe'
  default['php']['conf_dir']      = 'C:\Program Files (x86)\PHP'
  default['php']['ext_conf_dir']  = node['php']['conf_dir']
  # These extensions are installed by default by the GUI MSI
  default['php']['packages']      = %w{ cgi ScriptExecutable PEAR
                                        iis4FastCGI ext_php_bz2 ext_php_curl
                                        ext_php_exif ext_php_gd2 ext_php_gettext
                                        ext_php_gmp ext_php_imap ext_php_mbstring
                                        ext_php_mysql ext_php_mysqli ext_php_openssl
                                        ext_php_pdo_mysql ext_php_pdo_odbc ext_php_pdo_sqlite
                                        ext_php_pgsql ext_php_soap ext_php_sockets
                                        ext_php_sqlite3 ext_php_tidy ext_php_xmlrpc
                                      }
  default['php']['pear']          = 'pear.bat'
  default['php']['pecl']          = 'pecl.bat'
else
  default['php']['conf_dir']      = '/etc/php5/cli'
  default['php']['ext_conf_dir']  = '/etc/php5/conf.d'
  default['php']['fpm_user']      = 'www-data'
  default['php']['fpm_group']     = 'www-data'
  default['php']['packages']      = %w{ php5-cgi php5 php5-dev php5-cli php-pear }
end

default['php']['url'] = 'http://us1.php.net/get'
default['php']['version'] = '5.5.9'
default['php']['checksum'] = '378de162efdaeeb725ed38d7fe956c9f0b9084ff'
default['php']['prefix_dir'] = '/usr/local'

default['php']['configure_options'] = %W{--prefix=#{php['prefix_dir']}
                                         --with-libdir=#{lib_dir}
                                         --with-config-file-path=#{php['conf_dir']}
                                         --with-config-file-scan-dir=#{php['ext_conf_dir']}
                                         --with-pear
                                         --enable-fpm
                                         --with-fpm-user=#{php['fpm_user']}
                                         --with-fpm-group=#{php['fpm_group']}
                                         --with-zlib
                                         --with-openssl
                                         --with-kerberos
                                         --with-bz2
                                         --with-curl
                                         --enable-ftp
                                         --enable-zip
                                         --enable-exif
                                         --with-gd
                                         --enable-gd-native-ttf
                                         --with-gettext
                                         --with-gmp
                                         --with-mhash
                                         --with-iconv
                                         --with-imap
                                         --with-imap-ssl
                                         --enable-sockets
                                         --enable-soap
                                         --with-xmlrpc
                                         --with-libevent-dir
                                         --with-mcrypt
                                         --enable-mbstring
                                         --with-t1lib
                                         --with-mysql
                                         --with-mysqli=/usr/bin/mysql_config
                                         --with-mysql-sock
                                         --with-sqlite3
                                         --with-pdo-mysql
                                         --with-pdo-sqlite}

default['php']['ini']['template'] = "php.ini.erb"
default['php']['ini']['cookbook'] = "php"
