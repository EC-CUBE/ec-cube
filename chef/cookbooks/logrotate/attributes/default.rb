#
# Cookbook Name:: logrotate
# Attribute:: default
#
# Copyright 2013, Opscode
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

default['logrotate']['global'] = {
  'weekly' => true,
  'rotate' => 4,
  'create' => '',

  '/var/log/wtmp' => {
    'missingok' => true,
    'monthly' => true,
    'create' => '0664 root utmp',
    'rotate' => 1
  },

  '/var/log/btmp' => {
    'missingok' => true,
    'monthly' => true,
    'create' => '0660 root utmp',
    'rotate' => 1
  }
}
