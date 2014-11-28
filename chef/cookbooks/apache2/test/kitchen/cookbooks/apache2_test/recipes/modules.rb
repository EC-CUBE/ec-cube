#
# Cookbook:: apache2_test
# Recipe:: modules
#
# Author:: Joshua Timberman <joshua@opscode.com>
# Copyright:: Copyright (c) 2012, Opscode, Inc.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#    http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
#

include_recipe 'apache2::default'

# Duplicates the list in the modules_test minitest, which is
# distasteful duplication.
%w{
  auth_digest
  authnz_ldap
  dav_fs
  deflate
  expires
  fcgid
  headers
  ldap
  proxy
  proxy_balancer
  proxy_connect
  proxy_http
  rewrite
  wsgi
  xsendfile
}.each do |a2mod|
  include_recipe "apache2::mod_#{a2mod}"
end
