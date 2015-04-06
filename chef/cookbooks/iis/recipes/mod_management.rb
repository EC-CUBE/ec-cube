#
# Author:: Seth Chisamore (<schisamo@opscode.com>)
# Cookbook Name:: iis
# Recipe:: mod_management
#
# Copyright 2011, Opscode, Inc.
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

include_recipe "iis"

if Opscode::IIS::Helper.older_than_windows2008r2?
  features = %w{Web-Mgmt-Console Web-Mgmt-Service}
else
  features = %w{IIS-ManagementConsole IIS-ManagementService}
end

features.each do |feature|
  windows_feature feature do
    action :install
  end
end
