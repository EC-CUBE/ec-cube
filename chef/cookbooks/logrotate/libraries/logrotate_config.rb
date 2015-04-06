#
# Cookbook Name:: logrotate
# Library:: CookbookLogrotate
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

# Helper module for Logrotate configuration
module CookbookLogrotate
  DIRECTIVES = %w[
    compress        copy        copytruncate    daily           dateext
    delaycompress   ifempty     mailfirst       maillast        missingok
    monthly         nocompress  nocopy          nocopytruncate  nocreate
    nodelaycompress nodateext   nomail          nomissingok     noolddir
    nosharedscripts noshred     notifempty      sharedscripts   shred
    weekly          yearly
  ] unless const_defined?(:DIRECTIVES)

  VALUES = %w[
    compresscmd    uncompresscmd  compressext    compressoptions
    create         dateformat     include        mail
    maxage         minsize        rotate         size
    shredcycles    start          tabooext
  ] unless const_defined?(:VALUES)

  SCRIPTS = %w[firstaction  prerotate  postrotate  lastaction] unless const_defined?(:SCRIPTS)

  DIRECTIVES_AND_VALUES = DIRECTIVES + VALUES unless const_defined?(:DIRECTIVES_AND_VALUES)

  # Helper class for creating configurations
  class LogrotateConfiguration
    attr_reader :directives, :values, :paths

    class << self
      def from_hash(hash)
        new(hash)
      end

      def directives_from(hash)
        hash.select { |k, v| DIRECTIVES.include?(k) && v }.keys
      end

      def values_from(hash)
        hash.select { |k| VALUES.include?(k) }
      end

      def paths_from(hash)
        hash.select { |k| !(DIRECTIVES_AND_VALUES.include?(k)) }.reduce({}) do | accum_paths, (path, config) |
          accum_paths[path] = {
            'directives' => directives_from(config),
            'values' => values_from(config),
            'scripts' => scripts_from(config)
          }

          accum_paths
        end
      end

      def scripts_from(hash)
        defined_scripts = hash.select { |k| SCRIPTS.include?(k) }
        defined_scripts.reduce({}) do | accum_scripts, (script, lines) |
          if lines.respond_to?(:join)
            accum_scripts[script] = lines.join("\n")
          else
            accum_scripts[script] = lines
          end

          accum_scripts
        end
      end
    end

    private

    def initialize(hash)
      @directives = LogrotateConfiguration.directives_from(hash)
      @values = LogrotateConfiguration.values_from(hash)
      @paths = LogrotateConfiguration.paths_from(hash)
    end
  end
end
