#
# Author:: Kendrick Martin (kendrick.martin@webtrends.com>)
# Contributor:: David Dvorak (david.dvorak@webtrends.com)
# Cookbook Name:: iis
# Resource:: pool
#
# Copyright:: 2011, Webtrends Inc.
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

actions :add, :config, :delete, :start, :stop, :restart, :recycle

attribute :pool_name, :kind_of => String, :name_attribute => true
attribute :runtime_version, :kind_of => String
attribute :no_managed_code, :kind_of => [TrueClass, FalseClass], :default => false
attribute :pipeline_mode, :kind_of => Symbol, :equal_to => [:Integrated, :Classic]
attribute :private_mem, :kind_of => Integer
attribute :worker_idle_timeout, :kind_of => String
attribute :recycle_after_time, :kind_of => String
attribute :recycle_at_time, :kind_of => String
attribute :max_proc, :kind_of => Integer
attribute :thirty_two_bit, :kind_of => Symbol
attribute :pool_username, :kind_of => String
attribute :pool_password, :kind_of => String

attr_accessor :exists, :running

def initialize(*args)
  super
  @action = :add
end
