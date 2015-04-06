#
# Cookbook Name:: apache2_test
# Recipe:: mod_auth_basic
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
include_recipe 'apache2::mod_auth_basic'

directory "#{node['apache_test']['root_dir']}/secure" do
  action :create
end

execute 'add-credentials' do
  command "htpasswd -b -c #{node['apache_test']['root_dir']}/secure/.htpasswd #{node['apache_test']['auth_username']} #{node['apache_test']['auth_password']}"
  action :run
end

web_app 'secure' do
  template 'auth_basic.conf.erb'
  auth_user_file "#{node['apache_test']['root_dir']}/secure/.htpasswd"
end
