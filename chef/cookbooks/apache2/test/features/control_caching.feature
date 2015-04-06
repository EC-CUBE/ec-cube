@mod_expires
Feature: Control caching

In order to control caching of responses by intermediate servers
As a developer
I want to control the expiry times on served pages

  Scenario: Set expiry time
    Given a new webserver with support for setting expiry times enabled
     When I request a path which has a cache directive applied
     Then the expiry time returned will match that configured
