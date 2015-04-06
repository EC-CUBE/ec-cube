#
# Author:: Jon DeCamp (<jon.decamp@nordstrom.com>)
# Cookbook Name:: iis
# Resource:: module
#
# Copyright:: 2012, Nordstrom, Inc.
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

actions :add, :delete

attribute :module_name, :kind_of => String, :name_attribute => true
attribute :type, :kind_of => String, :default => nil
attribute :precondition, :kind_of => String, :default => nil
attribute :application, :kind_of => String, :default => nil

attr_accessor :exists

def initialize(*args)
  super
  @action = :add
end
