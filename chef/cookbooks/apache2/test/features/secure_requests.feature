@mod_ssl
Feature: Secure requests

In order to prevent a malicious third party from eavesdropping or hijacking a user session
As a developer
I want to secure communication between the client and server

  Scenario: Request homepage
    Given a new webserver
     When I request the root url over HTTPS
     Then the default page should be returned
