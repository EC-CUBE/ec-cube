#
# Cookbook Name:: apache2
# Recipe:: fastcgi
#
# Copyright 2008-2013, Opscode, Inc.
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
  package 'libapache2-mod-fastcgi'
elsif platform_family?('rhel')
  %w[gcc make libtool httpd-devel apr-devel apr].each do |package|
    yum_package package do
      action :upgrade
    end
  end

  src_filepath  = "#{Chef::Config['file_cache_path']}/fastcgi.tar.gz"
  remote_file 'download fastcgi source' do
    source node['apache']['mod_fastcgi']['download_url']
    path   src_filepath
    backup false
  end

  top_dir = node['apache']['lib_dir']
  bash 'compile fastcgi source' do
    notifies :run, 'execute[generate-module-list]', :immediately
    not_if   "test -f #{node['apache']['dir']}/mods-available/fastcgi.conf"
    cwd      ::File.dirname(src_filepath)
    code <<-EOH
      tar zxf #{::File.basename(src_filepath)} &&
      cd mod_fastcgi-* &&
      cp Makefile.AP2 Makefile &&
      make top_dir=#{top_dir} && make install top_dir=#{top_dir}
    EOH
  end
end

apache_module 'fastcgi' do
  conf true
end
