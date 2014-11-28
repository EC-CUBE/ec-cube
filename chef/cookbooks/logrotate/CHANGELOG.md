logrotate Cookbook CHANGELOG
============================
This file is used to list changes made in each version of the logrotate cookbook.

v1.5.0
------

### Bugs
- Fix missing end tag in template
- Don't re-initialize constants.
- Fix rubocop finding

### Improvements
- [COOK-3911] Allow to use maxsize parameter.
- [COOK-4000] Allow to use dateyesterday option.
- [COOK-4024] Allow to use su parameter.
- [COOK-4175] Allows use of the dateformat parameter.
- Loosen test-kitchen version constraint
- Add rvm files to gitignore


v1.4.0
------
### Bug
- **[COOK-3632](https://tickets.opscode.com/browse/COOK-3632)** - Raise Exception when adding more than one invalid option
- **[COOK-3141](https://tickets.opscode.com/browse/COOK-3141)** - Do not duplicate template entires for multiple paths
- **[COOK-3034](https://tickets.opscode.com/browse/COOK-3034)** - Update logrotate_app params to accept arrays and strings

### Improvement
- **[COOK-2646](https://tickets.opscode.com/browse/COOK-2646)** - Add ability to choose file mode for logrotate template

v1.3.0
------
### Improvement
- **[COOK-3341](https://tickets.opscode.com/browse/COOK-3341)** - Add optional `frequency` and `rotate` params when defined globally
- **[COOK-3298](https://tickets.opscode.com/browse/COOK-3298)** - Use `Array` instead of `respond_to?(:each)`
- **[COOK-3285](https://tickets.opscode.com/browse/COOK-3285)** - Change `logrotate.d` config file mode to `0644`
- **[COOK-3250](https://tickets.opscode.com/browse/COOK-3250)** - Add `minsize`

### Bug
- **[COOK-3274](https://tickets.opscode.com/browse/COOK-3274)** - Fix README typo that suggested the opposite action

### New Feature
- **[COOK-2923](https://tickets.opscode.com/browse/COOK-2923)** - Add `olddir` option
- **[COOK-1651](https://tickets.opscode.com/browse/COOK-1651)** - Add `dateext` ability

v1.2.2
-----
### Bug
- [COOK-2872]: Add firstaction/lastaction ability to logrotate
- [COOK-2908]: Argument error in `logrotate_app` definition

v1.2.0
-----
- [COOK-2401] - Add the ability to manage the global logrotate configuration

v1.1.0
-----
- [COOK-2218] - Logrotate size parameter

v1.0.2
-----
- [COOK-1027] - Add support for pre-/post-rotate commands
- [COOK-1338] - Update log rotate for more flexibility of rotate options
- [COOK-1598] - "Create" isn't a mandatory option
