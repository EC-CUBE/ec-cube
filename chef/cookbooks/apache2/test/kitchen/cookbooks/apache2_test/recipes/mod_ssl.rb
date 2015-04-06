#
# Cookbook Name:: apache2_test
# Recipe:: mod_ssl
#
# Copyright 2012, Opscode, Inc.
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

include_recipe 'apache2::default'
include_recipe 'apache2::mod_ssl'

directory node['apache_test']['ssl_dir'] do
  owner node['apache']['user']
  group node['apache']['group']
  recursive true
  action :create
end

execute 'create-private-key' do
  command "openssl genrsa > #{node['apache_test']['ssl_cert_key_file']}"
  not_if "test -f #{node['apache_test']['ssl_cert_key_file']}"
end

execute 'create-certficate' do
  command %Q{openssl req -new -x509 -key #{node['apache_test']['ssl_cert_key_file']} -out #{node['apache_test']['ssl_cert_file']} -days 1 <<EOF
US
Washington
Seattle
Opscode, Inc

example.com
webmaster@example.com
EOF}
  not_if "test -f #{node['apache_test']['ssl_cert_file']}"
end

web_app 'ssl' do
  template 'ssl.conf.erb'
  server_name node['domain']
  document_root node['apache_test']['root_dir']
  ssl_cert_file node['apache_test']['ssl_cert_file']
  ssl_cert_key_file node['apache_test']['ssl_cert_key_file']
end
