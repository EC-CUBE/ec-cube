@mod_authz_user
Feature: Authorize access to content by user

In order to restrict part of my website
As a developer
I want to restrict access to specific users

  Scenario: Authorize named users
    Given a new webserver configured to authorize access to specific named users
     When the authenticated user is listed as authorized
     Then access will be granted

  Scenario: Authorize named users
    Given a new webserver configured to authorize access to specific named users
     When the authenticated user is not listed as authorized
     Then access will be rejected requiring authentication
