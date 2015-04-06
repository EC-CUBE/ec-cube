#
# Cookbook Name:: logrotate
# Definition:: logrotate_instance
#
# Copyright 2009, Scott M. Likens
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

log_rotate_params = {
  :enable         => true,
  :frequency      => 'weekly',
  :template       => 'logrotate.erb',
  :cookbook       => 'logrotate',
  :template_mode  => '0440',
  :template_owner => 'root',
  :template_group => 'root',
  :postrotate     => nil,
  :prerotate      => nil,
  :firstaction    => nil,
  :lastaction     => nil,
  :sharedscripts  => false
}

define(:logrotate_app, log_rotate_params) do
  include_recipe 'logrotate::default'

  acceptable_options = %w(missingok compress delaycompress dateext dateyesterday copytruncate notifempty delaycompress ifempty mailfirst nocompress nocopy nocopytruncate nocreate nodelaycompress nomail nomissingok noolddir nosharedscripts notifempty sharedscripts)
  options_tmp = params[:options] ||= %w(missingok compress delaycompress copytruncate notifempty)
  options = options_tmp.respond_to?(:each) ? options_tmp : options_tmp.split

  if params[:enable]
    invalid_options = options - acceptable_options
    unless invalid_options.empty?
      Chef::Application.fatal! "The passed value(s) [#{invalid_options.join(',')}] are not valid"
    end

    template "/etc/logrotate.d/#{params[:name]}" do
      source   params[:template]
      cookbook params[:cookbook]
      mode     params[:template_mode]
      owner    params[:template_owner]
      group    params[:template_group]
      backup   false
      variables(
        :path          => Array(params[:path]).map { |path| path.to_s.inspect }.join(' '),
        :create        => params[:create],
        :frequency     => params[:frequency],
        :dateformat    => params[:dateformat],
        :size          => params[:size],
        :minsize       => params[:minsize],
        :maxsize       => params[:maxsize],
        :su            => params[:su],
        :rotate        => params[:rotate],
        :olddir        => params[:olddir],
        :sharedscripts => params[:sharedscripts],
        :postrotate    => Array(params[:postrotate]).join("\n"),
        :prerotate     => Array(params[:prerotate]).join("\n"),
        :firstaction   => Array(params[:firstaction]).join("\n"),
        :lastaction    => Array(params[:lastaction]).join("\n"),
        :options       => options
      )
    end
  else
    file "/etc/logrotate.d/#{params[:name]}" do
      action :delete
    end
  end
end
