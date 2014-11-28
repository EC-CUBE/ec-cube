#
# Author::  Seth Chisamore (<schisamo@opscode.com>)
# Author::  Lucas Hansen (<lucash@opscode.com>)
# Cookbook Name:: php
# Recipe:: package
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

if platform?('windows')

  include_recipe 'iis::mod_cgi'

  install_dir = File.expand_path(node['php']['conf_dir']).gsub('/', '\\')
  windows_package node['php']['windows']['msi_name'] do
    source node['php']['windows']['msi_source']
    installer_type :msi

    options %W[
          /quiet
          INSTALLDIR="#{install_dir}"
          ADDLOCAL=#{node['php']['packages'].join(',')}
    ].join(' ')
  end

  # WARNING: This is not the out-of-the-box go-pear.phar. It's been modified to patch this bug:
  # http://pear.php.net/bugs/bug.php?id=16644
  cookbook_file "#{node['php']['conf_dir']}/PEAR/go-pear.phar" do
    source 'go-pear.phar'
  end

  template "#{node['php']['conf_dir']}/pear-options" do
    source 'pear-options.erb'
  end

  execute 'install-pear' do
    cwd node['php']['conf_dir']
    command 'go-pear.bat < pear-options'
    creates "#{node['php']['conf_dir']}/pear.bat"
  end

  ENV['PATH'] += ";#{install_dir}"
  windows_path install_dir

else
  node['php']['packages'].each do |pkg|
    package pkg do
      action :install
    end
  end
end

include_recipe "php::ini"
