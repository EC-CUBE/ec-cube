@mod_php5
Feature: Host PHP applications

In order to host dynamic websites
As a developer
I want to be able to host PHP websites

  Scenario: Host PHP website
    Given a new webserver with PHP support enabled
     When a request is made to a PHP script that generates a list of environment variables
     Then the expected environment variables will be present
