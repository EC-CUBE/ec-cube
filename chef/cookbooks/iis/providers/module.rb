#
# Author:: Jon DeCamp (<jon.decamp@nordstrom.com>)
# Cookbook Name:: iis
# Provider:: site
#
# Copyright:: 2013, Nordstrom, Inc.
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

require 'chef/mixin/shell_out'

include Chef::Mixin::ShellOut
include Windows::Helper

# Support whyrun
def whyrun_supported?
  true
end

# appcmd syntax for adding modules
# appcmd add module /name:string /type:string /preCondition:string
action :add do
  unless @current_resource.exists
    converge_by("add IIS module #{@new_resource.module_name}") do
      cmd = "#{appcmd} add module /module.name:\"#{@new_resource.module_name}\""

      if @new_resource.application
        cmd << " /app.name:\"#{@new_resource.application}\""
      end

      if @new_resource.type
        cmd << " /type:\"#{@new_resource.type}\""
      end

      if @new_resource.precondition
        cmd << " /preCondition:\"#{@new_resource.precondition}\""
      end

      shell_out!(cmd, {:returns => [0,42]})

      Chef::Log.info("#{@new_resource} added module '#{@new_resource.module_name}'")
    end
  else
    Chef::Log.debug("#{@new_resource} module already exists - nothing to do")
  end
end

action :delete do
  if @current_resource.exists
    converge_by("delete IIS module #{@new_resource.module_name}") do

      cmd = "#{appcmd} delete module /module.name:\"#{@new_resource.module_name}\""
      if @new_resource.application
        cmd << " /app.name:\"#{@new_resource.application}\""
      end

      shell_out!(cmd, {:returns => [0,42]})
    end

    Chef::Log.info("#{@new_resource} deleted")
  else
    Chef::Log.debug("#{@new_resource} module does not exist - nothing to do")
  end
end

def load_current_resource
  @current_resource = Chef::Resource::IisModule.new(@new_resource.name)
  @current_resource.module_name(@new_resource.module_name)

  if @new_resource.application
    cmd = shell_out("#{appcmd} list module /module.name:\"#{@new_resource.module_name}\" /app.name:\"#{@new_resource.application}\"")
  else
    cmd = shell_out("#{appcmd} list module /module.name:\"#{@new_resource.module_name}\"")
  end

  # 'MODULE "Module Name" ( type:module.type, preCondition:condition )'
  # 'MODULE "Module Name" ( native, preCondition:condition )'

  Chef::Log.debug("#{@new_resource} list module command output: #{cmd.stdout}")
  if cmd.stdout.empty?
    @current_resource.exists = false
  else
    @current_resource.exists = true
  end
end

private
def appcmd
  @appcmd ||= begin
    "#{node['iis']['home']}\\appcmd.exe"
  end
end
