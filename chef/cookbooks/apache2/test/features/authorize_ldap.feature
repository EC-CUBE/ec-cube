@mod_authnz_ldap
Feature: Authorize access to content against corporate directory

In order to restrict part of my website
As a developer
I want to restrict access to people in my corporate directory

  Scenario: Authorized user access
    Given a new webserver configured to authorize against a corporate directory
     When the authenticated user is listed in the directory as authorized
     Then access will be granted

  Scenario: User not in directory
    Given a new webserver configured to authorize against a corporate directory
     When the authenticated user is not listed in the directory as authorized
     Then access will be rejected requiring authentication
