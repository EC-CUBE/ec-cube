@mod_perl
Feature: Host Perl applications

In order to host dynamic websites
As a developer
I want to be able to host Perl applications

  Scenario: Host Perl application
    Given a new webserver with Perl support enabled
     When a request is made to a Perl script that generates a list of environment variables
     Then the expected environment variables will be present
