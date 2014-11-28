#
# Cookbook Name:: apache2_test
# Recipe:: mod_ajp
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
include_recipe 'apache2::mod_proxy'
include_recipe 'apache2::mod_proxy_ajp'

if platform_family?('rhel') && node['platform_version'].to_f < 6.0
  # include jpackage
  include_recipe 'jpackage::default'
end

include_recipe 'tomcat::default'

if platform?('debian', 'ubuntu')
  package 'tomcat6-examples' do
    action :install
  end
else
  package 'tomcat6-webapps' do
    action :install
  end
end

web_app 'java_env' do
  template 'java_env.conf.erb'
  ajp_host 'localhost'
  ajp_port 8009
end
