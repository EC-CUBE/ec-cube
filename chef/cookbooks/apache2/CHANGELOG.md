apache2 Cookbook Changelog
==========================
This file is used to list changes made in each version of the apache2 cookbook.

v1.10.4 (2014-04-23)
--------------------
- [COOK-4249] mod_proxy_http requires mod_proxy


v1.10.2 (2014-04-09)
--------------------
- [COOK-4490] - Fix minitest `apache_configured_ports` helper
- [COOK-4491] - Fix minitest: escape regex interpolation
- [COOK-4492] - Fix service[apache2] CHEF-3694 duplication
- [COOK-4493] - Fix template[ports.conf] CHEF-3694 duplication

As of 2014-04-04 and per [Community Cookbook Diversification](https://wiki.opscode.com/display/chef/Community+Cookbook+Diversification) this cookbook now maintained by OneHealth Solutions. Please be patient as we get into the swing of things.

v1.10.0 (2014-03-28)
--------------------
- [COOK-3990] - Fix minitest failures on EL5
- [COOK-4416] - Support the ability to point to local apache configs
- [COOK-4469] - Use reload instead of restart on RHEL


v1.9.6 (2014-02-28)
-------------------
[COOK-4391] - uncommenting the PIDFILE line


v1.9.4 (2014-02-27)
-------------------
Bumping version for toolchain


v1.9.1 (2014-02-27)
-------------------
[COOK-4348] Allow arbitrary params in sysconfig


v1.9.0 (2014-02-21)
-------------------
### Improvement
- **[COOK-4076](https://tickets.opscode.com/browse/COOK-4076)** - foodcritic: dependencies are not defined properly
- **[COOK-2572](https://tickets.opscode.com/browse/COOK-2572)** - Add mod_pagespeed recipe to apache2 

### Bug
- **[COOK-4043](https://tickets.opscode.com/browse/COOK-4043)** - apache2 cookbook does not depend on 'iptables'
- **[COOK-3919](https://tickets.opscode.com/browse/COOK-3919)** - Move the default pidfile for apache2 on Ubuntu 13.10 or greater
- **[COOK-3863](https://tickets.opscode.com/browse/COOK-3863)** - Add recipe for mod_jk
- **[COOK-3804](https://tickets.opscode.com/browse/COOK-3804)** - Fix incorrect datatype for apache/default_modules, use recipes option in metadata
- **[COOK-3800](https://tickets.opscode.com/browse/COOK-3800)** - Cannot load modules that use non-standard module identifiers
- **[COOK-1689](https://tickets.opscode.com/browse/COOK-1689)** - The perl package name should be configurable


v1.8.14
-------
Version bump for toolchain sanity


v1.8.12
-------
Fixing various style issues for travis


v1.8.10
-------
fixing metadata version error. locking to 3.0"


v1.8.8
------
Version bump for toolchain sanity


v1.8.6
------
Locking yum dependency to '< 3'


v1.8.4
------
### Bug
- **[COOK-3769](https://tickets.opscode.com/browse/COOK-3769)** - Fix a critical bug where the `apache_module` could not enable modules


v1.8.2
------
### Bug
- **[COOK-3766](https://tickets.opscode.com/browse/COOK-3766)** - Fix an issue where the `mod_ssl` recipe fails due to a missing attribute


v1.8.0
------
### Bug
- **[COOK-3680](https://tickets.opscode.com/browse/COOK-3680)** - Update template paths
- **[COOK-3570](https://tickets.opscode.com/browse/COOK-3570)** - Apache cookbook breaks on RHEL / CentOS 6
- **[COOK-2944](https://tickets.opscode.com/browse/COOK-2944)** - Fix foodcritic failures
- **[COOK-2893](https://tickets.opscode.com/browse/COOK-2893)** - Improve mod_auth_openid recipe with guards and idempotency
- **[COOK-2758](https://tickets.opscode.com/browse/COOK-2758)** - Fix use of non-existent attribute

### New Feature
- **[COOK-3665](https://tickets.opscode.com/browse/COOK-3665)** - Add recipe for mod_userdir
- **[COOK-3646](https://tickets.opscode.com/browse/COOK-3646)** - Add recipe for mod_cloudflare
- **[COOK-3213](https://tickets.opscode.com/browse/COOK-3213)** - Add recipe for mod_info

### Improvement
- **[COOK-3656](https://tickets.opscode.com/browse/COOK-3656)** - Parameterize apache2 binary
- **[COOK-3562](https://tickets.opscode.com/browse/COOK-3562)** - Allow mod_proxy settings to be configured as attributes
- **[COOK-3326](https://tickets.opscode.com/browse/COOK-3326)** - Fix default_test to use ServerTokens attribute
- **[COOK-2635](https://tickets.opscode.com/browse/COOK-2635)** - Add support for SVG mime types
- **[COOK-2598](https://tickets.opscode.com/browse/COOK-2598)** - FastCGI Module only works on Debian-based platforms
- **[COOK-1984](https://tickets.opscode.com/browse/COOK-1984)** - Add option to configure the address apache listens to


v1.7.0
------
### Improvement

- [COOK-3073]: make access.log location configurable per-platform
- [COOK-3074]: don't hardcode the error.log location in the default site config
- [COOK-3268]: don't hardcode DocumentRoot and cgi-bin locations in `default_site`

### New Feature

- [COOK-3184]: Add `mod_filter` recipe to Apache2-cookbook
- [COOK-3236]: Add `mod_action` recipe to Apache2-cookbook

v1.6.6
------
1.6.4 had a missed step in the automated release, long live 1.6.6.

### Bug

- [COOK-3018]: apache2_module does duplicate delayed restart of apache2 service when conf = true
- [COOK-3027]: Default site enable true, then false, does not disable default site
- [COOK-3109]: fix apache lib_dir arch attribute regexp

v1.6.2
------
- [COOK-2535] - `mod_auth_openid` requires libtool to run autogen.sh
- [COOK-2667] - Typo in usage documentation
- [COOK-2461] - `apache2::mod_auth_openid` fails on some ubuntu systems
- [COOK-2720] - Apache2 minitest helper function `ran_recipe` is not portable

v1.6.0
------
- [COOK-2372] - apache2 mpm_worker: add ServerLimit attribute (default to 16)

v1.5.0
------
The `mod_auth_openid` attributes are changed. The upstream maintainer deprecated the older release versions, and the source repository has releases available at specific SHA1SUM references. The new attribute, `node['apache']['mod_auth_openid']['ref']` is used to set this.

- [COOK-2198] - `apache::mod_auth_openid` compiles from source, but does not install make on debian/ubuntu
- [COOK-2224] - version conflict between cucumber and other gems
- [COOK-2248] - `apache2::mod_php5` uses `not_if` "which php" without ensuring package 'which' is installed
- [COOK-2269] - Set allow list for mod_status incase external monitor scripts need
- [COOK-2276] - cookbook apache2 documentation regarding listening ports doesn't match default attributes
- [COOK-2296] - `mod_auth_openid` doesn't have tags/releases for the version I need for features and fixes
- [COOK-2323] - Add Oracle linux support

v1.4.2
------
- [COOK-1721] - fix logrotate recipe

v1.4.0
------
- [COOK-1456] - iptables enhancements
- [COOK-1473] - apache2 does not disable default site when setting "`default_site_enabled`" back to false
- [COOK-1824] - the apache2 cookbook needs to specify which binary is used on rhel platform
- [COOK-1916] - Download location wrong for apache2 `mod_auth_openid` >= 0.7
- [COOK-1917] - Improve `mod_auth_openid` recipe to handle module upgrade more gracefully
- [COOK-2029] - apache2 restarts on every run on RHEL and friends, generate-module-list on every run.
- [COOK-2036] - apache2: Cookbook style

v1.3.2
------
- [COOK-1804] - fix `web_app` definition parameter so site can be disabled.

v1.3.0
------
- [COOK-1738] - Better configuration for `mod_include` and some overrides in `web_app` definition
- [COOK-1470] - Change SSL Ciphers to Mitigate BEAST attack

v1.2.0
------
- [COOK-692] - delete package conf.d files in module recipes, for EL
- [COOK-1693] - Foodcritic finding for unnecessary string interpolation
- [COOK-1757] - platform_family and better style / usage practices

v1.1.16
-------
re-releasing as .16 due to error on tag 1.1.14

- [COOK-1466] - add `mod_auth_cas` recipe
- [COOK-1609] - apache2 changes ports.conf twice per run when using apache2::mod_ssl

v1.1.12
-------
- [COOK-1436] - restore apache2 web_app definition
- [COOK-1356] - allow ExtendedStatus via attribute
- [COOK-1403] - add mod_fastcgi recipe

v1.1.10
-------
- [COOK-1315] - allow the default site to not be enabled
- [COOK-1328] - cookbook tests (minitest, cucumber)

v1.1.8
------
- Some platforms with minimal installations that don't have perl won't have a `node['languages']['perl']` attribute, so remove the conditional and rely on the power of idempotence in the package resource.
- [COOK-1214] - address foodcritic warnings
- [COOK-1180] - add `mod_logio` and fix `mod_proxy`

v1.1.6
------
FreeBSD users: This release requires the `freebsd` cookbook. See README.md.

- [COOK-1025] - freebsd support in mod_php5 recipe

v1.1.4
------
- [COOK-1100] - support amazon linux

v1.1.2
------
- [COOK-996] - apache2::mod_php5 can cause PHP and module API mismatches
- [COOK-1083] - return string for v_f_p and use correct value for default

v1.1.0
------
- [COOK-861] - Add `mod_perl` and apreq2
- [COOK-941] - fix `mod_auth_openid` on FreeBSD
- [COOK-1021] - add a commented-out LoadModule directive to keep apxs happy
- [COOK-1022] - consistency for icondir attribute
- [COOK-1023] - fix platform test for attributes
- [COOK-1024] - fix a2enmod script so it runs cleanly on !bash
- [COOK-1026] - fix `error_log` location on FreeBSD

v1.0.8
------
- COOK-548 - directory resource doesn't have backup parameter

v1.0.6
------
- COOK-915 - update to `mod_auth_openid` version 0.6, see __Recipes/mod_auth_openid__ below.
- COOK-548 - Add support for FreeBSD.

v1.0.4
------
- COOK-859 - don't hardcode module paths

v1.0.2
------
- Tickets resolved in this release: COOK-788, COOK-782, COOK-780

v1.0.0
------
- Red Hat family support is greatly improved, all recipes except `god_monitor` converge.
- Recipe `mod_auth_openid` now works on RHEL family distros
- Recipe `mod_php5` will now remove config from package on RHEL family so it doesn't conflict with the cookbook's.
- Added `php5.conf.erb` template for `mod_php5` recipe.
- Create the run state directory for `mod_fcgid` to prevent a startup error on RHEL version 6.
- New attribute `node['apache']['lib_dir']` to handle lib vs lib64 on RHEL family distributions.
- New attribute `node['apache']['group']`.
- Scientific Linux support added.
- Use a file resource instead of the generate-module-list executed perl script on RHEL family.
- "default" site can now be disabled.
- web_app now has an "enable" parameter.
- Support for dav_fs apache module.
- Tickets resolved in this release: COOK-754, COOK-753, COOK-665, COOK-624, COOK-579, COOK-519, COOK-518
- Fix node references in template for a2dissite
- Use proper user and group attributes on files and templates.
- Replace the anemic README.rdoc with this new and improved superpowered README.md :).
