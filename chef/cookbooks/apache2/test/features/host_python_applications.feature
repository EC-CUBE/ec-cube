@mod_python
Feature: Host Python applications

In order to host dynamic websites
As a developer
I want to be able to host Python applications

  Scenario: Host Python website
    Given a new webserver with Python support enabled
     When a request is made to a Python script that generates a list of environment variables
     Then the expected environment variables will be present
