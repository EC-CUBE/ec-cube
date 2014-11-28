Feature: Host source control repositories

In order to provide access to source control
As a developer
I want to host source control repositories

  @mod_dav_svn
  Scenario: Commit changes
    Given a new webserver with subversion support enabled
      And a subversion repository
     When a developer commits a change to the repository
     Then the change will be visible when browsing the repository
