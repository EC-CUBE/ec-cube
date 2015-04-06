@mod_cgi
Feature: Host CGI scripts

In order to host dynamic websites
As a developer
I want to be able to host CGI scripts

  Scenario: Host CGI scripts
    Given a new webserver with CGI support enabled
     When a request is made to a CGI script that generates a list of environment variables
     Then the expected environment variables will be present
