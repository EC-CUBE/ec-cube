#
# Cookbook Name:: apache2_test
# Recipe:: mod_dav_svn
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

package 'subversion' do
  action :install
end

include_recipe 'apache2::mod_dav'
include_recipe 'apache2::mod_dav_svn'

directory node['apache_test']['svn_dir'] do
  owner node['apache']['user']
  group node['apache']['group']
  recursive true
  action :create
end

execute 'create-repo' do
  user node['apache']['user']
  command "svnadmin create --config-dir #{Chef::Config[:file_cache_path]} #{node['apache_test']['svn_dir']}"
  not_if "bash -c 'svnadmin verify #{node['apache_test']['svn_dir']}'"
end

web_app 'svn' do
  template 'svn_repo.conf.erb'
  repo_dir node['apache_test']['svn_dir']
end
