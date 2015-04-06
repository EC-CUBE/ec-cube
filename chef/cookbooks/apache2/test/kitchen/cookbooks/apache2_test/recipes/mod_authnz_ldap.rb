#
# Cookbook Name:: apache2_test
# Recipe:: mod_authnz_ldap
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

directory '/var/cache/local/preseeding' do
  recursive true
  action :create
  only_if { platform?('debian', 'ubuntu') }
end

include_recipe 'openldap::server'

service 'slapd' do
  action :start
end

cbf = resources("cookbook_file[#{node['openldap']['ssl_dir']}/#{node['openldap']['server']}.pem]")
cbf.cookbook 'apache2_test'

ldif_path = '/tmp/entries.ldif'

template ldif_path do
  source 'entries.ldif.erb'
  action :create
end

bash 'load-directory-entries' do
  code %Q{
    ldapsearch -x -D 'cn=admin,#{node['openldap']['basedn']}' -w '#{node['openldap']['rootpw_plain']}' -b '#{node['openldap']['basedn']}'
    if [ $? -ne 0 ]
    then
      ldapadd -x -D 'cn=admin,#{node['openldap']['basedn']}' -w '#{node['openldap']['rootpw_plain']}' -f #{ldif_path}
    fi
  }
  action :run
end

include_recipe 'apache2::default'
include_recipe 'apache2::mod_ldap'
include_recipe 'apache2::mod_authnz_ldap'

directory "#{node['apache_test']['root_dir']}/secure" do
  action :create
end

web_app 'secure' do
  template 'authnz_ldap.conf.erb'
  base_dn node['openldap']['basedn']
end
