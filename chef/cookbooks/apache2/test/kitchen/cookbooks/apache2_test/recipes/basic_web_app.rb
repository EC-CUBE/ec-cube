#
# Cookbook Name:: apache2_test
# Recipe:: basic_web_app
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

app_dir = "#{node['apache_test']['root_dir']}/basic_web_app"

directory app_dir do
  action :create
end

file "#{app_dir}/index.html" do
  content 'Hello World'
  action :create
end

web_app 'basic_webapp' do
  cookbook 'apache2'
  server_name node['hostname']
  server_aliases [node['fqdn']]
  docroot app_dir
end
