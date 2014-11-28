@mod_auth_basic
Feature: Basic Authentication

In order to perform authorization or vary the provided content
As a developer
I want to authenticate the remote user

  Scenario: Authenticate access to a page
    Given a new webserver configured to require authentication to access a page
     When the user requests the secure page with no credentials
     Then access will be rejected requiring authentication

  Scenario Outline: Authenticate access to a page (basic authentication)
    Given a new webserver configured to require authentication to access a page
     When the user requests the secure page authenticating with <credentials> over basic auth
     Then access will be <access>
  Examples:
    | credentials         | access                            |
    | valid credentials   | granted                           |
    | invalid credentials | rejected requiring authentication |
