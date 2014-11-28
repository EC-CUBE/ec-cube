Description
===========

Installs and configures Microsoft Internet Information Services (IIS) 7.0/7.5/8.0

Requirements
============

Platform
--------

* Windows Vista
* Windows 7
* Windows 8
* Windows Server 2008 (R1, R2)
* Windows Server 2012
* Windows Server 2012R2

Windows 2003R2 is *not* supported because it lacks Add/Remove Features.

Cookbooks
---------

* windows

Attributes
==========

* `node['iis']['home']` - IIS main home directory. default is `%WINDIR%\System32\inetsrv`
* `node['iis']['conf_dir']` - location where main IIS configs lives. default is `%WINDIR%\System32\inetsrv\config`
* `node['iis']['pubroot']` - . default is `%SYSTEMDRIVE%\inetpub`
* `node['iis']['docroot']` - IIS web site home directory. default is `%SYSTEMDRIVE%\inetpub\wwwroot`
* `node['iis']['log_dir']` - location of IIS logs. default is `%SYSTEMDRIVE%\inetpub\logs\LogFiles`
* `node['iis']['cache_dir']` - location of cached data. default is `%SYSTEMDRIVE%\inetpub\temp`

Resource/Provider
=================

iis_site
---------

Allows easy management of IIS virtual sites (ie vhosts).

### Actions

- :add: - add a new virtual site
- :delete: - delete an existing virtual site
- :start: - start a virtual site
- :stop: - stop a virtual site
- :restart: - restart a virtual site

### Attribute Parameters

- product_id: name attribute. Specifies the ID of a product to install.
- site_name: name attribute.
- site_id: . if not given IIS generates a unique ID for the site
- path: IIS will create a root application and a root virtual directory mapped to this specified local path
- protocol: http protocol type the site should respond to. valid values are :http, :https. default is :http
- port: port site will listen on. default is 80
- host_header: host header (also known as domains or host names) the site should map to. default is all host headers
- options: additional options to configure the site
- bindings: Advanced options to configure the information required for requests to communicate with a Web site. See http://www.iis.net/configreference/system.applicationhost/sites/site/bindings/binding for parameter format. When binding is used, port protocol and host_header should not be used.
- application_pool: set the application pool of the site
- options: support for additional options -logDir, -limits, -ftpServer, etc...

### Examples

    # stop and delete the default site
    iis_site 'Default Web Site' do
      action [:stop, :delete]
    end

    # create and start a new site that maps to
    # the physical location C:\inetpub\wwwroot\testfu
    iis_site 'Testfu Site' do
      protocol :http
      port 80
      path "#{node['iis']['docroot']}/testfu"
      action [:add,:start]
    end

    # do the same but map to testfu.opscode.com domain
    iis_site 'Testfu Site' do
      protocol :http
      port 80
      path "#{node['iis']['docroot']}/testfu"
      host_header "testfu.opscode.com"
      action [:add,:start]
    end

iis_config
-----------
Runs a config command on your IIS instance.

### Actions

- :config: - Runs the configuration command

### Attribute Parameters

- cfg_cmd: name attribute. What ever command you would pass in after "appcmd.exe set config"

### Example

    #Sets up logging
    iis_config "/section:system.applicationHost/sites /siteDefaults.logfile.directory:"D:\\logs"" do
        action :config
    end

    #Loads an array of commands from the node
    cfg_cmds = node['iis']['cfg_cmd']
    cfg_cmds.each do |cmd|
        iis_config "#{cmd}" do
            action :config
        end
    end

iis_pool
---------
Creates an application pool in IIS.

### Actions

- :add: - add a new application pool
- :delete: - delete an existing application pool
- :start: - start a application pool
- :stop: - stop a application pool
- :restart: - restart a application pool

### Attribute Parameters

- pool_name: name attribute. Specifies the name of the pool to create.
- runtime_version: specifies what .NET version of the runtime to use.
- pipeline_mode: specifies what pipeline mode to create the pool with
- private_mem: specifies the amount of private memory (in kilobytes) after which you want the pool to recycle
- worker_idle_timeout: specifies the idle time-out value for a pool, d.hh:mm:ss, d optional
- recycle_after_time: specifies a pool to recycle at regular time intervals, d.hh:mm:ss, d optional
- recycle_at_time: schedule a pool to recycle at a specific time, d.hh:mm:ss, d optional
- max_proc: specifies the number of worker processes associated with the pool.
- thirty_two_bit: set the pool to run in 32 bit mode, true or false
- no_managed_code: allow Unmanaged Code in setting up IIS app pools

### Example

     #creates a new app pool
     iis_pool 'myAppPool_v1_1' do
         runtime_version "2.0"
         pipeline_mode :Classic
         action :add
     end


iis_app
--------

Creates an application in IIS.

### Actions

- :add: - add a new application pool
- :delete: - delete an existing application pool

### Attribute Parameters

- app_name: name attribute. The name of the site to add this app to
- path: The virtual path for this application
- applicationPool: The pool this application belongs to
- physicalPath: The physical path where this app resides.
- enabled_protocols: The enabled protocols that this app provides (http, https, net.pipe, net.tcp, etc)

### Example

    #creates a new app
    iis_app "myApp" do
      path "/v1_1"
      application_pool "myAppPool_v1_1"
      physical_path "#{node['iis']['docroot']}/testfu/v1_1"
      enabled_protocols "http,net.pipe"
      action :add
    end

iis_module
--------

Manages modules globally or on a per site basis.

### Actions

- :add: - add a new module
- :delete: - delete a module

### Attribute Parameters

- module_name: The name of the module to add or delete
- type: The type of module
- precondition: precondition for module
- application: The application or site to add the module to

### Example

    # Adds a module called "My 3rd Party Module" to mySite/
    iis_module "My 3rd Party Module" do
      application "mySite/"
      precondition "bitness64"
      action :add
    end

    # Adds a module called "MyModule" to all IIS sites on the server
    iis_module "MyModule"


Usage
=====

default
-------

Installs and configures IIS 7.0/7.5/8.0 using the default configuration.

mod_*
-----

This cookbook also contains recipes for installing individual IIS modules (extensions).  These recipes can be included in a node's run_list to build the minimal desired custom IIS installation.

* `mod_aspnet` - installs ASP.NET runtime components
* `mod_auth_basic` - installs Basic Authentication support
* `mod_auth_windows` - installs Windows Authentication (authenticate clients by using NTLM or Kerberos) support
* `mod_compress_dynamic` - installs dynamic content compression support. *PLEASE NOTE* - enabling dynamic compression always gives you more efficient use of bandwidth, but if your server's processor utilization is already very high, the CPU load imposed by dynamic compression might make your site perform more slowly.
* `mod_compress_static` - installs static content compression support
* `mod_iis6_metabase_compat` - installs IIS 6 Metabase Compatibility component.
* `mod_isapi` - installs ISAPI (Internet Server Application Programming Interface) extension and filter support.
* `mod_logging` - installs and enables HTTP Logging (logging of Web site activity), Logging Tools (logging tools and scripts) and Custom Logging (log any of the HTTP request/response headers, IIS server variables, and client-side fields with simple configuration) support
* `mod_management` - installs Web server Management Console which supports management of local and remote Web servers
* `mod_security` - installs URL Authorization (Authorizes client access to the URLs that comprise a Web application), Request Filtering (configures rules to block selected client requests) and IP Security (allows or denies content access based on IP address or domain name) support.
* `mod_tracing` -  installs support for tracing ASP.NET applications and failed requests.

Note: Not every possible IIS module has a corresponding recipe. The foregoing recipes are included for convenience, but users may also place additional IIS modules that are installable as Windows features into the ``node['iis']['components']`` array.

License and Author
==================

* Author:: Seth Chisamore (<schisamo@opscode.com>)
* Author:: Julian Dunn (<jdunn@getchef.com>)

Copyright:: 2011-2013, Chef Software, Inc.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
