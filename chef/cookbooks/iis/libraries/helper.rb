#
# Cookbook Name:: iis
# Library:: helper
#
# Author:: Julian C. Dunn <jdunn@getchef.com>
#
# Copyright 2013, Chef Software, Inc.
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

if RUBY_PLATFORM =~ /mswin|mingw32|windows/
  require 'chef/win32/version'
end

module Opscode
  module Opscode::IIS
    class Helper

      def self.older_than_windows2008r2?
        if RUBY_PLATFORM =~ /mswin|mingw32|windows/
          win_version = Chef::ReservedNames::Win32::Version.new
          win_version.windows_server_2008? ||
            win_version.windows_vista? ||
            win_version.windows_server_2003_r2? ||
            win_version.windows_home_server? ||
            win_version.windows_server_2003? ||
            win_version.windows_xp? ||
            win_version.windows_2000?
        end
      end
    end
  end
end
