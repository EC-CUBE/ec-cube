#
# Cookbook Name:: apache2_test
# Recipe:: mod_perl
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

include_recipe 'yum::epel' if platform?('centos')

include_recipe 'apache2::mod_perl'

package 'perl-CGI-SpeedyCGI' do
  action :install
  only_if { platform?('redhat', 'centos', 'scientific', 'fedora', 'amazon') }
end

file "#{node['apache']['dir']}/conf.d/apreq.conf" do
  action :delete
  only_if { platform?('redhat', 'centos', 'scientific', 'fedora', 'amazon') }
end

file "#{node['apache']['dir']}/conf.d/perl.conf" do
  action :delete
  only_if { platform?('redhat', 'centos', 'scientific', 'fedora', 'amazon') }
end

directory node['apache_test']['app_dir'] do
  recursive true
  action :create
end

file "#{node['apache_test']['app_dir']}/perl" do
  content %q{
#!/usr/bin/perl -wT
use strict;
use CGI qw(:standard);
use CGI::Carp qw(warningsToBrowser fatalsToBrowser);

print header('text/plain');

foreach my $key (sort(keys(%ENV))) {
    print "$key=$ENV{$key}\n";
}
}.strip
  mode '0755'
  action :create
end

web_app 'perl_env' do
  template 'perl_env.conf.erb'
  app_dir node['apache_test']['app_dir']
end
