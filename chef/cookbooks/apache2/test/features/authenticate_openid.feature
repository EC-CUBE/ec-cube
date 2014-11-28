@mod_auth_openid
Feature: OpenID Authentication

In order to perform authorization or vary the provided content
As a developer
I want to authenticate the remote user

  Scenario: Authenticate access to a page
    Given a new webserver configured to require authentication to access a page
     When the user requests the secure page with no credentials
     Then access will be rejected requiring OpenID authentication
