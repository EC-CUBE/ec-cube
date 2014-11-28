Feature: Authorize access to content by host

In order to restrict part of my website
As a developer
I want to restrict access to known remote hosts

  @mod_authz_listed_host
  Scenario: Known remote address
    Given a new webserver configured to authorize access based on the remote address
     When the remote address is listed as authorized
     Then access will be granted

  @mod_authz_unlisted_host
  Scenario: Unlisted remote address
    Given a new webserver configured to authorize access based on the remote address
     When the remote address is not listed as authorized
     Then access will be denied
