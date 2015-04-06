#
# Cookbook Name:: apache2
# Recipe:: default
#
# Copyright 2013, ZOZI
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

if platform_family?('debian')
  remote_file "#{Chef::Config[:file_cache_path]}/mod-pagespeed.deb" do
    source node['apache2']['mod_pagespeed']['package_link']
    mode '0644'
    action :create_if_missing
  end

  package 'mod_pagespeed' do
    source "#{Chef::Config[:file_cache_path]}/mod-pagespeed.deb"
    action :install
  end

  apache_module 'pagespeed' do
    conf true
  end
else
  Chef::Log.warn "apache::mod_pagespeed does not support #{node["platform_family"]} yet, and is not being installed"
end
