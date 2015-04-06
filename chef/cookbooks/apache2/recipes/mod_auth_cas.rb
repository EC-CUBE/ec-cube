#
# Cookbook Name:: apache2
# Recipe:: auth_basic
#
# Copyright 2013, Opscode, Inc.
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

if node['apache']['mod_auth_cas']['from_source']
  package 'httpd-devel' do
    package_name value_for_platform_family(
      %w[rhel fedora suse] => 'httpd-devel',
      'debian' => 'apache2-dev'
    )
  end

  git '/tmp/mod_auth_cas' do
    repository 'git://github.com/Jasig/mod_auth_cas.git'
    revision   node['apache']['mod_auth_cas']['source_revision']
    notifies   :run, 'execute[compile mod_auth_cas]', :immediately
  end

  execute 'compile mod_auth_cas' do
    command './configure && make && make install'
    cwd     '/tmp/mod_auth_cas'
    not_if  "test -f #{node['apache']['libexecdir']}/mod_auth_cas.so"
  end

  template "#{node['apache']['dir']}/mods-available/auth_cas.load" do
    source 'mods/auth_cas.load.erb'
    owner  'root'
    group  node['apache']['root_group']
    mode   '0644'
  end
else
  case node['platform_family']
  when 'debian'
    package 'libapache2-mod-auth-cas'

  when 'rhel', 'fedora'
    yum_package 'mod_auth_cas' do
      notifies :run, 'execute[generate-module-list]', :immediately
    end

    file "#{node['apache']['dir']}/conf.d/auth_cas.conf" do
      action :delete
      backup false
    end
  end
end

apache_module 'auth_cas' do
  conf true
end

directory "#{node['apache']['cache_dir']}/mod_auth_cas" do
  owner node['apache']['user']
  group node['apache']['group']
  mode  '0700'
end
