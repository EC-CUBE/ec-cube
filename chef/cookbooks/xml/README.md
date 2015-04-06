XML Cookbook
============
[![Build Status](https://secure.travis-ci.org/opscode-cookbooks/xml.png?branch=master)](http://travis-ci.org/opscode-cookbooks/xml)

Installs development package for libxml.


Requirements
------------
Debian, Ubuntu, CentOS, Red Hat, Scientific, Fedora, SUSE, ArchLinux

Attributes
----------
- `node['xml']['packages']` - Array of package names that should be installed
- `node['xml']['nokogiri']['use_system_libraries']` - Whether to use system libraries for nokogiri (defaults to `true`)


Recipes
-------
### default
Installs the development packages for libxml2 and libxslt.

For installing the packages during compile time:

```ruby
node.set['xml']['compiletime'] = true
include_recipe 'xml::default'
```

### ruby
Installs the nokogiri gem into Chef's Ruby environment so it can be used in recipes.


License & Authors
-----------------
- Author:: Joshua Timberman (<joshua@opscode.com>)

```text
Copyright 2009-2013, Opscode, Inc.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
```
