@mod_authz_groupfile
Feature: Authorize access to content by user

In order to restrict part of my website
As a developer
I want to restrict access to specific users

  Scenario: Authorize based on group file
    Given a new webserver configured to authorize users listed in a group file
     When the authenticated user is listed in the file
     Then access will be granted

  Scenario: Valid authentication but not a member of the group
    Given a new webserver configured to authorize users listed in a group file
     When the authenticated user is not listed in the file
     Then access will be rejected requiring authentication
