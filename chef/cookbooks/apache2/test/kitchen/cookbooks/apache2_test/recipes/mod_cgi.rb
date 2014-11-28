#
# Cookbook Name:: apache2_test
# Recipe:: mod_cgi
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
include_recipe 'apache2::mod_cgi'

directory node['apache_test']['cgi_dir'] do
  action :create
end

file "#{node['apache_test']['cgi_dir']}/env" do
  content %q{
#!/bin/bash
echo -e "Content-type: text/plain\n"
/usr/bin/env
}.strip
  mode '0755'
  action :create
end
